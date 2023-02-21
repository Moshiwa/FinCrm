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
            'options' => 'required|array',
            'options.display' => 'array',
            'options.display.stages' => 'array',
            'options.display.stages.*.id' => 'exclude_if:options.display.stages.*.is_active,false',
            'options.stage_id' => 'nullable',
            'options.pipeline_id' => 'nullable',
            'options.responsible_id' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            //
        ];
    }
}
