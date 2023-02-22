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
            'pipeline_id' => 'required',
            'color' => 'nullable|string',
            'icon' => 'nullable|string',
            'visible' => 'array',
            'visible.*.id' => 'exclude_unless:visible.*.is_active,true',
            'action' => 'required|array',
            'action.stage_id' => 'nullable',
            'action.pipeline_id' => 'nullable',
            'action.responsible_id' => 'nullable',
            'action.comment' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            //
        ];
    }
}
