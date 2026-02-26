<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Policies;

use App\Models\User;
use Mortezaa97\SmsManager\Models\SmsGroup;

class SmsGroupPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, SmsGroup $smsGroup): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, SmsGroup $smsGroup): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, SmsGroup $smsGroup): bool
    {
        return $user->hasRole('admin');
    }
}
