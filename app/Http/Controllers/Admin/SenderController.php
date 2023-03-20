<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CommentTypeEnum;
use App\Enums\IntegrationEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\SenderRequest;
use App\Models\Client;
use App\Models\Deal;
use App\Services\Sender\SenderService;

class SenderController extends Controller
{
    public function send(SenderRequest $request)
    {
        $data = $request->validated();

        $client = Client::query()->find($data['client_id']);
        $deal = Deal::query()->with(['comments'])->find($data['deal_id']);

        $service = SenderService::factory($data['integration']);
        $response = $service->send($data['message'], $data['recipient']);

        if (empty($service->getError())) {
            $deal->comments()->create([
                'type' => CommentTypeEnum::REMOTE->value,
                'title' => 'Сообщение на ' . $data['recipient'],
                'content' => $data['message'],
                'author_id' => backpack_user()->id
            ]);

            $message = $service->getTitle() . ' сообщение доставлено.';
            $success = true;
        } else {
            $message = $service->getError();
            $success = false;
        }

        $type = $request->get('type');
        $sort = $request->get('date_sort', 'desc');
        $deal->load([
            'stage',
            'pipeline',
            'responsible',
            'client',
            'fields.type',
            'comments' => function ($query) use ($type, $sort) {
                $query->when($type, function ($query, $type) {
                    $query->where('type', $type);
                })
                    ->offset(0)->limit(10)->orderBy('created_at', $sort);
            },
            'comments.files',
            'comments.author' => function ($query) {
                $query->select('id', 'name');
            }
        ]);

        return response()->json([
            'success' => $success,
            'data' => $deal,
            'message' => $message
        ]);
    }
}
