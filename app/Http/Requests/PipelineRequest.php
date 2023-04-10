<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PipelineRequest extends FormRequest
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
            'stages' => 'array',
            'stages.*.deadline' => 'required|integer',
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
