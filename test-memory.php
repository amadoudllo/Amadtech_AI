<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Derniers messages ===\n";
$messages = DB::table('messages')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

foreach ($messages as $msg) {
    echo "ID: {$msg->id}, Conv: {$msg->conversation_id}, Role: {$msg->role}, Content: " . substr($msg->content, 0, 50) . "...\n";
}

echo "\n=== Conversations ===\n";
$conversations = DB::table('conversations')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

foreach ($conversations as $conv) {
    $count = DB::table('messages')->where('conversation_id', $conv->id)->count();
    echo "ID: {$conv->id}, Title: {$conv->title}, Messages: {$count}\n";
}
