<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PayrollRecord extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'payroll_records';

    protected $fillable = ['id', 'payroll_period_id', 'employee_id', 'status', 'total_amount', 'notes', 'transfer_proof', 'created_by', 'updated_by'];

    protected $casts = [
        'total_amount' => 'integer',
    ];

    // canonical statuses
    public const STATUS_PENDING = 'pending';
    public const STATUS_LOCKED = 'locked';
    public const STATUS_TRANSFERRED = 'transferred';

    public static function getStatuses(): array
    {
        return [self::STATUS_PENDING, self::STATUS_LOCKED, self::STATUS_TRANSFERRED];
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function period()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function items()
    {
        return $this->hasMany(PayrollItem::class, 'payroll_record_id')->orderBy('order_index');
    }
}
