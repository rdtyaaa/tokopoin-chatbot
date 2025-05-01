<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentLog;
use Carbon\Carbon;
use Illuminate\View\View;

class PaymentLogController extends Controller

{

    public function __construct(){
        $this->middleware(['permissions:view_log']);
    }
    public function index() :View
    {
    	$title        = translate('Payments log');
    	$paymentLogs  = PaymentLog::latest()
                                    ->order()
                                    ->search()
                                    ->date()
                                    ->where('status', '!=', 0)
                                    ->with('user','order','paymentGateway','paymentGateway.currency')
                                    ->paginate(site_settings('pagination_number',10));
    	return view('admin.payment_log.index', compact('title', 'paymentLogs'));
    }

    public function pending() :View
    {
    	$title       = translate('Pending payments log');
    	$paymentLogs = PaymentLog::with(['order'])->latest()
        ->order()->search()->date()->where('status', 1)->with('user','paymentGateway','paymentGateway.currency')->paginate(site_settings('pagination_number',10));
    	return view('admin.payment_log.index', compact('title', 'paymentLogs'));
    }

    public function approved() :View
    {
    	$title       = translate('Approved payments log');
    	$paymentLogs = PaymentLog::with(['order'])->latest()
        ->order()->search()->date()->where('status', 2)->with('user','paymentGateway','paymentGateway.currency')->paginate(site_settings('pagination_number',10));
    	return view('admin.payment_log.index', compact('title', 'paymentLogs'));
    }

    public function rejected() :View
    {
    	$title = translate('Rejected payments log');
    	$paymentLogs= PaymentLog::with(['order'])->latest()
        ->order()->search()->date()->where('status', 3)->with('user','paymentGateway','paymentGateway.currency')->paginate(site_settings('pagination_number',10));
    	return view('admin.payment_log.index', compact('title', 'paymentLogs'));
    }

}
