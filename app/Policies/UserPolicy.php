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
     * Either a super user or the actual user.
     *
     * @param \App\User $user1
     * @param \App\User $user2
     * @return bool
     */
    protected function isAdminOrOwner(User $user1, User $user2): bool
    {
        return $user1->isAdmin() || $user1->id === $user2->id;
    }

    /**
     * @param \App\User $user
     * @param \App\User $user2
     * @return bool
     */
    public function view(User $user, User $user2)
    {
        return $this->isAdminOrOwner($user, $user2);
    }

    /**
     * @param \App\User $user
     * @param \App\User $user2
     * @return bool
     */
    public function delete(User $user, User $user2)
    {
        return $this->isAdminOrOwner($user, $user2);
    }

    /**
     * @param \App\User $user
     * @param \App\User $user2
     * @return bool
     */
    public function update(User $user, User $user2)
    {
        return $this->isAdminOrOwner($user, $user2);
    }

    /**
     * @param \App\User $user
     * @param \App\User $user2
     * @return bool
     */
    public function show(User $user, User $user2)
    {
        return $this->isAdminOrOwner($user, $user2);
    }
}
