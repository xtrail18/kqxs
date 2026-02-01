<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', '');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
    }

    public function generateContent(string $prompt, array $options = []): ?string
    {
        if (empty($this->apiKey)) {
            Log::error('OpenAI API key is not configured');
            return null;
        }

        $payload = [
            'model' => $options['model'] ?? $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Bạn là một copywriter chuyên nghiệp viết bài SEO tiếng Việt cho trang kết quả xổ số và giải mã giấc mơ.',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => $options['temperature'] ?? 0.8,
            'max_tokens' => $options['max_tokens'] ?? 4096,
        ];

        try {
            $response = Http::timeout(120)
                ->retry(2, 5000)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/chat/completions", $payload);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? null;
            }

            Log::error('OpenAI API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('OpenAI API exception', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
