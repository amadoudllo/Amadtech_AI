<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Groq Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour l'API Groq compatible OpenAI
    |
    */

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL', 'mixtral-8x7b-32768'),
        'base_url' => 'https://api.groq.com/openai/v1',
        'timeout' => 60,
        'retries' => 3,
        'retry_delay' => 2000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Chat Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration globale du système de chat
    |
    */

    'chat' => [
        // Nombre de messages à inclure dans le contexte (sliding window)
        'context_limit' => 10,

        // Température pour la génération (0-2, plus bas = plus déterministe)
        'temperature' => 0.7,

        // Top P (nucleus sampling)
        'top_p' => 1,

        // Nombre maximum de tokens pour la réponse
        'max_tokens' => 1024,

        // System prompt par défaut
        'system_prompt' => 'Tu es un assistant IA utile et bienveillant. Tu réponds en français. Tu es attentif, honnête et polis. Tu fournis des réponses précises et complètes.',

        // Longueur maximale du titre auto-généré
        'max_title_length' => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Limites de taux pour l'API Groq
    |
    */

    'rate_limiting' => [
        // Nombre de requêtes par minute
        'requests_per_minute' => 30,

        // Nombre de tokens par minute
        'tokens_per_minute' => 7900,
    ],
];
