<?php

namespace App\Listeners;

use App\Enums\CommentTypeEnum;
use App\Events\CreateTask;

class CreateTaskNotification
{
    public function __construct()
    {
        //
    }

    public function handle(CreateTask $event)
    {
        if (backpack_user()?->id) {
            $event->task->comments()->create([
                'type' => CommentTypeEnum::ACTION->value,
                'title' => 'Задача создана',
                'author_id' => backpack_user()->id
            ]);
        } else {
            $event->task->comments()->create([
                'type' => CommentTypeEnum::ACTION->value,
                'title' => 'Задача создана автоматически',
            ]);
        }
    }
}
