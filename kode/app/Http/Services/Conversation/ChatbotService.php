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
use App\Http\Services\Conversation\RAGService;
use App\Http\Services\Conversation\WhatsAppService;
use App\Http\Services\Conversation\IntentDetectionService;

class ChatbotService
{
    protected $apiUrl;
    protected $intentService;
    protected $ragService;

    public function __construct()
    {
        $this->apiUrl = config('chatbot.api_url', 'https://dummy-ai-api.example.com/chat');

        $this->intentService = new IntentDetectionService();
        $this->ragService = new RAGService();
    }

    /**
     * Cek apakah seller online
     */
    public function isSellerOnline($sellerId): bool
    {
        try {
            $globalUserId = "seller-{$sellerId}";

            $isOnline = Redis::sismember('online_users', $globalUserId);

            $onlineUsersCount = Redis::scard('online_users');

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
     * Cek apakah harus trigger chatbot dengan sistem trigger baru
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

            if ($setting->trigger_when_offline) {
                $isSellerOnline = $this->isSellerOnline($sellerId);
                if (!$isSellerOnline) {
                    $shouldTrigger = true;
                    $reasons[] = 'seller_offline';
                }
            }

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
     * Now handles null seller by searching all customer conversations
     * Returns array with product and updated seller_id
     */
    private function getProductFromConversation($sellerId, $customerId, $productId = null): array
    {
        if ($productId) {
            $product = Product::with(['stock', 'category', 'brand'])->find($productId);
            if ($product) {
                $finalSellerId = $sellerId ?? $product->seller_id;
                return [
                    'product' => $product,
                    'seller_id' => $finalSellerId,
                ];
            }
            return ['product' => null, 'seller_id' => $sellerId];
        }

        $query = CustomerSellerConversation::where('customer_id', $customerId)->whereNotNull('files');

        if ($sellerId !== null) {
            $query->where('seller_id', $sellerId);
        } else {
            Log::info('Searching product from all customer conversations (seller is null)', [
                'customer_id' => $customerId,
            ]);
        }

        $conversationWithProduct = $query->latest()->first();

        if ($conversationWithProduct && $conversationWithProduct->files) {
            try {
                $files = is_array($conversationWithProduct->files) ? $conversationWithProduct->files : json_decode($conversationWithProduct->files, true);

                if (is_array($files)) {
                    foreach ($files as $file) {
                        $fileData = is_string($file) ? json_decode($file, true) : $file;

                        if (isset($fileData['type']) && $fileData['type'] === 'product' && isset($fileData['product_id'])) {
                            $product = Product::with(['stock', 'category', 'brand'])->find($fileData['product_id']);
                            if ($product) {
                                $finalSellerId = $sellerId ?? $product->seller_id;

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

        return ['product' => null, 'seller_id' => $sellerId];
    }

    /**
     * Ambil riwayat obrolan untuk context
     */
    private function getConversationHistory($sellerId, $customerId, $limit = 10): array
    {
        $conversations = CustomerSellerConversation::where('seller_id', $sellerId)->where('customer_id', $customerId)->orderBy('created_at', 'desc')->limit($limit)->get()->reverse()->values();

        $history = [];
        foreach ($conversations as $conv) {
            $role = $conv->sender_role;

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
                'sender_role' => $conv->sender_role,
            ];
        }

        return $history;
    }

    /**
     * Kirim pesan ke AI Engine
     */
    public function sendToAI($message, $sellerId, $customerId, $productId = null): ?string
{
    $seller = Seller::with('sellerShop')->find($sellerId);
    if (!$seller) {
        Log::warning('Seller not found', [
            'seller_id' => $sellerId,
            'customer_id' => $customerId,
        ]);
        return 'Maaf, toko tidak ditemukan.';
    }

    $product = null;
    if ($productId) {
        $product = Product::find($productId);
    }

    $intentResult = $this->intentService->detectIntentWithProductContext($message, $sellerId, $productId);

    if (!$product && isset($intentResult['product_id'])) {
        $product = Product::find($intentResult['product_id']);
        Log::info('Using product from intent detection', [
            'product_id' => $product?->id,
            'product_name' => $product?->name,
        ]);
    }

    $ragContext = $this->ragService->retrieveRelevantInfo(
        $intentResult['intent'],
        $sellerId,
        $customerId,
        $product?->id ?? $productId,
        $message
    );

    if (config('chatbot.use_ollama', false)) {
        $response = $this->sendToOllama($message, $seller, $customerId, $product, $intentResult, $ragContext);

        if (!$response) {
            Log::warning('Ollama failed, returning fallback message', [
                'seller_id' => $sellerId,
                'customer_id' => $customerId,
            ]);

            return $this->getFallbackResponse($intentResult['intent'], $ragContext);
        }

        return $response;
    }

    try {
        if (!$product) {
            $productResult = $this->getProductFromConversation($sellerId, $customerId, $productId);
            $product = $productResult['product'];
        }

        $conversationHistory = $this->getConversationHistory($sellerId, $customerId, 5);

        $payload = [
            'message' => $message,
            'intent' => $intentResult,
            'context' => $ragContext,
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
                    'price' => $product->price,
                ]
                : null,
            'conversation_history' => $conversationHistory,
            'context_type' => 'customer_inquiry',
        ];

        $response = Http::timeout(30)->post($this->apiUrl, $payload);

        if ($response->successful()) {
            $data = $response->json();
            return $data['response'] ?? null;
        }

        Log::error('AI API Error: ' . $response->body());
        return $this->getFallbackResponse($intentResult['intent'], $ragContext);
    } catch (\Exception $e) {
        Log::error('Error sending message to AI: ' . $e->getMessage());
        return $this->getFallbackResponse($intentResult['intent'], $ragContext);
    }
}

    /**
     * Kirim pesan ke Ollama
     */
    private function sendToOllama($message, $seller, $customerId, $product = null, $intentResult = null, $ragContext = null): ?string
    {
        set_time_limit(0);

        $totalStartTime = microtime(true);
        $timingBreakdown = [];

        try {
            $prepStartTime = microtime(true);

            $sellerId = $seller->id;

            if (!$product) {
                if (isset($ragContext['product']['id'])) {
                    $foundProduct = Product::where('id', $ragContext['product']['id'])->where('seller_id', $sellerId)->first();

                    if ($foundProduct) {
                        $product = $foundProduct;
                        Log::info('sendToOllama - Using product found by RAG search', [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'seller_id' => $sellerId,
                        ]);
                    } else {
                        $productResult = $this->getProductFromConversation($sellerId, $customerId, null);
                        $product = $productResult['product'];

                        Log::info('sendToOllama - Using product from conversation', [
                            'product_id' => $product?->id,
                            'product_name' => $product?->name ?? 'null',
                            'seller_id' => $sellerId,
                        ]);
                    }
                } else {
                    $productResult = $this->getProductFromConversation($sellerId, $customerId, null);
                    $product = $productResult['product'];

                    Log::info('sendToOllama - No RAG product, using conversation product', [
                        'product_id' => $product?->id,
                        'product_name' => $product?->name ?? 'null',
                        'seller_id' => $sellerId,
                    ]);
                }
            }

            $conversationHistory = $this->getConversationHistory($sellerId, $customerId, 8);

            $contextPrompt = '';
            if ($ragContext && isset($ragContext['type'])) {
                $contextPrompt = $this->ragService->generateContextPrompt($ragContext, $intentResult['intent'] ?? 'unknown');

                Log::info('sendToOllama - RAG Context Generated', [
                    'seller_id' => $sellerId,
                    'customer_id' => $customerId,
                    'intent' => $intentResult['intent'] ?? 'unknown',
                    'context_length' => strlen($contextPrompt),
                    'context_preview' => substr($contextPrompt, 0, 200) . '...',
                    'found_from_search' => $ragContext['found_from_search'] ?? false,
                ]);
            }

            $model = config('chatbot.ollama_model', 'llama3.2:3b');
            // $model = config('chatbot.ollama_model', 'deepseek-r1:8b');
            $prompt = $this->buildDetailedPromptWithContext($message, $seller, $product, $intentResult, $ragContext, $contextPrompt, $conversationHistory);

            $timingBreakdown['data_preparation'] = round((microtime(true) - $prepStartTime) * 1000, 2);

            $payloadStartTime = microtime(true);

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

            $timingBreakdown['payload_preparation'] = round((microtime(true) - $payloadStartTime) * 1000, 2);

            Log::info('Ollama Request', [
                'seller_id' => $sellerId,
                'customer_id' => $customerId,
                'product_id' => $product?->id,
                'customer_message' => $message,
                'detected_intent' => $intentResult['intent'] ?? 'unknown',
                'intent_confidence' => $intentResult['confidence'] ?? 0,
                'context_type' => $ragContext['type'] ?? 'none',
                'found_from_search' => $ragContext['found_from_search'] ?? false,
                'model' => $payload['model'],
                'history_entries' => count($conversationHistory),
                'prompt_length' => strlen($prompt),
                'has_context_prompt' => !empty($contextPrompt),
                'timing_so_far' => $timingBreakdown,
                'prompt' => "\n",
                $prompt,
            ]);

            $ollamaUrl = config('chatbot.ollama_url', 'http://localhost:11434') . '/api/generate';

            $httpStartTime = microtime(true);

            $response = Http::withOptions([
                'timeout' => 300,
                'connect_timeout' => 10,
                'read_timeout' => 300,
            ])->post($ollamaUrl, $payload);

            $timingBreakdown['http_request'] = round((microtime(true) - $httpStartTime) * 1000, 2);

            $processStartTime = microtime(true);

            if ($response->successful()) {
                $data = $response->json();
                $rawResponse = $data['response'] ?? null;

                if ($rawResponse && trim($rawResponse) !== '') {
                    $cleanResponse = $this->cleanResponseAdvanced($rawResponse);

                    $timingBreakdown['response_processing'] = round((microtime(true) - $processStartTime) * 1000, 2);
                    $timingBreakdown['total_execution'] = round((microtime(true) - $totalStartTime) * 1000, 2);

                    Log::info('Ollama Final Response with Timing', [
                        'seller_id' => $sellerId,
                        'customer_id' => $customerId,
                        'intent' => $intentResult['intent'] ?? 'unknown',
                        'found_from_search' => $ragContext['found_from_search'] ?? false,
                        'raw_response' => $rawResponse,
                        'cleaned_response' => $cleanResponse,
                        'final_length' => mb_strlen($cleanResponse),
                        'timing_breakdown' => $timingBreakdown,
                        'performance_metrics' => [
                            'total_time_ms' => $timingBreakdown['total_execution'],
                            'total_time_s' => round($timingBreakdown['total_execution'] / 1000, 3),
                            'total_time_min' => round($timingBreakdown['total_execution'] / 60000, 3),
                            'http_percentage' => round(($timingBreakdown['http_request'] / $timingBreakdown['total_execution']) * 100, 1),
                            'processing_percentage' => round(($timingBreakdown['response_processing'] / $timingBreakdown['total_execution']) * 100, 1),
                            'preparation_percentage' => round((($timingBreakdown['data_preparation'] + $timingBreakdown['payload_preparation']) / $timingBreakdown['total_execution']) * 100, 1),
                        ],
                    ]);

                    return $cleanResponse;
                } else {
                    $timingBreakdown['response_processing'] = round((microtime(true) - $processStartTime) * 1000, 2);
                    $timingBreakdown['total_execution'] = round((microtime(true) - $totalStartTime) * 1000, 2);

                    Log::warning('Ollama returned empty response', [
                        'seller_id' => $sellerId,
                        'customer_id' => $customerId,
                        'intent' => $intentResult['intent'] ?? 'unknown',
                        'context_type' => $ragContext['type'] ?? 'none',
                        'found_from_search' => $ragContext['found_from_search'] ?? false,
                        'timing_breakdown' => $timingBreakdown,
                    ]);

                    return $this->getFallbackResponse($intentResult['intent'] ?? 'unknown', $ragContext);
                }
            }

            $timingBreakdown['response_processing'] = round((microtime(true) - $processStartTime) * 1000, 2);
            $timingBreakdown['total_execution'] = round((microtime(true) - $totalStartTime) * 1000, 2);

            Log::error('Ollama API Error', [
                'seller_id' => $sellerId,
                'customer_id' => $customerId,
                'intent' => $intentResult['intent'] ?? 'unknown',
                'context_type' => $ragContext['type'] ?? 'none',
                'found_from_search' => $ragContext['found_from_search'] ?? false,
                'status_code' => $response->status(),
                'error_body' => $response->body(),
                'timing_breakdown' => $timingBreakdown,
            ]);

            return null;
        } catch (\Exception $e) {
            $timingBreakdown['total_execution'] = round((microtime(true) - $totalStartTime) * 1000, 2);

            Log::error('Error sending message to Ollama', [
                'seller_id' => $seller->id ?? 'unknown',
                'customer_id' => $customerId,
                'product_id' => $product?->id,
                'customer_message' => $message,
                'intent' => $intentResult['intent'] ?? 'unknown',
                'context_type' => $ragContext['type'] ?? 'none',
                'found_from_search' => $ragContext['found_from_search'] ?? false,
                'error_message' => $e->getMessage(),
                'timing_breakdown' => $timingBreakdown,
            ]);

            return null;
        }
    }

    /**
     * Fungsi alternatif dengan lebih banyak kontrol untuk cleaning
     */
    // function parseProductDescriptionAdvanced($htmlDescription) {
    //     if (empty($htmlDescription)) {
    //         return '';
    //     }

    //     $decoded = html_entity_decode($htmlDescription, ENT_QUOTES | ENT_HTML5, 'UTF-8');
/**
*        $decoded = preg_replace('/<br\s*\/?>/i', ' ', $decoded);
*/
    //     $cleanText = strip_tags($decoded);

    //     $cleanText = preg_replace('/[\s\t\n\r]+/', ' ', $cleanText);

    //     $cleanText = trim($cleanText);

    //     return $cleanText;
    // }

    /**
     * Build prompt yang lebih detailed dengan info produk lengkap
     */
    private function buildDetailedPromptWithContext($message, $seller, $product, $intentResult, $ragContext, $contextPrompt, $conversationHistory): string
    {
        $shopName = $seller && $seller->sellerShop ? $seller->sellerShop->shop_name : 'Toko Online';
        $sellerName = $seller ? $seller->name : 'Customer Service TokoPoin';
        $intent = $intentResult['intent'] ?? 'unknown';
        $confidence = $intentResult['confidence'] ?? 0;

        $prompt = "Anda adalah customer service AI yang ramah dan profesional untuk {$shopName}.\n\n";

        $prompt .= "=== ANALISIS PESAN ===\n";
        $prompt .= "Intent terdeteksi: {$intent}\n";
        $prompt .= "Confidence level: {$confidence}%\n";
        $prompt .= "Pesan customer: \"{$message}\"\n\n";

        if (!empty($contextPrompt)) {
            $prompt .= "=== INFORMASI RELEVAN ===\n";
            $prompt .= $contextPrompt . "\n";
        }

        // if ($product) {
        //     $prompt .= "=== PRODUK YANG DIBAHAS ===\n";
        //     $prompt .= "Nama: {$product->name}\n";

        //     $finalPrice = $product->price;
        //     $hasDiscount = false;

        //     if (!empty($product->discount)) {
        //         $finalPrice = $product->price - $product->discount;
        //         $hasDiscount = true;
        //     }

        //     if ($hasDiscount) {
        //         $prompt .= 'Harga: Rp ' . number_format($finalPrice, 0, ',', '.') . ' (Diskon dari Rp ' . number_format($product->price, 0, ',', '.') . ')' . "\n";
        //     } else {
        //         $prompt .= 'Harga: Rp ' . number_format($finalPrice, 0, ',', '.') . "\n";
        //     }

        //     if ($product->description) {
        //         $cleanDescription = $this->parseProductDescriptionAdvanced($product->description);
        //         if (!empty($cleanDescription)) {
        //             $prompt .= "Deskripsi: {$cleanDescription}\n";
        //         }
        //     }
        //     $prompt .= "\n";
        // }

        if (!empty($conversationHistory)) {
            $prompt .= "=== RIWAYAT PERCAKAPAN ===\n";
            foreach (array_slice($conversationHistory, -5) as $conv) {
                $role = $conv['sender_role'] === 'customer' ? 'Customer' : 'CS';
                $prompt .= "{$role}: {$conv['message']}\n";
            }
            $prompt .= "\n";
        }

        $prompt .= $this->getIntentSpecificInstructions($intent, $ragContext);

        $prompt .= "=== INSTRUKSI RESPONS ===\n";
        $prompt .= "- Jawab dalam Bahasa Indonesia dengan singkat tetapi tetap ramah dan profesional\n";
        $prompt .= "- Berikan informasi yang akurat berdasarkan context yang tersedia\n";
        $prompt .= "- Jika tidak ada informasi yang cukup, minta customer untuk menjelaskan lebih spesifik\n";
        $prompt .= "- Gunakan emoji secukupnya untuk memberikan kesan ramah\n";
        $prompt .= "- Jangan membuat informasi yang tidak ada dalam context\n";
        $prompt .= "- Fokus menjawab sesuai dengan intent: {$intent}\n\n";

        $prompt .= "Customer bertanya: \"{$message}\"\n\n";
        $prompt .= 'Jawaban CS:';

        return $prompt;
    }

    private function getIntentSpecificInstructions(string $intent, array $ragContext): string
    {
        $instructions = '=== PANDUAN KHUSUS INTENT: ' . strtoupper($intent) . " ===\n";

        switch ($intent) {
            case 'product_listing':
                $instructions .= "- Tampilkan daftar produk yang tersedia dengan format yang rapi\n";
                $instructions .= "- Sertakan nama, harga, dan status stok\n";
                $instructions .= "- Tawarkan bantuan untuk informasi lebih detail\n";
                break;

            case 'price_inquiry':
                $instructions .= "- Berikan informasi harga yang jelas dan akurat\n";
                $instructions .= "- Berikan informasi harga dari produk yang kamu ketahui saja\n";
                $instructions .= "- Sebutkan jika ada diskon atau promo khusus\n";
                $instructions .= "- Tawarkan informasi tambahan tentang produk\n";
                break;

            case 'stock_availability':
                $instructions .= "- Berikan informasi stok yang akurat dan real-time\n";
                $instructions .= "- Jika habis, tawarkan alternatif atau restock info\n";
                $instructions .= "- Sarankan untuk order segera jika stok terbatas\n";
                break;

            case 'product_recommendation':
                $instructions .= "- Berikan rekomendasi yang relevan dengan kebutuhan customer\n";
                $instructions .= "- Jelaskan keunggulan produk yang direkomendasikan\n";
                $instructions .= "- Tanyakan preferensi spesifik jika diperlukan\n";
                break;

            case 'order_process':
                $instructions .= "- Jelaskan langkah-langkah order dengan jelas dan bertahap\n";
                $instructions .= "- Tawarkan bantuan untuk proses pemesanan\n";
                $instructions .= "- Berikan informasi estimasi waktu proses\n";
                break;

            case 'payment_method':
                $instructions .= "- Jelaskan semua metode pembayaran yang tersedia\n";
                $instructions .= "- Berikan informasi tentang keamanan pembayaran\n";
                $instructions .= "- Tawarkan bantuan untuk proses pembayaran\n";
                break;

            case 'shipping_info':
                $instructions .= "- Berikan informasi lengkap tentang pengiriman\n";
                $instructions .= "- Jelaskan estimasi waktu dan biaya pengiriman\n";
                $instructions .= "- Sebutkan ekspedisi yang tersedia\n";
                break;

            case 'return_policy':
                $instructions .= "- Jelaskan kebijakan return dengan jelas\n";
                $instructions .= "- Sebutkan syarat dan ketentuan return\n";
                $instructions .= "- Berikan informasi proses refund\n";
                break;

            case 'greeting':
                $instructions .= "- Balas salam dengan ramah dan welcoming\n";
                $instructions .= "- Perkenalkan diri sebagai CS toko\n";
                $instructions .= "- Tawarkan bantuan dan tanyakan kebutuhan customer\n";
                break;

            case 'appreciation':
                $instructions .= "- Terima kasih dengan tulus\n";
                $instructions .= "- Pastikan customer puas dengan pelayanan\n";
                $instructions .= "- Tawarkan bantuan lanjutan jika diperlukan\n";
                break;

            default:
                $instructions .= "- Pahami kebutuhan customer dengan baik\n";
                $instructions .= "- Berikan bantuan sesuai dengan konteks yang tersedia\n";
                $instructions .= "- Jika tidak yakin, minta klarifikasi lebih lanjut\n";
                break;
        }

        $instructions .= "\n";
        return $instructions;
    }

    private function getFallbackResponse(string $intent, array $ragContext): string
    {
        $fallbacks = [
            'product_listing' => 'Mohon maaf, saat ini saya sedang mengalami kendala dalam mengambil daftar produk. Silakan hubungi CS kami langsung untuk informasi produk terbaru. 😊',

            'price_inquiry' => 'Mohon maaf, untuk informasi harga yang akurat, silakan sebutkan nama produk yang spesifik atau hubungi CS kami langsung. 😊',

            'stock_availability' => 'Untuk informasi stok yang real-time, mohon sebutkan produk yang ingin ditanyakan atau hubungi CS kami langsung. 😊',

            'product_recommendation' => 'Saya akan senang memberikan rekomendasi produk! Bisakah Anda memberitahu saya kategori produk atau budget yang diinginkan? 😊',

            'order_process' => 'Untuk panduan pemesanan, Anda dapat: 1) Pilih produk yang diinginkan, 2) Klik "Beli Sekarang", 3) Isi data pengiriman, 4) Pilih metode pembayaran. Butuh bantuan lebih lanjut? 😊',

            'payment_method' => 'Kami menerima pembayaran melalui Transfer Bank, E-wallet (OVO, DANA, GoPay), dan COD untuk area tertentu. Ada yang ingin ditanyakan tentang pembayaran? 😊',

            'shipping_info' => 'Pengiriman menggunakan JNE, J&T, SiCepat, dan Pos Indonesia. Estimasi 1-3 hari Jabodetabek, 2-5 hari luar kota. Butuh info pengiriman spesifik? 😊',

            'return_policy' => 'Produk dapat diretur dalam 7 hari jika ada kerusakan atau tidak sesuai deskripsi. Syarat: kondisi utuh, kemasan lengkap, ada bukti pembelian. Ada yang ingin ditanyakan? 😊',

            'greeting' => 'Halo! Selamat datang di toko kami 😊 Saya customer service yang siap membantu Anda. Ada yang bisa saya bantu hari ini?',

            'appreciation' => 'Sama-sama! Senang bisa membantu Anda 😊 Jika ada pertanyaan lain, jangan ragu untuk bertanya ya!',

            'general_help' => 'Saya siap membantu Anda! Silakan tanyakan tentang produk, harga, stok, cara order, atau informasi lainnya. Ada yang bisa dibantu? 😊',

            'unknown' => 'Mohon maaf, saya kurang memahami pertanyaan Anda. Bisakah dijelaskan lebih spesifik? Atau hubungi CS kami langsung untuk bantuan lebih lanjut. 😊',
        ];

        $fallback = $fallbacks[$intent] ?? $fallbacks['unknown'];

        if (isset($ragContext['seller_info']) && !empty($ragContext['seller_info']['whatsapp'])) {
            $whatsapp = $ragContext['seller_info']['whatsapp'];
            if ($whatsapp !== 'Tidak tersedia') {
                $fallback .= "\n\nUntuk bantuan langsung, hubungi WhatsApp: {$whatsapp}";
            }
        }

        return $fallback;
    }

    /**
     * Bersihkan response dengan method yang lebih advanced
     */
    private function cleanResponseAdvanced($response): string
    {
        $cleaned = trim($response);

        // Remove thinking patterns
        $thinkingPatterns = ['/<think>.*?<\/think>/si', '/<thinking>.*?<\/thinking>/si', '/\[thinking\].*?\[\/thinking\]/si', '/\*thinking\*.*?\*\/thinking\*/si', '/\*\*thinking\*\*.*?\*\*\/thinking\*\*/si', '/<!-- thinking -->.*?<!-- \/thinking -->/si', '/\{thinking\}.*?\{\/thinking\}/si', '/\(thinking\).*?\(\/thinking\)/si'];

        foreach ($thinkingPatterns as $pattern) {
            $cleaned = preg_replace($pattern, '', $cleaned);
        }

        // Remove prefixes
        $prefixes = ['Customer Service:', 'CS:', 'Admin:', 'Jawaban:', 'Reply:', 'Response:'];
        foreach ($prefixes as $prefix) {
            if (stripos($cleaned, $prefix) === 0) {
                $cleaned = trim(substr($cleaned, strlen($prefix)));
            }
        }

        $cleaned = preg_replace('/\*\*(.*?)\*\*/', '$1', $cleaned); // Remove **bold**
        $cleaned = preg_replace('/\*(.*?)\*/', '$1', $cleaned); // Remove *italic*
        $cleaned = preg_replace('/_{2,}(.*?)_{2,}/', '$1', $cleaned); // Remove __underline__
        $cleaned = preg_replace('/_([^_]+)_/', '$1', $cleaned); // Remove _single underscore_

        $cleaned = preg_replace('/\s+/', ' ', $cleaned);

        $cleaned = preg_replace('/(\d+\.\s)/', "\n$1", $cleaned);

        $cleaned = preg_replace('/([^\n])\s*(-\s|•\s)/', "$1\n$2", $cleaned);

        $cleaned = preg_replace('/([^\n])\s*(🔹\s)/', "$1\n$2", $cleaned);

        $cleaned = preg_replace('/\n{3,}/', "\n\n", $cleaned);

        $cleaned = ltrim($cleaned, ':-*');
        $cleaned = trim($cleaned);

        if (empty($cleaned) || mb_strlen($cleaned) < 5) {
            $cleaned = 'Halo! Terima kasih sudah menghubungi kami 😊 Ada yang bisa saya bantu?';
        }

        $lastChar = mb_substr(trim($cleaned), -1);
        if (!preg_match('/[.!?😊👍🙏]$/', $lastChar)) {
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

            $delay = $responseDelay ?? ($setting->response_delay ?? 0);
            Log::info('Chatbot preparing response', [
                'customer_id' => $customerId,
                'seller_id' => $sellerId,
                'response_delay' => $delay,
                'response_preview' => substr($response, 0, 100),
            ]);

            if ($delay > 0) {
                sleep($delay);
            }

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

            $message->load(['customer', 'seller', 'seller.sellerShop']);

            event(new NewMessageSent($message, $customerId, $sellerId));

            Log::info('Chatbot NewMessageSent event dispatched', [
                'message_id' => $message->id,
                'customer_id' => $customerId,
                'seller_id' => $sellerId,
                'event_class' => NewMessageSent::class,
            ]);

            try {
                $whatsappService = app(WhatsAppService::class);

                $customerName = $message->customer->name ?? 'Customer';

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
            }
        } catch (\Exception $e) {
            Log::error('Failed to save chatbot response', [
                'customer_id' => $customerId,
                'seller_id' => $sellerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function handleChatbotFailure($customerId, $sellerId, $originalMessage): void
    {
        try {
            $whatsappService = app(WhatsAppService::class);

            $customer = \App\Models\User::find($customerId);
            $customerName = $customer->name ?? 'Customer';

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
