<?php

namespace App\Listeners;

use App\Enums\CommentTypeEnum;
use App\Events\CreateDeal;

class CreateDealNotification
{
    public function __construct()
    {
        //
    }

    public function handle(CreateDeal $event)
    {
        $event->deal->comments()->create([
            'type' => CommentTypeEnum::action->name,
            'content' => 'Сделка создана',
            'author_id' => backpack_user()->id
        ]);
    }
}
