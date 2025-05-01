<?php

namespace App\Http\Requests\Api\Seller;

use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
class ProductStoreRequest extends FormRequest
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
            'name' => 'required|max:255',
            'slug'                   => 'required|max:191',
            'price' => 'required|numeric|gt:0',
            'weight' => 'required|numeric|gt:-1',
            'point' => 'required|numeric|gt:-1|max:2000000',
            'shipping_fee' => 'required|numeric|gt:-1',
            'discount_percentage' => 'nullable|numeric|gt:-1',
            'minimum_purchase_qty' => 'required|integer|min:1',
            'maximum_purchase_qty' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'short_description' => 'required',
            'description' => 'required',
            'shipping_delivery_id' => 'nullable|array',
            'shipping_delivery_id.*' => 'nullable|exists:shipping_deliveries,id',
            'featured_image' => ['required',new FileExtentionCheckRule(file_format())],
            'gallery_image.*' => ['required',new FileExtentionCheckRule(file_format())],
            'meta_title' => 'nullable|max:250',
            'meta_keywords.*' => 'nullable|max:250',
            'meta_description' => 'nullable|max:500',
            'choice_no'    => 'required|array',
            'choice_no.*'  => 'required|exists:attributes,id',

            'tax_id'=> "nullable|array",
            'tax_id.*'=> "nullable|exists:taxes,id",
            'tax_amount'=> "nullable|array",
            'tax_amount.*'=> "nullable|numeric|gt:-1",
            'tax_type'=> "nullable|array",
            'tax_type.*'=> ["nullable",Rule::in(['0', '1'])]
        ];
    }


    public function failedValidation(Validator $validator) :JsonResponse {
        throw new HttpResponseException(api(['errors'=>$validator->errors()->all()])->fails(__('response.fail')));
    }
}
