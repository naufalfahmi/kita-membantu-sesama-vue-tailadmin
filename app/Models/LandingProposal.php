<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandingProposal extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'landing_proposals';

    protected $fillable = [
        'name',
        'proposal_date',
        'file',
        'file_name',
        'file_size',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'proposal_date' => 'date',
    ];
}
