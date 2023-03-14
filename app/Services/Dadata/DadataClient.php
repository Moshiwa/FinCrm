<?php

namespace App\Services\Dadata;

use Exception;

class DadataClient
{
    private $clean_url = "https://cleaner.dadata.ru/api/v1/clean";
    private $suggest_url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs";
    private string $token;
    private string $secret;
    private $handle;

    public function __construct()
    {
        $this->token = env('DADATA_TOKEN');
        $this->secret = env('DADATA_SECRET');
    }

    public function init()
    {
        $this->handle = curl_init();
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->handle, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Accept: application/json",
            "Authorization: Token " . $this->token,
            "X-Secret: " . $this->secret,
        ));
        curl_setopt($this->handle, CURLOPT_POST, 1);
    }

    public function clean($type, $value)
    {
        $url = $this->clean_url . "/$type";
        $fields = array($value);
        return $this->executeRequest($url, $fields);
    }

    public function suggest($type, $fields)
    {
        $url = $this->suggest_url . "/suggest/$type";
        return $this->executeRequest($url, $fields);
    }

    public function close()
    {
        curl_close($this->handle);
    }

    private function exec()
    {
        $result = curl_exec($this->handle);
        $info = curl_getinfo($this->handle);
        if ($info['http_code'] == 429) {
            throw new Exception('Many requests');
        } elseif ($info['http_code'] != 200) {
            throw new Exception('Request failed with http code ' . $info['http_code'] . ': ' . $result);
        }
        return $result;
    }

    private function executeRequest($url, $fields)
    {
        curl_setopt($this->handle, CURLOPT_URL, $url);
        if ($fields != null) {
            curl_setopt($this->handle, CURLOPT_POST, 1);
            curl_setopt($this->handle, CURLOPT_POSTFIELDS, json_encode($fields));
        } else {
            curl_setopt($this->handle, CURLOPT_POST, 0);
        }
        $result = $this->exec();
        $result = json_decode($result, true);
        return $result;
    }
}
