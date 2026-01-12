<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\OneSignalService;

class PushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'endpoint' => 'required|string',
            'keys.auth' => 'nullable|string',
            'keys.p256dh' => 'nullable|string',
            'device' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        $data = $validator->validated();
        $subscription = PushSubscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'endpoint' => $data['endpoint'],
            ],
            [
                'auth_key' => $request->input('keys.auth'),
                'p256dh' => $request->input('keys.p256dh'),
                'device' => $data['device'] ?? null,
                'user_agent' => $request->userAgent(),
                'subscribed_at' => now(),
                'last_seen_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $subscription,
        ]);
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'endpoint' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $deleted = PushSubscription::where('user_id', $request->user()->id)
            ->where('endpoint', $request->endpoint)
            ->delete();

        return response()->json([
            'success' => true,
            'deleted' => $deleted,
        ]);
    }

    public function test(Request $request, OneSignalService $push)
    {
        $user = $request->user();

        $sent = $push->sendToExternalId((string) $user->id, 'Test Notifikasi', 'Notifikasi percobaan dari aplikasi.', url('/admin/dashboard'));

        if (! $sent) {
            return response()->json([
                'success' => false,
                'message' => 'OneSignal request failed'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Test notification sent.'
        ]);
    }
}
