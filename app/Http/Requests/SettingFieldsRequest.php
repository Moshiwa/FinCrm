<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingFieldsRequest extends FormRequest
{
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            '*.id' => 'nullable',
            '*.type' => 'required|max:255',
            '*.name' => 'required|max:255',
            '*.options' => 'nullable|array',
            '*.settings' => 'nullable|array',
            '*.settings.*.id' => 'nullable',
            '*.settings.*.name' => 'nullable',
            '*.settings.*.type' => 'nullable',
            '*.settings.*.description' => 'nullable',
            '*.settings.*.pivot.is_enable' => 'nullable'
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
