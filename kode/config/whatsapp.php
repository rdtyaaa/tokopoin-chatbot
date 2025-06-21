<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fonnte WhatsApp Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Fonnte WhatsApp API integration
    |
    */

    'fonnte_token' => env('FONNTE_TOKEN', ''),
    'fonnte_device' => env('FONNTE_DEVICE', ''),

    /*
    |--------------------------------------------------------------------------
    | TokoPoin WhatsApp Business Number
    |--------------------------------------------------------------------------
    |
    | The main WhatsApp business number for TokoPoin
    |
    */
    'tokopoin_number' => env('TOKOPOIN_WHATSAPP_NUMBER', '6283865657318'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for webhook handling
    |
    */
    'webhook_url' => env('WHATSAPP_WEBHOOK_URL', ''),
    'webhook_secret' => env('WHATSAPP_WEBHOOK_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Default Messages
    |--------------------------------------------------------------------------
    |
    | Default messages for various scenarios
    |
    */
    'default_messages' => [
        'welcome' => 'Selamat datang di TokoPoin! 🛍️ Kami siap membantu Anda menemukan produk terbaik. Silakan kirim pesan tentang produk yang Anda cari.',
        'error' => 'Maaf, terjadi kesalahan sistem. Silakan coba lagi nanti atau hubungi admin kami.',
        'not_found' => 'Maaf, produk yang Anda cari tidak ditemukan. Silakan coba dengan kata kunci lain.',
        'processing' => 'Sedang memproses permintaan Anda... Mohon tunggu sebentar.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable/disable certain features
    |
    */
    'features' => [
        'auto_reply' => env('WHATSAPP_AUTO_REPLY', true),
        'log_conversations' => env('WHATSAPP_LOG_CONVERSATIONS', true),
        'media_support' => env('WHATSAPP_MEDIA_SUPPORT', false),
        'group_messages' => env('WHATSAPP_GROUP_MESSAGES', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Rate limiting configuration for WhatsApp API
    |
    */
    'rate_limit' => [
        'max_requests_per_minute' => env('WHATSAPP_RATE_LIMIT', 60),
        'max_requests_per_hour' => env('WHATSAPP_RATE_LIMIT_HOUR', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Timeout Configuration
    |--------------------------------------------------------------------------
    |
    | Timeout settings for various operations
    |
    */
    'timeouts' => [
        'api_request' => 30, // seconds
        'webhook_response' => 5, // seconds
        'chatbot_response' => 180, // seconds for AI processing
    ],
];
