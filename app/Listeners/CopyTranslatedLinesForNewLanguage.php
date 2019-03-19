<?php

namespace App\Listeners;

use App\Events\FileCreated;
use App\TranslatedLine;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CopyTranslatedLinesForNewLanguage
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\LanguageCreated $event
     * @return void
     */
    public function handle($event)
    {
        /** @var \App\Language $language */
        $language = $event->language;

        $file = $language->platform->files()->current()->first();

        // If we don't have a current file, there is nothing to do
        if (! $file) {
            return;
        }

        // Get the first language so we don't get duplicates
        $language->platform->serializedLines()
            ->where('serialized_lines.file_id', $file->id)
            ->chunk(500, function ($lines) use ($language) {
                foreach ($lines as $line) {
                    TranslatedLine::create([
                        'serialized_line_id' => $line->id,
                        'language_id' => $language->id,
                        'file_id' => $line->file_id,
                        'key' => $line->key,
                        'value' => null,
                        'needs_updating' => false,
                        'is_current' => true,
                    ]);
                }
            });
    }
}
