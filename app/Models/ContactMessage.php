<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'contact_messages';

    protected $fillable = [
        'name', 'email', 'phone', 'subject', 'message', 'ip', 'user_agent'
    ];
}
