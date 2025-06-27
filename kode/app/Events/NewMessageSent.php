<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $customerId;
    public $sellerId;

    public function __construct($message, $customerId, $sellerId)
    {
        $this->message = $message;
        $this->customerId = $customerId;
        $this->sellerId = $sellerId;

        Log::info('NewMessageSent event created', [
            'message_id' => $message->id,
            'customer_id' => $customerId,
            'seller_id' => $sellerId
        ]);
    }

    public function broadcastOn()
    {
        return new Channel("chat-channel.seller.{$this->sellerId}");
    }

    public function broadcastAs()
    {
        return 'new-message';
    }

    public function broadcastWith()
    {
        try {
            Log::info('Starting broadcast data preparation');

            $broadcastData = [
                'message' => [
                    'id' => $this->message->id,
                    'message' => $this->message->message,
                    'sender_role' => $this->message->sender_role,
                    'created_at' => $this->message->created_at,
                    'is_seen' => $this->message->is_seen,
                    'files' => $this->message->files ?? null,
                ],
                'customer_id' => $this->customerId,
                'seller_id' => $this->sellerId,
            ];

            if ($this->message->relationLoaded('seller')) {
                $seller = $this->message->seller;
                if ($seller) {
                    $broadcastData['seller_name'] = $seller->name . ' ' . $seller->last_name;

                    if ($seller->relationLoaded('sellerShop') && $seller->sellerShop) {
                        $broadcastData['shop_name'] = $seller->sellerShop->shop_name;
                    }
                }
            }

            if ($this->message->relationLoaded('customer') && $this->message->customer) {
                $broadcastData['customer_name'] = $this->message->customer->name;
            }

            Log::info('Broadcast data prepared successfully');
            return $broadcastData;

        } catch (\Exception $e) {
            Log::error('Error preparing broadcast data: ' . $e->getMessage(), [
                'message_id' => $this->message->id,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'message' => [
                    'id' => $this->message->id,
                    'message' => $this->message->message,
                    'sender_role' => $this->message->sender_role,
                    'created_at' => $this->message->created_at,
                ],
                'customer_id' => $this->customerId,
                'seller_id' => $this->sellerId,
            ];
        }
    }
}
