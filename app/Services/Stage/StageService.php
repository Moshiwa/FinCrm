<?php

namespace App\Services\Stage;

use App\Enums\SettingKeysEnum;
use App\Models\Setting;
use App\Models\Stage;

class StageService
{
    public static function getDefaultStages(): array
    {
        return [
            [
                'name' => 'В работе',
                'color' => '#0050FF'
            ],
            [
                'name' => 'Выполнено',
                'color' => '#28FC2A'
            ],
            [
                'name' => 'Отменено',
                'color' => '#FE3F6D'
            ],

        ];
    }
}
