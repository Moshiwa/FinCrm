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
            [ 'name' => 'В работе' ],
            [ 'name' => 'Выполнено' ],
            [ 'name' => 'Отменено' ],
        ];
    }
}
