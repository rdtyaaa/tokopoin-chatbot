<?php

namespace App\Http\Controllers\Seller\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seller;
use App\Models\SellerPasswordReset;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Http\Utility\SendMail;
use App\Jobs\SendMailJob;

class PasswordResetController extends Controller
{

    public function resetPassword($token)
    {
    	$title = translate('Password change');
    	$passwordToken = $token;
    	$email = session()->get('password_reset_email');
    	$sellerResetToken = SellerPasswordReset::where('email', $email)->where('verify_code', $token)->first();
    	if(!$sellerResetToken){

            return redirect(route('seller.reset.password.request'))->with('error', translate("Invalid token"));
    	}
    	return view('seller.auth.reset',compact('title', 'passwordToken'));
    }


    public function store(Request $request)
    {
    	$request->validate([
        	'password' => 'required|confirmed|min:6',
        	'token' => 'required|exists:seller_password_resets,verify_code',
        ]);
    	$email = session()->get('password_reset_email');
    	$sellerResetToken = SellerPasswordReset::where('email', $email)->where('verify_code', $request->token)->first();
    	if(!$sellerResetToken){
            return redirect(route('seller.reset.password.request'))->with('error', translate("Invalid token"));
    	}
    	$seller = Seller::where('email', $email)->first();
    	$seller->password = Hash::make($request->password);
    	$seller->save();

    	if(session()->get('password_reset_email')){
    		session()->forget('password_reset_email');
    	}
        $mailCode = [
            'time' => Carbon::now(),
        ];

		SendMailJob::dispatch($seller,'SELLER_PASSWORD_RESET_CONFIRM',$mailCode);
        $sellerResetToken->delete();
        return redirect(route('seller.login'))->with('success', translate("Password changed successfully"));
    }
}
