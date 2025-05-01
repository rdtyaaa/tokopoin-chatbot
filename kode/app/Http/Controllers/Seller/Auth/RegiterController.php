<?php

namespace App\Http\Controllers\Seller\Auth;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Seller;
use App\Models\SellerShopSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

use Illuminate\Support\Facades\Http;
use Closure;
class RegiterController extends Controller
{
    public function register()
    {

        $title = translate('Register as Seller');
        return view('seller.auth.register', compact('title'));
    }

    public function store(Request $request)
    {

        $rules =  [
            'username' => 'required|max:255|unique:sellers,username',
        	'email' => 'required|email|max:255|unique:sellers,email',
        	'password' => 'required|confirmed|min:6',
        ];


        if(site_settings('strong_password') == 1){
            $rules['password']    =  ["required","confirmed",Password::min(8)
                                            ->mixedCase()
                                            ->letters()
                                            ->numbers()
                                            ->symbols()
                                            ->uncompromised()
                                     ];
        }


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

        $seller = Seller::create([
            'username' => $request->username,
            'email' => $request->email,
            'status' => '1',
            'password' => Hash::make($request->password),
        ]);
        SellerShopSetting::create([
        	'seller_id' => $seller->id,
        ]);
        Auth::guard('seller')->login($seller);
        return redirect(route('seller.dashboard'));
    }
}
