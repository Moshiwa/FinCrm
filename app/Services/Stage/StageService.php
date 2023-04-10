<?php

namespace App\Services\Stage;

use App\Models\DeadlineFormat;

class StageService
{
    public static function getDefaultStages(): array
    {
        $format = DeadlineFormat::query()->where('name', 'day')->first();
        return [
            [ 'name' => 'В работе', 'deadline' => 1, 'deadline_format_id' => $format->id, 'lft' => 0 ],
            [ 'name' => 'Выполнено', 'deadline' => 1, 'deadline_format_id' => $format->id, 'lft' => 0 ],
            [ 'name' => 'Отменено', 'deadline' => 1, 'deadline_format_id' => $format->id, 'lft' => 0 ],
        ];
    }
}
