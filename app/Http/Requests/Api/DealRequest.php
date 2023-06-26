<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class DealRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'deadline' => 'nullable|date',
            'stage_id' => 'required|exists:stages,id',
            'responsible_id' => 'nullable|exists:users,id',
            'client_id' => 'required|exists:clients,id',
            'new_comment' => 'nullable',
            'new_comment.content' => 'required|max:255',
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
            'stage_id.exists' => 'Стадии с таким id не существует',
            'stage_id.required' => 'Стадия обязательное поле',
            'responsible_id.exists' => 'Пользователя с таким id не существует',
            'responsible_id.required' => 'Ответственный обязательное поле',
            'client_id.exists' => 'Клиента с таким id не существует',
            'client_id.required' => 'Клиент обязательное поле',
        ];
    }
}
