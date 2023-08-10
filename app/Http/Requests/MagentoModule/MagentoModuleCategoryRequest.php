<?php

namespace App\Http\Requests\MagentoModule;

use Illuminate\Foundation\Http\FormRequest;

class MagentoModuleCategoryRequest extends FormRequest
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
            'category_name' => 'required|max:150|unique:magento_module_categories,category_name',
        ];
    }

    public function messages()
    {
        return [
            'category_name.required' => __('validation.required', ['attribute' => 'Module Type']),
        ];
    }
}
