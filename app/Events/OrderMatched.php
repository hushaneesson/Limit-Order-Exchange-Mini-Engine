<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderMatched implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $buyerId;
    public $buyerData;

    public $sellerId;
    public $sellerData;

    /**
     * Create a new event instance.
     */
    public function __construct(int $buyerId, int $sellerId, array $buyerData, array $sellerData)
    {
        $this->buyerId = $buyerId;
        $this->buyerData = $buyerData;

        $this->sellerId = $sellerId;
        $this->sellerData = $sellerData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->buyerId),
            new PrivateChannel('user.' . $this->sellerId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.matched';
    }
}
