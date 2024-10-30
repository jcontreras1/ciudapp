<?php

namespace App\Listeners;

use App\Events\NewPostEvent;
use App\Mail\NuevoReporte;
use App\Models\Region;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;


class PolygonSearchListener
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
        /** Busco las zonas que tengan un polígono que contenga a este post */
        $post = $event->post;
        $putoABuscar = ['lat' => $post->lat, 'lng' => $post->lng];
        $match = [];
        $regions = Region::all()->filter(function ($region) use ($putoABuscar) {
            $poli = $region->points->map(fn($point) => ['lat' => $point->lat, 'lng' => $point->lng])->toArray();
            return pointInPolygon($putoABuscar, $poli);
        });
        
        foreach ($regions as $region) {
            $regionSubcategories = $region->regionSubcategories;
            
            foreach ($regionSubcategories as $regionSubcategory) {
                if ($regionSubcategory->subcategory_id == $post->subcategory_id) {
                    foreach ($regionSubcategory->userRegionSubcategories as $userRegionSubcategory) {
                        $user = $userRegionSubcategory->user->id;
                        
                        // Append
                        $match[$user][$region->institution->name . ' - ' . $region->name] = $regionSubcategory->subcategory->name;
                        // info("El post {$post->id} está en la región {$region->name} y se notificará a {$user}");
                    }
                }
            }
        }

        if (!empty($match)) {
            // info(json_encode($match));
            foreach($match as $userId => $regionData){
                //Debería revisar si el usuario quiere ser notificado
                $user = User::find($userId);
                $regionInfo = collect($regionData)->map(function($subcategory, $region) {
                    return $region; //, Subcategoría: $subcategory";
                });

                Mail::to($user->email)->send(new NuevoReporte($post, $regionInfo));

            }
        }
    }
}
