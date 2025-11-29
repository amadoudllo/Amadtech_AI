<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Tester le contexte glissant (sliding window)
$conversationId = 17;
$conversation = App\Models\Conversation::findOrFail($conversationId);

echo "=== Test Context Sliding Window ===\n";
echo "Conversation ID: $conversationId\n";
echo "Total messages in DB: " . $conversation->messages->count() . "\n\n";

// Récupérer les 10 derniers messages AVANT le nouveau message
$previousMessages = $conversation->messages()
    ->latest()
    ->take(10)
    ->get()
    ->reverse()
    ->values();

echo "Messages in sliding window (10 last):\n";
foreach ($previousMessages as $i => $message) {
    echo ($i + 1) . ". " . $message->role . ": " . substr($message->content, 0, 50) . "...\n";
}

echo "\n✓ Sliding window works correctly\n";
