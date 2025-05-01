<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Password;
use App\Http\Utility\SendMail;
use App\Jobs\SendMailJob;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {

        $title = translate('Forgot password');
        return redirect()->route('home');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);


        $user = User::where('email', $request->email)->first();
        if (!$user) {

            return back()->with('error', translate("User not found"));
        }
        PasswordReset::where('email', $request->email)->delete();
        $passwordReset = PasswordReset::create([
            'email' => $request->email,
            'token' => random_number(),
            'created_at' => Carbon::now(),
        ]);
        $mailCode = [
            'code' => $passwordReset->token,
            'time' => $passwordReset->created_at,
        ];

        SendMailJob::dispatch($user,'PASSWORD_RESET',$mailCode);


        session()->put('password_reset_user_email', $request->email);
        return redirect(route('password.verify.code'))->with('success', translate("Check your email password reset code sent successfully"));
    }


    public function passwordResetCodeVerify(){
        $title = translate('Password Reset');
        if(!session()->get('password_reset_user_email')) {
            return redirect()->route('home')->with('error', translate("Your email session expired please try again"));
        }
        return view('auth.verify_code',compact('title'));
    }


    public function emailVerificationCode(Request $request)
    {
        $this->validate($request, [
            'code' => 'required'
        ]);
        $code = preg_replace('/[ ,]+/', '', trim($request->code));
        $email = session()->get('password_reset_user_email');
        $userResetToken = PasswordReset::where('email', $email)->where('token', $code)->first();
        if(!$userResetToken){
            return back()->with('error',translate("Invalid token"));
        }
        return redirect()->route('password.reset', $code)->with('success', translate("Change your password"));

    }
}
