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
            [ 'name' => 'В работе', 'deadline' => 1, 'lft' => 0 ],
            [ 'name' => 'Выполнено', 'deadline' => 1, 'lft' => 0 ],
            [ 'name' => 'Отменено', 'deadline' => 1, 'lft' => 0 ],
        ];
    }
}
