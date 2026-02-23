<?php

namespace Mortezaa97\SmsManager\Policies;

use App\Models\User;
use Mortezaa97\SmsManager\Models\SmsDriver;

class SmsDriverPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, SmsDriver $smsDriver): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, SmsDriver $smsDriver): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, SmsDriver $smsDriver): bool
    {
        return $user->hasRole('admin');
    }
}
