<?php

namespace App\Policies;

use App\User;
use App\NotificationType;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotificationTypePolicy
{
    use HandlesAuthorization;

    /**
     * @param \App\User $user
     * @param $ability
     * @return bool|null
     */
    public function before(User $user, $ability)
    {
        return $user->isAdmin() ? true : null;
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, NotificationType $notification_type)
    {
        return $user->isAdmin();
    }
}
