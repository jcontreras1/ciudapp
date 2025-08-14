<?php
use App\Http\Resources\PostResource;
use App\Models\Institution;
use App\Models\Post;
use App\Models\Region;
use App\Models\User;
use App\Models\UserRegionSubcategory;
function pointInPolygon($point, $vertices, $pointOnVertex = false) {

    //Ejemplo de input
    /*
        punto: ['lat' => 12.34, 'lng' => 56.78]
        vertices: [
            ['lat' => 12.34, 'lng' => 56.78],
            ['lat' => 12.35, 'lng' => 56.79],
            ['lat' => 12.36, 'lng' => 56.80],
            ['lat' => 12.37, 'lng' => 56.81]
        ]
    */
    
    // Check if the point is inside the polygon or on the boundary
    $intersections = 0;
    $vertices_count = count($vertices);

    for ($i = 1; $i < $vertices_count; $i++) {
        $vertex1 = $vertices[$i - 1];
        $vertex2 = $vertices[$i];

        // Check if point is on an horizontal polygon boundary
        if ($vertex1['lat'] == $vertex2['lat'] && $vertex1['lat'] == $point['lat'] && 
            $point['lng'] > min($vertex1['lng'], $vertex2['lng']) && 
            $point['lng'] < max($vertex1['lng'], $vertex2['lng'])) {
            return true; // En el borde
        }

        if ($point['lat'] > min($vertex1['lat'], $vertex2['lat']) && 
            $point['lat'] <= max($vertex1['lat'], $vertex2['lat']) && 
            $point['lng'] <= max($vertex1['lng'], $vertex2['lng']) && 
            $vertex1['lat'] != $vertex2['lat']) {
            
            $xinters = ($point['lat'] - $vertex1['lat']) * ($vertex2['lng'] - $vertex1['lng']) / 
                       ($vertex2['lat'] - $vertex1['lat']) + $vertex1['lng'];
            
            if ($xinters == $point['lng']) {
                return true; // En el borde
            }
            if ($vertex1['lng'] == $vertex2['lng'] || $point['lng'] <= $xinters) {
                $intersections++;
            }
        }
    }

    // Si el número de intersecciones es impar, el punto está dentro
    return ($intersections % 2 != 0);
}

function interseccionUserRegion(User $user, Region $region) {
    $regionSubcategories = $region->regionSubcategories->pluck('id')->toArray();
    $userRegionSubcategories = UserRegionSubcategory::where('user_id', $user->id)->whereIn('region_subcategory_id', $regionSubcategories)->with('user')->get();
    return $userRegionSubcategories;
}

function postInRegion(Post $post, Region $region) {
    $point = ['lat' => $post->lat, 'lng' => $post->lng];
    $vertices = $region->points->toArray();
    return pointInPolygon($point, $vertices);
}

/**
 * Devuelve los posts cercanos a un post dado, de paso, recorre todas las regiones de la institucion.
 * Si el post está lo suficientemente cerca, pero no está en la región, se agrega a la lista de posts cercanos, aclarando que no está en la región.
 */
function postCercanos(Post $post, Institution $institution, int $offset = 350): array{
    $subcategories = $post->subcategory->relationships->filter(function($relationship){
        // return true;
        return $relationship->pivot->percentage >= 20;
    })->pluck('id')->toArray();
    $subcategories[] = $post->subcategory_id;
    // $posts = Post::where('subcategory_id', $post->subcategory_id)
    $posts = Post::whereIn('subcategory_id', $subcategories)
    ->where('id', '<>', $post->id)
    ->where('incident_id', null)
    ->get();
    $result = [];
    foreach ($posts as $p) {
        $distancia = haversineGreatCircleDistance($post->lat, $post->lng, $p->lat, $p->lng);
        if($distancia <= $offset){
            $enRegion = false;
            foreach($institution->regions as $region){
                if(postInRegion($p, $region)){
                    $enRegion = true;
                    break;
                }
            }
            $postArray = (new PostResource($p))->toArray(request());
            $result[] = array_merge($postArray, [
                'distancia' => $distancia,
                'enRegion' => $enRegion
            ]);
        }
    }
    return $result;
}
function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
    // Convertir de grados a radianes
    $latitudeFrom = deg2rad($latitudeFrom);
    $longitudeFrom = deg2rad($longitudeFrom);
    $latitudeTo = deg2rad($latitudeTo);
    $longitudeTo = deg2rad($longitudeTo);

    // Diferencias
    $latDiff = $latitudeTo - $latitudeFrom;
    $lonDiff = $longitudeTo - $longitudeFrom;

    // Fórmula de haversine
    $a = sin($latDiff / 2) * sin($latDiff / 2) +
         cos($latitudeFrom) * cos($latitudeTo) *
         sin($lonDiff / 2) * sin($lonDiff / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    // Distancia en metros
    return $earthRadius * $c; // Devuelve la distancia en metros
}