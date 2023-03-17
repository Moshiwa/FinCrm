<?php

namespace App\Services\Telephony\Uiscom;

use GuzzleHttp\Client;
//ToDo Дописать
class UiscomService
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

        $result = $this->client->post('login.user', $params);

        if (isset($result['error'])) {

        }

        dd($result);

    }

    public function call()
    {
        $params = [
            'access_token' => '8l1vxvi61873i2r2i3kozovb55dft846ejsnld7e',
            "first_call" => "employee",
            "virtual_phone_number" => "78452338065",
            "contact" => '79022007017',
            "employee" => [
                'id' => 3950243
            ]
        ];

        $result = $this->client->post('start.employee_call', $params);

        $call_session_id = $result['result']['data']['call_session_id'] ?? '';
dd($result);
    }

    //Завершение звонка
    public function releaseCall()
    {
        $params = [
            'access_token' => '8l1vxvi61873i2r2i3kozovb55dft846ejsnld7e',
            "call_session_id" => "",
        ];

        $result = $this->client->post('release.call', $params);
        dd($result);
    }

    //Удержание звонка
    public function holdCall()
    {
        $params = [
            'access_token' => '8l1vxvi61873i2r2i3kozovb55dft846ejsnld7e',
            "call_session_id" => "",
        ];

        $result = $this->client->post('hold.call', $params);
        dd($result);
    }
    //Снять с удержания звонка
    public function unholdCall()
    {
        $params = [
            'access_token' => '8l1vxvi61873i2r2i3kozovb55dft846ejsnld7e',
            "call_session_id" => "",
        ];

        $result = $this->client->post('unhold.call', $params);
        dd($result);
    }

    private function errorsList($code, $type)
    {

        $errors = [

        ];

        return $errors[$type][$code] ?? '';
    }
}
