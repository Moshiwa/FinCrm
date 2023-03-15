<?php

namespace App\Enums;

use App\Services\Sender\SmsCenter\SmsCenterService;
use App\Traits\EnumUpdateTrait;

enum IntegrationEnum: string
{
    use EnumUpdateTrait;

    case SMS_CENTER = 'sms_center';

    public static function getEntity($value): ?string
    {
        return match ($value) {
            IntegrationEnum::SMS_CENTER->value => SmsCenterService::class,
            default => null
        };
    }

    public static function getTitle($value): ?string
    {
        return match ($value) {
            IntegrationEnum::SMS_CENTER->value => 'SmsCenter',
            default => null
        };
    }

}
