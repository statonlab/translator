<?php

namespace App\Policies;

use App\User;
use App\TranslatedLine;
use Illuminate\Auth\Access\HandlesAuthorization;

class TranslatedLinePolicy
{
    use HandlesAuthorization;

    /**
     * All admins are allowed to modify a translated line.
     *
     * @param \App\User $user
     * @param string $ability
     * @return bool
     */
    public function before(User $user, string $ability)
    {
        return $user->isAdmin();
    }

    /**
     * Check if the user is assigned the language.
     *
     * @param \App\User $user
     * @param \App\TranslatedLine $translatedLine
     * @return bool
     */
    public function isAssigned(User $user, TranslatedLine $translatedLine): bool
    {
        return $translatedLine->language->users()
                ->where('users.id', $user->id)
                ->count() > 0;
    }

    /**
     * Determine whether the user can view the translated line.
     *
     * @param  \App\User $user
     * @param  \App\TranslatedLine $translatedLine
     * @return mixed
     */
    public function view(User $user, TranslatedLine $translatedLine)
    {
        return $this->isAssigned($user, $translatedLine);
    }

    /**
     * Determine whether the user can update the translated line.
     *
     * @param  \App\User $user
     * @param  \App\TranslatedLine $translatedLine
     * @return mixed
     */
    public function update(User $user, TranslatedLine $translatedLine)
    {
        return $this->isAssigned($user, $translatedLine);
    }
}
