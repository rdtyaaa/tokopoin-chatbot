<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\PasswordReset;
use App\Models\User;

class NewPasswordController extends Controller
{
    public function create($token)
    {
        $title = translate('Password change');
        $passwordToken = $token;
        $email = session()->get('password_reset_user_email');
        $userResetToken = PasswordReset::where('email', $email)->where('token', $token)->first();
        if(!$userResetToken){
            return back()->with('error', translate("Invalid token"));
        }
        return view('auth.reset',compact('title', 'passwordToken'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6',
            'token' => 'required|exists:password_resets,token',
        ]);
        $email = session()->get('password_reset_user_email');
        $userResetToken = PasswordReset::where('email', $email)->where('token', $request->token)->first();
        if(!$userResetToken){
            return redirect()->route('home')->with('error', translate("Invalid token"));
        }
        $user = User::where('email', $email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        if(session()->get('password_reset_user_email')){
            session()->forget('password_reset_user_email');
        }

        return redirect()->route('home')->with('success', translate("Password changed successfully"));
    }
}
