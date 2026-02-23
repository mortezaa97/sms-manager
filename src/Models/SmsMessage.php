<?php

namespace Mortezaa97\SmsManager\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'cost' => 'decimal:0',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder): void {
            $builder->orderByDesc('created_at');
        });
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(SmsDriver::class, 'driver_id');
    }

    public function pattern(): BelongsTo
    {
        return $this->belongsTo(SmsPattern::class, 'pattern_id');
    }
}

