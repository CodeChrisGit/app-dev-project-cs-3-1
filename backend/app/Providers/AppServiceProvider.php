<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Only users flagged as admin can use the /api/admin routes
        Gate::define('admin', fn (User $user) => (bool) $user->is_admin);
    }
}
