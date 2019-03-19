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
        $languages = $user->languages()->where('platform_id', $platform->id)->get();

        return $this->compute($languages);
    }

    /**
     * Compute progress on a language.
     *
     * @param TranslatedLine[]|\Illuminate\Support\Collection $language
     * @return float
     */
    public function compute($languages): float
    {
        if (! is_array($languages) && isset($languages->id)) {
            $languages = collect([$languages]);
        }

        $ids = $languages->pluck('id')->values();

        $complete = TranslatedLine::current()
            ->whereIn('language_id', $ids)
            ->where(function ($query) {
                $query->whereNotNull('value');
                $query->where('value', '!=', '');
                $query->where('needs_updating', false);
            })
            ->count();

        $total = TranslatedLine::current()->whereIn('language_id', $ids)->count();

        return $total === 0 ? 0 : $complete / $total * 100;
    }
}
