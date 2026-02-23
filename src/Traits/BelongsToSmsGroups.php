<?php

namespace Mortezaa97\SmsManager\Traits;

use Mortezaa97\SmsManager\Models\SmsGroup;

trait BelongsToSmsGroups
{
    public function smsGroups(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(
            SmsGroup::class,
            'model',
            'sms_model_has_groups',
            'model_id',
            'group_id'
        )
            ->withPivot('cellphone')
            ->withTimestamps();
    }
}
