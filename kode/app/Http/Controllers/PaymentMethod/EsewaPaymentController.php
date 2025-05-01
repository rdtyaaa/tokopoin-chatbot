<?php

namespace App\Http\Controllers\PaymentMethod;

use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PaymentMethod;
use App\Models\PaymentLog;
use App\Http\Utility\PaymentInsert;
use App\Http\Utility\Wallet\WalletRecharge;
use App\Models\GeneralSetting;
use App\Models\Order;
use Session;



class EsewaPaymentController extends Controller
{
  
    public $paymentMethod;
    public function __construct(){

        $this->paymentMethod = PaymentMethod::with(['currency'])->where('unique_code', 'ESEWA107')->first();
    }


    public function payment($trx_code = null)
    {
        $paymentTrackNumber = $trx_code ??  session()->get('payment_track');
        $paymentLog = PaymentLog::with(['paymentGateway','seller','user','order'])->where('status', PaymentLog::PENDING)
                                                ->where('trx_number', $paymentTrackNumber)
                                                ->first();
        if(!$paymentLog || !$this->paymentMethod)   return redirect()->route('home')->with('error', translate('Invalaid Transaction'));

        $amount       = round($paymentLog->final_amount,2) ;
        $productCode  = $this->paymentMethod->payment_parameter->product_code ;
      
        $secretKey  = $this->paymentMethod->payment_parameter->secret_key ;
        $tuid       = now()->timestamp;
        $message    = "total_amount=$amount,transaction_uuid=$tuid,product_code=$productCode";
        $s          = hash_hmac('sha256', $message, $secretKey, true);
        $signature  = base64_encode($s);
      
        $paymentURL = $this->paymentMethod->payment_parameter->environment == 'sandbox'
                                ? 'https://rc-epay.esewa.com.np/api/epay/main/v2/form' 
                                : 'https://epay.esewa.com.np/api/epay/main/v2/form';
          
        $dataArray = [
            "amount" => $amount,
            "failure_url" => route('esewa.callback',['trx_code' =>  $paymentTrackNumber , 'type' => 'failed']),
            "product_delivery_charge" => "0",
            "product_service_charge" => "0",
            "product_code" => $productCode,
            "signature" => $signature,
            "signed_field_names" => "total_amount,transaction_uuid,product_code",
            "success_url" => route('esewa.callback',['trx_code' =>  $paymentTrackNumber , 'type' => 'success']),
            "tax_amount" => "0",
            "total_amount" => $amount,
            "transaction_uuid" => $tuid,
             "data_url"        => $paymentURL,
             "message"         => 'total_amount,transaction_uuid,product_code',
        ];
      
        $view =  'user.payment.esewa';
        if($paymentLog->seller) $view =  'seller.deposit.esewa';

        return view($view,[
            'title' => translate('Payment with eSewa'),
            'paymentMethod' =>  $this->paymentMethod,
            'paymentLog' => $paymentLog,
            'data'       => (object) $dataArray
        ]);

    }


    public function callBack(Request $request ,$trx_code ,$type = null)
    {

        $decodedString = base64_decode($request->data);
        $data          = json_decode($decodedString,true);


        $paymentLog = PaymentLog::with(['paymentGateway','seller','user','order'])
                            ->where('status',PaymentLog::PENDING)
                            ->where('trx_number', $trx_code)
                            ->first();

        if(!$paymentLog || !$this->paymentMethod)  abort(404);


        if($data && array_key_exists("status",$data) && $data["status"] == "COMPLETE" && $type == 'success'){

            if($paymentLog->type == PaymentType::ORDER->value ){
                PaymentInsert::paymentUpdate($paymentLog->trx_number);
                Order::where('id',$paymentLog->order_id)->update([
                    'payment_info'=>  json_encode($request->all())
                ]);
            }else{
                $response = WalletRecharge::walletUpdate($paymentLog);

            }

          return $this->paymentResponse($request,$paymentLog ,true );
        }
      
        return $this->paymentResponse($request,$paymentLog);


      
    }

}
