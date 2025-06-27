<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Http\Services\Conversation\ChatbotService;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class ProcessChatbotResponse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $customerId;
    protected $sellerId;
    protected $message;
    protected $productId;

    public $tries = 2;
    public $timeout = 360;
    public $maxExceptions = 3;

    public function __construct($customerId, $sellerId, $message, $productId = null)
    {
        $this->customerId = $customerId;
        $this->sellerId = $sellerId;
        $this->message = $message;
        $this->productId = $productId;

        $this->delay(now()->addSeconds(30));
    }

    public function middleware()
    {
        return [
            new WithoutOverlapping("chatbot-{$this->customerId}-{$this->sellerId}")
        ];
    }

    public function handle(ChatbotService $chatbotService)
    {
        $startTime = microtime(true);

        try {
            if (!$chatbotService->shouldTriggerChatbot($this->sellerId, $this->customerId)) {
                return;
            }

            $response = $this->sendToAIWithTimeout($chatbotService);

            if ($response && !empty(trim($response))) {
                $chatbotService->saveChatbotResponse($this->customerId, $this->sellerId, $response);
            } else {
                $this->handleEmptyResponse($chatbotService);
            }

        } catch (\Exception $e) {
            $this->handleJobException($e, $chatbotService, $startTime);
        }
    }

    private function sendToAIWithTimeout(ChatbotService $chatbotService)
    {
        $startTime = microtime(true);

        try {
            $response = $chatbotService->sendToAI(
                $this->message,
                $this->sellerId,
                $this->customerId,
                $this->productId
            );

            $executionTime = microtime(true) - $startTime;

            if (empty($response) && $executionTime > 90) {
                throw new \Exception('AI response timeout - no response received after ' . round($executionTime, 2) . 's');
            }

            return $response;

        } catch (\Exception $e) {
            $executionTime = microtime(true) - $startTime;

            Log::error('AI request failed', [
                'customer_id' => $this->customerId,
                'seller_id' => $this->sellerId,
                'execution_time' => round($executionTime, 2) . 's',
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    private function handleEmptyResponse(ChatbotService $chatbotService)
    {
        Log::warning('AI response empty, using fallback', [
            'customer_id' => $this->customerId,
            'seller_id' => $this->sellerId
        ]);

        $chatbotService->handleChatbotFailure(
            $this->customerId,
            $this->sellerId,
            $this->message
        );

        $fallbackResponse = "Terima kasih atas pesan Anda 😊 Saat ini seller sedang tidak tersedia, namun pesan Anda akan segera dibalas. Untuk informasi lebih lanjut, silakan hubungi kami nanti.";
        $chatbotService->saveChatbotResponse($this->customerId, $this->sellerId, $fallbackResponse);
    }

    private function handleJobException(\Exception $e, ChatbotService $chatbotService, $startTime)
    {
        $executionTime = round(microtime(true) - $startTime, 2);

        Log::error('Error processing chatbot response job', [
            'customer_id' => $this->customerId,
            'seller_id' => $this->sellerId,
            'error_message' => $e->getMessage(),
            'execution_time' => $executionTime . 's',
            'attempt' => $this->attempts(),
            'max_tries' => $this->tries
        ]);

        $isLongTimeout = str_contains(strtolower($e->getMessage()), 'timeout') && $executionTime > 300;

        if ($this->attempts() < $this->tries && !$isLongTimeout) {
            Log::info('Will retry chatbot job', [
                'customer_id' => $this->customerId,
                'seller_id' => $this->sellerId,
                'next_attempt' => $this->attempts() + 1,
                'skip_reason' => $isLongTimeout ? 'Long timeout detected' : null
            ]);

            $this->release(60);
            return;
        }

        try {
            $fallbackResponse = "Maaf, saat ini sistem chatbot mengalami gangguan 🙏 Tim kami akan segera membalas pesan Anda.";
            $chatbotService->saveChatbotResponse($this->customerId, $this->sellerId, $fallbackResponse, 1);
            $chatbotService->handleChatbotFailure(
                $this->customerId,
                $this->sellerId,
                $this->message
            );

            Log::info('Fallback chatbot response sent', [
                'customer_id' => $this->customerId,
                'seller_id' => $this->sellerId,
                'execution_time' => $executionTime . 's'
            ]);
        } catch (\Exception $fallbackError) {
            Log::error('Failed to send fallback chatbot response', [
                'customer_id' => $this->customerId,
                'seller_id' => $this->sellerId,
                'fallback_error' => $fallbackError->getMessage()
            ]);

            $this->fail($e);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('ProcessChatbotResponse job failed permanently', [
            'customer_id' => $this->customerId,
            'seller_id' => $this->sellerId,
            'error_message' => $exception->getMessage(),
            'attempts' => $this->attempts(),
            'job_class' => self::class
        ]);
    }

    public function getJobIdentifier()
    {
        return "chatbot-{$this->customerId}-{$this->sellerId}";
    }
}
