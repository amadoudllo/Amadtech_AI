<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    /**
     * Display the chat interface
     */
    public function show()
    {
        return view('chat.index');
    }

    /**
     * Send a message to Groq API and get a response
     */
    public function sendMessage(Request $request)
    {
        // Force JSON response type for this endpoint
        $request->headers->set('Accept', 'application/json');
        
        $request->validate([
            'message' => 'required|string|max:4000',
        ]);

        $message = $request->input('message');
        $groqApiKey = config('services.groq.api_key');

        if (!$groqApiKey) {
            return response()->json([
                'error' => 'Groq API key is not configured',
            ], 500);
        }

        try {
            // Use Groq-compatible payload inspired by Groq Python SDK example
            $payload = [
                'model' => config('services.groq.model', 'openai/gpt-oss-120b'),
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $message,
                    ],
                ],
                // Groq uses `max_completion_tokens` and other fields in their SDK
                'temperature' => 0.7,
                'top_p' => 1,
                'reasoning_effort' => 'medium',
                'max_completion_tokens' => 1024,
                'stream' => false,
            ];

            $client = Http::withHeaders([
                'Authorization' => 'Bearer ' . $groqApiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60);

            // Optional proxy support via GROQ_PROXY in .env (example: http://127.0.0.1:8888)
            if ($proxy = env('GROQ_PROXY')) {
                $client = $client->withOptions(['proxy' => $proxy]);
            }

            // Retry a few times on transient network errors and use longer timeout
            $response = $client->retry(3, 2000)->post('https://api.groq.com/openai/v1/chat/completions', $payload);

            if ($response->failed()) {
                        // Log raw response body and headers to help debug 500 errors
                        \Log::error('Groq API Error', [
                            'status' => $response->status(),
                            'body_raw' => $response->body(),
                            'body_json' => $this->safeJsonDecode($response->body()),
                            'headers' => $response->headers(),
                        ]);

                        // If server error, try an alternate Groq endpoint as a fallback
                        if ($response->status() >= 500) {
                            try {
                                $fallbackClient = Http::withHeaders([
                                    'Authorization' => 'Bearer ' . $groqApiKey,
                                    'Content-Type' => 'application/json',
                                ])->timeout(60);

                                if ($proxy = env('GROQ_PROXY')) {
                                    $fallbackClient = $fallbackClient->withOptions(['proxy' => $proxy]);
                                }

                                $fallback = $fallbackClient->retry(2, 1500)->post('https://api.groq.com/openai/v1/chat/completions', $payload);

                                \Log::info('Groq API fallback attempt', [
                                    'status' => $fallback->status(),
                                    'body_raw' => $fallback->body(),
                                    'body_json' => $this->safeJsonDecode($fallback->body()),
                                ]);

                                if (!$fallback->failed()) {
                                    $data = $fallback->json();
                                    $reply = $data['choices'][0]['message']['content'] ?? '';
                                    return response()->json([
                                        'success' => true,
                                        'reply' => $reply,
                                    ]);
                                }
                            } catch (\Exception $e) {
                                \Log::error('Groq fallback exception', ['message' => $e->getMessage()]);
                            }
                        }

                        return response()->json([
                            'error' => 'Failed to get response from Groq API',
                            'details' => $this->safeJsonDecode($response->body()),
                        ], 500);
            }

            $data = $response->json();
            $reply = $data['choices'][0]['message']['content'] ?? '';

            return response()->json([
                'success' => true,
                'reply' => $reply,
            ]);
        } catch (\Exception $e) {
            \Log::error('Groq API Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Safely decode JSON strings, returning null on failure.
     */
    private function safeJsonDecode($string)
    {
        if (!$string) {
            return null;
        }

        $decoded = json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $decoded;
    }
}
