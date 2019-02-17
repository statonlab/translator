<?php

namespace App\Providers;

use App\File;
use App\Language;
use App\NotificationType;
use App\Platform;
use App\Policies\FilePolicy;
use App\Policies\LanguagePolicy;
use App\Policies\NotificationTypePolicy;
use App\Policies\PlatformPolicy;
use App\Policies\UserPolicy;
use App\Policies\TranslatedLinePolicy;
use App\TranslatedLine;
use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Language::class => LanguagePolicy::class,
        Platform::class => PlatformPolicy::class,
        File::class => FilePolicy::class,
        TranslatedLine::class => TranslatedLinePolicy::class,
        NotificationType::class => NotificationTypePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
