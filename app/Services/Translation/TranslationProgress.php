<?php

namespace App\Services\Translation;

use App\Language;
use App\Platform;
use App\TranslatedLine;
use App\User;
use Illuminate\Support\Collection;

class TranslationProgress
{
    /**
     * @param \App\User $user
     * @return float
     */
    public function user(User $user): float
    {
        return $this->compute($user->languages);
    }

    /**
     * @param \App\Platform $platform
     * @return float
     */
    public function platform(Platform $platform): float
    {
        return $this->compute($platform->languages);
    }

    /**
     * User progress in a specific platform.
     *
     * @param \App\User $user
     * @param \App\Platform $platform
     * @return float
     */
    public function userPlatform(User $user, Platform $platform): float
    {
        return $this->compute($platform->languages()
            ->whereIn('languages.id', $user->languages->pluck('id'))
            ->get());
    }

    /**
     * Compute progress on a language.
     *
     * @param TranslatedLine[]|\Illuminate\Support\Collection $language
     * @return float
     */
    public function compute($languages): float
    {
        if (isset($languages->id)) {
            $languages = collect([$languages]);
        }

        $ids = $languages->pluck('id')->values();

        $incomplete = TranslatedLine::whereIn('language_id', $ids)
            ->whereNull('value')
            ->count();

        $complete = TranslatedLine::whereIn('language_id', $ids)->count();

        return $complete === 0 ? 0 : $incomplete / $complete * 100;
    }
}
