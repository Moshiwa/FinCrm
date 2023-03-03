<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'id' => 'required|exists:deals,id',
            'name' => 'required|max:255',
            'description' => 'nullable',
            'start' => 'nullable',
            'end' => 'nullable',

            'task_stage_id' => 'nullable|exists:stages,id',
            'responsible_id' => 'nullable|exists:App\Models\User,id',
            'manager_id' => 'nullable|exists:App\Models\User,id',
            'executor_id' => 'nullable|exists:App\Models\User,id',

            'fields.*' => 'nullable',
            'comments.*' => 'nullable',
            'new_comment.*' => 'nullable',

            'action' => 'nullable|array',
            'action.id' => 'nullable|exists:button_actions,id',
            'action.pipeline_id' => 'nullable|exists:pipelines,id',
            'action.stage_id' => 'nullable|exists:stages,id',
            'action.responsible_id' => 'nullable|exists:App\Models\User,id',
            'action.comment' => 'nullable',

            'delete_comment_id' => 'nullable|numeric',
            'comment_offset' => 'nullable|numeric',
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
            'id.exists' => 'Задача с таким id не существует',
            'name.required' => 'Наименование задачи является обязательным полем',
            'name.max' => 'Наименование не должно превышать :max',
            'stage.id.exists' => 'Стадии с таким id не существует',
            'responsible_id.exists' => 'Пользователя с таким id не существует',
        ];
    }
}
