<?php

namespace App\Enums;

enum FieldsEntitiesEnum: string
{
    case client = 'client';
    case deal = 'deal';
    case task = 'task';

    public static function findValue($value): ?FieldsEntitiesEnum
    {
        return match ($value) {
            FieldsEntitiesEnum::client->value => FieldsEntitiesEnum::client,
            FieldsEntitiesEnum::deal->value => FieldsEntitiesEnum::deal,
            FieldsEntitiesEnum::task->value => FieldsEntitiesEnum::task,
            default => null
        };
    }
}
