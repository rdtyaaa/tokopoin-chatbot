<?php

namespace App\Http\Services\Conversation;

use App\Models\Product;
use App\Models\Seller;
use Illuminate\Support\Facades\Log;

class IntentDetectionService
{
    private $intents = [
        'product_popularity' => [
            'keywords' => ['populer', 'terlaris', 'bestseller', 'best seller', 'favorit', 'trending', 'paling laku', 'best', 'top', 'unggulan'],
            'patterns' => ['/produk.*(populer|terlaris|best.*seller|favorit|trending|paling.*laku|unggulan)/i', '/(best.*seller|terlaris|populer).*produk/i', '/.*best.*seller.*apa/i', '/produk.*top/i', '/yang.*paling.*laku/i'],
            'confidence_threshold' => 70,
            'priority' => 10,
        ],
        'product_recommendation' => [
            'keywords' => ['rekomendasi', 'sarankan', 'cocok', 'bagus', 'terbaik', 'pilih', 'saran'],
            'patterns' => ['/rekomendasi.*produk/i', '/produk.*(cocok|bagus|terbaik)/i', '/yang.*bagus/i', '/sarankan.*produk/i', '/produk.*yang.*direkomendasikan/i'],
            'confidence_threshold' => 70,
            'priority' => 9,
        ],
        'product_comparison' => [
            'keywords' => ['bandingkan', 'perbandingan', 'compare', 'vs', 'versus', 'beda', 'perbedaan'],
            'patterns' => ['/bandingkan.*produk/i', '/perbandingan.*antara/i', '/.*vs.*/i', '/perbedaan.*antara/i', '/mana.*yang.*lebih/i'],
            'confidence_threshold' => 75,
            'priority' => 9,
        ],
        'product_advantages' => [
            'keywords' => ['keunggulan', 'kelebihan', 'unggul', 'advantage', 'dibanding', 'vs kompetitor'],
            'patterns' => ['/keunggulan.*produk/i', '/kelebihan.*dibanding/i', '/unggul.*dari/i', '/kenapa.*pilih/i'],
            'confidence_threshold' => 70,
            'priority' => 8,
        ],
        'product_details' => [
            'keywords' => ['spesifikasi', 'detail', 'lengkap', 'spec', 'informasi detail', 'fitur', 'spek'],
            'patterns' => ['/spesifikasi.*lengkap/i', '/detail.*produk/i', '/info.*lengkap/i', '/fitur.*apa/i', '/spek.*produk/i'],
            'confidence_threshold' => 70,
            'priority' => 8,
        ],
        'price_inquiry' => [
            'keywords' => ['harga', 'berapa', 'biaya', 'ongkos', 'tarif', 'cost', 'mahal'],
            'patterns' => ['/berapa.*harga/i', '/harga.*berapa/i', '/biaya.*berapa/i', '/berapa.*biaya/i', '/harga.*produk/i'],
            'confidence_threshold' => 75,
            'priority' => 8,
        ],
        'stock_availability' => [
            'keywords' => ['stok', 'stock', 'tersedia', 'ada', 'habis', 'kosong', 'ready', 'masih ada', 'available', 'ketersediaan'],
            'patterns' => ['/.*masih.*ada/i', '/.*masih.*tersedia/i', '/.*tersedia.*tidak/i', '/.*tersedia.*\?/i', '/apakah.*tersedia/i', '/.*ada.*stok/i', '/stok.*tersedia/i', '/stok.*habis/i', '/ready.*stock/i', '/available.*\?/i', '/ketersediaan.*produk/i'],
            'confidence_threshold' => 70,
            'priority' => 8,
        ],
        'order_process' => [
            'keywords' => ['order', 'pesan', 'beli', 'checkout', 'cara beli', 'gimana beli', 'pemesanan'],
            'patterns' => ['/cara.*(order|pesan|beli)/i', '/gimana.*(beli|order|pesan)/i', '/proses.*pemesanan/i', '/mau.*beli/i'],
            'confidence_threshold' => 75,
            'priority' => 7,
        ],
        'payment_method' => [
            'keywords' => ['bayar', 'pembayaran', 'transfer', 'cod', 'cash', 'kartu', 'metode bayar'],
            'patterns' => ['/cara.*bayar/i', '/metode.*pembayaran/i', '/bisa.*cod/i', '/sistem.*pembayaran/i'],
            'confidence_threshold' => 75,
            'priority' => 7,
        ],
        'payment_options' => [
            'keywords' => ['cod', 'cash on delivery', 'bayar ditempat', 'daerah', 'wilayah'],
            'patterns' => ['/cod.*daerah/i', '/bayar.*ditempat/i', '/tersedia.*cod/i', '/cod.*area/i'],
            'confidence_threshold' => 75,
            'priority' => 7,
        ],
        'shipping_info' => [
            'keywords' => ['kirim', 'pengiriman', 'ongkir', 'ekspedisi', 'kurir', 'delivery'],
            'patterns' => ['/berapa.*ongkir/i', '/lama.*pengiriman/i', '/pakai.*ekspedisi/i', '/biaya.*kirim/i'],
            'confidence_threshold' => 70,
            'priority' => 6,
        ],
        'delivery_options' => [
            'keywords' => ['ekspedisi', 'kurir', 'delivery', 'pilihan kirim', 'jasa pengiriman', 'courier'],
            'patterns' => ['/pilihan.*ekspedisi/i', '/jasa.*pengiriman/i', '/ekspedisi.*tersedia/i', '/pakai.*kurir/i'],
            'confidence_threshold' => 70,
            'priority' => 6,
        ],
        'tracking_info' => [
            'keywords' => ['lacak', 'tracking', 'track', 'cek pesanan', 'status pesanan', 'resi'],
            'patterns' => ['/cara.*lacak/i', '/tracking.*pesanan/i', '/cek.*status/i', '/nomor.*resi/i'],
            'confidence_threshold' => 75,
            'priority' => 6,
        ],
        'return_policy' => [
            'keywords' => ['return', 'refund', 'tukar', 'kembali', 'garansi', 'komplain'],
            'patterns' => ['/bisa.*return/i', '/policy.*return/i', '/garansi.*berapa/i', '/tukar.*barang/i'],
            'confidence_threshold' => 75,
            'priority' => 6,
        ],
        'warranty_info' => [
            'keywords' => ['garansi', 'warranty', 'jaminan', 'ketentuan garansi'],
            'patterns' => ['/garansi.*produk/i', '/ketentuan.*garansi/i', '/berapa.*garansi/i', '/ada.*garansi/i'],
            'confidence_threshold' => 75,
            'priority' => 6,
        ],
        'customer_service' => [
            'keywords' => ['komplain', 'masalah', 'prosedur', 'laporan', 'keluhan', 'customer service'],
            'patterns' => ['/cara.*komplain/i', '/prosedur.*keluhan/i', '/ada.*masalah/i', '/customer.*service/i'],
            'confidence_threshold' => 70,
            'priority' => 5,
        ],
        'greeting' => [
            'keywords' => ['halo', 'hai', 'selamat', 'pagi', 'siang', 'malam', 'hi', 'hello'],
            'patterns' => ['/^(halo|hai|hi|hello)/i', '/selamat.*(pagi|siang|malam)/i', '/^(pagi|siang|malam)$/i'],
            'confidence_threshold' => 85,
            'priority' => 4,
        ],
        'appreciation' => [
            'keywords' => ['terima kasih', 'thanks', 'makasih', 'thx', 'thank you'],
            'patterns' => ['/terima.*kasih/i', '/makasih/i', '/thanks/i', '/thank.*you/i'],
            'confidence_threshold' => 80,
            'priority' => 4,
        ],
        'clarification' => [
            'keywords' => ['tidak mengerti', 'ga ngerti', 'bingung', 'maksud', 'jelaskan', 'kurang jelas'],
            'patterns' => ['/tidak.*mengerti/i', '/ga.*ngerti/i', '/jelaskan.*lagi/i', '/kurang.*jelas/i', '/maksudnya.*apa/i'],
            'confidence_threshold' => 70,
            'priority' => 3,
        ],
        'topic_transition' => [
            'keywords' => ['bertanya lain', 'hal lain', 'topik lain', 'ganti topik', 'yang lain'],
            'patterns' => ['/bertanya.*lain/i', '/hal.*lain/i', '/topik.*lain/i', '/ganti.*topik/i'],
            'confidence_threshold' => 65,
            'priority' => 2,
        ],
        'general_help' => [
            'keywords' => ['bantuan', 'bantu', 'help', 'tolong', 'bingung'],
            'patterns' => ['/butuh.*bantuan/i', '/tolong.*bantu/i', '/saya.*bingung/i', '/minta.*bantuan/i'],
            'confidence_threshold' => 65,
            'priority' => 2,
        ],
        'product_listing' => [
            'keywords' => ['produk apa saja', 'barang apa', 'jual apa', 'katalog', 'daftar produk', 'list produk'],
            'patterns' => ['/apa.*saja.*produk/i', '/produk.*apa.*saja/i', '/barang.*apa.*saja/i', '/daftar.*produk/i', '/katalog.*produk/i', '/ada.*produk.*apa.*saja/i', '/list.*produk/i', '/semua.*produk/i'],
            'confidence_threshold' => 65,
            'priority' => 1,
        ],
    ];

    /**
     * Deteksi intent dari pesan dengan konteks produk
     */
    public function detectIntentWithProductContext(string $message, int $sellerId, ?int $foundProductId = null): array
    {
        $message = strtolower(trim($message));
        $scores = [];

        if (!$foundProductId) {
            $foundProductId = $this->findProductFromMessage($message, $sellerId);
        }

        $hasProductMention = !is_null($foundProductId);

        $sortedIntents = $this->intents;
        uasort($sortedIntents, function($a, $b) {
            return ($b['priority'] ?? 0) <=> ($a['priority'] ?? 0);
        });

        foreach ($sortedIntents as $intent => $config) {
            $score = 0;
            $priority = $config['priority'] ?? 0;

            $score += $this->calculateBaseScore($message, $config, $priority);

            $score = $this->applyProductContextModifier($score, $intent, $hasProductMention);

            $scores[$intent] = $score;
        }

        arsort($scores);

        $topIntent = key($scores);
        $confidence = $scores[$topIntent];

        Log::info('Intent Detection', [
            'message' => $message,
            'seller_id' => $sellerId,
            'found_product_id' => $foundProductId,
            'has_product_mention' => $hasProductMention,
            'top_intent' => $topIntent,
            'confidence' => $confidence,
            'top_3_scores' => array_slice($scores, 0, 3, true)
        ]);

        if ($confidence >= $this->intents[$topIntent]['confidence_threshold']) {
            return [
                'intent' => $topIntent,
                'confidence' => $confidence,
                'all_scores' => $scores,
                'threshold_met' => true,
                'product_id' => $foundProductId,
                'has_product_context' => $hasProductMention,
            ];
        }

        return [
            'intent' => $topIntent,
            'confidence' => $confidence,
            'all_scores' => $scores,
            'below_threshold' => true,
            'product_id' => $foundProductId,
            'has_product_context' => $hasProductMention,
        ];
    }

    /**
     * Hitung score dasar dari keywords dan patterns
     */
    private function calculateBaseScore(string $message, array $config, int $priority): float
    {
        $score = 0;

        foreach ($config['keywords'] as $keyword) {
            $keyword = strtolower($keyword);

            if ($message === $keyword) {
                $score += 50 + ($priority * 2);
                continue;
            }

            $pos = strpos($message, $keyword);
            if ($pos !== false) {
                $score += 25 + ($priority * 1.5);

                if ($pos === 0) {
                    $score += 15;
                }

                if ($this->isCompleteWord($message, $keyword, $pos)) {
                    $score += 10;
                }
            }
        }

        foreach ($config['patterns'] as $pattern) {
            if (preg_match($pattern, $message)) {
                $score += 40 + ($priority * 2);
            }
        }

        foreach ($config['keywords'] as $keyword) {
            $similarity = $this->calculateSimilarity(strtolower($keyword), $message);
            if ($similarity > 0.7) {
                $score += ($similarity * 15) + ($priority * 1);
            }
        }

        return $score;
    }

    /**
     * Aplikasikan modifier berdasarkan konteks produk
     */
    private function applyProductContextModifier(float $baseScore, string $intent, bool $hasProductMention): float
    {
        if (!$hasProductMention) {
            return $baseScore;
        }

        $productRelatedIntents = [
            'product_details' => 1.4,
            'price_inquiry' => 1.3,
            'stock_availability' => 1.3,
            'product_advantages' => 1.25,
            'product_comparison' => 1.2,
            'product_recommendation' => 1.15,
            'order_process' => 1.1,
            'warranty_info' => 1.1,
        ];

        $generalIntents = [
            'product_listing' => 0.7,
            'general_help' => 0.8,
            'greeting' => 0.9,
            'topic_transition' => 0.85,
        ];

        if (isset($productRelatedIntents[$intent])) {
            $modifier = $productRelatedIntents[$intent];
            Log::debug("Product context boost applied", [
                'intent' => $intent,
                'base_score' => $baseScore,
                'modifier' => $modifier,
                'new_score' => $baseScore * $modifier
            ]);
            return $baseScore * $modifier;
        }

        if (isset($generalIntents[$intent])) {
            $modifier = $generalIntents[$intent];
            Log::debug("Product context penalty applied", [
                'intent' => $intent,
                'base_score' => $baseScore,
                'modifier' => $modifier,
                'new_score' => $baseScore * $modifier
            ]);
            return $baseScore * $modifier;
        }

        return $baseScore;
    }

    /**
     * Cari produk dari pesan customer (dipindah dari RAGService)
     */
    private function findProductFromMessage(string $message, int $sellerId): ?int
    {
        Log::info('Searching product from message in IntentDetection', [
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
                Log::info('Product found from message in IntentDetection', [
                    'product_id' => $bestMatch->id,
                    'product_name' => $bestMatch->name,
                    'similarity_score' => $bestScore,
                    'search_term' => implode(', ', $searchTerms),
                ]);

                return $bestMatch->id;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error finding product from message in IntentDetection', [
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
        $stopWords = ['berapa', 'harga', 'stok', 'ada', 'tidak', 'produk', 'barang', 'item', 'tersedia', 'ready', 'stock', 'price', 'cost', 'beli', 'jual', 'dijual', 'bisa', 'dapat', 'mau', 'ingin', 'butuh', 'cari', 'lihat', 'tanya', 'info', 'informasi', 'detail', 'spesifikasi', 'spec', 'apa', 'apakah', 'bagaimana', 'dimana', 'kapan', 'kenapa', 'mengapa', 'siapa', 'yang', 'dan', 'atau', 'dengan', 'untuk', 'dari', 'ke', 'di', 'pada', 'dalam', 'luar', 'atas', 'bawah', 'depan', 'belakang', 'bandingkan', 'perbandingan', 'vs', 'versus', 'dibanding', 'keunggulan', 'kelebihan', 'unggul', 'populer', 'terlaris', 'bestseller', 'favorit'];

        $words = preg_split('/\s+/', strtolower($message));
        $filteredWords = array_filter($words, function ($word) use ($stopWords) {
            return strlen($word) >= 3 && !in_array($word, $stopWords);
        });

        $searchTerms = [];

        // Single words
        foreach ($filteredWords as $word) {
            if (strlen($word) >= 4) {
                $searchTerms[] = $word;
            }
        }

        // Kombinasi 2-3 kata berturut-turut
        $wordArray = array_values($filteredWords);
        for ($i = 0; $i < count($wordArray) - 1; $i++) {
            $searchTerms[] = $wordArray[$i] . ' ' . $wordArray[$i + 1];

            if (isset($wordArray[$i + 2])) {
                $searchTerms[] = $wordArray[$i] . ' ' . $wordArray[$i + 1] . ' ' . $wordArray[$i + 2];
            }
        }

        usort($searchTerms, function ($a, $b) {
            return strlen($b) - strlen($a);
        });

        return array_unique($searchTerms);
    }

    /**
     * Cek apakah keyword adalah kata lengkap (bukan bagian dari kata lain)
     */
    private function isCompleteWord(string $text, string $keyword, int $position): bool
    {
        $keywordLength = strlen($keyword);
        $textLength = strlen($text);

        // Cek karakter sebelum keyword
        $charBefore = $position > 0 ? $text[$position - 1] : ' ';

        $charAfter = ($position + $keywordLength < $textLength) ?
                    $text[$position + $keywordLength] : ' ';

        return !ctype_alnum($charBefore) && !ctype_alnum($charAfter);
    }

    /**
     * Hitung similarity antara dua string menggunakan algoritma yang lebih baik
     */
    private function calculateSimilarity(string $str1, string $str2): float
    {
        $len1 = strlen($str1);
        $len2 = strlen($str2);

        if ($len1 == 0) {
            return $len2 == 0 ? 1.0 : 0.0;
        }
        if ($len2 == 0) {
            return 0.0;
        }

        // Untuk string pendek, gunakan exact matching
        if ($len1 < 4 && $len2 < 4) {
            return $str1 === $str2 ? 1.0 : 0.0;
        }

        // Hitung Levenshtein distance
        $distance = levenshtein($str1, $str2);
        $maxLen = max($len1, $len2);

        return 1 - $distance / $maxLen;
    }

    /**
     * Get context untuk intent tertentu
     */
    public function getIntentContext(string $intent): array
    {
        $contexts = [
            'product_listing' => ['requires_seller_products' => true],
            'price_inquiry' => ['requires_product_data' => true],
            'stock_availability' => ['requires_product_data' => true],
            'product_recommendation' => ['requires_seller_products' => true, 'requires_user_preference' => true],
            'order_process' => ['requires_seller_info' => true],
            'payment_method' => ['requires_seller_info' => true],
            'shipping_info' => ['requires_seller_info' => true],
            'return_policy' => ['requires_seller_policy' => true],
            'greeting' => ['simple_response' => true],
            'appreciation' => ['simple_response' => true],
            'general_help' => ['requires_seller_contact' => true],
            'clarification' => ['requires_conversation_history' => true],
            'product_details' => ['requires_product_data' => true, 'requires_detailed_info' => true],
            'product_comparison' => ['requires_multiple_products' => true, 'requires_product_data' => true],
            'product_advantages' => ['requires_product_data' => true, 'requires_competitor_info' => true],
            'product_popularity' => ['requires_seller_products' => true, 'requires_sales_data' => true],
            'delivery_options' => ['requires_seller_info' => true, 'requires_shipping_data' => true],
            'tracking_info' => ['requires_order_data' => true, 'simple_response' => true],
            'payment_options' => ['requires_seller_info' => true, 'requires_location_data' => true],
            'warranty_info' => ['requires_seller_policy' => true],
            'customer_service' => ['requires_seller_contact' => true, 'simple_response' => true],
            'topic_transition' => ['simple_response' => true],
        ];

        return $contexts[$intent] ?? ['requires_seller_contact' => true];
    }
}
