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

        $errors = [];
        $messages = [];

        $client = Client::query()->find($data['client_id']);
        $deal = Deal::query()->with(['comments'])->find($data['deal_id']);



        foreach ($data['integrations'] as $integration) {
            $service = SenderService::factory($integration['name']);
            if (empty($service)) {
                $errors[] = "Интеграция {$integration['name']} не настроена.";
                continue;
            }

            $response = $service->send($data['message'], $data['recipient']);

            if (empty($service->getError())) {
                $deal->comments()->create([
                    'type' => CommentTypeEnum::REMOTE->value,
                    'title' => 'Сообщение на номер ' . $data['recipient'],
                    'content' => $data['message'],
                    'author_id' => backpack_user()->id
                ]);

                $messages[] = $service->getTitle() . ' сообщение доставлено.';

            }

            $errors[] = $service->getError();

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
            'errors' => $errors,
            'data' => $deal,
            'messages' => $messages
        ]);
    }
}
