<?php

namespace App\Http\Requests\Api\Seller;

use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
class DigitalProductUpdateRequest extends FormRequest
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


    public function rules()
    {

        return [
            'uid'                    => 'required|exists:products,uid',
            'name'                   => 'required|max:255',
            'point' => 'required|numeric|gt:-1|max:2000000',
            'slug'                   => 'required|max:191',
            'category_id'            => 'required|exists:categories,id',
            'sub_category_id'        => 'nullable|exists:categories,id',
            'description'            => 'required',
            'featured_image'         => ['nullable',new FileExtentionCheckRule(file_format())],

            
            'tax_id'=> "nullable|array",
            'tax_id.*'=> "nullable|exists:taxes,id",
            'tax_amount'=> "nullable|array",
            'tax_amount.*'=> "nullable|numeric|gt:-1",
            'tax_type'=> "nullable|array",
            'tax_type.*'=> ["nullable",Rule::in(['0', '1'])],
            'type'=> [ "nullable","array"],
            'data_name'=> ["nullable","array"],
            'data_value'=> ["nullable" ,"array"],
            'data_required'=> ["nullable","array"],


        ];
    }


    public function messages()
    {
       return [
            'name.required' => 'Product title is required',
            'category_id.required' => 'Category is required',
            'description.required' => 'Description is required',
            'featured_image.required' => "Feature Image is required",
            'attribute_option.required' => 'Product Stock is Required',
            'attribute_option.name'     => 'Product Stock name is Required',
            'attribute_option.price'     => 'Product Stock price is Required',
        ];
    }


    public function failedValidation(Validator $validator) :JsonResponse {
        throw new HttpResponseException(api(['errors'=>$validator->errors()->all()])->fails(__('response.fail')));
    }
}
