<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsDriver extends Model
{
    use SoftDeletes;

    protected $table = 'sms_drivers';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'allow_single' => 'boolean',
        'allow_bulk' => 'boolean',
        'allow_pattern' => 'boolean',
        'is_default' => 'boolean',
    ];
}
