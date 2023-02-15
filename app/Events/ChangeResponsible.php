<?php

namespace App\Events;

use App\Models\Deal;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChangeResponsible
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(Deal $deal, array $old_data)
    {
        $this->deal = $deal;
        $this->old_stage = $old_data['responsible_id'] ? User::query()->find($old_data['responsible_id']) : null;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
