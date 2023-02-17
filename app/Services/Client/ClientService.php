<?php

namespace App\Services\Client;

use App\Models\Client;

class ClientService
{
    public function updateOrCreateClient($client, array $data): Client
    {
        if (empty($data['id'])) {
            $client = Client::query()->create([
                'name' => $data['name']
            ]);
        } else {
            $client->update([
                'name' => $data['name']
            ]);
        }

        return $client;
    }

    public function updateFields($client, array $fields): Client
    {
        $save_fields = [];
        foreach ($fields as $field) {
            if (empty($field['id']) || empty($field['value'])) {
                continue;
            }

            $save_fields[$field['id']] = ['value' => $field['value']];
        }

        $client->fields()->sync($save_fields);

        return $client;
    }
}
