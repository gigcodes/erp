<?php

namespace App\Http\Requests\Facebook;

use Illuminate\Foundation\Http\FormRequest;

class PostCreateRequest extends FormRequest
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
            'config_id' => 'required|exists:social_configs,id',
            'webpage'   => 'nullable',
            'source'    => 'nullable',
            'video1'    => 'nullable',
            'message'   => 'required|string',
            'date'      => 'nullable',
        ];
    }
}
