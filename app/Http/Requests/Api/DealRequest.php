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
            'pipeline_id' => 'required|exists:pipelines,id',
            'responsible_id' => 'nullable|exists:users,id',
            'client_id' => 'required|exists:clients,id',
            'fields.*' => 'nullable',
           /* 'new_comment.*' => 'nullable',
            'delete_comment_id' => 'nullable|exists:comments,id',
            'action' => 'nullable|array',*/
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
