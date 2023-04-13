<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DealRequest;
use App\Http\Resources\DealResource;
use App\Models\Client;
use App\Models\Deal;
use App\Models\Stage;
use App\Services\Deal\DealService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DealController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $deals = Deal::query()->get();

        return DealResource::collection($deals);
    }

    public function show(Request $request, Deal $deal): DealResource
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
            'responsible_id' => backpack_user()->id,
            'deadline' => $data['deadline'] ?? $deal->deadline
        ]);

        $service->createNewMessage($deal, $comment_data);
        $service->updateComments($deal, $data);

        $deal->fields()->sync($data['fields'] ?? []);

        return DealResource::make($deal);
    }

    public function store(DealRequest $request)
    {
        $data = $request->validated();
        $service = new DealService();

        $stage = Stage::query()->find($data['stage_id']);
        $deadline = time() + $stage->calculated_deadline;

        $deal = Deal::query()->create([
            'name' => $data['name'],
            'pipeline_id' => $data['pipeline_id'],
            'client_id' => $data['client_id'],
            'stage_id' => $stage->id,
            'responsible_id' => $data['responsible_id'],
            'deadline' => $data['deadline'] ?? Carbon::createFromTimestamp($deadline)
        ]);

       /* $comment_data = $service->prepareCommentData($deal, $data);
        $service->createNewMessage($deal, $comment_data);
        $service->updateComments($deal, $data);*/

        $deal->fields()->sync($data['fields'] ?? []);

        return DealResource::make($deal);
    }
}
