<?php

namespace App\Services;

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


}
