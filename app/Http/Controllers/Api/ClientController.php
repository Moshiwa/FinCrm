<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\Field;
use App\Services\Field\FieldService;
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

        //$fields = FieldService::prepareFieldsForSaveApi(Field::includedClient()->get(), $data['fields']);
        $client->fields()->sync($data['fields'] ?? []);

        return ClientResource::make($client);
    }

    public function store(ClientRequest $request)
    {
        $data = $request->validated();

        if (empty($data['name'])) {
            return response()->json([
                'errors' => ['Не указано имя']
            ]);
        }

        $client = Client::query()->create([
            'name' => $data['name']
        ]);

        $client->fields()->sync($data['fields'] ?? []);

        return ClientResource::make($client);
    }
}
