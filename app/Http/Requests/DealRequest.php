<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DealRequest extends FormRequest
{
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:deals,id',
            'name' => 'required|max:255',

            'stage_id' => 'nullable|exists:stages,id',
            'pipeline_id' => 'nullable|exists:pipelines,id',
            'responsible_id' => 'nullable|exists:App\Models\User,id',
            'client_id' => 'nullable|exists:clients,id',

            'client.*' => 'nullable',
            'fields.*' => 'nullable',
            'comments.*' => 'nullable',
            'new_comment.*' => 'nullable',

            'action' => 'nullable|array',
            'action.id' => 'required|exists:button_actions,id',

            'delete_comment_id' => 'nullable|numeric',
            'comment_offset' => 'nullable|numeric',
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
