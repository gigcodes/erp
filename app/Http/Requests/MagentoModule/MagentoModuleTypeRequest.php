<?php

namespace App\Http\Requests\MagentoModule;

use Illuminate\Foundation\Http\FormRequest;

class MagentoModuleTypeRequest extends FormRequest
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
            'magento_module_type' => 'required|max:150|unique:magento_module_types',
        ];
    }

    public function messages()
    {
        return [
            'magento_module_type.required' => __('validation.required', ['attribute' => 'Module Type']),
        ];
    }
}
