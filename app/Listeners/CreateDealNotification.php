<?php

namespace App\Listeners;

use App\Events\CreateDeal;
use App\Models\DealComment;

class CreateDealNotification
{
    public function __construct()
    {
        //
    }

    public function handle(CreateDeal $event)
    {
        if (backpack_user()?->id) {
            $event->deal->comments()->create([
                'type' => DealComment::ACTION,
                'title' => 'Сделка создана',
                'author_id' => backpack_user()->id
            ]);
        } else {
            $event->deal->comments()->create([
                'type' => DealComment::ACTION,
                'title' => 'Сделка создана автоматически',
            ]);
        }
    }
}
