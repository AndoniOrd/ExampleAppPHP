<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CampaignStarted implements ShouldBroadcast
{
    public int $campaignId;
    public int $total;

    public function __construct(int $campaignId, int $total)
    {
        $this->campaignId = $campaignId;
        $this->total      = $total;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('campaign-progress');  // canal público 💬 :contentReference[oaicite:0]{index=0}
    }
}
