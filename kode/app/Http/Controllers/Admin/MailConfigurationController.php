<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MailConfiguration;
use App\Models\GeneralSetting;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Enums\Settings\CacheKey;
use Illuminate\Support\Facades\Cache;
class MailConfigurationController extends Controller
{
    public function __construct(){

        $this->middleware(['permissions:view_configuration'])->only('index',"globalTemplate","globalTemplateUpdate");
        $this->middleware(['permissions:update_configuration'])->only('edit','mailUpdate','sendMailMethod');
    }
    public function index() : View
    {
        $title = translate("Mail Configuration");
        $mails = MailConfiguration::latest()->get();
        return view('admin.mail.index', compact('title', 'mails'));
    }

    public function edit(int $id) :View
    {
        $title = translate("Mail updated");
        $mail = MailConfiguration::where('id', $id)->first();
        return view('admin.mail.edit', compact('title', 'mail'));
    }

    public function mailUpdate(Request $request, $id) :RedirectResponse
    {
        $this->validate($request, [
            'driver'       => "required_if:name,==,smtp",
            'host'         => "required_if:name,==,smtp",
            'port'         => "required_if:name,==,smtp", 
            'encryption'   => "required_if:name,==,smtp",
            'username'     => "required_if:name,==,smtp",
            'password'     => "required_if:name,==,smtp",
            'from_address' => "required_if:name,==,smtp",
            'from_name'    => "required_if:name,==,smtp",
        ]);
        $mail = MailConfiguration::where('id', $id)->firstOrfail();
        if($mail->name == "SMTP"){
            $mail->driver_information = [
                'driver'     => $request->driver,
                'host'       => $request->host,
                'port'       => $request->port,
                'from'       => array('address' => $request->from_address, 'name' => $request->from_name),
                'encryption' => $request->encryption,
                'username'   => $request->username,
                'password'   => $request->password,
            ];
        }elseif($mail->name == "SendGrid Api"){
            $mail->driver_information = [
                'app_key'     => $request->app_key,
            ];
        }
        $mail->save();
     
        return back()->with('success',translate(ucfirst($mail->name).' mail method has been updated'));
        
    }

    


    public function sendMailMethod(Request $request) :RedirectResponse
    {
        $this->validate($request, [
            'id' => 'required|exists:mails,id'
        ]);
        $mail    = MailConfiguration::where('id',$request->id)->firstOrFail();

        Setting::updateOrInsert(
            ['key'    => 'email_gateway_id'],
            ['value'  => $mail->id]
        );
        Cache::forget(CacheKey::SITE_SETTINGS->value);
        return back()->with('success',translate('Email method has been updated'));
    }

    public function globalTemplate() :View
    {
        $title = translate("Global template");
        return view('admin.mail.global_template', compact('title'));
    }

    public function globalTemplateUpdate(Request $request) :RedirectResponse
    {
        $this->validate($request,[
            'mail_from' => 'required|email',
            'body'      => 'required',
        ]);
       
        Setting::updateOrInsert(
            ['key'    => 'mail_from'],
            ['value'  => $request->mail_from]
        );

        Setting::updateOrInsert(
            ['key'    => 'email_template'],
            ['value'  => $request->body]
        );
        Cache::forget(CacheKey::SITE_SETTINGS->value);
        return back()->with('success',translate('Global email template has been updated'));
    }
}
