<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'id' => 'nullable',
            'name' => 'required|min:3|max:255',
            'fields' => 'nullable|array',
            'fields.*.id' => 'nullable|numeric',
            'fields.*.value' => 'nullable|max:255'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Поле Имя обязательное',
            'name.max' => 'Наименование не должно превышать :max',
            'name.min' => 'Наименование должно быть длинее :min',
            'fields.*.value.max' => 'Значение поля не должно превышать :max',
        ];
    }
}
