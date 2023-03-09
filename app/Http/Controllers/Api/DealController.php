<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DealRequest;
use App\Http\Resources\DealResource;
use App\Models\Client;
use App\Models\Deal;
use App\Services\Deal\DealService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DealController extends Controller
{
    public function index(DealRequest $request): AnonymousResourceCollection
    {
        $deals = Deal::query()->get();

        return DealResource::collection($deals);
    }

    public function show(DealRequest $request, Deal $deal): DealResource
    {
        return DealResource::make($deal);
    }

    public function update(DealRequest $request, Deal $deal)
    {
        $data = $request->validated();
        $service = new DealService();
        $comment_data = $service->prepareCommentData($deal, $data);
        $deal->update([
            'name' => $data['name'],
            'pipeline_id' => $data['pipeline_id'],
            'client_id' => $data['client_id'],
            'stage_id' => $data['stage_id'],
            'responsible_id' => backpack_user()->id
        ]);

        $service->createNewMessage($deal, $comment_data);

        if (isset($data['fields'])) {
            $deal->fields()->detach();
            foreach ($data['fields'] as $field) {
                if (empty($field['id']) || empty($field['value'])) {
                    continue;
                }
                $deal->fields()->attach($field['id'], ['value' => $field['value']]);
            }
        }

        if (isset($data['client'])) {
            $client = Client::query()->find($data['client_id']);
            $new_client_data = $data['client'];
            $client->update([
                'name' => $new_client_data['name']
            ]);

            if(isset($new_client_data['fields'])) {
                $client->fields()->detach();
                foreach ($new_client_data['fields'] as $field) {
                    if (empty($field['id']) || empty($field['value'])) {
                        continue;
                    }
                    $client->fields()->attach($field['id'], ['value' => $field['value']]);
                }
            }
        }

        return DealResource::make($deal);
    }

}
