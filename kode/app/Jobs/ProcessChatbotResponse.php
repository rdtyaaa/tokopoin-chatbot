<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Http\Services\Conversation\ChatbotService;

class ProcessChatbotResponse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $customerId;
    protected $sellerId;
    protected $message;
    protected $productId;

    // PERBAIKAN: Tambahkan retry dan timeout
    public $tries = 3;
    public $timeout = 120;

    public function __construct($customerId, $sellerId, $message, $productId = null)
    {
        $this->customerId = $customerId;
        $this->sellerId = $sellerId;
        $this->message = $message;
        $this->productId = $productId;
    }

    public function handle(ChatbotService $chatbotService)
    {
        try {
            Log::info('Processing chatbot response job started', [
                'customer_id' => $this->customerId,
                'seller_id' => $this->sellerId,
                'product_id' => $this->productId,
                'message_preview' => substr($this->message, 0, 100)
            ]);

            // PERBAIKAN: Cek ulang apakah chatbot masih perlu di-trigger
            if (!$chatbotService->shouldTriggerChatbot($this->sellerId, $this->customerId)) {
                Log::info('Chatbot no longer needed, skipping', [
                    'customer_id' => $this->customerId,
                    'seller_id' => $this->sellerId
                ]);
                return;
            }

            // PERBAIKAN: Kirim customerId juga ke sendToAI
            $response = $chatbotService->sendToAI($this->message, $this->sellerId, $this->customerId, $this->productId);

            if ($response && !empty(trim($response))) {
                Log::info('AI response received, saving chatbot message', [
                    'customer_id' => $this->customerId,
                    'seller_id' => $this->sellerId,
                    'response_length' => strlen($response)
                ]);

                $chatbotService->saveChatbotResponse($this->customerId, $this->sellerId, $response);

                Log::info('Chatbot response processed successfully', [
                    'customer_id' => $this->customerId,
                    'seller_id' => $this->sellerId
                ]);
            } else {
                Log::warning('AI response empty, using fallback', [
                    'customer_id' => $this->customerId,
                    'seller_id' => $this->sellerId
                ]);

                $chatbotService->handleChatbotFailure(
                    $this->customerId,
                    $this->sellerId,
                    $this->message
                );

                // Fallback response jika AI tidak merespon
                $fallbackResponse = "Terima kasih atas pesan Anda 😊 Saat ini seller sedang tidak tersedia, namun pesan Anda akan segera dibalas. Untuk informasi lebih lanjut, silakan hubungi kami nanti.";
                $chatbotService->saveChatbotResponse($this->customerId, $this->sellerId, $fallbackResponse);
            }

        } catch (\Exception $e) {
            Log::error('Error processing chatbot response job', [
                'customer_id' => $this->customerId,
                'seller_id' => $this->sellerId,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            // PERBAIKAN: Jika gagal, coba kirim fallback response
            try {
                $fallbackResponse = "Maaf, saat ini sistem chatbot mengalami gangguan 🙏 Tim kami akan segera membalas pesan Anda.";
                $chatbotService->saveChatbotResponse($this->customerId, $this->sellerId, $fallbackResponse, 1);
                $chatbotService->handleChatbotFailure(
                    $this->customerId,
                    $this->sellerId,
                    $this->message
                );

                Log::info('Fallback chatbot response sent due to error', [
                    'customer_id' => $this->customerId,
                    'seller_id' => $this->sellerId
                ]);
            } catch (\Exception $fallbackError) {
                Log::error('Failed to send fallback chatbot response', [
                    'customer_id' => $this->customerId,
                    'seller_id' => $this->sellerId,
                    'fallback_error' => $fallbackError->getMessage()
                ]);

                // Re-throw original exception
                throw $e;
            }
        }
    }

    // PERBAIKAN: Handle job failure
    public function failed(\Throwable $exception)
    {
        Log::error('ProcessChatbotResponse job failed permanently', [
            'customer_id' => $this->customerId,
            'seller_id' => $this->sellerId,
            'error_message' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }
}
