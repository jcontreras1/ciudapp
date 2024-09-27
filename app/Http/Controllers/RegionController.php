<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegionRequest;
use App\Models\Institution;
use App\Models\Point;
use App\Models\Region;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegionController extends Controller
{
    public function index(Institution $institution){
        return $institution->regions;
        
        return Inertia::render('Region/Index',['regiones' => $institution->regions]);
    }

    public function create(Institution $institution){
        return Inertia::render('Region/Create', ['institution' => $institution]);
    }

    public function store(Institution $institution, StoreRegionRequest $request){
        $points = $request->input('puntos');
        if(count($points) < 3){
            return redirect()->back()->with('error', 'Debe ingresar al menos 3 puntos');
        }
        
        $region = Region::create([
            'name' => $request->input('name'),
            'institution_id' => $institution->id,
            'user_id' => auth()->user()->id ?? '1',
        ]);
        foreach ($points as $pointData) {
            Point::create([
                'lat' => $pointData['lat'],
                'lng' => $pointData['lng'],
                'region_id' => $region->id,
            ]);
        }

        return redirect()->route('region.index', $institution)->with('message', 'Región creada correctamente');
    }

    public function edit(Institution $institution, Region $region){
        return Inertia::render('Region/Edit', ['region' => $region, 'institution' => $institution]);
    }

    public function destroy(Institution $institution, Region $region){
        $region->delete();
        return redirect()->route('region.index', $institution)->with('message', 'Región eliminada correctamente');
    }

}
