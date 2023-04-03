<?php

namespace App\Services\Telephony\Uiscom;

use GuzzleHttp\Client;

class UiscomDataService
{
    private UiscomClient $client;

    public function __construct()
    {
        $this->client = new UiscomClient();
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
