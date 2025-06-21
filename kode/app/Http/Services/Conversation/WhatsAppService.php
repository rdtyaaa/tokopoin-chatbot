<?php

namespace App\Http\Services\Conversation;

use App\Models\SellerShopSetting;
use App\Models\ChatbotSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = 'https://api.fonnte.com/send';
        $this->apiKey = config('whatsapp.fonnte_token');
    }

    /**
     * Send WhatsApp notification for new message
     */
    public function notifyNewMessage($sellerId, $customerName, $message): bool
    {
        $chatbotSetting = ChatbotSetting::where('seller_id', $sellerId)->first();

        if (!$chatbotSetting || !$chatbotSetting->whatsapp_notify_new_message) {
            return false;
        }

        $phoneNumber = $this->getSellerPhoneNumber($sellerId);
        if (!$phoneNumber) {
            Log::warning('WhatsApp notification failed - no phone number', [
                'seller_id' => $sellerId,
                'type' => 'new_message',
            ]);
            return false;
        }

        $messageText = "📩 *Pesan Baru dari Customer*\n\n";
        $messageText .= "👤 Customer: {$customerName}\n";
        $messageText .= '💬 Pesan: ' . substr($message, 0, 100) . (strlen($message) > 100 ? '...' : '') . "\n\n";
        $messageText .= 'Silakan cek aplikasi untuk membalas pesan customer.';

        return $this->sendMessage($phoneNumber, $messageText, 'new_message', $sellerId);
    }

    /**
     * Send WhatsApp notification when chatbot replies
     */
    public function notifyChatbotReply($sellerId, $customerName, $chatbotResponse): bool
    {
        $chatbotSetting = ChatbotSetting::where('seller_id', $sellerId)->first();

        if (!$chatbotSetting || !$chatbotSetting->whatsapp_notify_chatbot_reply) {
            return false;
        }

        $phoneNumber = $this->getSellerPhoneNumber($sellerId);
        if (!$phoneNumber) {
            return false;
        }

        $messageText = "🤖 *Chatbot telah membalas customer*\n\n";
        $messageText .= "👤 Customer: {$customerName}\n";
        $messageText .= '🤖 Balasan Chatbot: ' . substr($chatbotResponse, 0, 150) . (strlen($chatbotResponse) > 150 ? '...' : '') . "\n\n";
        $messageText .= 'Anda dapat mengambil alih percakapan kapan saja.';

        return $this->sendMessage($phoneNumber, $messageText, 'chatbot_reply', $sellerId);
    }

    /**
     * Send WhatsApp notification for unanswered messages
     */
    public function notifyNoReply($sellerId, $customerName, $waitingMinutes = 0, $originalMessage = null): bool
    {
        $chatbotSetting = ChatbotSetting::where('seller_id', $sellerId)->first();

        if (!$chatbotSetting || !$chatbotSetting->whatsapp_notify_no_reply) {
            return false;
        }

        $phoneNumber = $this->getSellerPhoneNumber($sellerId);
        if (!$phoneNumber) {
            return false;
        }

        if ($waitingMinutes > 0) {
            // Notifikasi untuk pesan yang sudah lama tidak dibalas
            $messageText = "⏰ *Pesan customer belum dibalas*\n\n";
            $messageText .= "👤 Customer: {$customerName}\n";
            $messageText .= "⌛ Sudah menunggu: {$waitingMinutes} menit\n\n";
            $messageText .= 'Chatbot akan segera membalas jika tidak ada respon dari Anda.';
        } else {
            // Notifikasi untuk chatbot failure
            $messageText = "❌ *Chatbot gagal membalas customer*\n\n";
            $messageText .= "👤 Customer: {$customerName}\n";

            if ($originalMessage) {
                $messageText .= '💬 Pesan: ' . substr($originalMessage, 0, 100) . (strlen($originalMessage) > 100 ? '...' : '') . "\n\n";
            }

            $messageText .= 'Silakan segera membalas customer secara manual.';
        }

        return $this->sendMessage($phoneNumber, $messageText, 'no_reply', $sellerId);
    }

    /**
     * Get seller phone number from shop settings
     */
    private function getSellerPhoneNumber($sellerId): ?string
    {
        $sellerShop = SellerShopSetting::where('seller_id', $sellerId)->first();

        if (!$sellerShop || !$sellerShop->whatsapp_number) {
            return null;
        }

        // Clean and format phone number
        $phone = preg_replace('/[^0-9]/', '', $sellerShop->phone);

        // Convert to international format
        if (str_starts_with($phone, '08')) {
            $phone = '628' . substr($phone, 2);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Send WhatsApp message via API
     */
    private function sendMessage($phoneNumber, $message, $type, $sellerId): bool
    {
        try {
            $payload = [
                'target' => $phoneNumber,
                'message' => $message,
                'countryCode' => '62'
            ];

            Log::info('Sending WhatsApp notification', [
                'seller_id' => $sellerId,
                'phone' => $phoneNumber,
                'type' => $type,
                'message_preview' => substr($message, 0, 50),
            ]);

            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded'
            ])->timeout(30)->asForm()->post($this->apiUrl, $payload);

            if ($response->successful()) {
                Log::info('WhatsApp notification sent successfully', [
                    'seller_id' => $sellerId,
                    'type' => $type,
                    'response' => $response->json(),
                ]);
                return true;
            }

            Log::error('WhatsApp API error', [
                'seller_id' => $sellerId,
                'type' => $type,
                'status' => $response->status(),
                'error' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp notification failed', [
                'seller_id' => $sellerId,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Validate if phone number is properly configured
     */
    public function isPhoneNumberValid($sellerId): bool
    {
        $phoneNumber = $this->getSellerPhoneNumber($sellerId);
        return !empty($phoneNumber) && strlen($phoneNumber) >= 10;
    }

    /**
     * Get formatted phone number for display
     */
    public function getFormattedPhoneNumber($sellerId): ?string
    {
        $sellerShop = SellerShopSetting::where('seller_id', $sellerId)->first();
        return $sellerShop?->phone;
    }

    /**
     * Send WhatsApp message to TokoPoin customer (from chatbot)
     */
    public function sendChatbotReply($phoneNumber, $message): bool
    {
        try {
            $payload = [
                'target' => $phoneNumber,
                'message' => $message,
                'countryCode' => '62'
            ];

            Log::info('Sending chatbot reply via Fonnte', [
                'phone' => $phoneNumber,
                'message_preview' => substr($message, 0, 50)
            ]);

            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded'
            ])->timeout(30)->asForm()->post($this->apiUrl, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('Chatbot reply sent successfully via Fonnte', [
                    'phone' => $phoneNumber,
                    'response' => $responseData
                ]);
                return true;
            }

            Log::error('Fonnte API error for chatbot reply', [
                'phone' => $phoneNumber,
                'status' => $response->status(),
                'error' => $response->body()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Chatbot reply failed via Fonnte', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
