<?php

namespace App\Listeners;

use App\Events\NewPostEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;


class GetGeocodingFromPostListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewPostEvent $event): void
    {
        $post = $event->post;
        $url = "https://api.mapbox.com/search/geocode/v6/reverse?longitude=$post->lng&latitude=$post->lat&access_token=" . config('app.mapbox_api_key');

        $response = Http::get($url);
        info($response);
        $response = $response->object();
        $post->update([
            'location_short' => $response->features[0]?->properties?->place_formatted,
            // 'location_long' => $response->display_name,
        ]);
    }
}
