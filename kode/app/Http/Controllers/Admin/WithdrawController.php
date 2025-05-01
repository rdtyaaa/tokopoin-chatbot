<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Withdraw;
use App\Models\Transaction;
use App\Models\Seller;
use App\Jobs\SendMailJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WithdrawController extends Controller
{

    public function __construct(){

        $this->middleware(['permissions:view_log'])->only('index','pending','approved','rejected','detail','approvedBy','search');
        $this->middleware(['permissions:update_log'])->only('rejectedBy');
    }

    public function index() :View
    {
        $title     = translate('All withdraw log');
        $withdraws = Withdraw::search()->with('currency')->date()->where('status', '!=', 0)->latest()->with('method', 'seller','user','deliveryman')->paginate(site_settings('pagination_number',10))->appends(request()->all());
        return view('admin.withdraw.index', compact('title', 'withdraws'));
    }

    public function pending():View
    {
        $title     = translate('All withdraw log');
        $withdraws = Withdraw::with('currency')->search()->date()->where('status', '!=', 0)->pending()->latest()->with('method', 'seller','deliveryman','user')->paginate(site_settings('pagination_number',10))->appends(request()->all());
        return view('admin.withdraw.index', compact('title', 'withdraws'));
    }

    public function approved():View
    {
        $title     = translate('All withdraw log');
        $withdraws = Withdraw::search()->date()->with('currency')->where('status', '!=', 0)->approved()->latest()->with('method', 'seller','deliveryman','user')->paginate(site_settings('pagination_number',10))->appends(request()->all());
        return view('admin.withdraw.index', compact('title', 'withdraws'));
    }

    public function rejected():View
    {
        $title     = translate('All withdraw log');
        $withdraws = Withdraw::with(['currency'])->search()->date()->where('status', '!=', 0)->rejected()->latest()->with('method', 'seller','deliveryman','user')->paginate(site_settings('pagination_number',10))->appends(request()->all());
        return view('admin.withdraw.index', compact('title', 'withdraws'));
    }

    public function detail(int $id):View
    {
        $title    = translate('Withdraw Details');
        $withdraw = Withdraw::with('method', 'seller','deliveryman','currency','user')->where('status', '!=', 0)->where('id', $id)->firstOrFail();
        return view('admin.withdraw.detail', compact('title', 'withdraw'));
    }

    public function approvedBy(Request $request) :RedirectResponse
    {
        $request->validate(['id' => 'required|exists:withdraws,id']);

        $withdraw = Withdraw::with(['seller','deliveryman','user'])->where('id',$request->id)->where('status',2)->firstOrFail();
        $withdraw->status = 1;
        $withdraw->save();

        if($withdraw->seller) {
            $user = $withdraw->seller;
        }
        elseif($withdraw->deliveryman) {
            $user = $withdraw->deliveryman;
        }
        elseif($withdraw->user) {
            $user = $withdraw->user;
        }



        $mailCode = [
            'trx'              => $withdraw->trx,
            'amount'           => ($withdraw->amount),
            'charge'           => ($withdraw->charge),
            'currency'         => @session()->get('web_currency')->name,
            'rate'             => ($withdraw->rate),
            'method_name'      => $withdraw->method->name,
            'method_currency'  => $withdraw->currency->name,
            'method_amount'    => ($withdraw->final_amount),
            'user_balance'     => ($user->balance)
        ];

        SendMailJob::dispatch($user,'WITHDRAW_CANCEL',$mailCode);

        return back()->with('success',translate('Withdraw has been approved'));
    }

    public function rejectedBy(Request $request) :RedirectResponse
    {
        $request->validate(['id' => 'required|exists:withdraws,id']);

        $withdraw = Withdraw::with(['seller','deliveryman','user'])->where('id',$request->id)->where('status',2)->firstOrFail();
        $withdraw->status    = 3;
        $withdraw->feedback  = $request->details;
        $withdraw->save();

        if($withdraw->seller) {
            $user = $withdraw->seller;
        }
        elseif($withdraw->deliveryman) {
            $user = $withdraw->deliveryman;
        }
        elseif($withdraw->user){
            $user = $withdraw->user;
        }

        $user->balance += $withdraw->amount;
        $user->save();

        $transaction = Transaction::create([
            'seller_id'          => $withdraw->seller ? $withdraw->seller->id : null,
            'user_id'          => $withdraw->user ? $withdraw->user->id : null,
            'deliveryman_id'     => $withdraw->deliveryman ? $withdraw->deliveryman->id : null,
            'amount'             => $withdraw->amount,
            'post_balance'       => $user->balance,
            'transaction_type'   => Transaction::PLUS,
            'transaction_number' => $withdraw->trx_number,
            'details'            => short_amount($withdraw->amount) .' Refund for withdrawal rejected',
        ]);
        
        $mailCode = [
            'trx'              => $withdraw->trx,
            'amount'           => short_amount($withdraw->amount),
            'charge'           => short_amount($withdraw->charge),
            'currency'         => @session()->get('web_currency')->name,
            'rate'             => short_amount($withdraw->rate),
            'method_name'      => $withdraw->method->name,
            'method_currency'  => $withdraw->currency->name,
            'method_amount'    => short_amount($withdraw->final_amount),
            'user_balance'     => short_amount($user->balance)
        ];


        SendMailJob::dispatch($user,'WITHDRAW_APPROVED',$mailCode);

        return back()->with('success',translate('Withdraw has been rejected.'));
    }



}
