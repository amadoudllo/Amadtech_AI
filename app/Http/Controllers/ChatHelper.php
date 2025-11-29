<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;

/**
 * Classe utilitaire pour tester et interagir avec le système de chat
 * 
 * Usage:
 * php artisan tinker
 * > ChatHelper::testChat('Bonjour!');
 * > ChatHelper::showConversations();
 * > ChatHelper::showConversationMessages(1);
 */
class ChatHelper
{
    /**
     * Créer une conversation de test
     */
    public static function createTestConversation()
    {
        $conversation = Conversation::create([
            'user_id' => null,
            'title' => 'Conversation de test',
        ]);

        return $conversation;
    }

    /**
     * Ajouter un message de test
     */
    public static function addTestMessage(int $conversationId, string $content, string $role = 'user')
    {
        $message = Message::create([
            'conversation_id' => $conversationId,
            'role' => $role,
            'content' => $content,
        ]);

        return $message;
    }

    /**
     * Afficher toutes les conversations
     */
    public static function showConversations()
    {
        $conversations = Conversation::with('messages')->get();

        foreach ($conversations as $conversation) {
            echo "📌 Conversation ID: {$conversation->id}\n";
            echo "   Title: " . ($conversation->title ?? 'N/A') . "\n";
            echo "   Messages: " . $conversation->messages->count() . "\n";
            echo "   Created: " . $conversation->created_at . "\n";
            echo "\n";
        }
    }

    /**
     * Afficher les messages d'une conversation
     */
    public static function showConversationMessages(int $conversationId)
    {
        $conversation = Conversation::with('messages')->findOrFail($conversationId);

        echo "📌 Conversation: {$conversation->title}\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

        foreach ($conversation->messages as $message) {
            $roleEmoji = $message->role === 'user' ? '👤' : '🤖';
            echo "{$roleEmoji} [{$message->role}] ({$message->created_at})\n";
            echo "   " . substr($message->content, 0, 100) . "...\n\n";
        }
    }

    /**
     * Afficher le contexte pour l'API (sliding window)
     */
    public static function showContextMessages(int $conversationId, int $limit = 10)
    {
        $conversation = Conversation::findOrFail($conversationId);
        $contextMessages = $conversation->getContextMessages($limit);

        echo "📌 Context Messages (Last {$limit}):\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

        foreach ($contextMessages as $message) {
            echo "[{$message->role}]: " . substr($message->content, 0, 80) . "...\n";
        }
    }

    /**
     * Compter les tokens approximativement
     * (Estimation: 1 token ≈ 4 caractères en moyenne)
     */
    public static function estimateTokens(int $conversationId, int $limit = 10)
    {
        $conversation = Conversation::findOrFail($conversationId);
        $contextMessages = $conversation->getContextMessages($limit);

        $totalContent = '';
        foreach ($contextMessages as $message) {
            $totalContent .= $message->content;
        }

        $estimatedTokens = strlen($totalContent) / 4;

        echo "📊 Estimation de tokens:\n";
        echo "   Caractères: " . strlen($totalContent) . "\n";
        echo "   Tokens (estimation): " . round($estimatedTokens) . "\n";
        echo "   Limite Groq: ~7,900 tokens\n";

        return $estimatedTokens;
    }

    /**
     * Nettoyer les conversations de test
     */
    public static function deleteAllConversations()
    {
        $count = Conversation::count();
        Conversation::truncate();
        echo "✅ {$count} conversations supprimées\n";
    }
}
