<?php

namespace App\Http\Requests\Admin;

use App\Rules\General\FileExtentionCheckRule;
use App\Rules\General\FileLengthCheckRule;
use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
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
            'name' => 'required|max:255',
            'user_name' => 'required|max:255|unique:admins,user_name,'.request()->id,
            'email' => 'required|max:255|unique:admins,email,'.request()->id,
            'password' => request()->id? "":'required|confirmed|min:5',
            'image' => [  new FileExtentionCheckRule(file_format())]
        ];
        return $rules ;
    }
}
