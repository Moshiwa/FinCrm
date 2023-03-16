<?php

namespace App\Services\Telephony\Uiscom;

use GuzzleHttp\Client;
//ToDo Дописать
class UiscomDataService
{
    private UiscomClient $client;

    public function __construct()
    {
        $this->client = new UiscomClient();
    }

    public function login()
    {
        $params = [
            'login' => env('UISCOM_LOGIN'),
            'password' => env('UISCOM_PASSWORD')
        ];

        $result = $this->client->post('login.user', $params, false);

        if (isset($result['error'])) {

        }
        dd($result);
    }

    public function getAccountInfo()
    {
        $params = [
            'access_token' => '2fRN4g21cab3a550ba882e012a022f77fa4f27f7a6541455efcfe0dbdddd8b97d16c6f7e',

        ];
        $result = $this->client->post('get.account', $params, false);

        if (isset($result['error'])) {

        }
        dd($result);
    }

    private function errorsList($error)
    {

        $errors = [
            'Invalid parameter value' => 'В фильтрах было передано некорректное значение для regexp.',
            'Sort by parameter is prohibited' => 'Сортировка по параметру запрещена и невозможна, так как параметр для сортировки не находится в списке разрешенных для сортировки.',
            'Filter by parameter is prohibited' => 'Фильтрация по параметру запрещена и невозможна, так как параметр для фильтрации не находится в списке разрешенных для фильтрации.',
            'Max value of requested date interval is 3 months' => 'Период между указанными датами в date_from и date_till превышает 3 месяца.',
        ];

        return $errors[$error] ?? '';
    }
}
