<?php

namespace App\Http\Controllers\Seller\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seller;
use App\Models\SellerPasswordReset;
use App\Http\Utility\SendMail;
use App\Jobs\SendMailJob;

class ForgotPasswordController extends Controller
{

    public function create()
    {
    	$title = translate('Reset Password');
        return view('seller.auth.forgot-password', compact('title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);
        $seller = Seller::where('email', $request->email)->first();
        if (!$seller) {
            return back()->with('error', translate("Seller not found."));
        }
        SellerPasswordReset::where('email', $request->email)->delete();
        $sellerPasswordReset =  SellerPasswordReset::create([
        	'email' => $request->email,
        	'verify_code' => random_number(),
        ]);
        $mailCode = [
            'code' => $sellerPasswordReset->verify_code,
            'time' => $sellerPasswordReset->created_at,
        ];

        SendMailJob::dispatch($seller,'SELLER_PASSWORD_RESET',$mailCode);

        session()->put('password_reset_email', $request->email);
        return redirect(route('seller.password.verify.code'))->with('success', translate("check your email password reset code sent successfully"));
    }


    public function passwordResetCodeVerify(){
        $title = translate('Password Reset');
        if(!session()->get('password_reset_email')) {

            return redirect()->route('seller.password.request')->with('error', translate("Your email session expired please try again"));
        }
        return view('seller.auth.verify_code',compact('title'));
    }


    public function emailVerificationCode(Request $request)
    {
    	$this->validate($request, [
    		'code' => 'required'
    	]);
    	$code = preg_replace('/[ ,]+/', '', trim($request->code));
    	$email = session()->get('password_reset_email');
    	$sellerResetToken = SellerPasswordReset::where('email', $email)->where('verify_code', $code)->first();
    	if(!$sellerResetToken){
            return  redirect()->route('seller.reset.password.request')->with('error',translate("Invalid token"));
    	}
        return  redirect()->route('seller.password.reset.token',$code)->with('success',translate("Change your password."));
    }
}
