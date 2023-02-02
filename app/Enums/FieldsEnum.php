<?php

namespace App\Enums;

enum FieldsEnum: string
{
    case string = 'Текст';
    case number = 'Номер';
    case select = 'Выборка';
    case checkbox = 'Чекбокс';
    case date = 'Дата и время';
}
