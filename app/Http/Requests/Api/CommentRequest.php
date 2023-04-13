<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        return [
            'content' => 'required|min:3|max:255',
            'entity' => 'required',
            'entity_id' => 'required',
            'files' => 'nullable',
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
        ];
    }
}
