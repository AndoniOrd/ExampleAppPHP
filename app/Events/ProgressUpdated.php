<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProgressUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $campaignId;
    public $dispatched;
    public $total;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($campaignId, $dispatched, $total)
    {
        $this->campaignId = $campaignId;
        $this->dispatched = $dispatched;
        $this->total = $total;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('campaign.' . $this->campaignId);
    }
}
