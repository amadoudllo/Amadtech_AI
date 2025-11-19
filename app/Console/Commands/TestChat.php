<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestChat extends Command
{
    protected $signature = 'test:chat';

    protected $description = 'Test the chat endpoint directly';

    public function handle()
    {
        $this->info('Testing /chat/send endpoint...');

        try {
            $groqApiKey = config('services.groq.api_key');
            $this->info("Groq API Key: " . (strlen($groqApiKey) > 0 ? "OK" : "NOT SET"));

            $payload = [
                'model' => config('services.groq.model', 'openai/gpt-oss-120b'),
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => 'Hello! Say hi back.',
                    ],
                ],
                'temperature' => 0.7,
                'top_p' => 1,
                'reasoning_effort' => 'medium',
                'max_completion_tokens' => 1024,
                'stream' => false,
            ];

            $this->info("\nSending request to Groq API...");

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $groqApiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->retry(3, 2000)->post('https://api.groq.com/openai/v1/chat/completions', $payload);

            $this->info("Status: " . $response->status());
            $this->info("Headers: " . json_encode($response->headers()));
            $this->info("Body: " . $response->body());

            if ($response->failed()) {
                $this->error("Request failed!");
                return 1;
            }

            $data = $response->json();
            $reply = $data['choices'][0]['message']['content'] ?? 'NO REPLY';
            $this->info("Reply: " . $reply);

            return 0;
        } catch (\Exception $e) {
            $this->error("Exception: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}
