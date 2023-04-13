<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        return [
            'id' => 'nullable',
            'name' => 'required|min:3|max:255',
            'fields.*' => 'nullable',
        ];
    }

    public function attributes(): array
    {
        return [
            //
        ];
    }

    public function messages(): array
    {
        return [
            'id.exists' => 'Сделка с таким id не существует',
            'name.required' => 'Наименование сделки является обязательным полем',
            'name.max' => 'Наименование не должно превышать :max',
            'stage.id.exists' => 'Стадии с таким id не существует',
            'pipeline.id.exists' => 'Воронки с таким id не существует',
            'responsible.id.exists' => 'Пользователя с таким id не существует',
            'client.id.exists' => 'Клиента с таким id не существует',
        ];
    }
}
