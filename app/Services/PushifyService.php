<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushifyService
{
    public function sendPersonal(string $subscriberId, string $title, string $description, ?string $url = null): bool
    {
        $apiKey = config('services.pushify.api_key');
        $websiteId = config('services.pushify.website_id');

        if (!$apiKey || !$websiteId || !$subscriberId) {
            Log::warning('Pushify skipped: missing credentials or subscriber id', [
                'subscriber_id' => $subscriberId,
            ]);
            return false;
        }

        $payload = [
            'name' => 'attendance-reminder-' . now()->timestamp,
            'website_id' => $websiteId,
            'subscriber_id' => $subscriberId,
            'title' => $title,
            'description' => $description,
        ];

        if ($url) {
            $payload['url'] = $url;
        }

        $response = Http::withToken($apiKey)
            ->asMultipart()
            ->post('https://pushify.com/api/personal-notifications', $payload);

        if ($response->failed()) {
            Log::error('Pushify request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        }

        return true;
    }
}
