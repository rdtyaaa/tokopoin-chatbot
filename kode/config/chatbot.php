<?php

return [
    'api_url' => env('CHATBOT_API_URL', 'https://dummy-ai-api.example.com/chat'),
    'timeout' => env('CHATBOT_TIMEOUT', 30),
    'default_delay' => env('CHATBOT_DEFAULT_DELAY', 5),
    'fallback_response' => 'Terima kasih atas pesan Anda. Seller akan segera membalas pesan Anda.',

    // TESTING CONFIG - Akan dihapus ketika AI Engine sudah jadi
    'use_ollama' => env('CHATBOT_USE_OLLAMA', false),
    'ollama_url' => env('CHATBOT_OLLAMA_URL', 'http://localhost:11434'),

];
