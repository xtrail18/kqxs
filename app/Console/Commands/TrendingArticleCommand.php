<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Genre;
use App\Services\OpenAIService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TrendingArticleCommand extends Command
{
    protected $signature = 'trending:article {--sync : Run synchronously instead of dispatching to queue} {--no-thumb : Skip thumbnail generation}';

    protected $description = 'Fetch Google Trends Vietnam and generate SEO article about lottery/dreams';

    protected string $trendsUrl = 'https://trends.google.com/trending/rss?geo=VN';
    protected array $fetchedTrends = [];

    public function handle(): int
    {
        $this->info('🚀 Starting Trending Article Job...');
        $this->newLine();

        if (!$this->option('sync')) {
            $this->info('Dispatching to queue...');
            \App\Jobs\TrendingArticleJob::dispatch();
            $this->info('✅ Job dispatched! Check queue worker for progress.');
            return Command::SUCCESS;
        }

        // === SYNC MODE ===
        $this->info('Running synchronously with verbose output...');
        $this->newLine();

        // Bước 1: Fetch Google Trends
        $this->info('📡 Step 1: Fetching Google Trends RSS...');
        $this->fetchedTrends = $this->fetchGoogleTrends();

        if (empty($this->fetchedTrends)) {
            $this->error('❌ No trends found!');
            return Command::FAILURE;
        }

        $this->info("✅ Found " . count($this->fetchedTrends) . " trending topics:");
        $this->newLine();

        $this->table(
            ['#', 'Topic', 'Traffic'],
            collect($this->fetchedTrends)->map(fn($t, $i) => [$i + 1, Str::limit($t['title'], 50), $t['traffic'] ?: 'N/A'])->toArray()
        );
        $this->newLine();

        // Bước 2: Gọi AI
        $this->info('🤖 Step 2: Calling OpenAI to generate article...');
        $this->info('   (This may take 30-60 seconds)');

        $openai = new OpenAIService();
        $prompt = $this->buildPrompt($this->fetchedTrends);

        $startTime = microtime(true);
        $response = $openai->generateContent($prompt, [
            'temperature' => 0.85,
            'max_tokens' => 4096,
        ]);
        $elapsed = round(microtime(true) - $startTime, 2);

        if (empty($response)) {
            $this->error('❌ OpenAI returned empty response!');
            return Command::FAILURE;
        }

        $this->info("✅ AI response received in {$elapsed}s");
        $this->newLine();

        // Bước 3: Parse và lưu
        $this->info('💾 Step 3: Parsing and saving article...');

        $result = $this->parseAndSaveArticle($response);

        if ($result) {
            $this->newLine();
            $this->info('🎉 SUCCESS! Article created:');
            $tableData = [
                ['ID', $result['id']],
                ['Title', Str::limit($result['title'], 60)],
                ['Slug', $result['slug']],
                ['Genre', $result['genre']],
                ['Thumbnail', $result['thumbnail'] ?? 'None'],
                ['URL', url("/tin-tuc/{$result['slug']}")],
            ];
            $this->table(['Field', 'Value'], $tableData);
        } else {
            $this->error('❌ Failed to save article!');
            return Command::FAILURE;
        }

        $this->newLine();
        $this->info('✅ All done!');

        return Command::SUCCESS;
    }

    protected function fetchGoogleTrends(): array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/122 Safari/537.36',
                    'Accept-Language' => 'vi,en-US;q=0.9',
                ])
                ->get($this->trendsUrl);

            if (!$response->successful()) {
                $this->error("HTTP Error: {$response->status()}");
                return [];
            }

            $xml = simplexml_load_string($response->body());

            if (!$xml || !isset($xml->channel->item)) {
                $this->error('Invalid RSS XML structure');
                return [];
            }

            $trends = [];
            $count = 0;

            foreach ($xml->channel->item as $item) {
                if ($count >= 15) break;

                $title = (string) $item->title;
                $traffic = '';

                $htNamespace = $item->children('ht', true);
                if (isset($htNamespace->approx_traffic)) {
                    $traffic = (string) $htNamespace->approx_traffic;
                }

                $trends[] = [
                    'title' => $title,
                    'traffic' => $traffic,
                    'pubDate' => (string) $item->pubDate,
                ];

                $count++;
            }

            return $trends;
        } catch (\Throwable $e) {
            $this->error("Exception: {$e->getMessage()}");
            return [];
        }
    }

    protected function buildPrompt(array $trends): string
    {
        $trendsList = '';
        foreach ($trends as $i => $trend) {
            $num = $i + 1;
            $traffic = $trend['traffic'] ? " - {$trend['traffic']}" : '';
            $trendsList .= "{$num}. {$trend['title']}{$traffic}\n";
        }

        $today = Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y');

        return <<<PROMPT
Bạn là một copywriter chuyên viết bài SEO cho trang kết quả xổ số (KQXS) và giải mã giấc mơ.

Ngày hôm nay: {$today}

## Bước 1: Phân tích xu hướng Google Trends Vietnam

Dưới đây là danh sách các chủ đề đang trending tại Việt Nam:

{$trendsList}

Từ danh sách trên, hãy:
1. Chọn ra 1 chủ đề PHÙ HỢP NHẤT để viết bài giải mã giấc mơ/số đề
2. Ưu tiên chủ đề liên quan đến: người nổi tiếng, sự kiện thể thao, tin tức hot, hiện tượng tự nhiên
3. Có thể liên kết tự nhiên với giấc mơ (VD: Cầu thủ X ghi bàn → Mơ thấy đá bóng đánh con gì?)
4. TRÁNH chủ đề nhạy cảm: chính trị, tôn giáo, thảm họa chết người, bạo lực

## Bước 2: Viết bài SEO hoàn chỉnh

Dựa trên chủ đề đã chọn, viết bài viết chuẩn SEO với cấu trúc sau:

### Định dạng output (BẮT BUỘC tuân theo):

```
===META_START===
TITLE: [Tiêu đề bài viết - kết hợp chủ đề trending + giải mã giấc mơ + từ khóa số đề]
META_DESCRIPTION: [150-160 ký tự, chứa từ khóa chính, thu hút click]
META_KEYWORDS: [5-7 từ khóa, cách nhau bằng dấu phẩy]
EXCERPT: [Mô tả ngắn 100-150 ký tự]
===META_END===

===CONTENT_START===
[Nội dung bài viết HTML hoàn chỉnh theo cấu trúc bên dưới]
===CONTENT_END===
```

### Cấu trúc nội dung bài viết (trong phần CONTENT):

<h2>[Tiêu đề chính - SEO friendly, chứa từ khóa]</h2>

<h3>Cập nhật tin tức trending</h3>
<p>[200-300 từ: Tóm tắt ngắn gọn sự kiện/chủ đề đang hot, thông tin mới nhất, dẫn dắt tự nhiên sang phần giải mã giấc mơ]</p>

<h3>Liên hệ với giấc mơ</h3>
<p>[300-400 từ: Giải thích mối liên hệ giữa chủ đề trending và giấc mơ, các trường hợp mơ liên quan thường gặp, ý nghĩa tâm linh, điềm báo]</p>

<h3>Bảng số đề chi tiết</h3>
<p>[400-500 từ: Liệt kê các trường hợp cụ thể với con số tương ứng theo sổ mơ]</p>

<table class="table table-bordered">
<thead><tr><th>Giấc mơ</th><th>Con số may mắn</th><th>Ghi chú</th></tr></thead>
<tbody>
[Ít nhất 10 dòng với các trường hợp cụ thể]
</tbody>
</table>

<h3>Mẹo đánh số hiệu quả</h3>
<p>[150-200 từ: Thời điểm nên đánh, cách ghép số, lưu ý khi áp dụng]</p>

<h3>Câu hỏi thường gặp</h3>
[3-5 câu hỏi FAQ format:]
<p><strong>Câu hỏi?</strong></p>
<p>Trả lời...</p>

<h3>Kết luận</h3>
<p>[Tóm tắt nội dung, lời khuyên, mời xem KQXS trực tiếp]</p>
<p>Xem thêm: <a href="/ket-qua-xo-so-mien-bac">KQXS Miền Bắc</a> | <a href="/ket-qua-xo-so-mien-nam">KQXS Miền Nam</a> | <a href="/ket-qua-xo-so-mien-trung">KQXS Miền Trung</a></p>

### Yêu cầu SEO kỹ thuật:

1. Từ khóa:
   - Primary: [Chủ đề trending] + "đánh con gì" / "số mấy"
   - Secondary: "giải mã giấc mơ", "sổ mơ", "KQXS hôm nay", "số đề"

2. Internal Links: Thêm 3-5 links nội bộ đến các trang KQXS

3. Định dạng:
   - Đoạn văn ngắn (3-4 câu)
   - Sử dụng bảng cho số đề
   - Bullet points cho danh sách
   - Bold cho con số quan trọng

4. Tone: Thân thiện, gần gũi, cập nhật, thời sự. Không quá mê tín.

### Những điều KHÔNG được làm:

❌ Sử dụng từ AI: "unleash", "unlock", "craft", "leverage", "delve"
❌ Cụm từ sáo rỗng: "Bạn có đang...", "Chắc hẳn bạn..."
❌ Nội dung nhạy cảm, phản cảm
❌ Thông tin sai lệch về sự kiện
❌ Khuyến khích cờ bạc quá mức
❌ Viết bằng tiếng Anh - CHỈ viết tiếng Việt

Hãy viết bài viết hoàn chỉnh ngay bây giờ.
PROMPT;
    }

    protected function parseAndSaveArticle(string $response): ?array
    {
        $title = '';
        $metaDescription = '';
        $metaKeywords = '';
        $excerpt = '';
        $content = '';

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

        if (preg_match('/===CONTENT_START===(.+?)===CONTENT_END===/s', $response, $contentMatch)) {
            $content = trim($contentMatch[1]);
            $content = preg_replace('/^```html?\s*\n?/i', '', $content);
            $content = preg_replace('/\n?```\s*$/i', '', $content);
        }

        // Thêm phần trending keywords
        $content = $this->appendTrendingKeywords($content);

        if (empty($title) || empty($content)) {
            $this->error('Failed to parse: title or content empty');
            $this->line('Response preview: ' . Str::limit($response, 300));
            return null;
        }

        $this->info("   📝 Title: {$title}");

        $genre = Genre::where('slug', 'giai-ma-giac-mo')->first()
            ?? Genre::where('slug', 'tin-xo-so')->first()
            ?? Genre::first();

        if (!$genre) {
            $this->error('No genre found!');
            return null;
        }

        $slug = $this->uniqueSlug($title);

        $existsToday = Article::whereDate('created_at', Carbon::today('Asia/Ho_Chi_Minh'))
            ->where('title', 'LIKE', '%' . Str::limit($title, 50, '') . '%')
            ->exists();

        if ($existsToday) {
            $this->warn('Similar article already exists today, skipping');
            return null;
        }

        // Generate thumbnail if not disabled
        $thumbnail = null;
        $avatar = null;

        if (!$this->option('no-thumb')) {
            $this->info('   🎨 Generating thumbnail with DALL-E...');
            $openai = new OpenAIService();
            $thumbnail = $openai->generateThumbnail($title, $slug);

            if ($thumbnail) {
                $avatar = $thumbnail;
                $this->info("   ✅ Thumbnail: {$thumbnail}");
            } else {
                $this->warn('   ⚠️ Thumbnail generation failed, continuing without image');
            }
        }

        $article = Article::create([
            'genre_id' => $genre->id,
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt ?: Str::limit(strip_tags($content), 200),
            'content' => $content,
            'thumbnail' => $thumbnail,
            'avatar' => $avatar,
            'meta_title' => $title,
            'meta_description' => $metaDescription ?: Str::limit(strip_tags($content), 160),
            'meta_keywords' => $metaKeywords,
            'highlight' => 0,
            'hidden' => 0,
            'published_at' => Carbon::now('Asia/Ho_Chi_Minh'),
            'copyright' => 'AI Generated',
            'post_type' => 'trending',
        ]);

        $article->genres()->syncWithoutDetaching([$genre->id]);

        return [
            'id' => $article->id,
            'title' => $title,
            'slug' => $slug,
            'genre' => $genre->slug,
            'thumbnail' => $thumbnail,
        ];
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

    protected function appendTrendingKeywords(string $content): string
    {
        if (empty($this->fetchedTrends)) {
            return $content;
        }

        $today = Carbon::now('Asia/Ho_Chi_Minh')->format('d/m/Y');

        $keywordItems = [];
        foreach ($this->fetchedTrends as $trend) {
            $title = htmlspecialchars($trend['title'], ENT_QUOTES, 'UTF-8');
            $traffic = $trend['traffic'] ? " ({$trend['traffic']})" : '';
            $keywordItems[] = "<li><strong>{$title}</strong> đánh con gì{$traffic}</li>";
        }

        $keywordsList = implode("\n", $keywordItems);

        $trendingSection = <<<HTML

<div class="trending-keywords-section" style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #007bff;">
<h3>🔥 Xu hướng tìm kiếm hot nhất hôm nay ({$today})</h3>
<p>Dưới đây là tổng hợp các chủ đề đang được tìm kiếm nhiều nhất tại Việt Nam, kèm theo gợi ý con số may mắn:</p>
<ul class="trending-list">
{$keywordsList}
</ul>
<p><em>Cập nhật xu hướng từ Google Trends Vietnam. Xem thêm <a href="/giai-ma-giac-mo">giải mã giấc mơ</a> và <a href="/so-mo">sổ mơ</a> để tìm con số phù hợp.</em></p>
</div>
HTML;

        return $content . $trendingSection;
    }
}
