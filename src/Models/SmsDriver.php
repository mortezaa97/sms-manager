<?php

namespace Mortezaa97\SmsManager\Models;

use Illuminate\Database\Eloquent\Model;

class SmsDriver extends Model
{
    protected $table = 'sms_drivers';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'is_default' => 'boolean',
    ];
}
