<?php

namespace App\Enums;

enum FieldsEnum: string
{
    case phone = 'phone';
    case email = 'email';
    case number = 'number';
    case select = 'select';
    case checkbox = 'checkbox';
    case date = 'date';
}
