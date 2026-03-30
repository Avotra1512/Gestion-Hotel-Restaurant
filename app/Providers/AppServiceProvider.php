<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        Gate::define('access-admin', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('access-gerant', function (User $user) {
            return $user->role === 'admin' || $user->role === 'gerant';
        });
    }
}
