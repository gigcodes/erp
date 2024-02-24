<?php

namespace App\Http\Requests\SocialConfig;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'store_website_id' => 'required',
            'platform' => 'required',
            'name' => 'required',
            'status' => 'required',
            'page_id' => 'required',
            'account_id' => 'required',
            'page_token' => 'required',
            'webhook_token' => 'nullable',
            'ad_account_id' => 'nullable|exists:social_ad_accounts,id',
        ];
    }
}
