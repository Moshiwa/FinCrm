<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskButtonRequest extends FormRequest
{
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'id' => 'nullable',
            'name' => 'required|min:3|max:255',
            'color' => 'nullable|string',
            'icon' => 'nullable|string',
            'visible' => 'array',
            'visible.*.id' => 'exclude_unless:visible.*.is_active,true',
            'action' => 'required|array',
            'action.task_stage_id' => 'nullable|exists:stages,id',
            'action.responsible_id' => 'nullable|exists:App\Models\User,id',
            'action.manager_id' => 'nullable|exists:App\Models\User,id',
            'action.executor_id' => 'nullable|exists:App\Models\User,id',
            'action.comment' => 'nullable|boolean',
            'action.start_time' => 'nullable|date',
            'action.end_time' => 'nullable|date',
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
