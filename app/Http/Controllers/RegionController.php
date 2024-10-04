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
                'lat' => $pointData[1],
                'lng' => $pointData[0],
                'region_id' => $region->id,
            ]);
        }

        return redirect()->route('institution.edit', $institution)->with('message', 'Región creada correctamente');
    }

    public function edit(Institution $institution, Region $region){
        return Inertia::render('Region/Edit', ['region' => $region->loadMissing('points'), 'institucion' => $institution]);
    }


    public function destroy(Institution $institution, Region $region){
        $region->points()->delete();
        $region->delete();
        return redirect()->route('institution.edit', $institution)->with('message', 'Región eliminada correctamente');
    }

    public function update(Institution $institution, Region $region, Request $request){
        $points = $request->input('puntos');
        if(count($points) < 3){
            return redirect()->back()->with('error', 'Debe ingresar al menos 3 puntos');
        }

        $region->update([
            'name' => $request->input('name'),
        ]);

        $region->points()->delete();
        foreach ($points as $pointData) {
            Point::create([
                'lat' => $pointData[1],
                'lng' => $pointData[0],
                'region_id' => $region->id,
            ]);
        }
        return redirect()->route('institution.edit', $institution)->with('message', 'Región actualizada correctamente');
    }

}
