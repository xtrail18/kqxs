<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Article;
use App\Models\Genre;
use Carbon\Carbon;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate single sitemap.xml for home, search, genres, and articles';
    private string $base = 'https://kqxs.online';
    private int $MAX_URLS_ALLOWED = 50000; // chuẩn sitemap khuyến nghị

    public function handle()
    {
        $this->info("Đang tạo sitemap cho kqxs.online (1 file duy nhất)...");

        // Thu thập URL
        $urls = [];

        // 1) Trang chủ + Tìm kiếm (static)
        $urls[] = $this->makeUrl($this->abs('/'), null, 'hourly', '1.0');
        $urls[] = $this->makeUrl($this->abs('/tim-kiem'), null, 'weekly', '0.6');

        // 2) Genres
        Genre::query()
            ->where('hidden', 0)
            ->where('slug', '!=', '')
            ->orderByDesc('updated_at')
            ->chunkById(200, function ($genres) use (&$urls) {
                foreach ($genres as $g) {
                    $urls[] = $this->makeUrl(
                        $this->abs('/' . $this->e($g->slug)),
                        $g->updated_at ?? now(),
                        'daily',
                        '0.7'
                    );
                }
            });

        // 3) Articles (published)
        Article::published()
            ->where('slug', '!=', '')
            ->orderByDesc('published_at')
            ->chunkById(500, function ($articles) use (&$urls) {
                foreach ($articles as $a) {
                    $lastmod = $a->updated_at ?? $a->published_at ?? now();
                    $urls[] = $this->makeUrl(
                        $this->abs('/tin/' . $this->e($a->slug) . '.html'),
                        $lastmod,
                        $this->changefreqFor($lastmod),
                        '0.8'
                    );
                }
            });

        // (Tuỳ chọn) Cảnh báo nếu vượt quá giới hạn 50k URL
        $count = count($urls);
        if ($count > $this->MAX_URLS_ALLOWED) {
            $this->warn("⚠️ Số URL = {$count} vượt quá giới hạn 50,000 URL của sitemap tiêu chuẩn. Google có thể bỏ qua phần dư.");
        }

        // 4) Xuất đúng 1 file sitemap.xml
        Storage::disk('public')->makeDirectory('sitemaps');
        $xml = $this->buildUrlsetXml($urls);
        Storage::disk('public')->put('sitemaps/sitemap.xml', $xml);

        $this->info("✅ Đã tạo: storage/app/public/sitemaps/sitemap.xml");
        $this->info("👉 Public URL (sau khi storage:link): /storage/sitemaps/sitemap.xml");
    }

    private function abs(string $path): string
    {
        return rtrim($this->base, '/') . '/' . ltrim($path, '/');
    }

    private function e(string $segment): string
    {
        return rawurlencode($segment);
    }

    private function makeUrl(string $loc, $lastmod = null, string $changefreq = 'daily', string $priority = '0.8'): array
    {
        return [
            'loc'        => htmlspecialchars($loc, ENT_XML1, 'UTF-8'),
            'lastmod'    => Carbon::parse($lastmod ?? now())->toDateString(),
            'changefreq' => $changefreq,
            'priority'   => $priority,
        ];
    }

    private function changefreqFor($lastmod): string
    {
        $days = Carbon::parse($lastmod)->diffInDays(now());
        if ($days <= 1)  return 'hourly';
        if ($days <= 7)  return 'daily';
        if ($days <= 30) return 'weekly';
        return 'monthly';
    }

    private function buildUrlsetXml(array $urls): string
    {
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($urls as $url) {
            $xml .= "  <url>" . PHP_EOL;
            $xml .= "    <loc>{$url['loc']}</loc>" . PHP_EOL;
            $xml .= "    <lastmod>{$url['lastmod']}</lastmod>" . PHP_EOL;
            $xml .= "    <changefreq>{$url['changefreq']}</changefreq>" . PHP_EOL;
            $xml .= "    <priority>{$url['priority']}</priority>" . PHP_EOL;
            $xml .= "  </url>" . PHP_EOL;
        }

        $xml .= '</urlset>' . PHP_EOL;
        return $xml;
    }
}
