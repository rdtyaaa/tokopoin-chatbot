<?php

namespace App\Http\Controllers\User;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Utility\Wallet\WalletRecharge;
use App\Jobs\SendMailJob;
use App\Models\PaymentLog;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdraw;
use App\Models\WithdrawMethod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class WithdrawController extends Controller
{
    

    protected ? User $user;

    public function __construct(){
        $this->middleware(function ($request, $next) {
            if(site_settings('customer_wallet') == StatusEnum::false->status()) abort(403);
            $this->user = auth()->guard('web')->user();
            return $next($request);
        });
    }





    /**
     * Get withdraw methods
     *
     */
    public function methods() {

        $user = $this->user;
        $title    = translate('Withdraw Create');
        $methods  =  WithdrawMethod::with(['currency'])
                        ->active()
                        ->latest()
                        ->get();
        return view('user.withdraw.create', compact('title', 'methods','user'));


    }


    public function list()  {

        
        $user = $this->user;
        $title    = translate('Withdraw List');
        $withdraws  =  Withdraw::with('method', 'currency')
                            ->whereNull('deliveryman_id')
                            ->whereNull('seller_id')
                            ->where('user_id', $user->id)
                            ->date()
                            ->search()
                            ->where('status', '!=', Withdraw::INITIATE)
                            ->latest()->paginate(site_settings('pagination_number',10))
                            ->appends(request()->all());
                            
        return view('user.withdraw.list', compact('title', 'withdraws','user'));


    }



    /**
     * Withdraw request create 
     *
     * @param Request $request
     */
    public function request(Request $request) {

        $request->validate([
                'id'     => 'required|exists:withdraw_methods,id',
                'amount' => 'required|numeric|gt:0'
        ]);

        $user = $this->user;
        $withdrawMethod = WithdrawMethod::where('id', $request->id)->where('status', 1)->firstOrFail();

        if($request->amount < $withdrawMethod->min_limit || $request->amount > $withdrawMethod->max_limit) return back()->with('error',translate("Please follow withdraw limit"));
        if ($request->amount > $user->balance) return back()->with('error',translate("You do not have sufficient balance for withdraw."));

        $withdrawCharge = $withdrawMethod->fixed_charge + ($request->amount * $withdrawMethod->percent_charge / 100);
        $afterCharge = $request->amount - $withdrawCharge;
        $finalAmount = $afterCharge * $withdrawMethod->rate;

        $withdraw = new Withdraw();
        $withdraw->method_id = $withdrawMethod->id;
        $withdraw->user_id = $user->id;
        $withdraw->amount = $request->amount;
        $withdraw->currency_id = $withdrawMethod->currency_id;
        $withdraw->rate = $withdrawMethod->rate;
        $withdraw->charge = $withdrawCharge;
        $withdraw->final_amount = $finalAmount;
        $withdraw->trx_number = trx_number();
        $withdraw->created_at = Carbon::now();
        $withdraw->save();
        return redirect()->route('user.withdraw.preview',encrypt($withdraw->trx_number));

    }






    public function preview($trxNumber)
    {
        $title = translate('Withdraw Preview');
        $user = $this->user;
        $withdraw = Withdraw::where('trx_number', decrypt($trxNumber))
                         ->where('status', 0)
                         ->where('user_id', $user->id)
                         ->firstOrFail();
        return view('user.withdraw.preview', compact('title','withdraw','user'));
    }



    public function previewStore(Request $request, $id)
    {

        $user = $this->user;
        $withdraw =  Withdraw::where('id', $id)->where('status', 0)->where('user_id', $user->id)->firstOrFail();
        if($withdraw->amount > $user->balance)  return redirect()->back()->with('error',translate("Your request amount is larger then your current balance."));
        $rules = [];
        if ($withdraw->method->user_information != null) {
            foreach ($withdraw->method->user_information as $key => $value) {
                $rules[$key] = ['required'];
                if($value->type == 'file'){
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], 'mimes:jpeg,jpg,png');
                    array_push($rules[$key], 'max:2048');
                }
                if($value->type == 'text'){
                    array_push($rules[$key], 'max:191');
                }
                if($value->type == 'textarea'){
                    array_push($rules[$key], 'max:300');
                }
            }
        }
        $this->validate($request, $rules);
        $collection = collect($request);
        $userInformationData = [];
        if ($withdraw->method->user_information != null) {
            foreach ($collection as $firstKey => $firstValue) {
                foreach ($withdraw->method->user_information as $key => $value) {
                    if ($firstKey != $key){
                        continue;
                    }else{
                        if($value->type == 'file'){

                        }else{
                            $userInformationData[$key] = $firstValue;
                            $userInformationData[$key] = [
                                'data_name' => $firstValue,
                                'type' => $value->type,
                            ];
                        }
                    }
                }
            }
            $withdraw->withdraw_information = $userInformationData;
        }
        $withdraw->status = 2;
        $withdraw->save();

        $transaction = Transaction::create([
            'user_id'             => $user->id,
            'amount'              => $withdraw->amount,
            'post_balance'        => $user->balance,
            'transaction_type'    => Transaction::MINUS,
            'transaction_number'  => $withdraw->trx_number,
            'details'             => short_amount($withdraw->final_amount) .  ' Withdraw Via ' . $withdraw->method->name,
        ]);

        $user->balance  -=  $withdraw->amount;
        $user->save();

  
        $mailCode = [
            'trx' => $withdraw->trx,
            'amount' => ($withdraw->amount),
            'charge' => ($withdraw->charge),
            'currency' => @session()->get('web_currency')->name,
            'rate' => ($withdraw->rate),
            'method_name' => $withdraw->method->name,
            'method_currency' => $withdraw->currency->name,
            'method_amount' => ($withdraw->final_amount),
            'user_balance' => ($user->balance)
        ];

        SendMailJob::dispatch($user,'WITHDRAW_REQUEST_AMOUNT',$mailCode);
        return redirect()->route('user.withdraw.list')->with('success',translate("Withdraw request has been send"));

    }


    public function show($id){

        $user        = $this->user;
        $title       = translate('Withdraw Details');
        $withdraw    = Withdraw::where('id', $id)
                                    ->where('user_id',$user->id)
                                    ->firstOrFail();

        return view('user.withdraw.show', compact('title', 'withdraw','user'));

    }








}
