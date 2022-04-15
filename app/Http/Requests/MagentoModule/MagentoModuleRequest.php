<?php

namespace App\Http\Requests\MagentoModule;

use Illuminate\Foundation\Http\FormRequest;

class MagentoModuleRequest extends FormRequest
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
            'module_category_id' => 'required',
            'store_website_id' => 'required',
            'module' => 'required|max:150',
            'module_description' => 'required',
            // 'current_version' => 'required',
            // 'cron_time' => 'required',
            'task_status' => 'required',
            'module_type' => 'required',
            'status' => 'required',
            'payment_status' => 'required',
            // 'developer_name' => 'required',
            'is_customized' => 'required',
            'is_sql' => 'required',
            'is_third_party_plugin' => 'required',
            'is_third_party_js' => 'required',
            'is_js_css' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'module_category_id.required' => __('validation.required', ['attribute' => 'Module Category']),
            'module.required' => __('validation.required', ['attribute' => 'module']),
            'module.max' => __('validation.max', ['max' => 150, 'attribute' => 'Module']),
            'module_description.required' => __('validation.required', ['attribute' => 'Module Description']),
            'payment_status.required' => __('validation.required', ['attribute' => 'Payment Status']),
            'is_customized.required' => __('validation.required', ['attribute' => 'Customized']),
            'is_sql.required' => __('validation.required', ['attribute' => 'sql Query']),
            'is_third_party_plugin.required' => __('validation.required', ['attribute' => '3rd party plugin']),
            'is_third_party_js.required' => __('validation.required', ['attribute' => '3d party js']),
            'is_js_css.required' => __('validation.required', ['attribute' => 'js/css']),
        ];
    }
}