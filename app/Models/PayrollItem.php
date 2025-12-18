<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PayrollItem extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'payroll_items';

    protected $fillable = ['id', 'payroll_record_id', 'description', 'qty', 'qty_type', 'unit', 'unit_value', 'amount', 'order_index', 'base_item_id'];

    protected $casts = [
        'qty' => 'integer',
        'unit_value' => 'decimal:2',
        'amount' => 'integer',
        'base_item_id' => 'string',
    ];

    public function baseItem()
    {
        return $this->belongsTo(PayrollItem::class, 'base_item_id');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function record()
    {
        return $this->belongsTo(PayrollRecord::class, 'payroll_record_id');
    }
}
