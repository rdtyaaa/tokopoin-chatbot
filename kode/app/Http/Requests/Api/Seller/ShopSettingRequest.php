<?php

namespace App\Http\Requests\Api\Seller;

use App\Enums\StatusEnum;
use App\Models\Seller;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ShopSettingRequest extends FormRequest
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
            
            'id'                  => 'required|exists:seller_shop_settings,id',
            'name'                => 'required|max:255|unique:seller_shop_settings,name,'.request()->input('id'),
            'email'               => 'nullable|email|unique:seller_shop_settings,email,'.request()->input('id'),
            'phone'               => 'required|unique:seller_shop_settings,phone,'.request()->input('id'),
            'short_details'       => 'nullable|string|max:255',
            'address'             => 'nullable|string|max:255',
            'whatsapp_number'     => ['required'],
            'whatsapp_order'      => ['required',Rule::in(array_values(StatusEnum::toArray()))],
            'shop_logo'           => ['nullable','image',new FileExtentionCheckRule(file_format())],
            'shop_feature_image'  => ['nullable','image',new FileExtentionCheckRule(file_format())],
            'site_logo'           => ['nullable','image',new FileExtentionCheckRule(file_format()),],
            'site_logo_icon'      => ['nullable','image',new FileExtentionCheckRule(file_format()),],
        ];
    }


    public function failedValidation(Validator $validator) :JsonResponse {
        throw new HttpResponseException(api(['errors'=>$validator->errors()->all()])->fails(__('response.fail')));
    }
}
