<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|min:3|max:255',
            'entity' => 'required',
            'entity_id' => 'required',
            'author_id' => 'required|exists:users,id',
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
            'content.max' => 'Контент не должен превышать :max',
            'content.min' => 'Контент не должен превышать :min',
            'entity.required' => 'Поле сущности обязательное',
            'entity_id.required' => 'Поле идентификатор сущности обязательное поле',
            'author_id.required' => 'Автор обязательное поле',
            'author_id.exists' => 'Автор не найден среди пользователей',
        ];
    }
}
