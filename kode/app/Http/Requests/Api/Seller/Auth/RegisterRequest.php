<?php

namespace App\Http\Requests\Api\Seller\Auth;

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
                                            ]:

                                            ["required","confirmed",Password::min(6)];





        return  [
            'username' => 'required|max:255|unique:sellers,username',
        	'email'    => 'required|email|max:255|unique:sellers,email',
        	'password' =>  $passwordValidationRule,
        ];
    }


    public function failedValidation(Validator $validator) :JsonResponse {
        throw new HttpResponseException(api(['errors'=>$validator->errors()->all()])->fails(__('response.fail')));
    }
}
