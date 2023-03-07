<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DealRequest;
use App\Http\Resources\DealResource;
use App\Models\Deal;
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
   /*     dd($deal);*/

        /*$data = $request->validated();
        $deal->update([
            'name' => $data['name'],
            'pipeline_id' => $data['pipeline_id'],
            'client_id' => $data['client_id'],
            'stage_id' => $data['stage_id'],
            'from_api' => true,
            'responsible_id' => backpack_user()->id
        ]);

        if (isset($data['fields'])) {
            $deal->fields()->detach();
            foreach ($data['fields'] as $field) {
                if (empty($field['id']) || empty($field['value'])) {
                    continue;
                }
                $deal->fields()->attach($field['id'], ['value' => $field['value']]);
            }
        }

        return DealResource::make($deal);*/
    }

}
