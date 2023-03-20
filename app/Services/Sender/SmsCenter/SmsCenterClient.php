<?php

namespace App\Services\Sender\SmsCenter;

use App\Models\Integration;
use GuzzleHttp\Client;

class SmsCenterClient
{
    private string $url = 'https://smsc.ru/sys/';

    private array $params;
    private Client $client;

    public function __construct()
    {
        $login = Integration::query()->where('name','sms_center')->first()->login;
        $password = Integration::query()->where('name','sms_center')->first()->password;
        $this->params = [
            'login' =>  $login,
            'psw' => $password,
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
}
