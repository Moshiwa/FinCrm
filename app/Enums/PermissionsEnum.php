<?php

namespace App\Enums;

enum PermissionsEnum: string
{
    case deals = 'Сделки';
    case clients = 'Клиенты';
    case tasks = 'Задачи';
    case deal_buttons = 'Кнопки сделок';

    case users = 'Пользователи';
    case admin = 'Администрирование';
    case permission = 'Управление правами пользователей';
    case fields = 'Поля';
}
