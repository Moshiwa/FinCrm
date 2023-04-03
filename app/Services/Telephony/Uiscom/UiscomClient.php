<?php

namespace App\Services\Telephony\Uiscom;

use GuzzleHttp\Client;

class UiscomClient
{
    private string $call_url = 'https://callapi.uiscom.ru/v4.0';
    private string $data_url = 'https://dataapi.uiscom.ru/v2.0';

    private string $selected_url = '';

    private array $params;
    private Client $client;
    private string $jsonrpc = '2.0';
    private string $id = 'req1';

    public function __construct()
    {
        $this->params = [
            "jsonrpc" => $this->jsonrpc,
            "id" => $this->id,
            "method" => '',
            "params" => []
        ];

        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    public function post($method, $params, $is_call = true)
    {
        $this->selected_url = $is_call ? $this->call_url : $this->data_url;

        $this->params['method'] = $method;
        $this->params['params'] = $params;
        $params = json_encode($this->params);
        $response = $this->client->post($this->selected_url, [
            'body' => $params,
        ]);

        $response = $response->getBody()->getContents();

        return json_decode($response, true);

    }
}
