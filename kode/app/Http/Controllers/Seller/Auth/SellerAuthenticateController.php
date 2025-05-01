<?php

namespace App\Http\Controllers\Seller\Auth;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Closure;

class SellerAuthenticateController extends Controller
{
    public function __construct(){
        $this->middleware('seller.guest')->except('logout');
    }

    public function showLogin()
    {
        $title = translate('Seller Login');
        return view('seller.auth.login', compact('title'));
    }

    public function authenticate(Request $request)
    {

        $rules = [
            'username' => ['required'],
            'password' => ['required']
        ];
    
        if(site_settings("seller_captcha") == StatusEnum::true->status()){
            $rules['g-recaptcha-response'] = ['required' , function (string $attribute, mixed $value, Closure $fail) {
                $g_response =  Http::asForm()->post("https://www.google.com/recaptcha/api/siteverify",[
                    "secret"=> site_settings("recaptcha_secret_key"),
                    "response"=> $value,
                    "remoteip"=> request()->ip,
                ]);
                if (!$g_response->json("success")) (translate("Recaptcha validation failed"));
            }];
        }

        $request->validate($rules);

        if (Auth::guard('seller')->attempt([
            'username' => $request->input('username'),
            'password' => $request->input('password'),
        ])){
            $request->session()->regenerate();
            return redirect()->route('seller.dashboard');
        }
        return back()->with('error', translate("The provided credentials do not match our records"));
    }

    public function logout(Request $request)
    {
        Auth::guard('seller')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/seller');
    }
}
