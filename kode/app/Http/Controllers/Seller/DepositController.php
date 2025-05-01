<?php

namespace App\Http\Controllers\Seller;

use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use App\Http\Utility\Wallet\WalletRecharge;
use App\Models\PaymentLog;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class DepositController extends Controller
{
    

    public function __construct()
    {
         $this->middleware('sellercheckstatus');
    }


    public function list(){

        $title    = translate('Deposit list');
        $seller   = auth()->guard('seller')->user();

        $reports  = PaymentLog::filter()->date()->with(['paymentGateway','paymentGateway.currency'])->whereNotNull('seller_id')
                                ->latest()
                                ->where('seller_id',$seller->id)
                                ->paginate(site_settings('pagination_number',10));


        return view('seller.deposit.list', compact('title', 'reports','seller'));

    }


    public function method(){

        $title    = translate('Deposit Method');
        $seller   = auth()->guard('seller')->user();
        $methods  = PaymentMethod::with(['currency'])->active()->get();
        return view('seller.deposit.method', compact('title', 'methods','seller'));

    }


    public function  money(Request $request) {

        $seller   = auth()->guard('seller')->user();

        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'id' => 'required|exists:payment_methods,id',
        ]);


        $method = PaymentMethod::with('currency')
                        ->active()
                        ->where('id',$request->input('id'))
                        ->firstOrfail();


        $title = translate("Depoist VIA ").$method->name;

        $amount = default_currency_converter((int) $request->input('amount'),session()->get('web_currency'));


        if(  $amount  < (int) site_settings('seller_min_deposit_amount',0) ||  
             $amount  > (int) site_settings('seller_max_deposit_amount',0))
            {
                return redirect()->back()->with('error',translate('Please follow the deposit limit'));
        }

        $log = WalletRecharge::creteLog($seller , $method , $amount);

        session()->put('payment_track', $log->trx_number);

        if($method->type == PaymentMethod::MANUAL)  return view('seller.deposit.manual', compact('title', 'method','seller','log'));


        if(  $method->unique_code == "INSTA106" || 
             $method->unique_code == "BKASH102" ||  
             $method->unique_code == "NAGAD104" ){
            return redirect()->route("seller.deposit.custom.view",['code'=> $method->unique_code, 'trx_code'=>$log->trx_number]);
        }
        try {
            return redirect($this->getPaymentRoute($method,$log));
        } catch (\Exception $ex) {
      
        }

    }


    public function customView($gwCode,$trx_code ){

        $method = PaymentMethod::with('currency')
                            ->active()
                            ->where('unique_code',$gwCode)
                            ->firstOrfail();

        $log = PaymentLog::where("trx_number",$trx_code)->firstOrFail();
        if($gwCode == "INSTA106"){
            $title = translate("Deposit with Instamojo");
            return view('seller.deposit.instamojo', compact('title', 'method', 'log'));
        }
        elseif($gwCode == "BKASH102"){
    		$title = translate("Deposit with Bkash");
    		return view('seller.deposit.bkash', compact('title','log','method'));
    	}
        elseif($gwCode == "NAGAD104"){
    		$title = translate("Deposit with Nagad");
    		return view('seller.deposit.nagad', compact('title','log','method'));
    	}

    }


    public function getPaymentRoute(PaymentMethod $paymentMethod , PaymentLog $paymentLog) {


        $gwCodes = [
            'STRIPE101'      => 'stripe',
            'BKASH102'       => 'bkash',
            'PAYSTACK103'    => 'paystack',
            'NAGAD104'       => 'nagad',
            'PAYPAL102'      => 'paypal',
            'FLUTTERWAVE105' => 'flutterwave',
            'RAZORPAY106'    => 'razorpay',
            'INSTA106'       => 'instamojo',
            'MERCADO101'     => 'mercado',
            'PAYEER105'      => 'payeer',
            'AAMARPAY107'    => 'aamarpay',
            'PAYU101'        => 'payumoney',
            'PAYHERE101'     => 'payhere',
            'PAYKU108'       => 'payku',
            'PHONEPE102'     => 'phonepe',
            'SENANGPAY107'   => 'senangpay',
            'NGENIUS111'     => 'ngenius',
            'ESEWA107'       => 'esewa',
            'WEBXPAY109'       => 'webxpay',
        ];

        $method = Arr::get($gwCodes , $paymentMethod->unique_code ); 

        if(!$method) return null;
        
        return  route($method.".payment",['trx_code' => $paymentLog->trx_number]);

    }


    public function paymentSuccess($trxCode){


        $title = translate('Deposit Success');

        $seller   = auth()->guard('seller')->user();
        $log  = PaymentLog::with(['paymentGateway'])->whereNotNull('seller_id')
                                ->latest()
                                ->where('seller_id',$seller->id)
                                ->where('trx_number',$trxCode)
                                ->firstOrfail();


       return view('seller.deposit.response', compact('title','seller','log'));


    }


    public function paymentFailed($trxCode){

        $title = translate('Deposit Failed');
        
        $seller   = auth()->guard('seller')->user();
        $log      = PaymentLog::whereNotNull('seller_id')
                                ->latest()
                                ->where('seller_id',$seller->id)
                                ->where('trx_number',$trxCode)
                                ->firstOrfail();

    
         return view('seller.deposit.response', compact('title','seller','log'));
        
    }



    /**
     * Handle manual payment request
     *
     * @param Request $request
     */
    public function manualDeposit(Request $request)  {


        $request->validate([
            'gw_id' => 'required'
        ]);
        $paymentTrackNumber = session()->get('payment_track');

        $paymentLog = PaymentLog::with(['paymentGateway'])
                            ->where('method_id',$request->input('gw_id'))
                            ->whereIn('status',[PaymentLog::PENDING,PaymentLog::SUCCESS])->where('trx_number',$paymentTrackNumber)
                            ->firstOrfail();

        $paymentLog->custom_info =  $request->input("custom_input");
        $paymentLog->save();

        return redirect()->route("seller.deposit.list")->with("success",translate('Your request is submitted, please wait for confirmation'));


    }


    public function show($id) 
    {

        $seller   = auth()->guard('seller')->user();
    	$title = translate('Deposit log details');
    	$paymentLog = PaymentLog::with('user','seller','paymentGateway','paymentGateway.currency')
                                ->where("type",PaymentType::WALLET->value)
                                ->where("seller_id", $seller->id)
                                ->where('id',$id)
                                ->firstOrfail();

    	return view('seller.deposit.show', compact('title', 'paymentLog'));
    }
}
