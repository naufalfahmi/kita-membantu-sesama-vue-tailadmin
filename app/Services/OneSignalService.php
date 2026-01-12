<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneSignalService
{
    public function sendToExternalId(string $externalId, string $title, string $message, ?string $url = null): bool
    {
        return $this->sendToExternalIds([$externalId], $title, $message, $url);
    }

    public function sendToExternalIds(array $externalIds, string $title, string $message, ?string $url = null): bool
    {
        $appId = config('services.onesignal.app_id');
        $apiKey = config('services.onesignal.api_key');

        if (!$appId || !$apiKey || empty($externalIds)) {
            Log::warning('OneSignal skipped: missing credentials or external ids', [
                'app_id_present' => (bool) $appId,
                'has_external_ids' => !empty($externalIds),
            ]);
            return false;
        }

        $payload = [
            'app_id' => $appId,
            'include_external_user_ids' => array_values($externalIds),
            'headings' => ['en' => $title],
            'contents' => ['en' => $message],
        ];

        if ($url) {
            $payload['url'] = $url;
        }

        $response = Http::withHeaders([
            'Authorization' => "Basic {$apiKey}",
            'Content-Type' => 'application/json',
        ])->post('https://api.onesignal.com/notifications', $payload);

        if ($response->failed()) {
            Log::error('OneSignal request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        }

        // Log response body for debugging (contains recipients/delivery info)
        try {
            Log::info('OneSignal response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $e) {
            // ignore logging errors
        }

        return true;
    }
}
