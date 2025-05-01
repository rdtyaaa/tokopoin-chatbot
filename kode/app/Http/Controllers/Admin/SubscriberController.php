<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Models\GeneralSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubscriberController extends Controller
{
    public function __construct(){
        $this->middleware(['permissions:manage_customer']);
    }
    public function index() :View
    {
        $title       = translate('Manager Subscriber');
        $subscribers = Subscriber::orderBy('id','DESC')->paginate(site_settings('pagination_number',10));
        return view('admin.subscriber.index', compact('title','subscribers'));
    }

    public function sendMail() :View
    {
        $title = translate('Send mail to subscriber');
        return view('admin.subscriber.mail', compact('title'));
    }

    public function delete(int $id) :RedirectResponse
    {
        $subscriber = Subscriber::findOrFail($id);
        $subscriber->delete();
        return back()->with('success',translate('Subscriber has been deleted'));

    }

    public function sendEmailSubscriber(Request $request) :RedirectResponse
    {
        $this->validate($request, [
            'subject' => 'required',
            'details' => 'required',
        ]);
        $subscribers = Subscriber::all();
        if ($subscribers->isEmpty()){
            return back()->with('error',translate("No subscribers"));
        }
        $mailFrom = site_settings('mail_from');
        foreach ($subscribers as $subscriber){
            $header = "From: <$mailFrom> \r\n";
            $header .= "Reply-To: $subscriber->email \r\n";
            $header .= "MIME-Version: 1.0\r\n";
            $header .= "Content-type: text/html; charset=utf-8\r\n";
            @mail($subscriber->email, $request->subject, build_dom_document($request->details,'send_mail'.rand(10,22222)), $header);
        }

        return back()->with('success',translate('Email will be sent to all subscribers.'));
    }
}
