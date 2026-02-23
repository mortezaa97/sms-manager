<?php

namespace Mortezaa97\SmsManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsModelHasGroup extends Model
{
    use SoftDeletes;

    protected $table = 'sms_model_has_groups';

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(SmsGroup::class, 'group_id');
    }

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }
}
