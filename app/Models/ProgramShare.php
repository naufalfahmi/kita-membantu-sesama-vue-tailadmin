<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramShare extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'program_shares';

    protected $fillable = ['program_id', 'program_share_type_id', 'type', 'value', 'created_by', 'updated_by', 'deleted_by'];

    protected $casts = [
        'value' => 'decimal:2',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ProgramShareType::class, 'program_share_type_id');
    }
}
