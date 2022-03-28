<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CheckOrder
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $ids = [];
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $entity_id, string $user_id, string $order_id, string $action)
    {
        $this->ids['entity_id'] = $entity_id;
        $this->ids['user_id'] = $user_id;
        $this->ids['order_id'] = $order_id;
        $this->ids['action'] = $action;
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
