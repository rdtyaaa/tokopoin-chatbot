<?php

namespace App\Http\Requests\Auth;

use App\Enums\StatusEnum;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Closure;

use Illuminate\Support\Facades\Http;
class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [
            'email'       => ['required', 'string'],
            'password'    => [Rule::requiredIf(function ()  {
                return  site_settings('login_with_password',StatusEnum::true->status()) ==  StatusEnum::true->status()  ;
            })],
        ];


        if(site_settings("recaptcha_status") == StatusEnum::true->status()){

            $rules['g-recaptcha-response'] = ['required' , function (string $attribute, mixed $value, Closure $fail) {
                $g_response =  Http::asForm()->post("https://www.google.com/recaptcha/api/siteverify",[
                    "secret"=> site_settings("recaptcha_secret_key"),
                    "response"=> $value,
                    "remoteip"=> request()->ip,
                ]);

                if (!$g_response->json("success")) {
                    $fail(translate("Recaptcha validation failed"));
                }
            }];

        }




        return $rules;
    }



    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages()
    {


        return [
            'email.required'       => translate('Login credential is required'),
            'password.required'    => translate('Password is required')
        ];
    }




    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('email')).'|'.$this->ip();
    }
}
