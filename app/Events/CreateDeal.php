<?php

namespace App\Events;

use App\Models\Deal;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreateDeal
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(Deal $deal)
    {
        $this->deal = $deal;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
