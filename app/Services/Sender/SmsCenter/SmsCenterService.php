<?php

namespace App\Services\Sender\SmsCenter;

use App\Enums\IntegrationEnum;
use App\Services\Sender\SenderService;

class SmsCenterService extends SenderService
{
    public function __construct()
    {
        $this->title = IntegrationEnum::getTitle(IntegrationEnum::SMS_CENTER->value);
        $this->client = new SmsCenterClient();
    }

    public function send($message, $recipient)
    {
        $response = $this->client->get('send.php', [
            'mes' => $message,
            'phones' => $recipient,
            'all' => 3
        ]);

        $response = json_decode($response, true);

        if (isset($response['error_code'])) {
            $this->error = $this->errorsList($response['error_code'], 'send') ?? $response['error'];
            return [];
        }

        return $response;
    }

    public function getSenders()
    {
        $response = $this->client->get('senders.php', [
            'get' => 1,
        ]);

        $response = json_decode($response, true);

        if (isset($response['error_code'])) {
            $this->error = $this->errorsList($response['error_code'], 'send') ?? $response['error'];
            return [];
        }

        return $response;
    }

    public function check(): bool
    {
        $response = $this->client->get('balance.php', []);
        $response = json_decode($response, true);
        if (isset($response['error_code'])) {
            $this->error = $this->errorsList($response['error_code'], 'send');
            return false;
        }

        return true;
    }

    private function errorsList($code, $type)
    {

        $errors = [
            'send' => [
                1 => 'Ошибка в параметрах.',
                2 => 'Неверный логин или пароль. Также возникает при попытке отправки сообщения с IP-адреса, не входящего в список разрешенных Клиентом (если такой список был настроен Клиентом ранее).',
                3 => 'Недостаточно средств на счете Клиента.',
                4 => 'IP-адрес временно заблокирован из-за частых ошибок в запросах.',
                5 => 'Неверный формат даты.',
                6 => 'Сообщение запрещено (по тексту или по имени отправителя). Также данная ошибка возникает при попытке отправки массовых и (или) рекламных сообщений без заключенного договора.',
                7 => 'Неверный формат номера телефона.',
                8 => 'Сообщение на указанный номер не может быть доставлено.',
                9 => 'Отправка более одного одинакового запроса на передачу SMS-сообщения либо более пяти одинаковых запросов на получение стоимости сообщения в течение минуты.
                    Данная ошибка возникает также при попытке отправки пятнадцати и более запросов одновременно с разных подключений под одним логином (too many concurrent requests).'
            ]
        ];

        return $errors[$type][$code] ?? '';
    }
}
