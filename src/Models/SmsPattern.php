<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Models;

use Illuminate\Database\Eloquent\Model;

class SmsPattern extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [];
}
