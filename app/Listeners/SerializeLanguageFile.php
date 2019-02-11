<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SerializeLanguageFile
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\FileCreated $event
     * @return void
     */
    public function handle($event)
    {
        $file = $event->file;
    }
}
