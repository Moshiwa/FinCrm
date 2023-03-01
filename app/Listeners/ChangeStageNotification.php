<?php

namespace App\Listeners;

use App\Events\ChangeStage;
use App\Models\DealComment;

class ChangeStageNotification
{
    public function __construct()
    {
        //
    }

    public function handle(ChangeStage $event)
    {
        $message = 'Смена стадии с '
            . "<i><b>" . $event->old_stage->name . "</b></i>"
            . ' на '
            . "<i><b>" . $event->deal->stage->name . "</b></i>";

        $event->deal->comments()->create([
            'type' => DealComment::ACTION,
            'content' => $message,
            'author_id' => backpack_user()?->id ?? null
        ]);
    }
}
