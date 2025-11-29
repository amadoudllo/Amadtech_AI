<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * System prompt for the AI assistant
     */
    private const SYSTEM_PROMPT = "Tu es un assistant IA créé par Amadou Diallo, jeune entrepreneur tech guinéen, et déployé sur la plateforme Amadtech_AI. Tu réponds en français de manière DIRECTE, CONCISE et sans détails inutiles. Tu vas droit au but. Tu es utile, honnête et poli. Lorsqu'on te demande qui t'a créé, tu réponds: 'Je suis créé et déployé par Amadou Diallo, un jeune entrepreneur tech guinéen.' Tes réponses doivent être courtes et efficaces.";

    /**
     * Display the chat interface
     */
    public function show()
    {
        return view('chat.index');
    }

    /**
     * Send a message and get a response from Groq API
     * Handles conversation persistence and context management
     */
    public function sendMessage(Request $request): JsonResponse
    {
        // Validation
        $validated = $request->validate([
            'message' => 'required|string|max:4000',
            'conversation_id' => 'nullable|integer|exists:conversations,id',
        ]);

        $userMessage = $validated['message'];
        $conversationId = $validated['conversation_id'] ?? null;

        try {
            // Gestion de la conversation
            $conversation = $this->getOrCreateConversation($conversationId);
            
            // Store session identifier for guests to retrieve their conversations later
            if (!auth()->check()) {
                session(['guest_conversation_id' => $conversation->id]);
            }

            // Construction du contexte avec messages de TOUTES les conversations
            // pour que l'IA puisse se baser sur l'historique complet de l'utilisateur
            $allMessages = [
                [
                    'role' => 'system',
                    'content' => self::SYSTEM_PROMPT,
                ],
            ];

            // 1. Récupérer les messages des AUTRES conversations (passées)
            // - SEULEMENT pour les utilisateurs authentifiés
            // - Les guests ne voient que la conversation courante
            $otherConversations = [];
            if (auth()->check()) {
                // Récupérer jusqu'à 5 dernières conversations (en excluant la courante)
                $otherConversations = Conversation::where('user_id', auth()->id())
                    ->where('id', '!=', $conversation->id)
                    ->latest()
                    ->take(5)
                    ->get();
            }

            // Ajouter les messages des anciennes conversations (5 messages par conversation max)
            foreach ($otherConversations as $oldConv) {
                $oldMessages = $oldConv->messages()
                    ->latest()
                    ->take(5)
                    ->get()
                    ->reverse()
                    ->values();

                foreach ($oldMessages as $message) {
                    $allMessages[] = $message->toApiFormat();
                }
            }

            // 2. Récupérer les 10 derniers messages de la conversation COURANTE
            $previousMessages = $conversation->messages()
                ->latest()
                ->take(10)
                ->get()
                ->reverse()
                ->values();

            foreach ($previousMessages as $message) {
                $allMessages[] = $message->toApiFormat();
            }

            // 3. Ajouter le nouveau message utilisateur au contexte
            $allMessages[] = [
                'role' => 'user',
                'content' => $userMessage,
            ];

            // Sauvegarde du message de l'utilisateur en BD
            $userMessageModel = $conversation->messages()->create([
                'role' => 'user',
                'content' => $userMessage,
            ]);

            // Appel à l'API avec le contexte complet
            $contextMessages = $allMessages;

            // Appel à l'API Groq
            $aiResponse = $this->callGroqApi($contextMessages);

            if (!$aiResponse) {
                return response()->json([
                    'success' => false,
                    'error' => 'Impossible de récupérer une réponse de l\'API Groq',
                ], 500);
            }

            // Sauvegarde de la réponse de l'IA
            $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $aiResponse,
            ]);

            // Générer ou mettre à jour le titre de la conversation
            if ($conversation->title === null) {
                $conversation->update([
                    'title' => $this->generateConversationTitle($userMessage),
                ]);
            }

            return response()->json([
                'success' => true,
                'reply' => $aiResponse,
                'conversation_id' => $conversation->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Chat sendMessage Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Une erreur est survenue : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get an existing conversation or create a new one
     * SECURITY: Only allow users to access their own conversations
     */
    private function getOrCreateConversation(?int $conversationId): Conversation
    {
        try {
            if ($conversationId) {
                $conversation = Conversation::findOrFail($conversationId);
                
                // SECURITY CHECK: Ensure user owns this conversation
                if (auth()->check() && $conversation->user_id !== auth()->id()) {
                    throw new \Exception('Accès non autorisé. Cette conversation ne vous appartient pas.');
                }
                
                // If guest tries to access a non-guest conversation, reject
                if (!auth()->check() && $conversation->user_id !== null) {
                    throw new \Exception('Accès non autorisé.');
                }
            } else {
                $conversation = Conversation::create([
                    'user_id' => auth()->id(),
                    'title' => null, // Sera généré après le premier message
                ]);
            }

            return $conversation;
        } catch (\PDOException $e) {
            Log::error('Database connection error in getOrCreateConversation: ' . $e->getMessage());
            // Retry once after a short delay
            sleep(1);
            if ($conversationId) {
                $conversation = Conversation::findOrFail($conversationId);
                
                // SECURITY CHECK: Ensure user owns this conversation
                if (auth()->check() && $conversation->user_id !== auth()->id()) {
                    throw new \Exception('Accès non autorisé. Cette conversation ne vous appartient pas.');
                }
                
                if (!auth()->check() && $conversation->user_id !== null) {
                    throw new \Exception('Accès non autorisé.');
                }
            } else {
                $conversation = Conversation::create([
                    'user_id' => auth()->id(),
                    'title' => null,
                ]);
            }
            return $conversation;
        }
    }

    /**
     * Call Groq API with the provided messages
     */
    private function callGroqApi(array $messages): ?string
    {
        $groqApiKey = config('services.groq.api_key');

        if (!$groqApiKey) {
            Log::error('Groq API key is not configured');
            return null;
        }

        try {
            // Log the number of messages being sent for debugging memory issues
            Log::info('Groq API called with ' . count($messages) . ' messages in context');
            
            $payload = [
                'model' => config('services.groq.model', 'mixtral-8x7b-32768'),
                'messages' => $messages,
                'temperature' => 0.7,
                'top_p' => 1,
                'max_completion_tokens' => 1024,
                'stream' => false,
            ];

            $client = Http::withHeaders([
                'Authorization' => 'Bearer ' . $groqApiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60);

            // Support optionnel du proxy via GROQ_PROXY
            if ($proxy = env('GROQ_PROXY')) {
                $client = $client->withOptions(['proxy' => $proxy]);
            }

            $response = $client->retry(3, 2000)->post(
                'https://api.groq.com/openai/v1/chat/completions',
                $payload
            );

            if ($response->failed()) {
                Log::error('Groq API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            return $data['choices'][0]['message']['content'] ?? null;

        } catch (\Exception $e) {
            Log::error('Groq API Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Get all conversations for the current user
     * - Authenticated users: see only THEIR conversations (user_id = auth()->id())
     * - Guests: return empty array (they use localStorage only)
     * 
     * SECURITY: Guests must NOT see other guests' conversations!
     * Each user/guest should only see their own.
     */
    public function getConversations(): JsonResponse
    {
        try {
            // If NOT authenticated, guests use localStorage (not BD persistence)
            if (!auth()->check()) {
                return response()->json([
                    'success' => true,
                    'conversations' => [],
                ]);
            }

            // Authenticated users: return ONLY their own conversations
            $conversations = Conversation::where('user_id', auth()->id())
                ->orderBy('updated_at', 'desc')
                ->limit(50)
                ->get(['id', 'title', 'updated_at'])
                ->map(function ($conv) {
                    return [
                        'id' => $conv->id,
                        'title' => $conv->title ?? 'Nouvelle conversation',
                        'updated_at' => $conv->updated_at->toDateTimeString(),
                    ];
                });

            return response()->json([
                'success' => true,
                'conversations' => $conversations,
            ]);
        } catch (\Exception $e) {
            Log::error('Get conversations error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des conversations',
            ], 500);
        }
    }

    /**
     * Get messages for a specific conversation
     * SECURITY: Only authenticated users can access conversations they own
     */
    public function getConversationMessages(int $conversationId): JsonResponse
    {
        try {
            $conversation = Conversation::findOrFail($conversationId);

            // SECURITY CHECK: Ensure user owns this conversation
            if (!auth()->check() || $conversation->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès non autorisé. Cette conversation ne vous appartient pas.',
                ], 403);
            }

            $messages = $conversation->messages()
                ->orderBy('created_at', 'asc')
                ->get(['role', 'content'])
                ->map(function ($msg) {
                    return [
                        'role' => $msg->role,
                        'content' => $msg->content,
                    ];
                });

            return response()->json([
                'success' => true,
                'conversation_id' => $conversationId,
                'title' => $conversation->title ?? 'Nouvelle conversation',
                'messages' => $messages,
            ]);
        } catch (\Exception $e) {
            Log::error('Get messages error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération des messages',
            ], 500);
        }
    }

    /**
     * Delete a conversation
     */
    public function deleteConversation(int $conversationId): JsonResponse
    {
        try {
            $conversation = Conversation::findOrFail($conversationId);

            // Verify ownership - guests cannot delete
            if (auth()->check() && $conversation->user_id === auth()->id()) {
                $conversation->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Conversation supprimée',
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Accès non autorisé',
            ], 403);
        } catch (\Exception $e) {
            Log::error('Delete conversation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la suppression',
            ], 500);
        }
    }

    /**
     * Update conversation title
     * SECURITY: Only authenticated users can update conversations they own
     */
    public function updateConversation(Request $request, int $conversationId): JsonResponse
    {
        try {
            $conversation = Conversation::findOrFail($conversationId);

            // SECURITY CHECK: Ensure user owns this conversation
            if (!auth()->check() || $conversation->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès non autorisé.',
                ], 403);
            }

            // Valider et mettre à jour le titre
            $validated = $request->validate([
                'title' => 'required|string|max:255',
            ]);

            $conversation->update([
                'title' => $validated['title'],
            ]);

            return response()->json([
                'success' => true,
                'conversation' => $conversation,
            ]);
        } catch (\Exception $e) {
            Log::error('Update conversation error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la mise à jour',
            ], 500);
        }
    }

    /**
     * Generate a title for the conversation based on the first message
     */
    private function generateConversationTitle(string $firstMessage): string
    {
        // Prendre les 50 premiers caractères et ajouter des points de suspension si nécessaire
        $maxLength = 50;
        if (strlen($firstMessage) > $maxLength) {
            return substr($firstMessage, 0, $maxLength) . '...';
        }

        return $firstMessage;
    }
}
