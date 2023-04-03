<?php

namespace App\Enums;

enum PermissionsEnum: string
{
    case deals = 'Сделки';
    case tasks = 'Задачи';
    case clients = 'Клиенты';
    case fields = 'Поля';
    case deal_buttons = 'Кнопки сделок';
    case task_buttons = 'Кнопки задач';
    case pipelines = 'Воронки';
    case task_stages = 'Статусы задач';
    case comments = 'Комментарии';
    case sms_center = 'СмсЦентр';
}
