<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EmailProcessed implements ShouldBroadcast
{
    public int $campaignId;

    public function __construct(int $campaignId)
    {
        $this->campaignId = $campaignId;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('campaign-progress');  // mismo canal :contentReference[oaicite:1]{index=1}
    }
}
