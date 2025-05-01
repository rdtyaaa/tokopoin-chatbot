<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailTemplates;

class EmailTemplateController extends Controller
{
    public function __construct(){
        $this->middleware(['permissions:view_configuration'])->only('index');
        $this->middleware(['permissions:update_configuration'])->only('edit','update');
    }

    public function index()
    {
        $title = translate("Notification templates");
        $emailTemplates = EmailTemplates::latest()->paginate(site_settings('pagination_number',10));
        return view('admin.email_template.index', compact('title', 'emailTemplates'));
    }

    public function edit(int $id)
    {
        $title         = translate("Notification templates");
        $emailTemplate = EmailTemplates::where('id', $id)->first();
        return view('admin.email_template.edit', compact('title', 'emailTemplate'));
    }

    public function update(Request $request, int $id)
    {
        $this->validate($request, [
            'subject'     => 'required|max:255',
            'status'      => 'required|in:1,2',
            'body'        => 'required',
            'sms_body'    => 'required',
        ]);

        $emailTemplate              = EmailTemplates::where('id', $id)->first();
        $emailTemplate->subject     = $request->subject;
        $emailTemplate->status      = $request->status;
        $emailTemplate->body        = $request->body;
        $emailTemplate->sms_body    = $request->sms_body;
        $emailTemplate->save();
        return back()->with('success',translate('Notification template has been updated'));
    }
}
