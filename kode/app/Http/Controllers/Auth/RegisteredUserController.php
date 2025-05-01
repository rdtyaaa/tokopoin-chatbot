<?php

namespace App\Http\Controllers\Auth;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Http\Services\Frontend\ProductService;
use App\Models\GeneralSetting;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

use Closure;

class RegisteredUserController extends Controller
{


    public $productService;
    public function __construct()
    {
        $this->productService = new ProductService();
    }



    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

        $rules =  [

            'email'     => [
                'nullable',
                Rule::requiredIf(function () {

                    $phoneOTP = site_settings('phone_otp_login', StatusEnum::false->status());
                    $emailOTP = site_settings('email_otp_login', StatusEnum::false->status());
                    return $emailOTP ==  StatusEnum::true->status() || ($phoneOTP == StatusEnum::false->status() && $emailOTP == StatusEnum::false->status());
                }), 'email', 'unique:users'
            ],

            'phone'     => ['nullable', Rule::requiredIf(function () {
                return site_settings('phone_otp_login', StatusEnum::false->status()) ==  StatusEnum::false->status();
            }), 'unique:users,phone'],
            'password'            => ['required', Password::min(6), 'confirmed']
        ];

        $messages = [
            'password.required' => translate('The password field is required.'),
            'password.confirmed' => translate('The password confirmation does not match.'),
            'password.min' => translate('The password must be at least 6 characters.'),
        ];




        if (site_settings('strong_password') == 1) {
            $rules['password'] = [
                "required", "confirmed", Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ];
        }



        if (site_settings("recaptcha_status") == StatusEnum::true->status()) {

            $rules['g-recaptcha-response'] = ['required', function (string $attribute, mixed $value, Closure $fail) {
                $g_response =  Http::asForm()->post("https://www.google.com/recaptcha/api/siteverify", [
                    "secret" => site_settings("recaptcha_secret_key"),
                    "response" => $value,
                    "remoteip" => request()->ip,
                ]);

                if (!$g_response->json("success")) {
                    $fail(translate("Recaptcha validation failed"));
                }
            }];
        }

        $request->validate($rules , $messages);

        $user = User::create([
            'name'     => $request->name,
            'email'    => @$request->email,
            'phone'    => @$request->phone,
            'password' => Hash::make($request->password),
        ]);


        if (
            site_settings('email_otp_login', StatusEnum::false->status()) == StatusEnum::true->status()
            || site_settings('phone_otp_login', StatusEnum::false->status()) == StatusEnum::true->status()
        ) {
            return (new AuthenticatedSessionController())->sendOTP($user);
        }


        Auth::login($user);
        $this->productService->updateCart(auth_user('web'));
        return redirect(RouteServiceProvider::HOME);
    }
}
