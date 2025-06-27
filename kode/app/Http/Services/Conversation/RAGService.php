<?php

namespace App\Http\Services\Conversation;

use App\Models\Product;
use App\Models\Seller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class RAGService
{
    /**
     * Knowledge base untuk FAQ umum
     */
    private $knowledgeBase = [
        'payment_method' => [
            'title' => 'Metode Pembayaran',
            'content' => 'Kami menerima berbagai metode pembayaran: Transfer Bank (BCA, Mandiri, BRI, BNI), E-wallet (OVO, DANA, GoPay, ShopeePay), dan COD (Cash on Delivery) untuk area tertentu. Semua pembayaran aman dan terjamin.',
        ],
        'shipping_info' => [
            'title' => 'Informasi Pengiriman',
            'content' => 'Pengiriman menggunakan ekspedisi terpercaya seperti JNE, J&T, SiCepat, dan Pos Indonesia. Estimasi pengiriman 1-3 hari untuk Jabodetabek, 2-5 hari untuk luar kota. Ongkos kirim dihitung otomatis berdasarkan berat dan tujuan.',
        ],
        'return_policy' => [
            'title' => 'Kebijakan Return & Refund',
            'content' => 'Barang dapat diretur dalam 7 hari jika ada kerusakan atau tidak sesuai deskripsi. Syarat: barang dalam kondisi utuh, kemasan lengkap, dan disertai bukti pembelian. Refund akan diproses dalam 3-7 hari kerja.',
        ],
        'order_process' => [
            'title' => 'Cara Order',
            'content' => 'Cara order mudah: 1) Pilih produk yang diinginkan, 2) Klik tombol "Beli Sekarang" atau "Masuk Keranjang", 3) Isi data pengiriman, 4) Pilih metode pembayaran, 5) Konfirmasi pesanan. Tim kami akan segera memproses orderan Anda.',
        ],
        'greeting' => [
            'title' => 'Greeting',
            'content' => 'Halo! Selamat datang di toko kami 😊 Saya customer service yang siap membantu Anda. Ada yang bisa saya bantu hari ini?',
        ],
        'appreciation' => [
            'title' => 'Appreciation Response',
            'content' => 'Sama-sama! Senang bisa membantu Anda 😊 Jika ada pertanyaan lain, jangan ragu untuk bertanya ya!',
        ],
        'delivery_options' => [
            'title' => 'Pilihan Ekspedisi',
            'content' => 'Kami bekerja sama dengan berbagai ekspedisi terpercaya: JNE (REG, OKE, YES), J&T Express, SiCepat (REG, HALU), Pos Indonesia, AnterAja, dan Ninja Xpress. Pilih ekspedisi yang sesuai dengan kebutuhan dan budget Anda.',
        ],
        'tracking_info' => [
            'title' => 'Cara Melacak Pesanan',
            'content' => 'Setelah pesanan dikirim, Anda akan menerima nomor resi melalui WhatsApp/SMS. Gunakan nomor resi untuk melacak status pengiriman di website atau aplikasi ekspedisi yang bersangkutan. Anda juga bisa menanyakan status pesanan langsung kepada kami.',
        ],
        'payment_options' => [
            'title' => 'Layanan COD',
            'content' => 'Layanan Cash on Delivery (COD) tersedia untuk wilayah Jabodetabek, Bandung, Surabaya, Medan, dan Makassar. Untuk wilayah lain, silakan hubungi kami untuk pengecekan ketersediaan layanan COD di daerah Anda.',
        ],
        'warranty_info' => [
            'title' => 'Informasi Garansi',
            'content' => 'Setiap produk memiliki garansi sesuai dengan kebijakan masing-masing brand. Garansi resmi berlaku 6-24 bulan tergantung jenis produk. Garansi mencakup kerusakan pabrik dan tidak mencakup kerusakan akibat pemakaian yang salah.',
        ],
        'customer_service' => [
            'title' => 'Prosedur Komplain',
            'content' => 'Jika ada masalah dengan produk: 1) Hubungi customer service kami dengan menyertakan foto/video, 2) Tim kami akan verifikasi dalam 1x24 jam, 3) Solusi akan diberikan berupa tukar barang, refund, atau perbaikan sesuai kondisi.',
        ],
        'general_help' => [
            'title' => 'Bantuan Umum',
            'content' => 'Kami siap membantu Anda dengan berbagai pertanyaan seputar produk, pemesanan, pembayaran, dan pengiriman. Jangan ragu untuk bertanya, tim customer service kami akan dengan senang hati membantu Anda.',
        ],
        'topic_transition' => [
            'title' => 'Topik Baru',
            'content' => 'Tentu! Silakan tanyakan hal lain yang ingin Anda ketahui. Saya siap membantu dengan pertanyaan baru Anda.',
        ],
    ];

    /**
     * Retrieve relevant information berdasarkan intent
     */
    public function retrieveRelevantInfo(string $intent, ?int $sellerId, int $customerId, ?int $productId = null, ?string $message = null): array
    {
        $context = [];

        try {
            // Validasi seller ID - jika null atau 0, return error context
            if (empty($sellerId) || $sellerId <= 0) {
                Log::warning('Seller ID is null or invalid', [
                    'intent' => $intent,
                    'seller_id' => $sellerId,
                    'customer_id' => $customerId,
                    'product_id' => $productId,
                    'message' => $message,
                ]);

                return [
                    'type' => 'seller_not_specified',
                    'message_hint' => 'Mohon maaf, saya tidak dapat menentukan toko mana yang Anda maksud. Silakan sebutkan nama toko atau penjual yang ingin Anda tanyakan, atau pastikan Anda sudah memilih toko yang tepat.',
                    'available_actions' => ['Sebutkan nama toko yang ingin ditanyakan', 'Pilih toko dari daftar yang tersedia', 'Hubungi customer service untuk bantuan'],
                    'error_code' => 'SELLER_ID_MISSING',
                ];
            }

            $sellerExists = Seller::where('id', $sellerId)->exists();
            if (!$sellerExists) {
                Log::warning('Seller not found in database', [
                    'intent' => $intent,
                    'seller_id' => $sellerId,
                    'customer_id' => $customerId,
                ]);

                return [
                    'type' => 'seller_not_found',
                    'message_hint' => 'Mohon maaf, toko dengan ID tersebut tidak ditemukan atau mungkin sudah tidak aktif. Silakan pilih toko lain atau hubungi customer service untuk bantuan.',
                    'seller_id' => $sellerId,
                    'error_code' => 'SELLER_NOT_FOUND',
                ];
            }

            switch ($intent) {
                case 'product_listing':
                    $context = $this->getSellerProducts($sellerId);
                    break;

                case 'price_inquiry':
                    if (!$productId && $message) {
                        $productId = $this->findProductFromMessage($message, $sellerId);
                    }
                    $context = $this->getProductPriceInfo($sellerId, $productId, $message);
                    break;

                case 'stock_availability':
                    if (!$productId && $message) {
                        $productId = $this->findProductFromMessage($message, $sellerId);
                    }
                    $context = $this->getProductStockInfo($sellerId, $productId, $message);
                    break;

                case 'product_recommendation':
                    $context = $this->getProductRecommendations($sellerId, $customerId);
                    break;

                case 'product_details':
                    if (!$productId && $message) {
                        $productId = $this->findProductFromMessage($message, $sellerId);
                    }
                    $context = $this->getProductDetailInfo($sellerId, $productId, $message);
                    break;

                case 'product_comparison':
                    $context = $this->getProductComparisonInfo($sellerId, $message);
                    break;

                case 'product_advantages':
                    if (!$productId && $message) {
                        $productId = $this->findProductFromMessage($message, $sellerId);
                    }
                    $context = $this->getProductAdvantagesInfo($sellerId, $productId, $message);
                    break;

                case 'product_popularity':
                    $context = $this->getPopularProductsInfo($sellerId);
                    break;

                case 'delivery_options':
                case 'tracking_info':
                case 'payment_options':
                case 'warranty_info':
                case 'customer_service':
                case 'general_help':
                case 'topic_transition':
                case 'payment_method':
                case 'shipping_info':
                case 'return_policy':
                case 'order_process':
                case 'greeting':
                case 'appreciation':
                    $context = $this->getKnowledgeBaseInfo($intent);
                    break;

                default:
                    $context = $this->getSellerContactInfo($sellerId);
                    break;
            }

            if (!isset($context['error_code'])) {
                $context['seller_info'] = $this->getSellerInfo($sellerId);
            }
        } catch (\Exception $e) {
            Log::error('RAG Error retrieving context', [
                'intent' => $intent,
                'seller_id' => $sellerId,
                'customer_id' => $customerId,
                'product_id' => $productId,
                'message' => $message,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if (!empty($sellerId) && $sellerId > 0) {
                try {
                    $context = $this->getSellerContactInfo($sellerId);
                    $context['error_occurred'] = true;
                    $context['original_error'] = 'Terjadi kesalahan saat memproses permintaan Anda.';
                } catch (\Exception $contactError) {
                    Log::error('Failed to get seller contact info as fallback', [
                        'seller_id' => $sellerId,
                        'error' => $contactError->getMessage(),
                    ]);

                    $context = [
                        'type' => 'system_error',
                        'message_hint' => 'Mohon maaf, terjadi kesalahan sistem. Silakan coba lagi dalam beberapa saat atau hubungi customer service untuk bantuan.',
                        'error_code' => 'SYSTEM_ERROR',
                    ];
                }
            } else {
                $context = [
                    'type' => 'seller_not_specified',
                    'message_hint' => 'Mohon maaf, saya tidak dapat menentukan toko mana yang Anda maksud. Silakan sebutkan nama toko atau penjual yang ingin Anda tanyakan.',
                    'error_code' => 'SELLER_ID_MISSING',
                ];
            }
        }

        return $context;
    }

    /**
     * Get available sellers untuk membantu customer memilih
     */
    private function getAvailableSellers(int $limit = 10): array
    {
        try {
            $sellers = Seller::with('sellerShop')
                ->where('status', 1) // Asumsi 1 = active
                ->limit($limit)
                ->get();

            $sellerList = [];
            foreach ($sellers as $seller) {
                $sellerList[] = [
                    'id' => $seller->id,
                    'name' => $seller->name,
                    'shop_name' => $seller->sellerShop->name ?? 'Toko ' . $seller->name,
                    'description' => $seller->sellerShop->short_details ?? '',
                ];
            }

            return [
                'type' => 'available_sellers',
                'sellers' => $sellerList,
                'total' => count($sellerList),
                'message_hint' => 'Berikut adalah daftar toko yang tersedia:',
            ];
        } catch (\Exception $e) {
            Log::error('Error getting available sellers', [
                'error' => $e->getMessage(),
            ]);

            return [
                'type' => 'sellers_fetch_error',
                'sellers' => [],
                'total' => 0,
                'message_hint' => 'Terjadi kesalahan saat mengambil daftar toko.',
            ];
        }
    }

    /**
     * Cari produk dari pesan customer menggunakan fuzzy matching
     */
    private function findProductFromMessage(string $message, int $sellerId): ?int
    {
        Log::info('Searching product from message', [
            'message' => $message,
            'seller_id' => $sellerId,
        ]);

        try {
            $searchTerms = $this->extractProductSearchTerms($message);

            if (empty($searchTerms)) {
                return null;
            }

            $bestMatch = null;
            $bestScore = 0;

            foreach ($searchTerms as $term) {
                $products = Product::where('seller_id', $sellerId)
                    ->where('status', 1)
                    ->where(function ($q) use ($term) {
                        $searchBy = '%' . $term . '%';
                        return $q
                            ->where('name', 'like', $searchBy)
                            ->orWhereHas('category', function ($q) use ($searchBy) {
                                return $q->where('name', 'like', $searchBy);
                            })
                            ->orWhereHas('brand', function ($q) use ($searchBy) {
                                return $q->where('name', 'like', $searchBy);
                            });
                    })
                    ->get();

                foreach ($products as $product) {
                    $score = $this->calculateSimilarity($term, $product->name);

                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $bestMatch = $product;
                    }
                }
            }

            if ($bestMatch && $bestScore > 0.3) {
                // Threshold 30%
                Log::info('Product found from message', [
                    'product_id' => $bestMatch->id,
                    'product_name' => $bestMatch->name,
                    'similarity_score' => $bestScore,
                    'search_term' => implode(', ', $searchTerms),
                ]);

                return $bestMatch->id;
            }

            Log::info('No suitable product match found', [
                'search_terms' => $searchTerms,
                'best_score' => $bestScore,
                'seller_id' => $sellerId,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Error finding product from message', [
                'message' => $message,
                'seller_id' => $sellerId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Extract search terms dari pesan customer
     */
    private function extractProductSearchTerms(string $message): array
    {
        // Bersihkan pesan dari kata-kata umum
        $stopWords = ['berapa', 'harga', 'stok', 'ada', 'tidak', 'produk', 'barang', 'item', 'tersedia', 'ready', 'stock', 'price', 'cost', 'beli', 'jual', 'dijual', 'bisa', 'dapat', 'mau', 'ingin', 'butuh', 'cari', 'lihat', 'tanya', 'info', 'informasi', 'detail', 'spesifikasi', 'spec', 'apa', 'apakah', 'bagaimana', 'dimana', 'kapan', 'kenapa', 'mengapa', 'siapa', 'yang', 'dan', 'atau', 'dengan', 'untuk', 'dari', 'ke', 'di', 'pada', 'dalam', 'luar', 'atas', 'bawah', 'depan', 'belakang', 'bandingkan', 'perbandingan', 'vs', 'versus', 'dibanding', 'keunggulan', 'kelebihan', 'unggul', 'populer', 'terlaris', 'bestseller', 'favorit'];

        // Konversi ke lowercase dan split
        $words = preg_split('/\s+/', strtolower($message));

        // Filter stopwords dan kata pendek
        $filteredWords = array_filter($words, function ($word) use ($stopWords) {
            return strlen($word) >= 3 && !in_array($word, $stopWords);
        });

        // Ambil kombinasi kata yang mungkin nama produk
        $searchTerms = [];

        // Single words
        foreach ($filteredWords as $word) {
            if (strlen($word) >= 4) {
                // Minimal 4 karakter
                $searchTerms[] = $word;
            }
        }

        // Kombinasi 2-3 kata berturut-turut
        $wordArray = array_values($filteredWords);
        for ($i = 0; $i < count($wordArray) - 1; $i++) {
            // 2 kata
            $searchTerms[] = $wordArray[$i] . ' ' . $wordArray[$i + 1];

            // 3 kata jika ada
            if (isset($wordArray[$i + 2])) {
                $searchTerms[] = $wordArray[$i] . ' ' . $wordArray[$i + 1] . ' ' . $wordArray[$i + 2];
            }
        }

        // Urutkan berdasarkan panjang (yang lebih panjang lebih spesifik)
        usort($searchTerms, function ($a, $b) {
            return strlen($b) - strlen($a);
        });

        return array_unique($searchTerms);
    }

    /**
     * Hitung similarity antara dua string
     */
    private function calculateSimilarity(string $search, string $productName): float
    {
        $search = strtolower($search);
        $productName = strtolower($productName);

        // Exact match
        if ($search === $productName) {
            return 1.0;
        }

        // Contains match
        if (strpos($productName, $search) !== false) {
            return 0.8;
        }

        if (strpos($search, $productName) !== false) {
            return 0.7;
        }

        // Levenshtein distance based similarity
        $maxLen = max(strlen($search), strlen($productName));
        if ($maxLen == 0) {
            return 0;
        }

        $distance = levenshtein($search, $productName);
        $similarity = 1 - $distance / $maxLen;

        return max(0, $similarity);
    }

    /**
     * Get seller products untuk product listing
     */
    private function getSellerProducts(int $sellerId, int $limit = 10): array
    {
        try {
            $products = Product::where('seller_id', $sellerId)
                ->where('status', 1) // atau 'active' sesuai dengan struktur database
                ->with(['category', 'stock'])
                ->limit($limit)
                ->get();

            if ($products->isEmpty()) {
                return [
                    'type' => 'product_listing',
                    'total_products' => 0,
                    'products' => [],
                    'message_hint' => 'Belum ada produk yang tersedia di toko ini.',
                ];
            }

            $productList = [];

            foreach ($products as $product) {
                $totalStock = $this->calculateProductStock($product);
                $finalPrice = $this->calculateFinalPrice($product);

                $productList[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $finalPrice,
                    'formatted_price' => 'Rp ' . number_format($finalPrice, 0, ',', '.'),
                    'stock' => $totalStock,
                    'category' => $product->category->name ?? 'Umum',
                    'status' => $totalStock > 0 ? 'Tersedia' : 'Habis',
                ];
            }

            return [
                'type' => 'product_listing',
                'total_products' => $products->count(),
                'products' => $productList,
                'message_hint' => 'Berikut adalah daftar produk yang tersedia di toko kami:',
            ];
        } catch (\Exception $e) {
            Log::error('Error getting seller products', [
                'seller_id' => $sellerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'type' => 'product_listing',
                'error_code' => 'PRODUCT_NOT_FOUND',
                'total_products' => 0,
                'products' => [],
                'message_hint' => 'Terjadi kesalahan saat mengambil daftar produk.',
            ];
        }
    }

    private function calculateFinalPrice($product): int
    {
        if ($product->discount && $product->discount > 0) {
            return $product->price - $product->discount;
        }

        // Default gunakan price
        return $product->price;
    }

    /**
     * Get product price information
     */
    private function getProductPriceInfo(int $sellerId, ?int $productId = null, ?string $originalMessage = null): array
    {
        if ($productId) {
            $product = Product::where('id', $productId)
                ->where('seller_id', $sellerId)
                ->with(['stock'])
                ->first();

            if ($product) {
                $finalPrice = $this->calculateFinalPrice($product);

                return [
                    'type' => 'price_info',
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'original_price' => $product->price,
                        'selling_price' => $finalPrice,
                        'formatted_price' => 'Rp ' . number_format($finalPrice, 0, ',', '.'),
                        'discount' => $product->discount ?? 0,
                        'discount_percentage' => $product->discount_percentage ?? 0,
                    ],
                    'message_hint' => 'Berikut informasi harga produk yang Anda tanyakan:',
                    'found_from_search' => true,
                ];
            }
        }

        // Jika tidak ditemukan, berikan informasi pencarian
        $searchHint = $originalMessage ? "Mohon maaf, saya tidak dapat menemukan produk yang Anda maksud dari pesan '{$originalMessage}'. " : '';

        return [
            'type' => 'price_inquiry_failed',
            'error_code' => 'PRODUCT_NOT_FOUND',
            'message_hint' => $searchHint . 'Mohon sebutkan nama produk dengan lebih spesifik, atau kirimkan link produknya.',
            'found_from_search' => false,
        ];
    }

    /**
     * Get product stock information
     */
    private function getProductStockInfo(int $sellerId, ?int $productId = null, ?string $originalMessage = null): array
    {
        if ($productId) {
            $product = Product::where('id', $productId)
                ->where('seller_id', $sellerId)
                ->with(['stock'])
                ->first();

            if ($product) {
                $stockInfo = $this->getDetailedStockInfo($product);
                $totalStock = $stockInfo['total_stock'];

                $result = [
                    'type' => 'stock_info',
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'total_stock' => $totalStock,
                        'status' => $totalStock > 0 ? 'Tersedia' : 'Habis',
                        'stock_message' => $totalStock > 0 ? "Total stok tersedia: {$totalStock} unit" : 'Maaf, produk sedang habis',
                        'has_variants' => $stockInfo['has_variants'],
                    ],
                    'message_hint' => 'Berikut informasi stok produk yang Anda tanyakan:',
                    'found_from_search' => true,
                ];

                if ($stockInfo['has_variants'] && !empty($stockInfo['stock_details'])) {
                    $result['product']['variants'] = $stockInfo['stock_details'];
                    $result['message_hint'] = 'Berikut informasi stok detail per varian produk:';
                }

                return $result;
            }
        }

        $searchHint = $originalMessage ? "Mohon maaf, saya tidak dapat menemukan produk yang Anda maksud dari pesan '{$originalMessage}'. " : '';

        return [
            'type' => 'stock_inquiry_failed',
            'message_hint' => $searchHint . 'Mohon sebutkan nama produk dengan lebih spesifik, atau kirimkan link produknya.',
            'found_from_search' => false,
        ];
    }

    /**
     * Get product recommendations
     */
    private function getProductRecommendations(int $sellerId, int $customerId, int $limit = 5): array
    {
        // Log start process
        Log::info('Starting product recommendations process', [
            'seller_id' => $sellerId,
            'customer_id' => $customerId,
            'limit' => $limit,
        ]);

        try {
            // Query langsung dari Product dengan filter seller_id
            $products = Product::where('seller_id', $sellerId)
                ->where('status', 1)
                ->with(['category', 'stock'])
                ->orderByDesc('price')
                ->limit($limit)
                ->get();

            Log::info('Products query executed', [
                'seller_id' => $sellerId,
                'products_found' => $products->count(),
                'limit' => $limit,
            ]);

            if ($products->isEmpty()) {
                Log::warning('No products found for recommendations', [
                    'seller_id' => $sellerId,
                    'customer_id' => $customerId,
                ]);

                return [
                    'type' => 'product_recommendations',
                    'recommendations' => [],
                    'total' => 0,
                    'message_hint' => 'Belum ada produk yang bisa direkomendasikan.',
                ];
            }

            $recommendations = [];
            $outOfStockCount = 0;

            foreach ($products as $product) {
                $totalStock = $this->calculateProductStock($product);
                $finalPrice = $this->calculateFinalPrice($product);

                Log::debug('Processing product', [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'total_stock' => $totalStock,
                    'final_price' => $finalPrice,
                ]);

                if ($totalStock > 0) {
                    // Hanya recommend yang ready stock
                    $recommendations[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $finalPrice,
                        'formatted_price' => 'Rp ' . number_format($finalPrice, 0, ',', '.'),
                        'category' => $product->category->name ?? 'Umum',
                        'stock' => $totalStock,
                        'reason' => 'Produk terlaris dan berkualitas',
                    ];

                    Log::debug('Product added to recommendations', [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                    ]);
                } else {
                    $outOfStockCount++;
                    Log::debug('Product skipped - out of stock', [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                    ]);
                }
            }

            $result = [
                'type' => 'product_recommendations',
                'recommendations' => $recommendations,
                'total' => count($recommendations),
                'message_hint' => 'Berikut rekomendasi produk terbaik dari toko kami:',
            ];

            Log::info('Product recommendations completed successfully', [
                'seller_id' => $sellerId,
                'customer_id' => $customerId,
                'total_products_processed' => $products->count(),
                'recommendations_count' => count($recommendations),
                'out_of_stock_count' => $outOfStockCount,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Error in getProductRecommendations', [
                'seller_id' => $sellerId,
                'customer_id' => $customerId,
                'limit' => $limit,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);

            return [
                'type' => 'product_recommendations',
                'recommendations' => [],
                'total' => 0,
                'message_hint' => 'Terjadi kesalahan saat mengambil rekomendasi produk.',
            ];
        }
    }

    /**
     * Get knowledge base information
     */
    private function getKnowledgeBaseInfo(string $intent): array
    {
        $info = $this->knowledgeBase[$intent] ?? null;

        if ($info) {
            return [
                'type' => 'knowledge_base',
                'title' => $info['title'],
                'content' => $info['content'],
                'message_hint' => $info['content'],
            ];
        }

        return [
            'type' => 'knowledge_not_found',
            'message_hint' => 'Mohon maaf, informasi yang Anda cari tidak tersedia. Silakan hubungi langsung untuk bantuan lebih lanjut.',
        ];
    }

    /**
     * Get seller contact information
     */
    private function getSellerContactInfo(int $sellerId): array
    {
        $seller = Seller::with('sellerShop')->find($sellerId);

        if ($seller) {
            return [
                'type' => 'seller_contact',
                'seller' => [
                    'name' => $seller->sellerShop->name,
                    'shop_name' => $seller->sellerShop->shop_name ?? 'Toko',
                    'phone' => $seller->phone ?? 'Tidak tersedia',
                    'whatsapp' => $seller->whatsapp_number ?? ($seller->phone ?? 'Tidak tersedia'),
                ],
                'message_hint' => 'Untuk bantuan lebih lanjut, Anda dapat menghubungi penjual langsung:',
            ];
        }

        return [
            'type' => 'contact_not_found',
            'message_hint' => 'Mohon maaf, informasi kontak tidak tersedia saat ini.',
        ];
    }

    /**
     * Get basic seller information
     */
    private function getSellerInfo(int $sellerId): array
    {
        $seller = Seller::with('sellerShop')->find($sellerId);

        if ($seller) {
            return [
                'id' => $seller->id,
                'name' => $seller->sellerShop->name,
                'shop_name' => $seller->sellerShop->name ?? 'Toko Online',
                'description' => $seller->sellerShop->short_details ?? '',
                'phone' => $seller->phone ?? null,
                'whatsapp' => $seller->sellerShop->whatsapp_number ?? ($seller->sellerShop->phone ?? null),
            ];
        }

        return [
            'id' => $sellerId,
            'name' => 'Penjual',
            'shop_name' => 'Toko Online',
        ];
    }

    /**
     * Calculate product stock from various sources
     */
    private function calculateProductStock($product): int
    {
        try {
            // Pastikan relasi stock sudah di-load
            if (!$product->relationLoaded('stock')) {
                $product->load('stock');
            }

            // Jika ada relasi stock (ProductStock) dan memiliki data
            if ($product->stock && $product->stock->count() > 0) {
                $totalStock = $product->stock->sum('qty');

                return max(0, $totalStock);
            }

            // Fallback: jika tidak ada ProductStock records
            Log::warning('No ProductStock records found, using fallback', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'maximum_purchase_qty' => $product->maximum_purchase_qty ?? 0,
            ]);

            // Fallback ke maximum_purchase_qty jika tidak ada stock relation
            return max(0, $product->maximum_purchase_qty ?? 0);
        } catch (\Exception $e) {
            Log::error('Error calculating product stock', [
                'product_id' => $product->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return 0;
        }
    }

    /**
     * Get detailed stock information with attributes breakdown
     */
    private function getDetailedStockInfo($product): array
    {
        try {
            if (!$product->relationLoaded('stock')) {
                $product->load('stock');
            }

            $stockDetails = [];
            $totalStock = 0;

            if ($product->stock && $product->stock->count() > 0) {
                foreach ($product->stock as $stockItem) {
                    $qty = max(0, $stockItem->qty);
                    $totalStock += $qty;

                    $stockDetails[] = [
                        'attributes' => $stockItem->attributes ?? 'Default',
                        'qty' => $qty,
                        'status' => $qty > 0 ? 'Tersedia' : 'Habis',
                    ];
                }
            }

            return [
                'total_stock' => $totalStock,
                'stock_details' => $stockDetails,
                'has_variants' => count($stockDetails) > 1,
            ];
        } catch (\Exception $e) {
            Log::error('Error getting detailed stock info', [
                'product_id' => $product->id ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return [
                'total_stock' => 0,
                'stock_details' => [],
                'has_variants' => false,
            ];
        }
    }

    /**
     * Get detailed product information
     */
    private function getProductDetailInfo(int $sellerId, ?int $productId = null, ?string $originalMessage = null): array
    {
        if ($productId) {
            $product = Product::where('id', $productId)
                ->where('seller_id', $sellerId)
                ->with(['category', 'brand', 'stock'])
                ->first();

            if ($product) {
                $finalPrice = $this->calculateFinalPrice($product);
                $stockInfo = $this->getDetailedStockInfo($product);

                return [
                    'type' => 'product_details',
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'description' => $this->parseProductDescriptionAdvanced($product->description) ?? 'Deskripsi tidak tersedia',
                        'category' => $product->category->name ?? 'Umum',
                        'brand' => $product->brand->name ?? 'Brand tidak tersedia',
                        'price' => $finalPrice,
                        'formatted_price' => 'Rp ' . number_format($finalPrice, 0, ',', '.'),
                        'stock' => $stockInfo['total_stock'],
                        'specifications' => $product->specifications ?? 'Spesifikasi tidak tersedia',
                        'features' => $product->features ?? 'Fitur tidak tersedia',
                    ],
                    'message_hint' => 'Berikut detail lengkap produk yang Anda tanyakan:',
                    'found_from_search' => true,
                ];
            }
        }

        $searchHint = $originalMessage ? "Mohon maaf, saya tidak dapat menemukan produk yang Anda maksud dari pesan '{$originalMessage}'. " : '';

        return [
            'type' => 'product_details_failed',
            'message_hint' => $searchHint . 'Mohon sebutkan nama produk dengan lebih spesifik.',
            'found_from_search' => false,
        ];
    }

    /**
     * Get product comparison information
     */
    private function getProductComparisonInfo(int $sellerId, ?string $message = null): array
    {
        if ($message) {
            $productNames = $this->extractMultipleProductNames($message);

            if (count($productNames) >= 2) {
                $products = [];

                foreach ($productNames as $productName) {
                    $productId = $this->findProductFromMessage($productName, $sellerId);
                    if ($productId) {
                        $product = Product::where('id', $productId)
                            ->where('seller_id', $sellerId)
                            ->with(['category', 'brand', 'stock'])
                            ->first();

                        if ($product) {
                            $finalPrice = $this->calculateFinalPrice($product);
                            $stockInfo = $this->getDetailedStockInfo($product);

                            $products[] = [
                                'id' => $product->id,
                                'name' => $product->name,
                                'price' => $finalPrice,
                                'formatted_price' => 'Rp ' . number_format($finalPrice, 0, ',', '.'),
                                'category' => $product->category->name ?? 'Umum',
                                'brand' => $product->brand->name ?? 'Brand tidak tersedia',
                                'stock' => $stockInfo['total_stock'],
                                'description' => $this->parseProductDescriptionAdvanced($product->description) ?? 'Deskripsi tidak tersedia',
                            ];
                        }
                    }
                }

                if (count($products) >= 2) {
                    return [
                        'type' => 'product_comparison',
                        'products' => $products,
                        'message_hint' => 'Berikut perbandingan produk yang Anda minta:',
                        'comparison_count' => count($products),
                    ];
                }
            }
        }

        return [
            'type' => 'product_comparison_failed',
            'message_hint' => 'Mohon sebutkan minimal 2 nama produk yang ingin Anda bandingkan dengan lebih spesifik.',
            'found_from_search' => false,
        ];
    }

    /**
     * Get product advantages information
     */
    private function getProductAdvantagesInfo(int $sellerId, ?int $productId = null, ?string $originalMessage = null): array
    {
        if ($productId) {
            $product = Product::where('id', $productId)
                ->where('seller_id', $sellerId)
                ->with(['category', 'brand'])
                ->first();

            if ($product) {
                $finalPrice = $this->calculateFinalPrice($product);

                return [
                    'type' => 'product_advantages',
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $finalPrice,
                        'formatted_price' => 'Rp ' . number_format($finalPrice, 0, ',', '.'),
                        'category' => $product->category->name ?? 'Umum',
                        'brand' => $product->brand->name ?? 'Brand tidak tersedia',
                    ],
                    'message_hint' => 'Berikut keunggulan produk yang Anda tanyakan:',
                    'found_from_search' => true,
                ];
            }
        }

        $searchHint = $originalMessage ? "Mohon maaf, saya tidak dapat menemukan produk yang Anda maksud dari pesan '{$originalMessage}'. " : '';

        return [
            'type' => 'product_advantages_failed',
            'message_hint' => $searchHint . 'Mohon sebutkan nama produk dengan lebih spesifik.',
            'found_from_search' => false,
        ];
    }

    /**
     * Get popular products information
     */
    private function getPopularProductsInfo(int $sellerId, int $limit = 5): array
    {
        try {
            $products = Product::where('seller_id', $sellerId)
                ->where('status', 1)
                ->where('best_selling_item_status', 2)
                ->with(['category', 'stock'])
                ->limit($limit)
                ->get();

            Log::info('products', [
                'seller_id' => $sellerId,
                'error' => $e->getMessage(),
            ]);

            if ($products->isEmpty()) {
                return [
                    'type' => 'product_popularity',
                    'popular_products' => [],
                    'total' => 0,
                    'message_hint' => 'Belum ada data produk populer yang tersedia.',
                ];
            }

            $popularProducts = [];
            foreach ($products as $product) {
                $totalStock = $this->calculateProductStock($product);
                $finalPrice = $this->calculateFinalPrice($product);

                if ($totalStock > 0) {
                    $popularProducts[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $finalPrice,
                        'formatted_price' => 'Rp ' . number_format($finalPrice, 0, ',', '.'),
                        'category' => $product->category->name ?? 'Umum',
                        'stock' => $totalStock,
                    ];
                }
            }

            return [
                'type' => 'product_popularity',
                'popular_products' => $popularProducts,
                'total' => count($popularProducts),
                'message_hint' => 'Berikut adalah produk-produk terpopuler di toko kami:',
            ];
        } catch (\Exception $e) {
            Log::error('Error getting popular products', [
                'seller_id' => $sellerId,
                'error' => $e->getMessage(),
            ]);

            return [
                'type' => 'product_popularity',
                'popular_products' => [],
                'total' => 0,
                'message_hint' => 'Terjadi kesalahan saat mengambil data produk populer.',
            ];
        }
    }

    /**
     * Extract multiple product names from comparison message
     */
    private function extractMultipleProductNames(string $message): array
    {
        $patterns = ['/bandingkan\s+(.+?)\s+(?:dan|dengan|vs)\s+(.+)/i', '/perbandingan\s+(?:antara\s+)?(.+?)\s+(?:dan|dengan|vs)\s+(.+)/i', '/(.+?)\s+vs\s+(.+)/i', '/perbedaan\s+(.+?)\s+(?:dan|dengan)\s+(.+)/i'];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                return [trim($matches[1]), trim($matches[2])];
            }
        }

        $separators = ['dan', 'dengan', 'vs', 'versus'];
        foreach ($separators as $separator) {
            if (strpos($message, $separator) !== false) {
                $parts = explode($separator, $message);
                if (count($parts) >= 2) {
                    return [trim($parts[0]), trim($parts[1])];
                }
            }
        }

        return [];
    }

    /**
     * Fungsi alternatif dengan lebih banyak kontrol untuk cleaning
     */
    function parseProductDescriptionAdvanced($htmlDescription) {
        if (empty($htmlDescription)) {
            return '';
        }

        $decoded = html_entity_decode($htmlDescription, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $decoded = preg_replace('/<br\s*\/?>/i', ' ', $decoded);

        $cleanText = strip_tags($decoded);

        $cleanText = preg_replace('/[\s\t\n\r]+/', ' ', $cleanText);

        $cleanText = trim($cleanText);

        return $cleanText;
    }

    /**
     * Generate context prompt untuk AI
     */
    public function generateContextPrompt(array $context, string $intent): string
    {
        $prompt = '';

        switch ($context['type'] ?? '') {
            case 'product_listing':
                $prompt = "=== DAFTAR PRODUK ===\n";
                $prompt .= 'Total produk tersedia: ' . ($context['total_products'] ?? 0) . "\n\n";

                if (isset($context['products']) && is_array($context['products']) && count($context['products']) > 0) {
                    foreach ($context['products'] as $product) {
                        $prompt .= "• {$product['name']}\n";
                        $prompt .= "  Harga: {$product['formatted_price']}\n";
                        $prompt .= "  Stok: {$product['stock']} unit ({$product['status']})\n";
                        $prompt .= "  Kategori: {$product['category']}\n\n";
                    }
                } else {
                    $prompt .= "Belum ada produk yang tersedia atau produk sedang dalam proses update.\n";
                }
                break;

            case 'price_info':
                if (isset($context['product'])) {
                    $product = $context['product'];
                    $prompt = "=== INFORMASI HARGA ===\n";
                    $prompt .= "Produk: {$product['name']}\n";
                    $prompt .= "Harga: {$product['formatted_price']}\n";
                    if (($product['discount'] ?? 0) > 0) {
                        $prompt .= 'Harga Asli: Rp ' . number_format($product['original_price'], 0, ',', '.') . "\n";
                        $prompt .= 'Diskon: Rp ' . number_format($product['discount'], 0, ',', '.') . "\n";
                        if (($product['discount_percentage'] ?? 0) > 0) {
                            $prompt .= "Diskon: {$product['discount_percentage']}%\n";
                        }
                    }
                }
                break;

            case 'stock_info':
                if (isset($context['product'])) {
                    $product = $context['product'];
                    $prompt = "=== INFORMASI STOK ===\n";
                    $prompt .= "Produk: {$product['name']}\n";
                    $prompt .= "{$product['stock_message']}\n";

                    if (($product['has_variants'] ?? false) && isset($context['product']['variants'])) {
                        $prompt .= "\nDetail Stok per Varian:\n";
                        foreach ($context['product']['variants'] as $variant) {
                            $prompt .= "• {$variant['attributes']}: {$variant['qty']} unit ({$variant['status']})\n";
                        }
                    }
                }
                break;

            case 'product_recommendations':
                $prompt = "=== REKOMENDASI PRODUK ===\n";
                $prompt .= 'Total rekomendasi: ' . ($context['total'] ?? 0) . "\n\n";

                if (isset($context['recommendations']) && is_array($context['recommendations']) && count($context['recommendations']) > 0) {
                    foreach ($context['recommendations'] as $index => $rec) {
                        $prompt .= $index + 1 . ". {$rec['name']}\n";
                        $prompt .= "   Harga: {$rec['formatted_price']}\n";
                        $prompt .= "   Kategori: {$rec['category']}\n";
                        $prompt .= "   Stok: {$rec['stock']} unit\n";
                        $prompt .= "   Alasan: {$rec['reason']}\n\n";
                    }
                } else {
                    $prompt .= "Belum ada rekomendasi produk yang tersedia.\n";
                }
                break;

            case 'product_details':
                if (isset($context['product'])) {
                    $product = $context['product'];
                    $prompt = "=== DETAIL PRODUK ===\n";
                    $prompt .= "Nama: {$product['name']}\n";
                    $prompt .= "Harga: {$product['formatted_price']}\n";
                    $prompt .= "Kategori: {$product['category']}\n";
                    $prompt .= "Brand: {$product['brand']}\n";
                    $prompt .= "Stok: {$product['stock']} unit\n";
                    $prompt .= "Deskripsi: {$product['description']}\n";
                }
                break;

            case 'product_comparison':
                $prompt = "=== PERBANDINGAN PRODUK ===\n";
                $prompt .= 'Jumlah produk dibandingkan: ' . ($context['comparison_count'] ?? 0) . "\n\n";

                if (isset($context['products']) && is_array($context['products'])) {
                    foreach ($context['products'] as $index => $product) {
                        $prompt .= 'PRODUK ' . ($index + 1) . ":\n";
                        $prompt .= "• Nama: {$product['name']}\n";
                        $prompt .= "• Harga: {$product['formatted_price']}\n";
                        $prompt .= "• Brand: {$product['brand']}\n";
                        $prompt .= "• Kategori: {$product['category']}\n";
                        $prompt .= "• Stok: {$product['stock']} unit\n";
                        $prompt .= "• Deskripsi: {$product['description']}\n\n";
                    }
                }
                break;

            case 'product_advantages':
                if (isset($context['product'])) {
                    $product = $context['product'];
                    $prompt = "=== KEUNGGULAN PRODUK ===\n";
                    $prompt .= "Produk: {$product['name']}\n";
                    $prompt .= "Harga: {$product['formatted_price']}\n";
                    $prompt .= "Brand: {$product['brand']}\n";
                    $prompt .= "Kategori: {$product['category']}\n\n";
                    $prompt .= "Keunggulan produk ini akan dijelaskan berdasarkan kategori dan brand-nya.\n";
                }
                break;

            case 'product_popularity':
                $prompt = "=== PRODUK POPULER ===\n";
                $prompt .= 'Total produk populer: ' . ($context['total'] ?? 0) . "\n\n";

                if (isset($context['popular_products']) && is_array($context['popular_products']) && count($context['popular_products']) > 0) {
                    foreach ($context['popular_products'] as $product) {
                        $prompt .= "   Harga: {$product['formatted_price']}\n";
                        $prompt .= "   Kategori: {$product['category']}\n";
                        $prompt .= "   Stok: {$product['stock']} unit\n";
                        $prompt .= "\n";
                    }
                } else {
                    $prompt .= "Belum ada data produk populer yang tersedia.\n";
                }
                break;

            case 'knowledge_base':
                $prompt = '=== ' . ($context['title'] ?? 'INFORMASI') . " ===\n";
                $prompt .= ($context['content'] ?? 'Informasi tidak tersedia.') . "\n";
                break;

            case 'seller_contact':
                if (isset($context['seller'])) {
                    $seller = $context['seller'];
                    $prompt = "=== KONTAK PENJUAL ===\n";
                    $prompt .= "Nama Toko: {$seller['shop_name']}\n";
                    $prompt .= "Penjual: {$seller['name']}\n";
                    if (isset($seller['phone']) && $seller['phone'] !== 'Tidak tersedia') {
                        $prompt .= "Telepon: {$seller['phone']}\n";
                    }
                    if (isset($seller['whatsapp']) && $seller['whatsapp'] !== 'Tidak tersedia') {
                        $prompt .= "WhatsApp: {$seller['whatsapp']}\n";
                    }
                }
                break;

            case 'seller_not_specified':
                $prompt = "=== TOKO TIDAK DITENTUKAN ===\n";
                $prompt .= ($context['message_hint'] ?? 'Toko tidak ditentukan.') . "\n";
                if (isset($context['available_actions']) && is_array($context['available_actions'])) {
                    $prompt .= "\nSaran:\n";
                    foreach ($context['available_actions'] as $action) {
                        $prompt .= "• {$action}\n";
                    }
                }
                break;

            case 'seller_not_found':
                $prompt = "=== TOKO TIDAK DITEMUKAN ===\n";
                $prompt .= ($context['message_hint'] ?? 'Toko tidak ditemukan.') . "\n";
                if (isset($context['seller_id'])) {
                    $prompt .= "ID Toko yang dicari: {$context['seller_id']}\n";
                }
                break;

            case 'available_sellers':
                $prompt = "=== DAFTAR TOKO TERSEDIA ===\n";
                $prompt .= 'Total toko: ' . ($context['total'] ?? 0) . "\n\n";

                if (isset($context['sellers']) && is_array($context['sellers']) && count($context['sellers']) > 0) {
                    foreach ($context['sellers'] as $seller) {
                        $prompt .= "• {$seller['shop_name']} (ID: {$seller['id']})\n";
                        $prompt .= "  Penjual: {$seller['name']}\n";
                        if (!empty($seller['description'])) {
                            $prompt .= "  Deskripsi: {$seller['description']}\n";
                        }
                        $prompt .= "\n";
                    }
                }
                break;

            case 'system_error':
                $prompt = "=== KESALAHAN SISTEM ===\n";
                $prompt .= ($context['message_hint'] ?? 'Terjadi kesalahan sistem.') . "\n";
                break;

            case 'price_inquiry_failed':
            case 'stock_inquiry_failed':
            case 'product_details_failed':
            case 'product_comparison_failed':
            case 'product_advantages_failed':
            case 'knowledge_not_found':
            case 'contact_not_found':
                $prompt = "=== INFORMASI ===\n";
                $prompt .= ($context['message_hint'] ?? 'Informasi tidak tersedia.') . "\n";
                break;

            default:
                $prompt = "=== INFORMASI ===\n";
                $prompt .= "Mohon maaf, saya tidak dapat menemukan informasi yang Anda cari.\n";

                if (isset($context['seller_info'])) {
                    $seller = $context['seller_info'];
                    $prompt .= "\nUntuk bantuan lebih lanjut, Anda dapat menghubungi:\n";
                    $prompt .= "Toko: {$seller['shop_name']}\n";
                    if (isset($seller['whatsapp']) && $seller['whatsapp']) {
                        $prompt .= "WhatsApp: {$seller['whatsapp']}\n";
                    }
                }
                break;
        }

        if (isset($context['seller_info']) && !in_array($context['type'] ?? '', ['seller_contact', 'contact_not_found'])) {
            $seller = $context['seller_info'];
            $prompt .= "\n=== INFORMASI TOKO ===\n";
            $prompt .= "Toko: {$seller['shop_name']}\n";
            if (isset($seller['description']) && !empty($seller['description'])) {
                $prompt .= "Deskripsi: {$seller['description']}\n";
            }
            if (isset($seller['whatsapp']) && $seller['whatsapp']) {
                $prompt .= "WhatsApp: {$seller['whatsapp']}\n";
            }
        }

        return $prompt;
    }
}
