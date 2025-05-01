<?php

namespace App\Http\Controllers\User;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Utility\Wallet\WalletRecharge;
use App\Models\PaymentLog;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class DepositController extends Controller
{
    
    protected ? User $user;
    public function __construct(){

        $this->middleware(function ($request, $next) {
            if(site_settings('customer_wallet') == StatusEnum::false->status()) abort(403);
            $this->user = auth()->guard('web')->user();
            return $next($request);
        });
    }




    public function list(){


        $user = $this->user;

        $title    = translate('Deposit list');

        $reports  = PaymentLog::filter()
                                ->deposit()
                                ->date()
                                ->with(['paymentGateway','paymentGateway.currency'])
                                ->whereNotNull('user_id')
                                ->latest()
                                ->where('user_id',$this->user->id)
                                ->paginate(site_settings('pagination_number',10));



        return view('user.deposit.list', compact('title', 'reports','user'));

    }


    public function create(){

        $user = $this->user;

        $title    = translate('Deposit Create');
        $methods  = PaymentMethod::with(['currency'])->active()->get();
        return view('user.deposit.create', compact('title', 'methods','user'));

    }


    public function show($id){

        $user = $this->user;

        $title       = translate('Deposit Details');
        $paymentLog  = PaymentLog::with(['paymentGateway','paymentGateway.currency'])
                                ->whereNotNull('user_id')
                                ->latest()
                                ->where('user_id',$this->user->id)
                                ->first();

        return view('user.deposit.show', compact('title', 'paymentLog','user'));

    }


    public function  money(Request $request) {



        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'id' => 'required|exists:payment_methods,id',
        ]);

        $user = $this->user;

        $method = PaymentMethod::with('currency')
                        ->active()
                        ->where('id',$request->input('id'))
                        ->firstOrfail();


        $title = translate("Depoist VIA ").$method->name;

        $amount = default_currency_converter((int) $request->input('amount'),session()->get('web_currency'));


        if(  $amount  < (int) site_settings('customer_min_deposit_amount',0) ||  
             $amount  > (int) site_settings('customer_max_deposit_amount',0))
            {
                return redirect()->back()->with('error',translate('Please follow the deposit limit'));
        }

        $log = WalletRecharge::creteLog($this->user , $method , $amount,true);

        session()->put('payment_track', $log->trx_number);

        if($method->type == PaymentMethod::MANUAL)  {
            $paymentLog =   $log->load(['paymentGateway']);
            return view('user.payment.manual', compact('title', 'method','user','paymentLog'));
        }


        if(  $method->unique_code == "INSTA106" || 
             $method->unique_code == "BKASH102" ||  
             $method->unique_code == "NAGAD104" ){
            return redirect()->route("user.deposit.custom.view",['code'=> $method->unique_code, 'trx_code'=>$log->trx_number]);
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

        $paymentLog = PaymentLog::with(['paymentGateway'])
                                       ->where("trx_number",$trx_code)
                                       ->firstOrFail();
        if($gwCode == "INSTA106"){
            $title = translate("Deposit with Instamojo");
            return view('user.payment.instamojo', compact('title', 'method', 'paymentLog'));
        }
        elseif($gwCode == "BKASH102"){
    		$title = translate("Deposit with Bkash");
    		return view('user.payment.bkash', compact('title','paymentLog','method'));
    	}
        elseif($gwCode == "NAGAD104"){
    		$title = translate("Deposit with Nagad");
    		return view('user.payment.nagad', compact('title','paymentLog','method'));
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


   








}
