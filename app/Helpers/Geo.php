<?php
use App\Models\Post;
use App\Models\Region;
use App\Models\User;
use App\Models\UserRegionSubcategory;
function pointInPolygon($point, $vertices, $pointOnVertex = false) {
    
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