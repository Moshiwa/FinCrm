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

    public function call()
    {
        $params = [


        ];
        $result = $this->client->post('start.simple_call', $params);

        dd($result);
    }

    private function errorsList($code, $type)
    {

        $errors = [

        ];

        return $errors[$type][$code] ?? '';
    }
}
