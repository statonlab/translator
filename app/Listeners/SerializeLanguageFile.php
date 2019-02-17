<?php

namespace App\Listeners;

use App\Events\SerializationCompleted;
use App\Notifications\SerializerFailedNotification;
use App\Services\Serializers\JsonSerializer;
use App\Services\Translation\SerializedDataHandler;
use App\TranslatedLine;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use Exception;

class SerializeLanguageFile implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param \App\Events\FileCreated $event
     * @return void
     */
    public function handle($event)
    {
        $file = $event->file;

        try {
            // Serialize the data
            $serializer = new JsonSerializer();
            $serialized = $serializer->serialize($file);

            // Mark all previously translated lines as not current
            TranslatedLine::where('is_current', true)->update(['is_current' => false]);

            // Created
            $handler = new SerializedDataHandler($file, $serialized);
            $handler->createSerializedRecords();

            event(new SerializationCompleted($file->platform));
        } catch (Exception $exception) {
            foreach (User::admins()->get() as $admin) {
                /** @var User $admin */
                $admin->notify(new SerializerFailedNotification($file, $exception));
            }
        }
    }
}
