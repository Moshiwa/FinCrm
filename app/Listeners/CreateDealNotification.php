<?php

namespace App\Listeners;

use App\Enums\CommentTypeEnum;
use App\Events\CreateDeal;
use App\Models\Comment;

class CreateDealNotification
{
    public function __construct()
    {
        //
    }

    public function handle(CreateDeal $event)
    {
        if (backpack_user()?->id || request()->user()?->id) {
            $user_id = backpack_user()?->id ?? request()->user()?->id;
            $event->deal->comments()->create([
                'type' => CommentTypeEnum::ACTION->value,
                'title' => 'Сделка создана',
                'author_id' => $user_id
            ]);
        } else {
            $event->deal->comments()->create([
                'type' => CommentTypeEnum::ACTION->value,
                'title' => 'Сделка создана автоматически',
            ]);
        }
    }
}
