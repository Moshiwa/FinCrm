<?php

namespace App\Http\Requests;

use App\Enums\FieldsEntitiesEnum;
use App\Enums\FieldsEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class FieldRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'name' => 'required|min:3|max:255',
            'type' => [new Enum(FieldsEnum::class)],
            'entity' => [new Enum(FieldsEntitiesEnum::class)],
            'options' => 'nullable|array',
            'is_active' => 'nullable|boolean'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Поле Имя обязательное',
            'name.max' => 'Наименование не должно превышать :max',
            'name.min' => 'Наименование должно быть длинее :min',
            'type' => 'Данного типа поля не существует',
            'entity' => 'Сущность указана неверно',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'options' => json_decode($this->options, true),
        ]);
    }
}
