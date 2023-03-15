<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SenderRequest extends FormRequest
{
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'integrations.*' => 'exclude_unless:integrations.*.value,true',
            'message' => 'required|max:255',
            'recipient' => 'required|max:255'
        ];
    }

    public function attributes()
    {
        return [
            //
        ];
    }

    public function messages()
    {
        return [
            //
        ];
    }
}
