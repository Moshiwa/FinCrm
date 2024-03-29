<?php

namespace App\Listeners;

use App\Enums\CommentTypeEnum;
use App\Events\ChangePipeline;
use App\Models\Deal;
use App\Models\Comment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ChangePipelineNotification
{
    public function __construct()
    {
        //
    }

    public function handle(ChangePipeline $event)
    {
        $message = 'Смена воронки с '
            . "<i><b>" . $event->old_pipeline->name . "</b></i>"
            . ' на '
            . "<i><b>" . $event->deal->pipeline->name . "</b></i>"
            . "<pre>";

        Deal::withoutEvents(function () use ($event) {
            $event->deal->update([
                'stage_id' => $event->deal->pipeline->stages->first()->id
            ]);
        });

        $message .= 'Смена стадии с '
            . "<i><b>" . $event->old_stage->name . "</b></i>"
            . ' на '
            . "<i><b>" . $event->deal->stage->name . "</b></i>";

        $event->deal->comments()->create([
            'type' => CommentTypeEnum::ACTION->value,
            'content' => $message,
            'author_id' => backpack_user()?->id ?? null
        ]);
    }
}
