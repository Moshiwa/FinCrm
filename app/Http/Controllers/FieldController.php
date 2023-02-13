<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingFieldsRequest;
use App\Models\Field;
use Illuminate\Support\Arr;

class FieldController extends Controller
{

    public function save(SettingFieldsRequest $request)
    {
        $data = $request->validated();

        $fields = Arr::pluck($data, 'id');
        $fields = Field::query()
           ->with('settings')
           ->whereIn('id', $fields)
           ->get();

        foreach ($fields as $field) {
            foreach ($data as $field_item) {
                if ($field->id !== $field_item['id']) {
                    continue;
                }

                $save = [];
                foreach ($field_item['settings'] as $setting) {
                    $save[$setting['id']] = ['is_enable' => $setting['pivot']['is_enable']];
                }

                $field->settings()->sync($save);
            }
       }

    }
}
