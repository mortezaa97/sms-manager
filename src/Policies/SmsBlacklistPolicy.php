<?php

declare(strict_types=1);

namespace Mortezaa97\SmsManager\Policies;

use App\Models\User;
use Mortezaa97\SmsManager\Models\SmsBlacklist;

class SmsBlacklistPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, SmsBlacklist $smsBlacklist): bool
    {
        return $user->id === $smsBlacklist->created_by || $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, SmsBlacklist $smsBlacklist): bool
    {
        return $user->id === $smsBlacklist->created_by || $user->hasRole('admin');
    }

    public function delete(User $user, SmsBlacklist $smsBlacklist): bool
    {
        return $user->id === $smsBlacklist->created_by || $user->hasRole('admin');
    }

    public function restore(User $user, SmsBlacklist $smsBlacklist): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, SmsBlacklist $smsBlacklist): bool
    {
        return $user->hasRole('admin');
    }
}
