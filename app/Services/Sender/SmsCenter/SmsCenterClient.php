<?php

namespace App\Services\Sender\SmsCenter;

use GuzzleHttp\Client;

class SmsCenterClient
{
    private string $url = 'https://smsc.ru/sys/';

    private array $params;
    private Client $client;

    public function __construct()
    {
        $this->params = [
            'login' =>  env('SMS_CENTER_LOGIN'),
            'psw' => env('SMS_CENTER_PASSWORD'),
            'fmt' => 3,
            'cost' => 3
        ];

        $this->client = new Client();
    }

    public function get($type, $params)
    {
        $auth_params = http_build_query($this->params);
        $url = "{$this->url}{$type}?{$auth_params}";

        if ($params) {
            $additional_params = http_build_query($params);
            $url .= "&{$additional_params}";
        }

        $result = $this->client->get($url);

        return $result->getBody()->getContents();
    }

    public function post($type, $params)
    {

    }


}
