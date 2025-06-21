<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Seller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Events\NewMessageSent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\CustomerSellerConversation;
use App\Http\Services\Conversation\ChatbotService;
use App\Http\Services\Conversation\WhatsAppService;

class WhatsAppWebhookController extends Controller
{
    protected $chatbotService;
    protected $whatsappService;

    public function __construct(ChatbotService $chatbotService, WhatsAppService $whatsappService)
    {
        $this->chatbotService = $chatbotService;
        $this->whatsappService = $whatsappService;
    }

    /**
     * Handle incoming WhatsApp webhook from Fonnte
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            // Set content type header
            header('Content-Type: application/json; charset=utf-8');

            // Get JSON input from Fonnte webhook
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            // Extract webhook data
            $device = $data['device'] ?? '';
            $sender = $data['sender'] ?? '';
            $message = $data['message'] ?? '';
            $member = $data['member'] ?? ''; // For group messages
            $name = $data['name'] ?? '';
            $location = $data['location'] ?? '';
            $url = $data['url'] ?? '';
            $filename = $data['filename'] ?? '';
            $extension = $data['extension'] ?? '';

            Log::info('WhatsApp webhook received', [
                'device' => $device,
                'sender' => $sender,
                'message' => $message,
                'name' => $name,
                'url' => $url,
                'filename' => $filename,
            ]);

            // Validate required fields
            if (empty($sender) || empty($message)) {
                Log::warning('WhatsApp webhook missing required fields', [
                    'sender' => $sender,
                    'message' => $message,
                ]);
                return response()->json(['status' => 'error', 'message' => 'Missing required fields'], 400);
            }

            // Process the message
            $response = $this->processIncomingMessage($sender, $message, $name, $url, $filename, $extension);

            return response()->json(['status' => 'success', 'message' => 'Processed successfully']);
        } catch (\Exception $e) {
            Log::error('WhatsApp webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    /**
     * Process incoming WhatsApp message and generate response
     */
    /**
     * Process incoming WhatsApp message and generate response
     */
    private function processIncomingMessage($sender, $message, $name = '', $url = '', $filename = '', $extension = ''): ?string
    {
        try {
            // Clean phone number
            $cleanSender = $this->cleanPhoneNumber($sender);

            Log::info('Processing WhatsApp message', [
                'sender' => $sender,
                'clean_sender' => $cleanSender,
                'message' => $message,
                'name' => $name,
            ]);

            // Handle predefined commands first
            $predefinedResponse = $this->handlePredefinedCommands($message);
            if ($predefinedResponse) {
                // Send predefined response via WhatsApp
                $this->whatsappService->sendChatbotReply($sender, $predefinedResponse);
                return $predefinedResponse;
            }

            // Extract product ID from message (URL or keywords)
            $productId = $this->extractProductFromMessage($message);

            // Find or create user
            $user = $this->findOrCreateUser($cleanSender, $name);

            // Determine which seller to route to
            $sellerId = $this->determineSeller($message, $user->id , $productId);

            $customerMessage = $this->saveCustomerMessage($user->id, $sellerId, $message, $productId);

            // Get AI response
            $aiResponse = $this->chatbotService->sendToAI($message, $sellerId, $user->id, $productId);

            if (!$aiResponse) {
                $aiResponse = $this->getDefaultResponse($user->id, $sellerId);
            }

            $this->saveChatbotResponseWithRealtime($user->id, $sellerId, $aiResponse);

            // Send AI response via WhatsApp
            $this->whatsappService->sendChatbotReply($sender, $aiResponse);

            Log::info('WhatsApp message processed successfully', [
                'sender' => $sender,
                'user_id' => $user->id,
                'seller_id' => $sellerId,
                'product_id' => $productId,
                'response_preview' => substr($aiResponse, 0, 100),
            ]);

            return $aiResponse;
        } catch (\Exception $e) {
            Log::error('Error processing WhatsApp message', [
                'sender' => $sender,
                'message' => $message,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Send error response to WhatsApp
            $errorResponse = 'Maaf, terjadi kesalahan sistem. Silakan coba lagi nanti.';
            $this->whatsappService->sendChatbotReply($sender, $errorResponse);

            return $errorResponse;
        }
    }

    /**
     * Handle predefined commands
     */
    private function handlePredefinedCommands($message): ?string
    {
        $message = strtolower(trim($message));

        switch ($message) {
            case 'test':
                return 'Sistem WhatsApp bot TokoPoin berfungsi dengan baik! 🤖✅';

            case 'help':
            case 'bantuan':
                return "🤖 *TokoPoin WhatsApp Bot*\n\n" . "Selamat datang di TokoPoin! Saya dapat membantu Anda:\n\n" . "📱 Informasi produk\n" . "💰 Cek harga dan stok\n" . "🛒 Panduan pembelian\n" . "📞 Hubungi seller\n\n" . 'Ketik nama produk atau kirim pesan Anda untuk mulai berbelanja!';

            case 'start':
            case 'mulai':
                return "🛍️ *Selamat datang di TokoPoin!*\n\n" . "Kami siap membantu Anda menemukan produk terbaik.\n\n" . "Silakan kirim pesan tentang produk yang Anda cari atau ketik 'bantuan' untuk panduan.";

            case 'info':
                return "ℹ️ *Tentang TokoPoin*\n\n" . "TokoPoin adalah platform marketplace yang menghubungkan pembeli dengan seller terpercaya.\n\n" . "🏪 Ribuan produk berkualitas\n" . "💯 Seller terverifikasi\n" . "🚚 Pengiriman cepat\n" . "💳 Pembayaran aman\n\n" . 'Mulai berbelanja sekarang!';

            default:
                return null;
        }
    }

    /**
     * Extract product ID from message (URL parsing + keyword matching)
     */
    private function extractProductFromMessage($message): ?int
    {
        // First, try to extract from URL
        $productId = $this->extractProductIdFromUrl($message);
        if ($productId) {
            return $productId;
        }

        // Fallback to keyword matching
        $keywords = ['sepatu', 'baju', 'tas', 'hp', 'laptop', 'handphone'];

        foreach ($keywords as $keyword) {
            if (stripos($message, $keyword) !== false) {
                // Find a product with matching keyword
                $product = Product::where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('description', 'LIKE', "%{$keyword}%")
                    ->first();

                if ($product) {
                    return $product->id;
                }
            }
        }

        return null;
    }

    /**
     * Extract product ID from URL
     */
    private function extractProductIdFromUrl($message): ?int
    {
        // Pattern untuk URL seperti: http://localhost:8000/product/poeticluzien-black-hoodie-heart-unisex-m/4
        $pattern = '/\/product\/([^\/]+)\/(\d+)/';

        if (preg_match($pattern, $message, $matches)) {
            $productId = (int) $matches[2];

            // Verify product exists
            $product = Product::find($productId);
            if ($product) {
                Log::info('Product extracted from URL', [
                    'product_id' => $productId,
                    'product_name' => $product->name,
                    'message' => $message,
                ]);
                return $productId;
            }
        }

        return null;
    }

    /**
     * IMPROVED: Determine seller ID from product or conversation history
     * Now handles comprehensive seller determination with fallback strategies
     * Similar pattern to getProductFromConversation
     */
    private function determineSeller($message, $customerId, $productId = null): ?int
    {
        Log::info('Determining seller - START', [
            'message' => $message,
            'customer_id' => $customerId,
            'product_id' => $productId,
        ]);

        // Strategy 1: If we have a product ID, use that seller
        if ($productId) {
            $product = Product::with('seller')->find($productId);
            if ($product && $product->seller) {
                $sellerId = $product->seller->id;
                Log::info('Seller determined from direct product', [
                    'product_id' => $productId,
                    'seller_id' => $sellerId,
                    'seller_name' => $product->seller->name ?? 'N/A',
                    'method' => 'direct_product',
                ]);
                return $sellerId;
            }

            Log::warning('Product found but no seller associated', [
                'product_id' => $productId,
                'product_exists' => $product ? 'yes' : 'no',
                'seller_exists' => $product && $product->seller ? 'yes' : 'no',
            ]);
        }

        // Strategy 2: Look for seller from recent conversation history with products
        try {
            $conversationWithProduct = CustomerSellerConversation::where('customer_id', $customerId)->whereNotNull('files')->whereNotNull('seller_id')->latest()->first();

            if ($conversationWithProduct && $conversationWithProduct->files) {
                // Parse JSON dari files untuk mencari product
                $files = is_array($conversationWithProduct->files) ? $conversationWithProduct->files : json_decode($conversationWithProduct->files, true);

                if (is_array($files)) {
                    foreach ($files as $file) {
                        $fileData = is_string($file) ? json_decode($file, true) : $file;

                        if (isset($fileData['type']) && $fileData['type'] === 'product' && isset($fileData['product_id'])) {
                            $product = Product::with('seller')->find($fileData['product_id']);
                            if ($product && $product->seller) {
                                $sellerId = $product->seller->id;

                                Log::info('Seller determined from conversation product history', [
                                    'conversation_id' => $conversationWithProduct->id,
                                    'product_id' => $product->id,
                                    'product_name' => $product->name,
                                    'seller_id' => $sellerId,
                                    'seller_name' => $product->seller->name ?? 'N/A',
                                    'customer_id' => $customerId,
                                    'method' => 'conversation_product_history',
                                ]);

                                return $sellerId;
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error parsing conversation files for seller determination: ' . $e->getMessage(), [
                'customer_id' => $customerId,
                'error_trace' => $e->getTraceAsString(),
            ]);
        }

        // Strategy 3: Get seller from most recent conversation (fallback)
        try {
            $recentConversation = CustomerSellerConversation::where('customer_id', $customerId)->whereNotNull('seller_id')->latest()->first();

            if ($recentConversation && $recentConversation->seller_id) {
                // Verify seller still exists
                $seller = Seller::find($recentConversation->seller_id);
                if ($seller) {
                    Log::info('Seller determined from recent conversation', [
                        'conversation_id' => $recentConversation->id,
                        'seller_id' => $seller->id,
                        'seller_name' => $seller->name ?? 'N/A',
                        'customer_id' => $customerId,
                        'method' => 'recent_conversation',
                    ]);

                    return $seller->id;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error getting seller from recent conversation: ' . $e->getMessage(), [
                'customer_id' => $customerId,
                'error_trace' => $e->getTraceAsString(),
            ]);
        }

        Log::info('No seller could be determined - returning null', [
            'customer_id' => $customerId,
            'product_id' => $productId,
            'message_length' => strlen($message),
            'method' => 'none_found',
        ]);

        return null;
    }

    /**
     * Find or create user based on phone number
     */
    private function findOrCreateUser($phone, $name = '')
    {
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            $user = User::create([
                'name' => $name ?: 'WhatsApp User',
                'phone' => $phone,
                'email' => null, // WhatsApp users might not have email
                'password' => null, // WhatsApp users don't need password
            ]);

            Log::info('New WhatsApp user created', [
                'user_id' => $user->id,
                'phone' => $phone,
                'name' => $name,
            ]);
        }

        return $user;
    }

    /**
     * Clean phone number format
     */
    private function cleanPhoneNumber($phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert to Indonesian format
        if (str_starts_with($phone, '628')) {
            return '08' . substr($phone, 3);
        } elseif (str_starts_with($phone, '62')) {
            return '0' . substr($phone, 2);
        } elseif (str_starts_with($phone, '8')) {
            return '0' . $phone;
        }

        return $phone;
    }

    /**
     * Get default response when no specific handler matches
     */
    private function getDefaultResponse($userId = null, $sellerId = null): string
    {
        $defaultMessage = "Halo! Terima kasih telah menghubungi TokoPoin 😊\n\n" . "Saya adalah asisten virtual yang siap membantu Anda mencari produk terbaik.\n\n" . "Silakan ceritakan produk apa yang Anda cari atau ketik 'bantuan' untuk panduan lengkap.";

        // Save default response to conversation if we have user and seller
        if ($userId && $sellerId) {
            try {
                CustomerSellerConversation::create([
                    'customer_id' => $userId,
                    'seller_id' => $sellerId,
                    'sender_role' => 'chatbot',
                    'message' => $defaultMessage,
                    'is_seen' => 0,
                    'source' => 'whatsapp',
                ]);
            } catch (\Exception $e) {
                Log::error('Error saving default response', ['error' => $e->getMessage()]);
            }
        }

        return $defaultMessage;
    }

    /**
     * Health check endpoint
     */
    public function healthCheck(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'message' => 'WhatsApp webhook is healthy',
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Save customer message and emit realtime event
     */
    private function saveCustomerMessage($customerId, $sellerId, $message, $productId = null): CustomerSellerConversation
    {
        try {
            // Prepare files array untuk product attachment
            $files = null;
            if ($productId) {
                $product = Product::find($productId);
                if ($product) {
                    // Generate product URL berdasarkan type
                    $productUrl = $product->product_type == Product::DIGITAL ? route('digital.product.details', [make_slug($product->name), $product->id]) : route('product.details', [make_slug($product->name), $product->id]);

                    $productAttachment = [
                        'type' => 'product',
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_url' => $productUrl,
                        'product_image' => $product->image,
                    ];

                    $files = [json_encode($productAttachment)];

                    Log::info('Product attachment prepared for customer message', [
                        'product_id' => $productId,
                        'product_name' => $product->name,
                        'product_url' => $productUrl,
                    ]);
                }
            }

            // Create and save customer message
            $customerMessage = new CustomerSellerConversation();
            $customerMessage->customer_id = $customerId;
            $customerMessage->seller_id = $sellerId;
            $customerMessage->sender_role = 'customer';
            $customerMessage->message = $message;
            $customerMessage->is_seen = 0;
            $customerMessage->source = 'whatsapp';
            $customerMessage->files = $files;
            $customerMessage->save();

            Log::info('Customer message saved', [
                'message_id' => $customerMessage->id,
                'customer_id' => $customerId,
                'seller_id' => $sellerId,
                'sender_role' => $customerMessage->sender_role,
                'has_attachment' => !is_null($files),
            ]);

            // Load relasi yang diperlukan untuk event
            $customerMessage->load(['customer', 'seller', 'seller.sellerShop']);

            // Emit event untuk real-time update
            event(new NewMessageSent($customerMessage, $customerId, $sellerId));

            Log::info('Customer MessageSent event dispatched', [
                'message_id' => $customerMessage->id,
                'customer_id' => $customerId,
                'seller_id' => $sellerId,
                'event_class' => NewMessageSent::class,
            ]);

            return $customerMessage;
        } catch (\Exception $e) {
            Log::error('Error saving customer message', [
                'customer_id' => $customerId,
                'seller_id' => $sellerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Save chatbot response with delay and realtime update
     */
    private function saveChatbotResponseWithRealtime($customerId, $sellerId, $response): void
    {
        try {
            Log::info('Chatbot preparing response', [
                'customer_id' => $customerId,
                'seller_id' => $sellerId,
                'response_preview' => substr($response, 0, 100),
            ]);

            // Buat dan simpan pesan
            $message = new CustomerSellerConversation();
            $message->customer_id = $customerId;
            $message->seller_id = $sellerId;
            $message->sender_role = 'chatbot';
            $message->message = $response;
            $message->is_seen = 0;
            $message->source = 'whatsapp';
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

            Log::info('Chatbot MessageSent event dispatched', [
                'message_id' => $message->id,
                'customer_id' => $customerId,
                'seller_id' => $sellerId,
                'event_class' => NewMessageSent::class,
            ]);

            // ** WhatsApp notification untuk chatbot reply **
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
}
