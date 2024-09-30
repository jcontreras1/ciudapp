<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
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
        Event::listen('App\Events\NewPostEvent', 'App\Listeners\PolygonSearchListener');
        Event::listen('App\Events\NewPostEvent', 'App\Listeners\GetGeocodingFromPostListener');
    }
}
