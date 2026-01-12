<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushSubscription extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'endpoint',
        'auth_key',
        'p256dh',
        'device',
        'onesignal_player_id',
        'user_agent',
        'subscribed_at',
        'last_seen_at',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    protected $hidden = [
        // hide internal ids from default JSON if needed
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
