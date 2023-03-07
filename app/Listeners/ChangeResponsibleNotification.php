<?php

namespace App\Listeners;

use App\Enums\CommentTypeEnum;
use App\Events\ChangeResponsible;
use App\Models\DealComment;

class ChangeResponsibleNotification
{
    public function __construct()
    {
        //
    }

    public function handle(ChangeResponsible $event)
    {
        $message = 'Смена ответственного с '
            . "<i><b>" . $event->old_stage->name . "</b></i>"
            . ' на '
            . "<i><b>" . $event->deal->responsible->name . "</b></i>";

        $event->deal->comments()->create([
            'type' => CommentTypeEnum::ACTION->value,
            'content' => $message,
            'author_id' => backpack_user()?->id ?? null
        ]);
    }
}
