<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Authorize listing tables
     *
     * @param \App\User $user
     * @return bool
     */
    public function lists(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * @param \App\User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * @param \App\User $user
     * @param \App\User $user2
     * @return bool
     */
    public function view(User $user, User $user2)
    {
        return $user->isAdmin() || $user->id === $user2->id;
    }

    public function delete(User $user, User $user2) {
        return $this->view($user, $user2);
    }
}
