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
        $foundRegions = [];
        foreach (Region::all() as $region){
            $poli = $region->points->map(function ($point) {
                return [
                    'lat' => $point->lat,
                    'lng' =>$point->lng,
                ];
            });
            
            if (pointInPolygon($putoABuscar, $poli->toArray())){
                $foundRegions[] = $region->name . ' - ' . $region->institution->name; 
            }else{
                info("El post {$post->id} NO está en la región {$region->name}");
            }
        }

        if (!empty($foundRegions)) {
            $newComment = implode("<br>", $foundRegions);
            $post->update(['comment' => $newComment]);
        }
    }
}
