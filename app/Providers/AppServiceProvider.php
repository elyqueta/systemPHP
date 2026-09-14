<?php

namespace App\Providers;

use App\Models\Institution;
use App\Policies\InstitutionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(Institution::class, InstitutionPolicy::class);
    }
}
