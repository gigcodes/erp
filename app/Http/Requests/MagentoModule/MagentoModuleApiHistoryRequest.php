<?php

namespace App\Http\Requests\MagentoModule;

use Illuminate\Foundation\Http\FormRequest;

class MagentoModuleApiHistoryRequest extends FormRequest
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
            'magento_module_id' => 'required',
            'resources' => 'required',
            'frequency' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'remark.required' => __('validation.required', ['attribute' => 'remark']),
            'magento_module_id.required' => __('validation.required', ['attribute' => 'module']),
        ];
    }
}
