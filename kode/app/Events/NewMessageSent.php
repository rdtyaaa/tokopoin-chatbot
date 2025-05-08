<?php

namespace App\Events;

use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use App\Models\CustomerSellerConversation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $sellerId;
    public $customerId;

    public function __construct(CustomerSellerConversation $message, $customerId, $sellerId)
    {
        $this->message = $message;
        $this->sellerId = (int) $sellerId;
        $this->customerId = (int) $customerId;

        Log::info('Event data', [
            'message' => $message,
            'sellerId' => $sellerId,
            'customerId' => $customerId,
        ]);
    }

    public function broadcastOn()
    {
        Log::info('Broadcast channel:', [
            'channel' => 'chat-channel.' . $this->sellerId . '.' . $this->customerId,
        ]);

        return new Channel('chat-channel.' . $this->sellerId . '.' . $this->customerId);
    }

    public function broadcastAs()
    {
        return 'new-message';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'seller_id' => $this->sellerId,
            'customer_id' => $this->customerId,
        ];
    }
}
