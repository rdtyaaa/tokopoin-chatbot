<?php

namespace App\Http\Requests\Api\Seller;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
class WithdrawPreviewRequest extends FormRequest
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
        return [
               'id'     => 'required|exists:withdraw_methods,id',
               'amount' => 'required|numeric|gt:0'
        ];
    }

    public function failedValidation(Validator $validator) :JsonResponse {
        throw new HttpResponseException(api(['errors'=>$validator->errors()->all()])->fails(__('response.fail')));
    }
}
