<?php

namespace App\Observers;

use App\Platform;
use Illuminate\Support\Facades\Storage;

class PlatformObserver
{
    /**
     * Clear all files before deleting a platform.
     *
     * @param  \App\Platform $platform
     * @return void
     */
    public function deleting(Platform $platform)
    {
        // Remove all files form disk.
        $files = $platform->files;

        foreach ($files as $file) {
            Storage::disk('files')->delete($file->name);
        }
    }
}
