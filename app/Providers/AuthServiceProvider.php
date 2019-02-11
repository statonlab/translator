<?php

namespace App\Providers;

use App\File;
use App\Language;
use App\Platform;
use App\Policies\FilePolicy;
use App\Policies\LanguagePolicy;
use App\Policies\PlatformPolicy;
use App\Policies\UserPolicy;
use App\User;
use Illuminate\Support\Facades\Gate;
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
        File::class => FilePolicy::class
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
