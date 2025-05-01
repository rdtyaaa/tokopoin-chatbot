<?php

namespace App\Http\Requests\Api\Deliveryman\Auth;

use App\Enums\StatusEnum;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Password;
class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() :array
    {


        $passwordValidationRule = site_settings('strong_password') == StatusEnum::true->status()

                                        ? ["required","confirmed",Password::min(8)
                                                ->mixedCase()
                                                ->letters()
                                                ->numbers()
                                                ->symbols()
                                                ->uncompromised()
                                            ]

                                        :

                                            ["required","confirmed",Password::min(6)];

                return  [
                    'first_name'   => "required|max:191",
                    'last_name'    => "nullable|string",
                    'username'     => "required|unique:delivery_men,username",
                    'email'        => "required|unique:delivery_men,email",
                    'phone'        => "required|unique:delivery_men,phone",
                    'phone_code'   => "required",
                    'country_id'   => "required|exists:countries,id",
                    'latitude'     => "nullable",
                    'longitude'    => "nullable",
                    'address'      => "required",
                    'password'     => $passwordValidationRule,
                ];
    }


    public function failedValidation(Validator $validator) :JsonResponse {
        throw new HttpResponseException(api(['errors'=>$validator->errors()->all()])->fails(__('response.fail')));
    }
}
