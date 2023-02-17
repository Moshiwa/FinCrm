<?php

namespace App\Services;

use App\Enums\SettingKeysEnum;
use App\Models\Stage;

class SettingService
{
    public static function convertEnumToArray(array $enum):array
    {
        $result = [];
        foreach ($enum as $item) {
            $result[$item->name] = __('fields.type.' . $item->value);
        }

        return $result;
    }

    public static function getAllowedStages($stage)
    {
        $stages = [];
        foreach ($stage->settings as $setting) {
            if ($setting->key->value === SettingKeysEnum::change_stage->value) {
                $stages[] = $setting->pivot->value;
            }
        }

        return Stage::query()->whereNotIn('id', $stages)->get();
    }


}
