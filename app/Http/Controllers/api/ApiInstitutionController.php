<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Post;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class ApiInstitutionController extends Controller
{
    public function reports(Institution $institution, Request $request) {
        // Necesito las regiones, y los reportes
        $regiones = [];
        foreach ($institution->regions as $region) {
            $regiones[] = [
                "name" => $region->name,
                "points" => array_map(function($item) {
                    return [$item['lng'], $item['lat']];
                }, $region->points->toArray())
            ];
        }
        
        if ($request->subcategories == null) {
            return response([
                'reportes' => [],
                'regiones' => $regiones,
            ], 200);
        }
        
        $reportes = [];
        $posts = Post::whereIn('subcategory_id', $request->subcategories)
                     ->orderBy('id', 'desc')
                     ->take(1000)
                     ->get();
                     
        foreach ($posts as $post) {
            foreach ($institution->regions as $region) {
                if (postInRegion($post, $region)) {
                    // Utiliza las coordenadas como clave para evitar duplicados
                    $key = "{$post->lat},{$post->lng}";
                    if (!isset($reportes[$key])) {
                        $reportes[$key] = ['lat' => $post->lat, 'lng' => $post->lng];
                    }
                }
            }
        }
        
        // Convierte el array asociativo a un array simple
        $reportes = array_values($reportes);
        
        return response([
            'reportes' => $reportes,
            'regiones' => $regiones,
        ], 200);
    }
    
    
    public function subcategories(Institution $institution){
        $subcategories = [];
        foreach($institution->regions as $region){
            foreach($region->subcategories as $subcategory){
                if(!in_array($subcategory->id, $subcategories)){
                    $subcategories[] = $subcategory->id;
                }
            }
        }
        $sub = Subcategory::whereIn('id', $subcategories)->get();
        return response($sub, 200);
    }
}
