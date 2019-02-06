<?php

namespace App\Policies;

use App\User;
use App\Platform;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlatformPolicy
{
    use HandlesAuthorization;

    /**
     * @param \App\User $user
     * @param string $ability
     * @return bool
     */
    public function before(User $user, string $ability)
    {
        return $user->isAdmin();
    }

    /**
     * @param \App\User $user
     * @return bool
     */
    public function lists(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * @param \App\User $user
     * @param \App\Platform $platform
     * @return bool
     */
    protected function assignedAPlatform(User $user, Platform $platform)
    {
        return $user->isAdmin();
        //return $platform->users()->where('user_id', $user->id)->count() > 0;
    }

    /**
     * Determine whether the user can view the platform.
     *
     * @param  \App\User $user
     * @param  \App\Platform $platform
     * @return mixed
     */
    public function view(User $user, Platform $platform)
    {
        return $this->assignedAPlatform($user, $platform);
    }

    /**
     * Determine whether the user can create platforms.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the platform.
     *
     * @param  \App\User $user
     * @param  \App\Platform $platform
     * @return mixed
     */
    public function update(User $user, Platform $platform)
    {
        return $this->assignedAPlatform($user, $platform);
    }

    /**
     * Determine whether the user can delete the platform.
     *
     * @param  \App\User $user
     * @param  \App\Platform $platform
     * @return mixed
     */
    public function delete(User $user, Platform $platform)
    {
        return $this->assignedAPlatform($user, $platform);
    }

    /**
     * Determine whether the user can restore the platform.
     *
     * @param  \App\User $user
     * @param  \App\Platform $platform
     * @return mixed
     */
    public function restore(User $user, Platform $platform)
    {
        return $this->assignedAPlatform($user, $platform);
    }

    /**
     * Determine whether the user can permanently delete the platform.
     *
     * @param  \App\User $user
     * @param  \App\Platform $platform
     * @return mixed
     */
    public function forceDelete(User $user, Platform $platform)
    {
        return $this->assignedAPlatform($user, $platform);
    }
}
