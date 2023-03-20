<?php

namespace App\Enums;

use App\Services\Sender\SmsCenter\SmsCenterEmailService;
use App\Services\Sender\SmsCenter\SmsCenterService;
use App\Traits\EnumUpdateTrait;

enum IntegrationEnum: string
{
    use EnumUpdateTrait;

    case SMS_CENTER = 'sms_center';
    case SMS_CENTER_EMAIL = 'sms_center_email';

    public static function getEntity($value): ?string
    {
        return match ($value) {
            IntegrationEnum::SMS_CENTER->value => SmsCenterService::class,
            IntegrationEnum::SMS_CENTER_EMAIL->value => SmsCenterEmailService::class,
            default => null
        };
    }

    public static function getTitle($value): ?string
    {
        return match ($value) {
            IntegrationEnum::SMS_CENTER->value,
            IntegrationEnum::SMS_CENTER_EMAIL->value => 'SmsCenter',
            default => null
        };
    }

}
