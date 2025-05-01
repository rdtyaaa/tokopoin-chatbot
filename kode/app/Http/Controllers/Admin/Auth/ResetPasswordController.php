<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\AdminPasswordReset;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Http\Utility\SendMail;
use App\Jobs\SendMailJob;

class ResetPasswordController extends Controller
{
    public function create(Request $request, $token)
    {
        $title = translate('Password change');
        $passwordToken = $token;
        $email = session()->get('admin_password_reset_user_email');
        $userResetToken = AdminPasswordReset::where('email', $email)->where('token', $token)->first();
        if(!$userResetToken){
        	if(session()->get('admin_password_reset_user_email')){
	            session()->forget('admin_password_reset_user_email');
	        }
            return redirect(route('admin.password.request'))->with('error', translate("Invalid token"));

        }
        return view('admin.auth.reset',compact('title', 'passwordToken'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:4',
            'token' => 'required|exists:admin_password_resets,token',
        ]);
        $email = session()->get('admin_password_reset_user_email');
        $userResetToken = AdminPasswordReset::where('email', $email)->where('token', $request->token)->first();
        if(!$userResetToken){
            return redirect(route('admin.password.request'))->with('error', translate("Invalid token"));
        }
        $userResetToken->delete();
        $admin = Admin::where('email', $email)->first();
        $admin->password = Hash::make($request->password);
        $admin->save();

        if(session()->get('admin_password_reset_user_email')){
            session()->forget('admin_password_reset_user_email');
        }
        $mailCode = [
            'time' => Carbon::now(),
        ];


        SendMailJob::dispatch($admin,'ADMIN_PASSWORD_RESET_CONFIRM',$mailCode);



        $userResetToken->delete();
        return redirect(route('admin.login'))->with('success', translate("Password changed successfully"));
    }
}
