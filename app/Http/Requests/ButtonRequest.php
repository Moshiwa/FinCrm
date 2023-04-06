<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ButtonRequest extends FormRequest
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
            'pipeline_id' => 'required|exists:pipelines,id',
            'color' => 'nullable|string',
            'icon' => 'nullable|string',
            'visible' => 'array',
            'visible.*.id' => 'exclude_unless:visible.*.is_active,true',
            'action' => 'required|array',
            'action.stage_id' => 'nullable|exists:stages,id',
            'action.pipeline_id' => 'nullable|exists:pipelines,id',
            'action.responsible_id' => 'nullable|exists:App\Models\User,id',
            'action.comment' => 'nullable|boolean',
            'action.deadline_value' => 'nullable|integer',
            'action.deadline_format_id' => 'nullable|exists:App\Models\DeadlineFormat,id',
        ];
    }

    public function messages()
    {
        return [
            //
        ];
    }
}
