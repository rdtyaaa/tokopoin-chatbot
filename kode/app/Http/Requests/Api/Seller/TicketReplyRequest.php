<?php

namespace App\Http\Requests\Api\Seller;

use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
class TicketReplyRequest extends FormRequest
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
            'ticket_number' => "required|exists:support_tickets,ticket_number",
            'message'  => 'required',
            'file.*'  => ["nullable",new FileExtentionCheckRule(['pdf','doc','exel','jpg','jpeg','png','jfif','webp'],'file')] 
        ];
    }

    public function failedValidation(Validator $validator) :JsonResponse {
        throw new HttpResponseException(api(['errors'=>$validator->errors()->all()])->fails(__('response.fail')));
    }
}
