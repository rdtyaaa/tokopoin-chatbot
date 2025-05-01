<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
      
    
        $validator = Validator::make($request->all(),([
            'name'      => 'required|string',
            'email'     => ['nullable',Rule::requiredIf(function ()  {
                $phoneOTP = site_settings('phone_otp_login', StatusEnum::false->status());
                $emailOTP = site_settings('email_otp_login', StatusEnum::false->status());
                return $emailOTP ==   StatusEnum::true->status() || ($phoneOTP ==  StatusEnum::false->status() && $emailOTP ==  StatusEnum::false->status());
            }) ,'email','unique:users'],

            'phone'     => ['nullable',Rule::requiredIf(function ()  {
                return site_settings('phone_otp_login', StatusEnum::false->status()) ==  StatusEnum::false->status() ;
            }) ,'unique:users,phone'],
            'password'  => 'required|confirmed',
        ]));

        if ($validator->fails()){
            return api(['errors'=>$validator->errors()->all()])->fails(__('response.fail'));
        }

        $user = new User([
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'phone'    => $request->input('phone'),
            'password' => bcrypt($request->input('password')),
        ]);


        if(site_settings('email_otp_login', StatusEnum::false->status()) == StatusEnum::true->status() 
        || site_settings('phone_otp_login', StatusEnum::false->status()) == StatusEnum::true->status() ){
            return (new LoginController())->sendOTP( $user );
        }

        $user->save();
        $token = $user->createToken('authToken')->plainTextToken;

        return api([
            'access_token' => $token
        ])->success(translate('Registration Success'));
    }
}
