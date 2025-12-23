<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $key = 'contact-message:' . $request->ip();
        $max = 2; // max allowed successful sends
        $decaySeconds = 60; // per minute

        if (RateLimiter::tooManyAttempts($key, $max)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan. Silakan coba lagi dalam ' . $seconds . ' detik.'
            ], 429)->header('Retry-After', $seconds);
        }

        $data = $request->only(['name','email','phone','subject','message']);

        $validated = $request->validate([
            'name' => ['required','string','max:191'],
            'email' => ['nullable','email','max:191'],
            'phone' => ['nullable','string','max:64'],
            'subject' => ['nullable','string','max:191'],
            'message' => ['required','string','max:5000'],
        ]);

        // Basic sanitization: strip tags for stored values to avoid HTML injection
        $sanitized = array_map(function($v){
            if (is_null($v)) return null;
            return strip_tags(trim((string)$v));
        }, $validated);

        $sanitized['ip'] = $request->ip();
        $sanitized['user_agent'] = $request->header('User-Agent');

        $message = ContactMessage::create($sanitized);

        // Count only successful sends
        RateLimiter::hit($key, $decaySeconds);

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dikirim',
            'data' => [ 'id' => $message->id ]
        ]);
    }
}
