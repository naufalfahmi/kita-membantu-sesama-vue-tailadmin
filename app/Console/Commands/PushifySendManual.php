<?php

namespace App\Console\Commands;

use App\Services\PushifyService;
use Illuminate\Console\Command;

class PushifySendManual extends Command
{
    protected $signature = 'pushify:send-manual {subscriber_id} {--title=Test Notification} {--description=Test from CLI} {--url=}';

    protected $description = 'Send a Pushify personal notification to a given subscriber id (dev only)';

    public function handle(PushifyService $pushify): int
    {
        if (!app()->environment('local') && !config('app.debug')) {
            $this->error('This command is for local/debug only.');
            return self::FAILURE;
        }

        $subscriber = $this->argument('subscriber_id');
        $title = $this->option('title') ?: 'Test Notification';
        $description = $this->option('description') ?: 'Test from CLI';
        $url = $this->option('url') ?: url('/');

        $this->info('Sending to subscriber: ' . $subscriber);
        $ok = $pushify->sendPersonal($subscriber, $title, $description, $url);

        if ($ok) {
            $this->info('Send OK');
            return self::SUCCESS;
        }

        $this->error('Send FAILED — check logs/config');
        return self::FAILURE;
    }
}
