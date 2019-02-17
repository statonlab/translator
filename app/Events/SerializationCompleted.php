<?php

namespace App\Events;

use App\Platform;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SerializationCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var \App\Platform */
    public $platform;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Platform $platform)
    {
        $this->platform = $platform;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
