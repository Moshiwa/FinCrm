<?php

namespace App\Enums;

enum PermissionsEnum: string
{
    case deals = 'Сделки';
    case users = 'Пользователи';
    case clients = 'Клиенты';
    case admin = 'Администрирование';
    case permission = 'Управление правами пользователей';
    case fields = 'Поля';
}
