<?php

namespace App\Services\Field;

use App\Enums\FieldsEntitiesEnum;
use App\Models\Field;
use Illuminate\Support\Str;

class FieldService
{
    public static function getEntityFromRequest($request): FieldsEntitiesEnum
    {
        $request_entity = $request->get('entity');
        if (empty($request_entity)) {
            $referer = $request->headers->get('referer');
            $referer = parse_url($referer);
            $query = $referer['query'] ?? '';

            $entity = match (true) {
                Str::contains('entity=client', $query) => FieldsEntitiesEnum::findValue('client'),
                Str::contains('entity=task', $query) => FieldsEntitiesEnum::findValue('task'),
                default => FieldsEntitiesEnum::findValue('deal')
            };

        } else {
            $entity = FieldsEntitiesEnum::findValue($request_entity);
        }

        return empty($entity) ? FieldsEntitiesEnum::deal : $entity;
    }

    public static function prepareFieldsForSaveApi($all_fields, $api_fields)
    {
        $fields = [];
        foreach ($all_fields as $field) {
            $type = $field->type?->name ?? '';
            foreach ($api_fields as $api_field_id => $api_field) {
                if ($api_field_id == $field->id) {
                    //Подгонка данных под селект
                    if ($type === 'select') {
                        $options = $field->options;
                        foreach ($options as $option) {
                            $option = strtolower($option['value'] ?? '');
                            $api_value = strtolower($api_field['value'] ?? '');
                            if ($option === $api_value) {
                                $fields[$field->id] = [
                                    'value' => $api_field['value'] ?? ''
                                ];
                            }
                        }
                    } else {
                        $fields[$field->id] = [
                            'value' => $api_field['value'] ?? ''
                        ];
                    }
                }
            }
        }

        return $fields;
    }
}
