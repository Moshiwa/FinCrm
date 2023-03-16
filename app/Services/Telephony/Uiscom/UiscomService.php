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

    }

    public function call()
    {
        $params = [
            'access_token' => '2fRN4g21c10614f0570001c38c9273486226edabd5a3e487914f42a8a1cad2f576cd85ca',
            "first_call" => "operator",
            "switch_at_once" => true,
            "show_virtual_phone_number" => false,
            "virtual_phone_number" => "78452398880",
            "external_id" => "334otr01",
            "dtmf_string" => ".1.2.3",
            "contact" => "79875227633",
            "operator" => "79262444491",

        ];
        $result = $this->client->post('start.simple_call', $params);

    }

    private function errorsList($code, $type)
    {

        $errors = [

        ];

        return $errors[$type][$code] ?? '';
    }
}
