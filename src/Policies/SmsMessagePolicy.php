<?php

namespace Mortezaa97\SmsManager\Policies;

use Mortezaa97\SmsManager\Models\SmsMessage;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SmsMessagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SmsMessage $smsMessage): bool
    {
        return $user->id === $smsMessage->created_by || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SmsMessage $smsMessage): bool
    {
        return $user->id === $smsMessage->created_by || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SmsMessage $smsMessage): bool
    {
        return $user->id === $smsMessage->created_by || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SmsMessage $smsMessage): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SmsMessage $smsMessage): bool
    {
        return $user->hasRole('admin');
    }
}

