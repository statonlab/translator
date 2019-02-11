<?php

namespace App\Observers;

use App\File;

class FileObserver
{
    /**
     * Handle the file "created" event.
     *
     * @param  \App\File  $file
     * @return void
     */
    public function deleting(File $file) {
        // Delete all translations
    }
}
