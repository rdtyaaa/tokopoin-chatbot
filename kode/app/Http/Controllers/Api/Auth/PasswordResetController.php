<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Utility\SendMail;
use App\Jobs\SendMailJob;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
  
    /**
     * Handle an incoming password reset link request.
     *
     */
    public function store(Request $request) :\Illuminate\Http\JsonResponse
    {

        $validator = Validator::make($request->all(),([
            'email' => ['required', 'email'],
        ]));

        if ($validator->fails())  return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

        $user = User::where('email', $request->email)->first();

        if (!$user)  return api(['errors'=>[translate('Invalid email')]])->fails(__('response.fail'));

        PasswordReset::where('email', $user->email)->delete();

        $passwordReset = PasswordReset::create([
                'email'      => $user->email,
                'token'      => random_number(),
                'created_at' => Carbon::now(),
        ]);

        $mailCode = [ 'code' => $passwordReset->token, 'time' => $passwordReset->created_at];

        SendMailJob::dispatch($user,'PASSWORD_RESET',$mailCode);

        return api(['message' => translate('Check your email password reset code sent successfully')])->success(__('response.success'));

    }



    public function verifyOTP(Request $request) :\Illuminate\Http\JsonResponse
    {

        $validator = Validator::make($request->all(),([
            'email' => ['required', 'email'],
            'otp'  => ['required']
        ]));

        if ($validator->fails()) return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

        $otp = preg_replace('/[ ,]+/', '', trim($request->otp));
        $userResetToken = PasswordReset::where('email', $request->email)
                         ->where('token', $otp)
                         ->first();

        if(!$userResetToken) return api(['errors'=>[translate('Invalid OTP code')]])->fails(__('response.fail'));
        return api(['message' => translate('Change your password')])->success(__('response.success'));
    }


    public function resetPassword(Request $request) :\Illuminate\Http\JsonResponse
    {
     
        $validator = Validator::make($request->all(),([
            'password' => 'required|confirmed|min:6',
            'otp'     => 'required|exists:password_resets,token',
            'email'   => ['required', 'email'],
        ]));

        if ($validator->fails())  return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));

        $email = $request->input("email");
        $userResetToken = PasswordReset::where('email', $email)
                              ->where('token', $request->otp)
                              ->first();

        if(!$userResetToken) return api(['errors'=>[translate('Invalid request')]])->fails(__('response.fail'));
        
        $user = User::where('email', $email)->first();

        $user->password = Hash::make($request->password);
        $user->save();

        return api(['message' => translate('Password changed successfully')])->success(__('response.success'));

    }
}
