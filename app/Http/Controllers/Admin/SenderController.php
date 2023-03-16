<?php

namespace App\Http\Controllers\Admin;

use App\Enums\IntegrationEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\SenderRequest;
use App\Services\Sender\SenderService;

class SenderController extends Controller
{
    public function send(SenderRequest $request)
    {
        $data = $request->validated();

        $errors = [];
        $messages = [];

        foreach ($data['integrations'] as $integration) {
            $service = SenderService::factory($integration['name'], $data['recipient']);
            if (empty($service)) {
                $errors[] = "Интеграция {$integration['name']} не настроена.";
                continue;
            }

            $response = $service->send($data['message']);

            if (empty($service->getError())) {
                $messages[] = $service->getTitle() . ' сообщение доставлено.';

            }

            $errors[] = $service->getError();

        }

        return response()->json([
            'errors' => $errors,
            'messages' => $messages
        ]);
    }
}
