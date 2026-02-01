<?php

namespace App\Console\Commands;

use App\Jobs\TrendingArticleJob;
use Illuminate\Console\Command;

class TrendingArticleCommand extends Command
{
    protected $signature = 'trending:article {--sync : Run synchronously instead of dispatching to queue}';

    protected $description = 'Fetch Google Trends Vietnam and generate SEO article about lottery/dreams';

    public function handle(): int
    {
        $this->info('Starting Trending Article Job...');

        if ($this->option('sync')) {
            $this->info('Running synchronously...');
            (new TrendingArticleJob())->handle();
        } else {
            $this->info('Dispatching to queue...');
            TrendingArticleJob::dispatch();
        }

        $this->info('Done!');

        return Command::SUCCESS;
    }
}
