<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentType;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentLogCollection;
use App\Http\Resources\PaymentLogResource;
use App\Http\Utility\Wallet\WalletRecharge;
use App\Models\PaymentLog;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
class DepositController extends Controller
{
    protected ? User $user;
    public function __construct(){


        $this->middleware(function ($request, $next) {
            if(site_settings('customer_wallet') == StatusEnum::false->status()) return api(['errors'=> translate('Wallet system is no incative') ])->fails(__('response.fail'));
            $this->user = auth()->guard('api')->user()?->load(['country','billingAddress']);
            return $next($request);
        });
    }


    /**
     * Summary of makeDeposit
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function makeDeposit(Request $request) : JsonResponse{

        $validator = Validator::make($request->all(),[
            'amount' => 'required|numeric|gt:0',
            'payment_id' => 'required|exists:payment_methods,id',
        ]);

        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));
        
        $method = PaymentMethod::with('currency')
                                ->active()
                                ->where('id',$request->input('payment_id'))
                                ->first();
            
        if(!$method)  return api(['errors'=> translate('Invalid payment method') ])->fails(__('response.fail'));
        
        $amount = ((int) $request->input('amount'));


        if(  $amount  < (int) site_settings('customer_min_deposit_amount',0) ||  
        $amount  > (int) site_settings('customer_max_deposit_amount',0)) return api(['errors'=> translate('Please follow the deposit limit') ])->fails(__('response.fail'));

        $log = WalletRecharge::creteLog($this->user , $method , $amount,true);

        if($method->type == PaymentMethod::MANUAL){
            $log->custom_info =  $request->input("custom_input");
            $log->save();
            return api(
                [
                    'message'      => translate('Your request is submitted, please wait for confirmation'),
                    'log'        => new PaymentLogResource($log),
                ])->success(__('response.success'));
        }

        $response = ['payment_log'  => new PaymentLogResource($log)];
        $paymentUrl = $this->getPaymentURL($method ,$log);
        if($paymentUrl) $response['payment_url'] = $paymentUrl;
        return api($response)->success(__('response.success'));


    }


    /**
     * Get depositLog
     *
     * @return JsonResponse
     */
    public function depositLog() : JsonResponse{
        
        $logs = PaymentLog::with(['paymentGateway','paymentGateway.currency'])->filter()
                             ->date()
                             ->latest()
                             ->where("type",PaymentType::WALLET->value)
                             ->whereNotNull('user_id')->where('user_id',$this->user->id)
                             ->paginate(site_settings('pagination_number',10))
                             ->appends(request()->all());

        return api([ 
            'deposit_logs' => new PaymentLogCollection($logs),
        ])->success(__('response.success'));

    }

}
