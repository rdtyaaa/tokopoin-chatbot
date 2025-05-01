<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailTemplates;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SmsTemplateController extends Controller
{
    public function __construct(){

        $this->middleware(['permissions:view_configuration'])->only('index');
        $this->middleware(['permissions:update_configuration'])->only('edit','update');
    }
    public function index() :View
    {
        $title        = translate("SMS templates");
        $smsTemplates = EmailTemplates::latest()->paginate(site_settings('pagination_number',10));
        return view('admin.sms_template.index', compact('title', 'smsTemplates'));
    }

    public function edit(int $id) :View
    {
        $title = translate("SMS template update");
        $smsTemplate = EmailTemplates::findOrFail($id);
        return view('admin.sms_template.edit', compact('title', 'smsTemplate'));
    }

    public function update(Request $request, int $id) :RedirectResponse
    {
        $this->validate($request, [
            'subject'  => 'required|max:255',
            'status'   => 'required|in:1,2',
            'sms_body' => 'required'
        ]);

        $smsTemplate           = EmailTemplates::findOrFail($id);
        $smsTemplate->subject  = $request->subject;
        $smsTemplate->status   = $request->status;
        $smsTemplate->sms_body = $request->sms_body;
        $smsTemplate->save();
        return back()->with('success',translate('SMS template has been updated'));
    }
}
