<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PostOrder
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public $pushNotifications;

    /**
     * Create a new event instance.
     *
     * @param Order $order
     * @param bool $statusUpdate
     */
    public function __construct(Order $order, $pushNotification = array())
    {
        $this->order = $order;
        $this->pushNotifications = $pushNotification;
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
