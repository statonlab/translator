<?php

namespace App\Providers;

use App\Events\FileCreated;
use App\Events\LanguageCreated;
use App\Events\SerializationCompleted;
use App\Listeners\CopyTranslatedLinesForNewLanguage;
use App\Listeners\SendUpdatesToSubscribers;
use App\Listeners\SerializeLanguageFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        FileCreated::class => [
            SerializeLanguageFile::class,
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SerializationCompleted::class => [
            SendUpdatesToSubscribers::class,
        ],
        LanguageCreated::class => [
            CopyTranslatedLinesForNewLanguage::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        //
    }
}
