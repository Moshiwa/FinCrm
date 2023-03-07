<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $clients = Client::query()->get();

        return ClientResource::collection($clients);
    }

    public function show(Request $request, Client $client): ClientResource
    {
        return ClientResource::make($client);
    }

    public function update(ClientRequest $request, Client $client): ClientResource
    {
        $data = $request->validated();

        if (! empty($data['name'])) {
            $client->update([
                'name' => $data['name']
            ]);
        }

        if (isset($data['fields'])) {
            $client->fields()->detach();
            foreach ($data['fields'] as $field) {
                if (empty($field['id']) || empty($field['value'])) {
                    continue;
                }
                $client->fields()->attach($field['id'], ['value' => $field['value']]);
            }
        }

        return ClientResource::make($client);
    }

    public function store(ClientRequest $request)
    {
        $data = $request->validated();

        if (empty($data['name'])) {
            return response()->json([
                'error' => 'Не указано имя'
            ]);
        }

        $client = Client::query()->create([
            'name' => $data['name']
        ]);

        if (isset($data['fields'])) {
            $client->fields()->detach();
            foreach ($data['fields'] as $field) {
                if (empty($field['id']) || empty($field['value'])) {
                    continue;
                }
                $client->fields()->attach($field['id'], ['value' => $field['value']]);
            }
        }


        return ClientResource::make($client);
    }
}
