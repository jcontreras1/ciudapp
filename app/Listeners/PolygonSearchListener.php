<?php

namespace App\Listeners;

use App\Events\NewPostEvent;
use App\Models\Region;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
                        $email = $userRegionSubcategory->user->email;
                        
                        // Append
                        $match[$email][$region->name . ' - ' . $region->institution->name] = $regionSubcategory->subcategory->name;
                        info("El post {$post->id} está en la región {$region->name} y se notificará a {$email}");
                    }
                }
            }
        }

        if (!empty($match)) {
            // info(json_encode($match));
            foreach($match as $clave => $valor){
                $user = User::find($clave);
                $user->notify(new PostInRegion($post, $valor));
                 info("Se notifica a $clave que el post $valor está en la región");
            }
        }
    }
}
