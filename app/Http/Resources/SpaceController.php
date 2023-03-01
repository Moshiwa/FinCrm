<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;

class SpaceController extends Controller
{
    public function index(Request $request)
    {
        $with = $request->get('with', 'fields');

        $clients = Client::query()
            ->with($with)
            ->get();

        return ClientResource::collection($clients);
    }

    public function show(Request $request, Client $client)
    {
        $with = $request->get('with', 'fields');
        $client->load($with);

        return ClientResource::make($client);
    }

    public function store(ClientRequest $request, Client $client)
    {

    }
}
