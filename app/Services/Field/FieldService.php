<?php

namespace App\Services\Field;

use App\Enums\FieldsEnum;
use App\Models\Client;
use App\Models\Field;

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

    private function castFieldValue($field)
    {
        $result = $field['pivot']['value'];

        return match ($field['type']) {
            FieldsEnum::checkbox->value => !($result === 'false'),
            FieldsEnum::number->value => (int) $result,
            default => $result,
        };
    }
}
