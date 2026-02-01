<?php

namespace App\Jobs;

use App\Models\Article;
use App\Services\OpenAIService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PredictionArticleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;
    public array $backoff = [60, 120, 180];

    protected array $yesterdayResults = [];

    public function __construct()
    {
    }

    public function handle(): void
    {
        Log::info('PredictionArticleJob started');

        // Bước 1: Lấy kết quả xổ số ngày hôm qua từ kqxs.online
        $this->yesterdayResults = $this->fetchYesterdayResults();

        if (empty($this->yesterdayResults)) {
            Log::warning('No lottery results found from yesterday');
            return;
        }

        Log::info('Fetched yesterday lottery results', ['regions' => array_keys($this->yesterdayResults)]);

        // Bước 2: Gọi AI để phân tích và viết bài dự đoán
        $openai = new OpenAIService();

        $prompt = $this->buildPrompt($this->yesterdayResults);

        $response = $openai->generateContent($prompt, [
            'temperature' => 0.85,
            'max_tokens' => 4096,
        ]);

        if (empty($response)) {
            Log::error('OpenAI returned empty response');
            return;
        }

        // Bước 3: Parse kết quả và lưu bài viết
        $this->parseAndSaveArticle($response);

        Log::info('PredictionArticleJob completed');
    }

    protected function fetchYesterdayResults(): array
    {
        $yesterday = Carbon::now('Asia/Ho_Chi_Minh')->subDay()->format('d-m-Y');
        $results = [];

        $urls = [
            'xsmb' => "https://kqxs.online/xsmb-{$yesterday}.html",
            'xsmn' => "https://kqxs.online/xsmn-{$yesterday}.html",
            'xsmt' => "https://kqxs.online/xsmt-{$yesterday}.html",
        ];

        foreach ($urls as $region => $url) {
            try {
                $response = Http::timeout(30)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/122 Safari/537.36',
                        'Accept-Language' => 'vi,en-US;q=0.9',
                    ])
                    ->get($url);

                if ($response->successful()) {
                    $html = $response->body();
                    $results[$region] = $this->parseResultsFromHtml($html, $region);
                    Log::info("Fetched {$region} results", ['url' => $url]);
                } else {
                    Log::warning("Failed to fetch {$region} results", ['status' => $response->status(), 'url' => $url]);
                }
            } catch (\Throwable $e) {
                Log::error("Exception fetching {$region} results", ['message' => $e->getMessage(), 'url' => $url]);
            }
        }

        return $results;
    }

    protected function parseResultsFromHtml(string $html, string $region): array
    {
        $results = [];

        // Tìm các giải đặc biệt và các giải khác trong HTML
        // Parse bảng kết quả xổ số
        preg_match_all('/<td[^>]*class="[^"]*giai[^"]*"[^>]*>(.*?)<\/td>/si', $html, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $number = strip_tags($match);
                $number = preg_replace('/\s+/', '', $number);
                if (preg_match('/^\d+$/', $number)) {
                    $results[] = $number;
                }
            }
        }

        // Fallback: tìm các số trong bảng kết quả
        if (empty($results)) {
            preg_match_all('/\b(\d{2,6})\b/', strip_tags($html), $allNumbers);
            if (!empty($allNumbers[1])) {
                $results = array_slice(array_unique($allNumbers[1]), 0, 30);
            }
        }

        return [
            'region' => $region,
            'numbers' => $results,
            'date' => Carbon::now('Asia/Ho_Chi_Minh')->subDay()->format('d/m/Y'),
        ];
    }

    protected function buildPrompt(array $results): string
    {
        $today = Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y');
        $yesterday = Carbon::now('Asia/Ho_Chi_Minh')->subDay()->format('d/m/Y');

        // Format kết quả xổ số ngày hôm qua
        $resultsText = '';
        foreach ($results as $region => $data) {
            $regionName = match($region) {
                'xsmb' => 'Miền Bắc',
                'xsmn' => 'Miền Nam',
                'xsmt' => 'Miền Trung',
                default => $region,
            };
            $numbers = !empty($data['numbers']) ? implode(', ', array_slice($data['numbers'], 0, 20)) : 'Không có dữ liệu';
            $resultsText .= "- {$regionName} ({$yesterday}): {$numbers}\n";
        }

        return <<<PROMPT
Bạn là một chuyên gia phân tích và dự đoán kết quả xổ số (KQXS) với nhiều năm kinh nghiệm.

Ngày hôm nay: {$today}
Ngày hôm qua: {$yesterday}

## Kết quả xổ số ngày hôm qua:

{$resultsText}

## Nhiệm vụ:

Dựa trên kết quả xổ số ngày hôm qua, hãy viết bài dự đoán kết quả xổ số cho ngày hôm nay ({$today}).

### TỪ KHÓA CẦN SỬ DỤNG (tự nhiên trong bài):

**Primary Keywords:**
- KQXS - Kết quả xổ số
- Xổ số hôm nay
- XSMB - Xổ số miền Bắc
- XSMN - Xổ số miền Nam
- XSMT - Xổ số miền Trung
- Dự đoán xổ số

**Secondary Keywords:**
- Thống kê loto
- Loto gan / Loto đầu / Loto đuôi
- Thống kê giải đặc biệt
- Xổ số 3 miền
- Số đẹp hôm nay

### INTERNAL LINKS (BẮT BUỘC sử dụng):

- KQXSMB: https://kqxs.online/xo-so-mien-bac/xsmb-p1.html
- KQXSMN: https://kqxs.online/xo-so-mien-nam/xsmn-p1.html
- KQXSMT: https://kqxs.online/xo-so-mien-trung/xsmt-p1.html
- VIETLOTT: https://kqxs.online/kqxs-vietlott-ket-qua-xo-so-vietlott.html

### Định dạng output (BẮT BUỘC tuân theo):

```
===META_START===
TITLE: [Tiêu đề bài viết - kết hợp "Dự đoán KQXS" + ngày hôm nay + từ khóa SEO]
META_DESCRIPTION: [150-160 ký tự, chứa từ khóa chính, thu hút click]
META_KEYWORDS: [5-7 từ khóa, cách nhau bằng dấu phẩy]
EXCERPT: [Mô tả ngắn 100-150 ký tự]
===META_END===

===CONTENT_START===
[Nội dung bài viết HTML hoàn chỉnh theo cấu trúc bên dưới]
===CONTENT_END===
```

### Cấu trúc nội dung bài viết (trong phần CONTENT):

<h2>Dự đoán KQXS ngày {$today} - Phân tích chuyên sâu 3 miền</h2>

<p>[Giới thiệu ngắn về bài phân tích, đề cập đến kết quả ngày hôm qua và xu hướng]</p>

<h3>📊 Tổng hợp kết quả xổ số ngày {$yesterday}</h3>
<p>[Tóm tắt ngắn gọn kết quả XSMB, XSMN, XSMT ngày hôm qua, các số đáng chú ý]</p>

<h3>🔮 Dự đoán XSMB - Xổ số Miền Bắc hôm nay {$today}</h3>
<p>[200-300 từ: Phân tích xu hướng, thống kê loto, đề xuất các cặp số đẹp cho miền Bắc]</p>
<p><strong>Số đẹp XSMB:</strong> [Liệt kê 5-10 cặp số]</p>
<p><strong>Loto đầu:</strong> [Gợi ý]</p>
<p><strong>Loto đuôi:</strong> [Gợi ý]</p>
<p>👉 Xem kết quả tại: <a href="https://kqxs.online/xo-so-mien-bac/xsmb-p1.html">KQXS Miền Bắc</a></p>

<h3>🔮 Dự đoán XSMN - Xổ số Miền Nam hôm nay {$today}</h3>
<p>[200-300 từ: Phân tích xu hướng, thống kê loto, đề xuất các cặp số đẹp cho miền Nam]</p>
<p><strong>Số đẹp XSMN:</strong> [Liệt kê 5-10 cặp số]</p>
<p><strong>Loto đầu:</strong> [Gợi ý]</p>
<p><strong>Loto đuôi:</strong> [Gợi ý]</p>
<p>👉 Xem kết quả tại: <a href="https://kqxs.online/xo-so-mien-nam/xsmn-p1.html">KQXS Miền Nam</a></p>

<h3>🔮 Dự đoán XSMT - Xổ số Miền Trung hôm nay {$today}</h3>
<p>[200-300 từ: Phân tích xu hướng, thống kê loto, đề xuất các cặp số đẹp cho miền Trung]</p>
<p><strong>Số đẹp XSMT:</strong> [Liệt kê 5-10 cặp số]</p>
<p><strong>Loto đầu:</strong> [Gợi ý]</p>
<p><strong>Loto đuôi:</strong> [Gợi ý]</p>
<p>👉 Xem kết quả tại: <a href="https://kqxs.online/xo-so-mien-trung/xsmt-p1.html">KQXS Miền Trung</a></p>

<h3>📈 Bảng thống kê loto gan</h3>
<table class="table table-bordered">
<thead><tr><th>Miền</th><th>Số gan lâu</th><th>Số ngày gan</th><th>Khuyến nghị</th></tr></thead>
<tbody>
[Ít nhất 6-9 dòng phân tích số gan cho 3 miền]
</tbody>
</table>

<h3>💡 Phương pháp chọn số hiệu quả</h3>
<p>[150-200 từ: Hướng dẫn cách phân tích, thời điểm tốt, lưu ý khi chơi]</p>

<h3>❓ Câu hỏi thường gặp</h3>
<p><strong>Dự đoán KQXS có chính xác không?</strong></p>
<p>[Trả lời trung thực về tính chất tham khảo]</p>

<p><strong>Nên chơi xổ số theo phương pháp nào?</strong></p>
<p>[Gợi ý phương pháp]</p>

<p><strong>Xem KQXS trực tiếp ở đâu?</strong></p>
<p>[Giới thiệu trang web]</p>

<h3>📌 Kết luận</h3>
<p>[Tóm tắt nội dung, nhắc nhở chơi có trách nhiệm, mời xem KQXS trực tiếp]</p>
<p>🎯 Xem thêm kết quả xổ số: <a href="https://kqxs.online/xo-so-mien-bac/xsmb-p1.html">XSMB</a> | <a href="https://kqxs.online/xo-so-mien-nam/xsmn-p1.html">XSMN</a> | <a href="https://kqxs.online/xo-so-mien-trung/xsmt-p1.html">XSMT</a> | <a href="https://kqxs.online/kqxs-vietlott-ket-qua-xo-so-vietlott.html">Vietlott</a></p>

### Yêu cầu SEO kỹ thuật:

1. Từ khóa:
   - Primary: "Dự đoán KQXS", "Xổ số hôm nay", "XSMB/XSMN/XSMT"
   - Secondary: "thống kê loto", "số đẹp", "loto gan"

2. Internal Links: Đã cung cấp ở trên, BẮT BUỘC sử dụng

3. Định dạng:
   - Đoạn văn ngắn (3-4 câu)
   - Sử dụng bảng cho thống kê
   - Bold cho con số quan trọng
   - Emoji để tăng tính trực quan

4. Tone: Chuyên nghiệp, phân tích logic, không mê tín quá mức

### Những điều KHÔNG được làm:

❌ Sử dụng từ AI: "unleash", "unlock", "craft", "leverage", "delve"
❌ Cụm từ sáo rỗng: "Bạn có đang...", "Chắc hẳn bạn..."
❌ Đảm bảo 100% chính xác - phải nhắc tính chất tham khảo
❌ Khuyến khích cờ bạc quá mức
❌ Viết bằng tiếng Anh - CHỈ viết tiếng Việt

Hãy viết bài viết hoàn chỉnh ngay bây giờ.
PROMPT;
    }

    protected function parseAndSaveArticle(string $response): void
    {
        // Parse META
        $title = '';
        $metaDescription = '';
        $metaKeywords = '';
        $excerpt = '';
        $content = '';
        $genreId = 1;

        // Extract META section
        if (preg_match('/===META_START===(.+?)===META_END===/s', $response, $metaMatch)) {
            $metaSection = $metaMatch[1];

            if (preg_match('/TITLE:\s*(.+?)(?:\n|$)/i', $metaSection, $m)) {
                $title = trim($m[1]);
            }
            if (preg_match('/META_DESCRIPTION:\s*(.+?)(?:\n|$)/i', $metaSection, $m)) {
                $metaDescription = trim($m[1]);
            }
            if (preg_match('/META_KEYWORDS:\s*(.+?)(?:\n|$)/i', $metaSection, $m)) {
                $metaKeywords = trim($m[1]);
            }
            if (preg_match('/EXCERPT:\s*(.+?)(?:\n|$)/i', $metaSection, $m)) {
                $excerpt = trim($m[1]);
            }
        }

        // Extract CONTENT section
        if (preg_match('/===CONTENT_START===(.+?)===CONTENT_END===/s', $response, $contentMatch)) {
            $content = trim($contentMatch[1]);
            // Remove markdown code blocks if any
            $content = preg_replace('/^```html?\s*\n?/i', '', $content);
            $content = preg_replace('/\n?```\s*$/i', '', $content);
        }

        // Thêm phần tổng hợp kết quả hôm qua vào cuối bài
        $content = $this->appendYesterdayResults($content);

        if (empty($title) || empty($content)) {
            Log::error('Failed to parse article from AI response', [
                'title_empty' => empty($title),
                'content_empty' => empty($content),
                'response_preview' => Str::limit($response, 500),
            ]);
            return;
        }

        // Tạo slug unique
        $slug = $this->uniqueSlug($title);

        // Kiểm tra bài viết trùng tiêu đề trong ngày
        $existsToday = Article::whereDate('created_at', Carbon::today('Asia/Ho_Chi_Minh'))
            ->where('title', 'LIKE', '%' . Str::limit($title, 50, '') . '%')
            ->exists();

        if ($existsToday) {
            Log::info('Similar article already exists today, skipping', ['title' => $title]);
            return;
        }

        // Generate thumbnail with DALL-E
        $thumbnail = null;
        $openai = new OpenAIService();

        Log::info('Generating thumbnail with DALL-E', ['title' => $title]);
        $thumbnail = $openai->generateThumbnail($title, $slug);

        if ($thumbnail) {
            Log::info('Thumbnail generated', ['path' => $thumbnail]);
        } else {
            Log::warning('Thumbnail generation failed, continuing without image');
        }

        // Lưu bài viết
        $article = Article::create([
            'genre_id' => $genreId,
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt ?: Str::limit(strip_tags($content), 200),
            'content' => $content,
            'thumbnail' => $thumbnail,
            'avatar' => $thumbnail,
            'meta_title' => $title,
            'meta_description' => $metaDescription ?: Str::limit(strip_tags($content), 160),
            'meta_keywords' => $metaKeywords,
            'highlight' => 0,
            'hidden' => 0,
            'published_at' => Carbon::now('Asia/Ho_Chi_Minh'),
            'copyright' => 'AI Generated',
            'post_type' => 'prediction',
        ]);

        Log::info('Prediction article saved', [
            'id' => $article->id,
            'title' => $title,
            'slug' => $slug,
            'thumbnail' => $thumbnail,
        ]);
    }

    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (Article::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    protected function appendYesterdayResults(string $content): string
    {
        if (empty($this->yesterdayResults)) {
            return $content;
        }

        $yesterday = Carbon::now('Asia/Ho_Chi_Minh')->subDay()->format('d/m/Y');
        $today = Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y');

        // Tạo danh sách kết quả ngày hôm qua
        $resultItems = [];
        foreach ($this->yesterdayResults as $region => $data) {
            $regionName = match($region) {
                'xsmb' => 'Miền Bắc',
                'xsmn' => 'Miền Nam',
                'xsmt' => 'Miền Trung',
                default => $region,
            };
            $link = match($region) {
                'xsmb' => 'https://kqxs.online/xo-so-mien-bac/xsmb-p1.html',
                'xsmn' => 'https://kqxs.online/xo-so-mien-nam/xsmn-p1.html',
                'xsmt' => 'https://kqxs.online/xo-so-mien-trung/xsmt-p1.html',
                default => '#',
            };
            $numbers = !empty($data['numbers']) ? implode(', ', array_slice($data['numbers'], 0, 10)) : 'Đang cập nhật';

            $resultItems[] = "<li><strong><a href=\"{$link}\">{$regionName}</a>:</strong> {$numbers}...</li>";
        }

        $resultsList = implode("\n", $resultItems);

        // Tạo phần tổng hợp kết quả
        $resultsSection = <<<HTML

<div class="yesterday-results-section" style="margin-top: 30px; padding: 20px; background: #e8f4f8; border-radius: 8px; border-left: 4px solid #17a2b8;">
<h3>📋 Tham khảo KQXS ngày {$yesterday}</h3>
<p>Dưới đây là tóm tắt kết quả xổ số 3 miền ngày hôm qua, làm cơ sở cho dự đoán hôm nay ({$today}):</p>
<ul class="results-list">
{$resultsList}
</ul>
<p><em>Dữ liệu tham khảo từ <a href="https://kqxs.online">kqxs.online</a>. Xem đầy đủ tại: <a href="https://kqxs.online/xo-so-mien-bac/xsmb-p1.html">XSMB</a> | <a href="https://kqxs.online/xo-so-mien-nam/xsmn-p1.html">XSMN</a> | <a href="https://kqxs.online/xo-so-mien-trung/xsmt-p1.html">XSMT</a></em></p>
</div>
HTML;

        return $content . $resultsSection;
    }

    public function failed(\Throwable $e): void
    {
        Log::error('PredictionArticleJob FAILED', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
