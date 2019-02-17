<?php

namespace App\Listeners;

use App\Notifications\ThereAreNewLinesToTranslate;
use App\TranslatedLine;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendUpdatesToSubscribers implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\SerializationCompleted $event
     * @return void
     */
    public function handle($event)
    {
        info('Started');
        $platform = $event->platform;

        // Check if there are translated lines that need updating or are new
        $has_updates = TranslatedLine::current()
            ->where(function ($query) {
            /** @var \Illuminate\Database\Eloquent\Builder $query */
            $query->where('needs_updating', true);
            $query->orWhereNull('value');
            $query->orWhere('value', '');
        })->exists();
        info('Started: '.$has_updates ? 'yes' : 'no');
        if(!$has_updates) {
            return;
        }

        $platform->users->map(function($user) use($platform){
            /** @var \App\User $user */
            $user->notify(new ThereAreNewLinesToTranslate($platform));
        });
    }
}
