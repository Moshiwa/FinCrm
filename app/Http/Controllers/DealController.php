<?php

namespace App\Http\Controllers;

use App\Http\Requests\DealRequest;
use App\Models\Comment;
use App\Models\Deal;
use App\Models\Pipeline;
use App\Services\Deal\DealPrepareService;
use Illuminate\Support\Facades\Auth;

class DealController extends Controller
{

    private DealPrepareService $service;

    public function __construct()
    {
        $this->service = new DealPrepareService();
    }

    public function update(DealRequest $request)
    {
        $data = $request->validated();
        $deal = Deal::query()->find($data['id']);
        $deal->update($data);

        $comments = $this->service->prepareComments($data);
        $deal->comments()->detach();

        $deal->client()->update($data['client']);

    }

    public function getStagesByPipeline(Pipeline $pipeline)
    {
        return $pipeline->stages()->select('id', 'name')->get();
    }
}
