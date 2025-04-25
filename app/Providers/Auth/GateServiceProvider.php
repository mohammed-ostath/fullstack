<?php

namespace App\Providers\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class GateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('admin-access', function ($user) {
        return User::whereHas('roles', function ($query) {
            $query->where('name', 'Admin');
            })->exists();
        });
    }
}
