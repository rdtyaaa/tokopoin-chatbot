<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
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

        // PERBAIKAN: Log untuk debugging
        Log::info('NewMessageSent event created', [
            'message_id' => $message->id ?? 'unknown',
            'sender_role' => $message->sender_role ?? 'unknown',
            'customer_id' => $customerId,
            'seller_id' => $sellerId,
            'message_preview' => substr($message->message ?? '', 0, 50)
        ]);
    }

    public function broadcastOn()
    {
        // PERBAIKAN: Broadcast ke channel seller untuk semua jenis pesan
        $channel = new Channel("chat-channel.seller.{$this->sellerId}");

        Log::info('Broadcasting to channel', [
            'channel' => "chat-channel.seller.{$this->sellerId}",
            'sender_role' => $this->message->sender_role ?? 'unknown',
            'message_id' => $this->message->id ?? 'unknown'
        ]);

        return $channel;
    }

    public function broadcastAs()
    {
        return 'new-message';
    }

    public function broadcastWith()
    {
        // PERBAIKAN: Pastikan data lengkap untuk semua jenis sender
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

        // PERBAIKAN: Tambahkan info seller untuk semua jenis pesan
        if ($this->message->seller) {
            $broadcastData['seller_name'] = $this->message->seller->name . ' ' . $this->message->seller->last_name;
            $broadcastData['shop_name'] = $this->message->seller->sellerShop->shop_name ?? '';
        }

        // PERBAIKAN: Tambahkan info customer jika pesan dari customer
        if ($this->message->customer) {
            $broadcastData['customer_name'] = $this->message->customer->name;
        }

        Log::info('Broadcasting message data', [
            'sender_role' => $broadcastData['message']['sender_role'],
            'message_id' => $broadcastData['message']['id'],
            'has_seller_info' => isset($broadcastData['seller_name']),
            'has_customer_info' => isset($broadcastData['customer_name'])
        ]);

        return $broadcastData;
    }
}
