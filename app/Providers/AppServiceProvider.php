<?php

namespace App\Providers;

use App\Models\Institution;
use App\Policies\InstitutionPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Gate::policy(Institution::class, InstitutionPolicy::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Event::listen('App\Events\NewPostEvent', 'App\Listeners\PolygonSearchListener');
        // Event::listen('App\Events\NewPostEvent', 'App\Listeners\GetGeocodingFromPostListener');
    }
}
