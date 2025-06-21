<?php

namespace App\Http\Services\Conversation;

use App\Models\Seller;
use App\Models\Product;
use App\Events\NewMessageSent;
use App\Models\ChatbotSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use App\Models\CustomerSellerConversation;
use App\Http\Services\Conversation\WhatsAppService;

class ChatbotService
{
    protected $apiUrl;

    public function __construct()
    {
        // Ganti dengan URL API yang sesungguhnya nanti
        $this->apiUrl = config('chatbot.api_url', 'https://dummy-ai-api.example.com/chat');
    }

    /**
     * Cek apakah seller online
     */
    public function isSellerOnline($sellerId): bool
    {
        try {
            $globalUserId = "seller-{$sellerId}";

            // Opsi 1: Menggunakan sismember (lebih efisien untuk check membership)
            $isOnline = Redis::sismember('online_users', $globalUserId);

            // Opsi 2: Jika ingin tetap menggunakan smembers
            // $onlineUsers = Redis::smembers('online_users');
            // $isOnline = in_array($globalUserId, $onlineUsers);

            // Get count untuk logging
            $onlineUsersCount = Redis::scard('online_users');

            Log::info('Seller online check', [
                'seller_id' => $sellerId,
                'global_user_id' => $globalUserId,
                'is_online' => (bool) $isOnline, // Cast ke boolean untuk konsistensi
                'online_users_count' => $onlineUsersCount,
            ]);

            return (bool) $isOnline;
        } catch (\Exception $e) {
            Log::error('Error checking seller online status: ' . $e->getMessage(), [
                'seller_id' => $sellerId,
                'error_type' => get_class($e),
            ]);

            return false;
        }
    }

    /**
     * Cek setting chatbot seller
     */
    public function getChatbotSetting($sellerId): ?ChatbotSetting
    {
        return ChatbotSetting::where('seller_id', $sellerId)->first();
    }

    /**
     * Updated: Cek apakah harus trigger chatbot dengan sistem trigger baru
     */
    public function shouldTriggerChatbot($sellerId, $customerId): bool
    {
        try {
            $setting = $this->getChatbotSetting($sellerId);

            if (!$setting || $setting->status !== 'active') {
                Log::info('Chatbot not triggered - setting inactive', [
                    'seller_id' => $sellerId,
                    'setting_status' => $setting->status ?? 'not_found',
                ]);
                return false;
            }

            $shouldTrigger = false;
            $reasons = [];

            // Check trigger when seller offline
            if ($setting->trigger_when_offline) {
                $isSellerOnline = $this->isSellerOnline($sellerId);
                if (!$isSellerOnline) {
                    $shouldTrigger = true;
                    $reasons[] = 'seller_offline';
                }

                Log::info('Chatbot offline trigger check', [
                    'seller_id' => $sellerId,
                    'seller_online' => $isSellerOnline,
                    'trigger_enabled' => true,
                    'should_trigger' => !$isSellerOnline,
                ]);
            }

            // Check trigger when no reply (works independently from offline trigger)
            if ($setting->trigger_when_no_reply && $setting->delay_minutes) {
                $lastSellerMessage = CustomerSellerConversation::where('seller_id', $sellerId)->where('customer_id', $customerId)->where('sender_role', 'seller')->latest()->first();

                if (!$lastSellerMessage) {
                    $shouldTrigger = true;
                    $reasons[] = 'no_previous_seller_message';

                    Log::info('Chatbot triggered - no previous seller message', [
                        'seller_id' => $sellerId,
                        'customer_id' => $customerId,
                    ]);
                } else {
                    $delayMinutes = $setting->delay_minutes;
                    $timeDiff = now()->diffInMinutes($lastSellerMessage->created_at);

                    if ($timeDiff >= $delayMinutes) {
                        $shouldTrigger = true;
                        $reasons[] = 'delay_exceeded';
                    }

                    Log::info('Chatbot no-reply trigger check', [
                        'seller_id' => $sellerId,
                        'customer_id' => $customerId,
                        'delay_minutes' => $delayMinutes,
                        'time_diff' => $timeDiff,
                        'should_trigger' => $timeDiff >= $delayMinutes,
                    ]);
                }
            }

            Log::info('Chatbot trigger decision', [
                'seller_id' => $sellerId,
                'customer_id' => $customerId,
                'should_trigger' => $shouldTrigger,
                'reasons' => $reasons,
                'triggers_enabled' => [
                    'offline' => $setting->trigger_when_offline,
                    'no_reply' => $setting->trigger_when_no_reply,
                ],
            ]);

            return $shouldTrigger;
        } catch (\Exception $e) {
            Log::error('Error checking chatbot trigger condition: ' . $e->getMessage(), [
                'seller_id' => $sellerId,
                'customer_id' => $customerId,
            ]);
            return false;
        }
    }

    /**
     * FIXED: Get product ID from conversation or URL
     * Now handles null seller by searching all customer conversations
     * Returns array with product and updated seller_id
     */
    private function getProductFromConversation($sellerId, $customerId, $productId = null): array
    {
        // Jika ada productId langsung, gunakan itu
        if ($productId) {
            // Load product dengan relasi stock untuk mendapatkan stok
            $product = Product::with(['stock', 'category', 'brand'])->find($productId);
            if ($product) {
                // Set seller_id dari product jika seller_id null
                $finalSellerId = $sellerId ?? $product->seller_id;
                return [
                    'product' => $product,
                    'seller_id' => $finalSellerId,
                ];
            }
            return ['product' => null, 'seller_id' => $sellerId];
        }

        // Build query untuk mencari conversation dengan file attachment
        $query = CustomerSellerConversation::where('customer_id', $customerId)->whereNotNull('files');

        // Jika sellerId ada, filter berdasarkan seller
        // Jika sellerId null, cari dari semua conversation customer tersebut
        if ($sellerId !== null) {
            $query->where('seller_id', $sellerId);
            Log::info('Searching product from specific seller conversation', [
                'seller_id' => $sellerId,
                'customer_id' => $customerId,
            ]);
        } else {
            Log::info('Searching product from all customer conversations (seller is null)', [
                'customer_id' => $customerId,
            ]);
        }

        $conversationWithProduct = $query->latest()->first();

        if ($conversationWithProduct && $conversationWithProduct->files) {
            try {
                // Parse JSON dari files
                $files = is_array($conversationWithProduct->files) ? $conversationWithProduct->files : json_decode($conversationWithProduct->files, true);

                if (is_array($files)) {
                    foreach ($files as $file) {
                        $fileData = is_string($file) ? json_decode($file, true) : $file;

                        if (isset($fileData['type']) && $fileData['type'] === 'product' && isset($fileData['product_id'])) {
                            $product = Product::with(['stock', 'category', 'brand'])->find($fileData['product_id']);
                            if ($product) {
                                // Set seller_id dari product jika seller_id null
                                $finalSellerId = $sellerId ?? $product->seller_id;

                                Log::info('Product found from conversation history', [
                                    'product_id' => $product->id,
                                    'product_name' => $product->name,
                                    'original_seller_id' => $sellerId ?? 'null',
                                    'final_seller_id' => $finalSellerId,
                                    'customer_id' => $customerId,
                                    'conversation_seller_id' => $conversationWithProduct->seller_id,
                                    'product_seller_id' => $product->seller_id,
                                ]);

                                return [
                                    'product' => $product,
                                    'seller_id' => $finalSellerId,
                                ];
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error parsing files from conversation: ' . $e->getMessage(), [
                    'seller_id' => $sellerId ?? 'null',
                    'customer_id' => $customerId,
                    'files_data' => $conversationWithProduct->files,
                ]);
            }
        }

        Log::info('No product found in conversation history', [
            'seller_id' => $sellerId ?? 'null',
            'customer_id' => $customerId,
        ]);

        return ['product' => null, 'seller_id' => $sellerId];
    }

    /**
     * Ambil riwayat obrolan untuk context
     */
    private function getConversationHistory($sellerId, $customerId, $limit = 10): array
    {
        $conversations = CustomerSellerConversation::where('seller_id', $sellerId)
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse() // Balik urutan agar chronological
            ->values();

        $history = [];
        foreach ($conversations as $conv) {
            $role = $conv->sender_role;

            // Normalisasi role untuk AI
            if ($role === 'customer') {
                $role = 'Customer';
            } elseif ($role === 'seller') {
                $role = 'Seller';
            } elseif ($role === 'chatbot') {
                $role = 'Customer Service';
            }

            $history[] = [
                'role' => $role,
                'message' => $conv->message,
                'timestamp' => $conv->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $history;
    }

    /**
     * Kirim pesan ke AI Engine
     */
    public function sendToAI($message, $sellerId, $customerId, $productId = null): ?string
    {
        // Jika environment testing dengan Ollama
        if (config('chatbot.use_ollama', false)) {
            $response = $this->sendToOllama($message, $sellerId, $customerId, $productId);

            // Jika Ollama gagal, return fallback message
            if (!$response) {
                Log::warning('Ollama failed, returning fallback message', [
                    'seller_id' => $sellerId,
                    'customer_id' => $customerId,
                ]);

                return 'Maaf, sistem sedang sibuk. Silakan tunggu sebentar dan coba lagi, atau hubungi admin jika masalah berlanjut.';
            }

            return $response;
        }

        try {
            $seller = Seller::with('sellerShop')->find($sellerId);
            $product = $this->getProductFromConversation($sellerId, $customerId, $productId);

            // TAMBAHAN: Ambil riwayat untuk external API juga
            $conversationHistory = $this->getConversationHistory($sellerId, $customerId, 5);

            $payload = [
                'message' => $message,
                'seller' => [
                    'id' => $seller->id,
                    'shop_name' => $seller->sellerShop->shop_name ?? '',
                    'description' => $seller->sellerShop->details ?? '',
                ],
                'product' => $product
                    ? [
                        'id' => $product->id,
                        'name' => $product->name,
                        'description' => $product->description,
                        'price' => $product->selling_price,
                    ]
                    : null,
                'conversation_history' => $conversationHistory, // TAMBAHAN
                'context' => 'customer_inquiry',
            ];

            $response = Http::timeout(30)->post($this->apiUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                return $data['response'] ?? null;
            }

            Log::error('AI API Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Error sending message to AI: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * TESTING METHOD - Kirim pesan ke Ollama (FIXED FOR DEEPSEEK-R1)
     */
    private function sendToOllama($message, $sellerId, $customerId, $productId = null): ?string
    {
        set_time_limit(350);
        Log::info('sendToOllama - Method Entry', [
            'seller_id_received' => $sellerId,
            'seller_id_type' => gettype($sellerId),
            'customer_id' => $customerId,
            'product_id' => $productId,
            'message' => $message,
        ]);
        try {
            $seller = Seller::with('sellerShop')->find($sellerId);

            Log::info('sendToOllama - After Seller Query', [
                'seller_id_queried' => $sellerId,
                'seller_found' => $seller ? 'yes' : 'no',
                'seller_actual_id' => $seller ? $seller->id : null,
            ]);

            $productResult = $this->getProductFromConversation($sellerId, $customerId, $productId);
            $product = $productResult['product'];
            $sellerId = $productResult['seller_id'];

            if ($sellerId && !$seller) {
                $seller = Seller::with('sellerShop')->find($sellerId);
                Log::info('sendToOllama - Seller updated from product', [
                    'new_seller_id' => $sellerId,
                    'seller_found' => $seller ? 'yes' : 'no',
                ]);
            }

            $conversationHistory = $this->getConversationHistory($sellerId, $customerId, 8);

            Log::info('sendToOllama - Conversation History Retrieved', [
                'seller_id' => $seller,
                'customer_id' => $customerId,
                'history_count' => count($conversationHistory),
            ]);

            // Gunakan model yang lebih simple untuk testing
            $model = config('chatbot.ollama_model', 'deepseek-r1:8b');

            // Build prompt yang lebih sederhana untuk DeepSeek-R1
            $prompt = $this->buildDetailedPrompt($message, $seller, $product);

            $payload = [
                'model' => $model,
                'prompt' => $prompt,
                'stream' => false,
                'options' => [
                    'temperature' => 0.5,
                    'top_p' => 0.8,
                    'repeat_penalty' => 1.1,
                    'top_k' => 40,
                ],
            ];

            Log::info('Ollama Request with Context', [
                'seller_id' => $sellerId,
                'customer_id' => $customerId,
                'product_id' => $product?->id,
                'customer_message' => $message,
                'model' => $payload['model'],
                'history_entries' => count($conversationHistory),
                'prompt' => $prompt,
            ]);

            $ollamaUrl = config('chatbot.ollama_url', 'http://localhost:11434') . '/api/generate';

            $startTime = microtime(true);

            $response = Http::withOptions([
                'timeout' => 300,
                'connect_timeout' => 10,
                'read_timeout' => 300,
            ])->post($ollamaUrl, $payload);

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            if ($response->successful()) {
                $data = $response->json();
                $rawResponse = $data['response'] ?? null;

                Log::info('Ollama Raw Response with Context', [
                    'seller_id' => $sellerId,
                    'customer_id' => $customerId,
                    'response_time_ms' => $responseTime,
                    'raw_response' => $rawResponse,
                    'response_length' => $rawResponse ? mb_strlen($rawResponse) : 0,
                    'model_used' => $model,
                ]);

                if ($rawResponse && trim($rawResponse) !== '') {
                    // Bersihkan response dengan method yang lebih aggressive
                    $cleanResponse = $this->cleanResponseAdvanced($rawResponse);

                    Log::info('Ollama Cleaned Response with Context', [
                        'seller_id' => $sellerId,
                        'customer_id' => $customerId,
                        'cleaned_response' => $cleanResponse,
                        'cleaned_length' => mb_strlen($cleanResponse),
                    ]);

                    return $cleanResponse;
                } else {
                    Log::warning('Ollama returned empty response', [
                        'seller_id' => $sellerId,
                        'customer_id' => $customerId,
                        'full_api_response' => $data,
                    ]);
                }
            }

            Log::error('Ollama API Error', [
                'seller_id' => $sellerId,
                'customer_id' => $customerId,
                'response_time_ms' => $responseTime,
                'status_code' => $response->status(),
                'error_body' => $response->body(),
                'payload' => $payload,
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error sending message to Ollama', [
                'seller_id' => $sellerId,
                'customer_id' => $customerId,
                'product_id' => $productId,
                'customer_message' => $message,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Build prompt yang lebih detailed dengan info produk lengkap
     */
    private function buildDetailedPrompt($message, $seller, $product = null): string
    {
        $shopName = $seller->name ?? 'Seluruh Toko di e-commerce TokoPoin';
        $shopDetails = $seller->sellerShop->short_details ?? '';

        $prompt = "Anda adalah customer service dari {$shopName}. ";
        if ($shopDetails) {
            $prompt .= "Toko kami: {$shopDetails}. ";
        }
        $prompt .= 'Jawab pertanyaan customer dengan ramah, dan informatif dalam bahasa Indonesia. ';
        $prompt .= 'PENTING! Jika informasi terkait produk termasuk seller kurang jelas, soalnya ada kamu menghandel banyak product dari banyak seller. silahkan bertanya terlebih dahulu mana yang dimaksud dengan mengirimkan linknya. ';
        $prompt .= "Berikan jawaban yang lengkap tapi tetap ringkas tanpa menggunakan bold (**) ataupun italic. Buat agar customer tertarik untuk membeli.\n\n";

        if ($product) {
            $originalPrice = $product->price ?? 0;
            $sellingPrice = $product->selling_price ?? $originalPrice;
            $formattedPrice = 'Rp ' . number_format($sellingPrice, 0, ',', '.');

            $stock = $product->maximum_purchase_qty ?? 0;
            if ($product->stock && $product->stock->count() > 0) {
                $totalStock = $product->stock->sum('qty');
                $stock = $totalStock > 0 ? $totalStock : $stock;
            }

            $prompt .= "=== INFORMASI PRODUK ===\n";
            $prompt .= "Nama: {$product->name}\n";
            $prompt .= "Harga: {$formattedPrice}\n";

            $prompt .= "Stok: {$stock} unit\n";

            if ($product->description) {
                // Bersihkan HTML dari deskripsi dan ambil 200 karakter pertama
                $cleanDesc = strip_tags($product->description);
                $cleanDesc = preg_replace('/\s+/', ' ', $cleanDesc);
                $cleanDesc = trim($cleanDesc);

                if (strlen($cleanDesc) > 500) {
                    $cleanDesc = substr($cleanDesc, 0, 500) . '...';
                }

                $prompt .= "Deskripsi: {$cleanDesc}\n";
            }

            $prompt .= "=== END PRODUK ===\n\n";
        }

        if (!empty($conversationHistory)) {
            $prompt .= "=== RIWAYAT OBROLAN SEBELUMNYA ===\n";

            foreach ($conversationHistory as $entry) {
                $role = $entry['role'];
                $msg = $entry['message'];

                // Batasi panjang pesan untuk menghemat token
                if (mb_strlen($msg) > 150) {
                    $msg = mb_substr($msg, 0, 150) . '...';
                }

                $prompt .= "{$role}: {$msg}\n";
            }

            $prompt .= "=== END RIWAYAT ===\n\n";
            $prompt .= "Berdasarkan riwayat obrolan di atas, berikan jawaban yang sesuai dengan konteks percakapan sebelumnya.\n\n";
        }

        $prompt .= "Pertanyaan Customer: {$message}\n\n";
        $prompt .= "Jawaban Customer Service {$shopName}:";

        return $prompt;
    }

    /**
     * Bersihkan response dengan method yang lebih advanced
     */
    private function cleanResponseAdvanced($response): string
    {
        $cleaned = trim($response);

        // Hapus semua pattern thinking/reasoning yang mungkin ada
        $thinkingPatterns = ['/<think>.*?<\/think>/si', '/<thinking>.*?<\/thinking>/si', '/\[thinking\].*?\[\/thinking\]/si', '/\*thinking\*.*?\*\/thinking\*/si', '/\*\*thinking\*\*.*?\*\*\/thinking\*\*/si', '/<!-- thinking -->.*?<!-- \/thinking -->/si', '/\{thinking\}.*?\{\/thinking\}/si', '/\(thinking\).*?\(\/thinking\)/si'];

        foreach ($thinkingPatterns as $pattern) {
            $cleaned = preg_replace($pattern, '', $cleaned);
        }

        // Hapus prefiks yang tidak diinginkan
        $prefixes = ['Customer Service:', 'CS:', 'Admin:', 'Jawaban:', 'Reply:', 'Response:'];

        foreach ($prefixes as $prefix) {
            if (stripos($cleaned, $prefix) === 0) {
                $cleaned = trim(substr($cleaned, strlen($prefix)));
            }
        }

        // Hapus whitespace berlebih dan bersihkan
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        $cleaned = trim($cleaned);

        // Hapus karakter yang tidak diinginkan di awal
        $cleaned = ltrim($cleaned, ':-*');
        $cleaned = trim($cleaned);

        // Jika response kosong atau terlalu pendek, gunakan fallback
        if (empty($cleaned) || mb_strlen($cleaned) < 5) {
            $cleaned = 'Halo! Terima kasih sudah menghubungi kami 😊 Ada yang bisa saya bantu?';
        }

        // Pastikan ada tanda baca di akhir
        if (!preg_match('/[.!?]$/', $cleaned)) {
            $cleaned .= '.';
        }

        return $cleaned;
    }
    /**
     * Simpan response chatbot ke database
     */
    public function saveChatbotResponse($customerId, $sellerId, $response, $responseDelay = null): void
    {
        try {
            $setting = $this->getChatbotSetting($sellerId);

            // Delay response sesuai setting
            $delay = $responseDelay ?? ($setting->response_delay ?? 0);
            Log::info('Chatbot preparing response', [
                'customer_id' => $customerId,
                'seller_id' => $sellerId,
                'response_delay' => $delay,
                'response_preview' => substr($response, 0, 100),
            ]);

            // Simulasi delay response (dalam detik)
            if ($delay > 0) {
                sleep($delay);
            }

            // Buat dan simpan pesan
            $message = new CustomerSellerConversation();
            $message->customer_id = $customerId;
            $message->seller_id = $sellerId;
            $message->sender_role = 'chatbot';
            $message->message = $response;
            $message->is_seen = 0;
            $message->save();

            Log::info('Chatbot message saved', [
                'message_id' => $message->id,
                'customer_id' => $customerId,
                'seller_id' => $sellerId,
                'sender_role' => $message->sender_role,
            ]);

            // Load relasi yang diperlukan untuk event
            $message->load(['customer', 'seller', 'seller.sellerShop']);

            // Emit event untuk real-time update
            event(new NewMessageSent($message, $customerId, $sellerId));

            Log::info('Chatbot NewMessageSent event dispatched', [
                'message_id' => $message->id,
                'customer_id' => $customerId,
                'seller_id' => $sellerId,
                'event_class' => NewMessageSent::class,
            ]);

            // ** PERBAIKAN: Tambahkan WhatsApp notification untuk chatbot reply **
            try {
                // Instantiate WhatsAppService
                $whatsappService = app(WhatsAppService::class);

                // Get customer name for notification
                $customerName = $message->customer->name ?? 'Customer';

                // Send WhatsApp notification to seller about chatbot reply
                $notificationSent = $whatsappService->notifyChatbotReply($sellerId, $customerName, $response);

                if ($notificationSent) {
                    Log::info('WhatsApp notification sent for chatbot reply', [
                        'message_id' => $message->id,
                        'customer_id' => $customerId,
                        'seller_id' => $sellerId,
                        'customer_name' => $customerName,
                    ]);
                } else {
                    Log::warning('WhatsApp notification failed for chatbot reply', [
                        'message_id' => $message->id,
                        'customer_id' => $customerId,
                        'seller_id' => $sellerId,
                        'customer_name' => $customerName,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('WhatsApp notification error in chatbot response', [
                    'message_id' => $message->id,
                    'customer_id' => $customerId,
                    'seller_id' => $sellerId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                // Don't throw exception here to avoid breaking the main flow
            }
        } catch (\Exception $e) {
            Log::error('Failed to save chatbot response', [
                'customer_id' => $customerId,
                'seller_id' => $sellerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw exception to let calling code handle it
            throw $e;
        }
    }

    public function handleChatbotFailure($customerId, $sellerId, $originalMessage): void
    {
        try {
            // Instantiate WhatsAppService
            $whatsappService = app(WhatsAppService::class);

            // Get customer info
            $customer = \App\Models\User::find($customerId);
            $customerName = $customer->name ?? 'Customer';

            // Send WhatsApp notification about chatbot failure
            $notificationSent = $whatsappService->notifyNoReply($sellerId, $customerName, 0, $originalMessage);

            if ($notificationSent) {
                Log::info('WhatsApp notification sent for chatbot failure', [
                    'customer_id' => $customerId,
                    'seller_id' => $sellerId,
                    'customer_name' => $customerName,
                    'original_message' => substr($originalMessage, 0, 100),
                ]);
            } else {
                Log::warning('WhatsApp notification failed for chatbot failure', [
                    'customer_id' => $customerId,
                    'seller_id' => $sellerId,
                    'customer_name' => $customerName,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error handling chatbot failure notification', [
                'customer_id' => $customerId,
                'seller_id' => $sellerId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
