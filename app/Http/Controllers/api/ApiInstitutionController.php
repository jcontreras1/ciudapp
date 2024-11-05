<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Institution;
use App\Models\Post;
use App\Models\Subcategory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiInstitutionController extends Controller
{
    public function reports(Institution $institution, Request $request) {
        
        if ($request->filled('from') && $request->filled('to')) {
            $fromDate = Carbon::parse($request->from);
            $toDate = Carbon::parse($request->to);
        
            // Validar que 'from' sea antes o igual a 'to'
            if ($fromDate->gt($toDate)) {
                return response()->json(['La fecha "desde" debe ser anterior o igual a la fecha "hasta".'], 400);
            }
        }
        
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
        ->when($request->filled('withIncidents'), function ($query) use ($request) {
            if (filter_var($request->withIncidents, FILTER_VALIDATE_BOOLEAN)) {
                // Incluir todos los registros si `withIncidents` es `true`
                return $query;
            } else {
                // Filtrar solo registros donde `incident_id` sea `null` si `withIncidents` es `false`
                return $query->whereNull('incident_id');
            }
        })
        ->when($request->filled('from'), function ($query) use ($request) {
            // Filtrar por la fecha de `from`, solo si se proporciona
            return $query->where('created_at', '>=', Carbon::parse($request->from)->startOfDay());
        })
        ->when($request->filled('to'), function ($query) use ($request) {
            // Filtrar por la fecha de `to`, solo si se proporciona
            return $query->where('created_at', '<=', Carbon::parse($request->to)->endOfDay());
        })
        ->orderBy('id', 'desc')
        ->take(1000)
        ->get();
        
        foreach ($posts as $post) {
            foreach ($institution->regions as $region) {
                if (postInRegion($post, $region)) {
                    // Utiliza las coordenadas como clave para evitar duplicados
                    $key = "{$post->lat},{$post->lng}";
                    if (!isset($reportes[$key])) {
                        $reportes[$key] = [
                            'lat' => $post->lat,
                            'lng' => $post->lng,
                            'likes' => $post->likes->count() + 1,
                            'post' => new PostResource($post),
                        ];
                    }
                }
            }
        }
        
        // Convierte el array asociativo a un array simple
        $reportes = array_values($reportes);
        $reportesPrueba = [[
            "lat"=> -42.768084555886304,
            "lng"=> -65.03802376260235,
            "likes"=> 8
        ],
        [
            "lat"=> -42.768097,
            "lng"=> -65.038312,
            "likes"=> 0
        ],
        [
            "lat"=> -42.76551,
            "lng"=> -65.04839,
            "likes"=> 1
            ]];
            return response([
                'reportes' => $reportes,
                // 'reportes' => $reportesPrueba,
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
    