<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsGroup extends Model
{
    use SoftDeletes;

    protected $table = 'sms_groups';

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function modelHasGroups(): HasMany
    {
        return $this->hasMany(SmsModelHasGroup::class, 'group_id');
    }
}
