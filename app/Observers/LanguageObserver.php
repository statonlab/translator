<?php

namespace App\Observers;

use App\Events\LanguageCreated;
use App\Language;

class LanguageObserver
{
    /**
     * Handle the language "created" event.
     *
     * @param  \App\Language $language
     * @return void
     */
    public function created(Language $language)
    {
        event(new LanguageCreated($language));
    }
}
