<?php

namespace App\Policies;

use App\User;
use App\Language;
use Illuminate\Auth\Access\HandlesAuthorization;

class LanguagePolicy
{
    use HandlesAuthorization;

    /**
     * @param \App\User $user
     * @return bool
     */
    public function lists(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the language.
     *
     * @param  \App\User $user
     * @param  \App\Language $Language
     * @return mixed
     */
    public function view(User $user, Language $Language)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create languages.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the language.
     *
     * @param  \App\User $user
     * @param  \App\Language $Language
     * @return mixed
     */
    public function update(User $user, Language $Language)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the language.
     *
     * @param  \App\User $user
     * @param  \App\Language $Language
     * @return mixed
     */
    public function delete(User $user, Language $Language)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the language.
     *
     * @param  \App\User $user
     * @param  \App\Language $Language
     * @return mixed
     */
    public function restore(User $user, Language $Language)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the language.
     *
     * @param  \App\User $user
     * @param  \App\Language $Language
     * @return mixed
     */
    public function forceDelete(User $user, Language $Language)
    {
        return $user->isAdmin();
    }

    /**
     * Ability to assign maintainers to the language.
     *
     * @param \App\User $user
     * @param \App\Language $language
     * @return bool
     */
    public function assign(User $user, Language $language) {
        return $user->isAdmin();
    }
}
