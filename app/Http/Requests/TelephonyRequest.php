<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TelephonyRequest extends FormRequest
{
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'deal_id' => 'required|exists:deals,id',
            'phone' => 'required|max:255',
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
