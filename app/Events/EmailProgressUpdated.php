<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class EmailProgressUpdated implements ShouldBroadcast
{
    use SerializesModels;

    public int $campaignId;
    public int $progress;

    public function __construct(int $campaignId, int $progress)
    {
        $this->campaignId = $campaignId;
        $this->progress   = $progress;
    }

    public function broadcastOn(): Channel
    {
        return new Channel("campaign.{$this->campaignId}");
    }

    public function broadcastAs(): string
    {
        return 'EmailProgressUpdated';
    }
}
