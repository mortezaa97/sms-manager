<?php

namespace Mortezaa97\SmsManager\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Mortezaa97\SmsManager\Models\SmsPattern;

class SmsPatternPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, SmsPattern $smsPattern): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, SmsPattern $smsPattern): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, SmsPattern $smsPattern): bool
    {
        return $user->hasRole('admin');
    }
}
