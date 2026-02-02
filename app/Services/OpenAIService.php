<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    /**
     * Generate image using DALL-E 3
     *
     * @param string $prompt Description of the image
     * @param array $options size, quality, style
     * @return string|null URL of generated image
     */
    public function generateImage(string $prompt, array $options = []): ?string
    {
        if (empty($this->apiKey)) {
            Log::error('OpenAI API key is not configured');
            return null;
        }

        $payload = [
            'model' => $options['model'] ?? 'dall-e-3',
            'prompt' => $prompt,
            'n' => 1,
            'size' => $options['size'] ?? '1792x1024', // Landscape for article thumbnail
            'quality' => $options['quality'] ?? 'standard',
            'style' => $options['style'] ?? 'vivid',
            'response_format' => 'url',
        ];

        try {
            $response = Http::timeout(120)
                ->retry(2, 5000)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/images/generations", $payload);

            if ($response->successful()) {
                $data = $response->json();
                return $data['data'][0]['url'] ?? null;
            }

            Log::error('OpenAI DALL-E API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('OpenAI DALL-E exception', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Generate and download thumbnail for article
     *
     * @param string $title Article title for prompt
     * @param string $slug Article slug for filename
     * @return string|null Relative path to saved image
     */
    public function generateThumbnail(string $title, string $slug): ?string
    {
        // Build image prompt
        $prompt = $this->buildThumbnailPrompt($title);

        Log::info('Generating thumbnail with DALL-E', ['title' => $title]);

        $imageUrl = $this->generateImage($prompt, [
            'size' => '1792x1024',
            'quality' => 'standard',
            'style' => 'vivid',
        ]);

        if (empty($imageUrl)) {
            Log::error('Failed to generate thumbnail image');
            return null;
        }

        // Download and save the image
        return $this->downloadAndSaveImage($imageUrl, $slug);
    }

    /**
     * Build prompt for thumbnail generation
     */
    protected function buildThumbnailPrompt(string $title): string
    {
        return <<<PROMPT
            Create a vibrant, eye-catching thumbnail image for a Vietnamese lottery results article.

            Topic: "{$title}"

            Requirements:
            - Modern, professional design with Vietnamese lottery aesthetic
            - Color palette: Gold, red, deep blue, emerald green (Vietnamese lucky colors)
            - Visual elements to include:
            + Lottery balls with numbers floating or arranged artistically
            + Golden ticket or lottery slip silhouettes
            + Sparkles, light rays, and celebration effects
            + Lucky symbols: coins, ingots, fortune wheels
            + Abstract number patterns in background
            - Atmosphere: Exciting, hopeful, celebratory mood
            - Style: Clean, modern infographic/editorial thumbnail
            - Lighting: Dramatic with golden highlights and soft glows
            - Composition: 16:9 aspect ratio, centered focal point

            Strict restrictions:
            - NO text, letters, or readable numbers
            - NO human faces or identifiable people
            - NO real currency or brand logos
            - Safe for all audiences
        PROMPT;
    }

    /**
     * Download image from URL and save to storage
     */
    protected function downloadAndSaveImage(string $url, string $slug): ?string
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ])
                ->get($url);

            if (!$response->successful()) {
                Log::error('Failed to download generated image', ['url' => $url]);
                return null;
            }

            $imageData = $response->body();

            // Verify it's a valid image
            $imageInfo = @getimagesizefromstring($imageData);
            if (!$imageInfo) {
                Log::error('Downloaded data is not a valid image');
                return null;
            }

            // Create GD image and convert to WebP
            $im = @imagecreatefromstring($imageData);
            if (!$im) {
                Log::error('Failed to create GD image from downloaded data');
                return null;
            }

            $filename = Str::slug($slug) . '-thumb-' . time() . '.webp';
            $folder = 'images/thumbs';
            $storagePath = storage_path("app/public/{$folder}");

            if (!is_dir($storagePath)) {
                @mkdir($storagePath, 0777, true);
            }

            $savePath = "{$storagePath}/{$filename}";

            // Convert to true color if needed
            if (function_exists('imagepalettetotruecolor')) {
                imagepalettetotruecolor($im);
            }
            imagealphablending($im, true);
            imagesavealpha($im, true);

            // Save as WebP
            if (function_exists('imagewebp')) {
                imagewebp($im, $savePath, 90);
            } else {
                // Fallback to PNG
                $savePath = str_replace('.webp', '.png', $savePath);
                $filename = str_replace('.webp', '.png', $filename);
                imagepng($im, $savePath, 6);
            }

            imagedestroy($im);

            Log::info('Thumbnail saved', ['path' => "{$folder}/{$filename}"]);

            return "{$folder}/{$filename}";
        } catch (\Throwable $e) {
            Log::error('Exception downloading/saving image', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
