<?php

namespace App\Services\Telephony\Uiscom;

use App\Services\Dadata\DadataService;
use GuzzleHttp\Client;
//ToDo Дописать Необходимо использовать softphone uis
class UiscomService
{
    private UiscomClient $client;
    protected string $error = '';

    public function __construct()
    {
        $this->client = new UiscomClient();
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function cleanPhone($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (empty($phone) || strlen($phone) != 11) {
            $this->error = 'Неверный формат номера телефона';
            return '';
        }

        return $phone;
    }

    public function authManager()
    {
        $params = [
            "login" => "",
            "password" => ""
        ];
        $client = new Client();
        $response = $client->post('https://my.uiscom.ru/sup/auth/login', [
            'body' => json_encode($params)
        ]);

        $response = $response->getBody()->getContents();
        $data = json_decode($response, true);

        return $data['data']['employee_id'] ?? '';
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

    public function call($phone)
    {
        $params = [
            'access_token' => '8l1vxvi61873i2r2i3kozovb55dft846ejsnld7e',
            "first_call" => "employee",
            "virtual_phone_number" => "78452338065",
            "contact" => $phone,
            "employee" => [
                'id' => 3950243
            ]
        ];

        $result = $this->client->post('start.employee_call', $params);

        return $result['result']['data']['call_session_id'] ?? '';
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
