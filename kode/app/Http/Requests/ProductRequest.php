<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\General\FileExtentionCheckRule;
use App\Rules\General\FileLengthCheckRule;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
            'slug'    => 'required|max:191',
            'price' => 'required|numeric|gt:0',
            'point' => 'required|numeric|gt:-1|max:2000000',
            'weight' => 'nullable|numeric|gt:-1',
            'shipping_fee' => 'required|numeric|gt:-1',
            'discount_percentage' => 'nullable|numeric|gt:-1',
            'minimum_purchase_qty' => 'required|integer|min:1',
            'maximum_purchase_qty' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
            'short_description' => 'required',
            'description' => 'required',
            'featured_image' => ['required',new FileExtentionCheckRule(file_format())],
            'gallery_image.*' => ['required',new FileExtentionCheckRule(file_format())],
            'meta_title' => 'nullable|max:250',
            'meta_keywords.*' => 'nullable|max:250',
            'meta_description' => 'nullable|max:500',
            'choice_no' => 'required',
            'status' => 'nullable|in:0,1,2',
            'tax_id'=> "nullable|array",
            'tax_id.*'=> "nullable|exists:taxes,id",
            'tax_amount'=> "nullable|array",
            'tax_amount.*'=> "nullable|numeric|gt:-1",
            'tax_type'=> "nullable|array",
            'tax_type.*'=> ["nullable",Rule::in(['0', '1'])]
  
        ];
    }


    public function messages()
    {
       return [
            'name.required' => 'Product title is required',
            'price.required' => 'Product Regular price is required',
            'minimum_purchase_qty.required' => 'Minimum Purchase Quantity is Required',
            'maximum_purchase_qty.required' => 'Maximum Purchase Quantity is Required',
            'category_id.required' => 'Category is required',
            'short_description.required' => 'Short Description is required',
            'description.required' => 'Description is required',
            'featured_image.required' => "Feature Image is required",
            'gallery_image.*.required' => "Gallery Image is Required",
            'choice_no.required' => 'Product Stock is Required',
        ];
    }
}
