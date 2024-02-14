<?php

namespace App\Http\Requests\SocialConfig;

use Illuminate\Foundation\Http\FormRequest;

class EditRequest extends FormRequest
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
            'store_website_id' => 'required|exists:store_websites,id',
            'platform' => 'required',
            'name' => 'required',
            'status' => 'required',
            'page_id' => 'required',
            'page_token' => 'required',
            'webhook_token' => 'required',
        ];
    }
}
