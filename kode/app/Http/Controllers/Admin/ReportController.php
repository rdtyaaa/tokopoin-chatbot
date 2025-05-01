<?php

namespace App\Http\Controllers\Admin;

use App\Enums\KYCStatus;
use App\Http\Controllers\Controller;
use App\Models\KycLog;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReportController extends Controller
{

    public function __construct(){
        $this->middleware(['permissions:view_log']);
    }
    public function userTransaction() :View
    {
        $title        = translate('User transactions');
        $transactions = Transaction::latest()->users()->search()->date()->latest()->with('user')->paginate(site_settings('pagination_number',10));
        return view('admin.report.index', compact('title', 'transactions'));
    }

    public function guestTransaction() :View
    {
        $title        = translate('User transactions');
        $transactions = Transaction::latest()->guest()->search()->date()->latest()->with('user')->paginate(site_settings('pagination_number',10));
        return view('admin.report.index', compact('title', 'transactions'));
    }


    public function sellerTransaction() :View
    {
        $title        = translate('Seller transactions');
        $transactions = Transaction::sellers()->search()->date()->latest()->with('seller')->paginate(site_settings('pagination_number',10));
        return view('admin.report.index', compact('title', 'transactions'));
    }


    public function deliverymanTransaction() :View
    {
        $title        = translate('Deliveryman transactions');
        $transactions = Transaction::latest()->deliverymen()->search()->date()->latest()->with('deliveryman')->paginate(site_settings('pagination_number',10));
        return view('admin.report.index', compact('title', 'transactions'));
    }



    public function kycLogs() :View
    {
        $title        = translate('KYC Logs');
        $reports      = KycLog::with(['seller','deliveryMan'])
                                  ->search()->date()
                                  ->latest()
                                  ->paginate(site_settings('pagination_number',10));
                                  
        return view('admin.report.kyc_log', compact('title', 'reports'));
    }
    public function kycLogShow($id) :View
    {
        $title        = translate('KYC Log Details');
        $report       = KycLog::with(['seller','deliveryMan'])
                                ->where('id',$id)
                                ->firstOrfail();
                                  
        return view('admin.report.kyc_log_details', compact('title', 'report'));
    }



    public function kycUpdate(Request $request) :RedirectResponse
    {

        $request->validate([
            'id'     => "required",
            'status' => 'required|in:1,4',
            'feedback' => 'required',
        ]);
        $report       = KycLog::with(['seller','deliveryMan'])
                                ->where('id',$request->id)
                                ->firstOrfail();


        $report->status = $request->status;
        $report->feedback = $request->feedback;
        $report->save();

        if($report->seller && $report->status == KYCStatus::APPROVED->value){
            $report->seller->kyc_status = 1;
            $report->seller->save();
        }
        if($report->deliveryMan && $report->status == KYCStatus::APPROVED->value){
            $report->deliveryMan->is_kyc_verified = 1;
            $report->deliveryMan->save();
        }
                    
        return redirect()->back()->with("success",translate('Status updated'));
    }





}
