<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Grant all permissions to super admin and master admin roles implicitly
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole(['super admin', 'master admin']) ? true : null;
        });
    }
}
