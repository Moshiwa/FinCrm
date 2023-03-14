<?php

namespace App\Services\Dadata;

use Exception;

class DadataService
{
    private DadataClient $client;

    /**
     * Max 20
     * @var int
     */
    private int $count = 10;

    /**
     * ru, en, fr
     * @var string
     */
    private string $language = 'ru';

    /**
     * administrative, municipal
     * @var string
     */
    private string $division = 'administrative';

    public function __construct()
    {
        $this->client = new DadataClient();
    }

    public function setCount($count): void
    {
        $this->count = $count;
    }

    public function setLanguage($language): void
    {
        $this->language = $language;
    }

    public function setDivision($division): void
    {
        $this->division = $division;
    }

    public function cleanFio($name)
    {
        $this->client->init();
        $result = $this->client->clean('name', $name);
        $this->client->close();

        return $result;
    }

    public function cleanAddress($address)
    {
        $this->client->init();
        $result = $this->client->clean('address', $address);
        $this->client->close();

        return $result;
    }

    public function cleanEmail($email)
    {
        $this->client->init();
        $result = $this->client->clean('email', $email);
        $this->client->close();

        return $result;
    }

    public function findCompany($name)
    {
        $query = [
            'query' => $name,
            'count' => $this->count,
            'language' => $this->language,
            'division' => $this->division
        ];

        $this->client->init();
        $result = $this->client->suggest('party', $query);
        $this->client->close();
        return $result;
    }

    public function findAddress($name)
    {
        $query = [
            'query' => $name,
            'count' => $this->count,
            'language' => $this->language,
            'division' => $this->division
        ];

        $this->client->init();
        $result = $this->client->suggest('address', $query);
        $this->client->close();
        return $result;
    }

    public function findPerson($name)
    {
        $query = [
            'query' => $name,
            'count' => $this->count,
            'language' => $this->language,
            'division' => $this->division
        ];

        $this->client->init();
        $result = $this->client->suggest('fio', $query);
        $this->client->close();
        return $result;
    }
}
