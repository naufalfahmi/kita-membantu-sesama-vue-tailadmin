<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceReminderLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'tanggal',
        'type',
        'sent_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'sent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
