<?php

namespace App\Http\Controllers;

use App\Enums\CommentTypeEnum;
use App\Events\ChangePipeline;
use App\Http\Requests\DealRequest;
use App\Models\Deal;
use App\Models\DealComment;
use App\Models\Pipeline;
use App\Services\Deal\DealService;
use App\Services\Space\SpaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DealController extends Controller
{
    private DealService $service;

    public function __construct()
    {
        $this->service = new DealService();
    }

    public function update(DealRequest $request)
    {
        $data = $request->validated();

        if ($data['id']) {
            $deal = Deal::query()->find($data['id']);
        } else {
            $deal = new Deal();
        }

        $this->service->saveDeal($deal, $data);
        $this->service->updateClient($deal, $data);
        $this->service->updateComments($deal, $data);

        $comment_count = $data['comment_count'] ?? 5;

        $deal->load([
            'stage',
            'stage.settings',
            'pipeline',
            'responsible',
            'client',
            'fields',
            'client.fields',
            'comments' => function ($query) use ($comment_count) {
                $query->orderBy('created_at', 'desc')->offset(0)->limit($comment_count);
            },
            'comments.files',
            'comments.author' => function ($query) {
                $query->select('id', 'name');
            }
        ]);

        return $deal;

    }

    public function getStagesByPipeline(Pipeline $pipeline)
    {
        return $pipeline->stages()->select('id', 'name')->get();
    }

    public function loadComments(Deal $deal, Request $request)
    {
        $offset = $request->get('offset');
        $deal->load([
            'comments' => function ($query) use ($offset) {
                $query->offset($offset)->limit(5)->orderBy('created_at', 'desc');
            },
            'comments.files',
            'comments.author' => function ($query) {
                $query->select('id', 'name');
            }
        ]);

        return $deal;
    }
}
