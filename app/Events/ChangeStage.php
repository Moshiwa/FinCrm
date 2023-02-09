<?php

namespace App\Events;

use App\Models\Deal;
use App\Models\Stage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChangeStage
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(Deal $deal, array $old_data)
    {
        $this->deal = $deal;
        $this->old_stage = $old_data['stage_id'] ? Stage::query()->find($old_data['stage_id']) : null;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
