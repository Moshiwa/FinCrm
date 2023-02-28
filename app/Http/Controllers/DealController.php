<?php

namespace App\Http\Controllers;

use App\Enums\CommentTypeEnum;
use App\Events\ChangePipeline;
use App\Http\Requests\DealRequest;
use App\Models\ButtonAction;
use App\Models\Deal;
use App\Models\DealComment;
use App\Models\Pipeline;
use App\Services\Deal\DealService;
use App\Services\SettingService;
use App\Services\Space\SpaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpParser\Comment;

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

        $action = null;
        if ($data['action']['id']) {
            $action = ButtonAction::query()->with(['pipeline', 'stage', 'responsible'])->find($data['action']['id']);
        }

        $deal = Deal::query()->with(['pipeline', 'stage', 'responsible'])->find($data['id']);

        $deal->name = $data['name'];
        $deal->pipeline_id = $data['pipeline_id'];
        $deal->client_id = $data['client_id'];
        $deal->stage_id = $data['stage_id'];
        $deal->responsible_id = $data['responsible_id'];
        $this->service->createNewMessage($deal, $data);
        $deal->save();

        $deal->fields()->sync($data['fields'] ?? []);
        $this->service->updateClient($deal, $data);
        $this->service->updateComments($deal, $data);

        $comment_count = $data['comment_count'] ?? 10;

        $deal->load([
            'stage',
            'pipeline',
            'pipeline.buttons',
            'pipeline.buttons.visible',
            'pipeline.buttons.action',
            'responsible'=> function ($query) {
                $query->select('id', 'name');
            },
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

        return response()->json([
            'deal' => $deal,
            'stages' => $deal->pipeline->stages,
            'pipelines' => Pipeline::query()->select(['id', 'name'])->get(),
        ]);

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
