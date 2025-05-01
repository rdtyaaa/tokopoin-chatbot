<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Utility\Wallet\WalletRecharge;
use Illuminate\Http\Request;
use App\Models\PaymentLog;
use Carbon\Carbon;
use Illuminate\View\View;

class DepositController extends Controller

{

    public function __construct(){
        $this->middleware(['permissions:view_log']);
    }
    public function index() :View
    {
    	$title        = translate('Deposit log');
    	$paymentLogs  = PaymentLog::latest()
                                    ->deposit()
                                    ->search()
                                    ->filter()
                                    ->date()
                                    ->where('status', '!=', 0)
                                    ->with('user','seller','paymentGateway','paymentGateway.currency')
                                    ->paginate(site_settings('pagination_number',10));
    	return view('admin.deposit_log.index', compact('title', 'paymentLogs'));
    }

    public function pending() :View
    {
    	$title       = translate('Pending Deposit log');
    	$paymentLogs = PaymentLog::latest()
                                    ->filter()
                                    ->deposit()
                                    ->search()
                                    ->date()
                                    ->where('status', 1)
                                    ->with('user','seller','paymentGateway','paymentGateway.currency')
                                    ->paginate(site_settings('pagination_number',10));
    	return view('admin.deposit_log.index', compact('title', 'paymentLogs'));
    }

    public function approved() :View
    {
    	$title       = translate('Approved Deposit log');
    	$paymentLogs = PaymentLog::latest()
                                    ->filter()
                                    ->deposit()
                                    ->search()
                                    ->date()
                                    ->where('status', 2)
                                    ->with('user','seller','paymentGateway','paymentGateway.currency')
                                    ->paginate(site_settings('pagination_number',10));
    	return view('admin.deposit_log.index', compact('title', 'paymentLogs'));
    }

    public function rejected() :View
    {
    	$title = translate('Rejected Deposit log');
    	$paymentLogs= PaymentLog::latest()
                                    ->filter()
                                    ->deposit()
                                    ->search()
                                    ->date()
                                    ->where('status', 3)
                                    ->with('user','seller','paymentGateway','paymentGateway.currency')
                                    ->paginate(site_settings('pagination_number',10));
    	return view('admin.deposit_log.index', compact('title', 'paymentLogs'));
    }


    public function show($id) :View
    {
    	$title = translate('Deposit log details');
    	$paymentLog= PaymentLog::with('user','seller','paymentGateway','paymentGateway.currency')
                                ->where('id',$id)
                                ->firstOrfail();

    	return view('admin.deposit_log.show', compact('title', 'paymentLog'));
    }


    public function update(Request $request) 
    {
        $request->validate([
            'id'     => "required|exists:payment_logs,id",
            'status' => 'required|in:2,3',
            'feedback' => 'required',
        ]);

       $paymentLog = PaymentLog::with('user','seller','paymentGateway','paymentGateway.currency')
                            ->where('id', $request->input('id'))
                            ->firstOrfail();

        $paymentLog->status = $request->input('status');
        $paymentLog->feedback = $request->input('feedback');
        $paymentLog->save();

        if($paymentLog->status == PaymentLog::SUCCESS)  WalletRecharge::walletUpdate($paymentLog);


        return back()->with('success',translate('Deposit log updated'));



    }


}
