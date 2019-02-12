<?php

namespace App\Listeners;

use App\Notifications\SerializerFailedNotification;
use App\Services\Serializers\JsonSerializer;
use App\Services\Translation\SerializedDataHandler;
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

            // Created
            $handler = new SerializedDataHandler($file, $serialized);
            $handler->createSerializedRecords();
        } catch (Exception $exception) {
            User::admins()->get()->each(function ($admin) use ($file, $exception) {
                /** @var User $admin */
                $admin->notify(new SerializerFailedNotification($file, $exception));
            });
        }
    }
}
