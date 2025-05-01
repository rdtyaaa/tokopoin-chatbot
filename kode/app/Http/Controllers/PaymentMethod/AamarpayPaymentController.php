<?php

namespace App\Http\Controllers\PaymentMethod;

use App\Enums\PaymentType;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Utility\PaymentInsert;
use App\Http\Utility\Wallet\WalletRecharge;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\PaymentLog;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
class AamarpayPaymentController extends Controller
{
  

    public $paymentMethod;



    public function __construct(){
        $this->paymentMethod = PaymentMethod::with(['currency'])
                                                ->where('unique_code', 'AAMARPAY107')
                                                ->first();

    }

    public function payment($trx_code = null)
    {
 

        try {

                $baseURL = $this->paymentMethod->payment_parameter->is_sandbox == StatusEnum::true->status()  
                                                                    ? "https://sandbox.aamarpay.com/jsonpost.php" 
                                                                    : "https://secure.aamarpay.com/jsonpost.php";

                $paymentTrackNumber = $trx_code ??  session()->get('payment_track');
                
                $paymentLog = PaymentLog::with(['user','order','seller'])
                                              ->where('status', PaymentLog::PENDING)
                                              ->where('trx_number', $paymentTrackNumber)
                                              ->first();

                if(!$paymentLog || !$this->paymentMethod)  return redirect()->route('home')
                                                                            ->with('error', translate('Invalaid Transaction'));



                if($paymentLog->type == PaymentType::WALLET->value ){

                    if($paymentLog->seller){
                        $seller     = @$paymentLog->seller;
                        $email      = $seller->email;
                        $phone      = $seller->phone;
                        $first_name = $seller->name;
                        $address    = @$seller->address->address;
                        $zip        = @$seller->address->zip;
                        $city       = @$seller->address->city;
                        $state      = @$seller->address->state;
                        $country    = 'USA';
                    }

                    elseif($paymentLog->user){
                        $user     = @$paymentLog->user;
                        $email      = $user->email;
                        $phone      = $user->phone;
                        $first_name = $user->name;
                        $address    = @$user->address->address;
                        $zip        = @$user->address->zip;
                        $city       = @$user->address->city;
                        $state      = @$user->address->state;
                        $country    = 'USA';
                    }
        
                }
                else{
    
                        $order =  @$paymentLog->order;

                        $phone      = @$order->billingAddress ? @$order->billingAddress->phone : @$order->billing_information->phone;
                        $email      = @$order->billingAddress ? @$order->billingAddress->email : @$order->billing_information->email;
                        $first_name = @$order->billingAddress ? @$order->billingAddress->first_name : @$order->billing_information->first_name;
                        $address    = @$order->billingAddress ? @$order->billingAddress->address->address : @$order->billing_information->address;
                        $country    = @$order->billingAddress ?  @$order->billingAddress->country->name : @$order->billing_information->country ;
                        $zip        = @$order->billingAddress ?  @$order->billingAddress->zip : @$order->billing_information->zip ;
                        $city       = @$order->billingAddress ?  @$order->billingAddress->city->name : @$order->billing_information->city ;
                        $state      = @$order->billingAddress ?  @$order->billingAddress->state->name : @$order->billing_information->state ;
    
                 }





                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post($baseURL,[
                        "store_id"      => $this->paymentMethod->payment_parameter->store_id,
                        "tran_id"       => $paymentLog->trx_number,
                        "success_url"   => route('aamarpay.callback',["trx_code" => $paymentLog->trx_number,"type" => 'success']),
                        "fail_url"      => route('aamarpay.callback',["trx_code" => $paymentLog->trx_number,"type" => 'failed']),
                        "cancel_url"    => route('aamarpay.callback',["trx_code" => $paymentLog->trx_number,"type" => 'cancel']),
                        "amount"        => round($paymentLog->final_amount,2),
                        "currency"      => $this->paymentMethod->currency->name,
                        "signature_key" => $this->paymentMethod->payment_parameter->signature_key,
                        "desc"          => "Payment to ".site_settings('site_name'),
                        "cus_name"      => @$first_name,
                        "cus_email"     => @$email,
                        "cus_add1"      => @$address,
                        "cus_add2"      => @$address,
                        "cus_city"      => @$city,
                        "cus_state"     => @$state,
                        "cus_postcode"  => @$zip,
                        "cus_country"   => $country ,
                        "cus_phone"     => @$phone,
                        "type"          => "json",

                    ]); 


              
                
                if ($response->successful()) {
                    $responseData = (object)$response->json();
                    if(@$responseData->payment_url)  return redirect($responseData->payment_url);
                    return back()->with('error',translate(@$responseData->scalar ?? 'Invalid payment credentials'));
                } 

                return back()->with('error',translate('Invalid payment credentials'));

        } catch (\Exception $ex) {
            return back()->with('error',$ex->getMessage());
        }

    }


    public function callBack(Request $request ,$trx_code ,$type = null)
    {


        $paymentLog = PaymentLog::with(['paymentGateway','seller','user'])
                                     ->where('status', PaymentLog::PENDING)
                                    ->where('trx_number', $trx_code)
                                    ->first();


        if(!$paymentLog || !$this->paymentMethod) abort(404);

        if($paymentLog->user){
            Auth::guard('web')->login($paymentLog->user);
        }

        if($type == 'success'){
            if($request->currency ==  $this->paymentMethod->currency->name) {

                if($paymentLog->type == PaymentType::ORDER->value ){
                    PaymentInsert::paymentUpdate($paymentLog->trx_number);
                    Order::where('id',$paymentLog->order_id)->update([
                        'payment_info'=>  json_encode($request->all())
                    ]);
                }else{
                    $response = WalletRecharge::walletUpdate($paymentLog);
                }

                return $this->paymentResponse($request,$paymentLog,true);
            }
        }
        return $this->paymentResponse($request,$paymentLog);
    }

}
