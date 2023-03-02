<?php

namespace App\Services\Field;

use App\Enums\FieldsEntitiesEnum;
use App\Models\Field;
use Illuminate\Support\Str;

class FieldService
{
    public function getClientFields($client): array
    {
        $included_fields = Field::includedClient()->get();
        $filled_client_fields = $client->fields->toArray();
        $all_client_fields = $included_fields->toArray();

        $fields = [];
        foreach ($all_client_fields as $index => $client_field) {
            $client_field['pivot'] = [ 'value' => '' ];
            $fields[$index] = $client_field;
            foreach ($filled_client_fields as $filled_client_field) {
                if ($client_field['id'] === $filled_client_field['id']) {
                    $filled_client_field['pivot']['value'] = $this->castFieldValue($filled_client_field);
                    $fields[$index] = $filled_client_field;
                }
            }
        }

        return $fields;
    }

    public function getDealFields($deal): array
    {
        $included_fields = Field::includedDeal()->get();
        $filled_deal_fields = $deal->fields->toArray();
        $all_deal_fields = $included_fields->toArray();

        $fields = [];
        foreach ($all_deal_fields as $index => $deal_field) {
            $deal_field['pivot'] = [ 'value' => '' ];
            $fields[$index] = $deal_field;
            foreach ($filled_deal_fields as $filled_deal_field) {
                if ($deal_field['id'] === $filled_deal_field['id']) {
                    $filled_deal_field['pivot']['value'] = $this->castFieldValue($filled_deal_field);
                    $fields[$index] = $filled_deal_field;
                }
            }
        }

        return $fields;
    }

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

    private function castFieldValue($field)
    {
        $result = $field['pivot']['value'];

        return match ($field['type']['name']) {
            'checkbox' => !($result === 'false'),
            'number' => (int) $result,
            default => $result,
        };
    }
}
