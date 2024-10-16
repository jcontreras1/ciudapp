<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\RegionSubcategory;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class ApiRegionSubcategoryController extends Controller
{
    public function store(Region $region, Request $request)
    {
        $request->validate([
            'subcategory_id' => 'required|exists:subcategory,id',
        ]);

        $regionSubcategory = RegionSubcategory::firstOrCreate([
            'region_id' => $region->id,
            'subcategory_id' => $request->subcategory_id,
        ]);

        return response($region->loadMissing(['subcategories', 'points']), 201);
    }
    public function all(Region $region, Request $request)	{
        $subcategories = Subcategory::whereHas('category', function($q) {
            $q->whereNull('deleted_at');
        })->get();
        foreach ($subcategories as $subcategory) {            
            
            $regionSubcategory = RegionSubcategory::firstOrCreate([
                'region_id' => $region->id,
                'subcategory_id' => $subcategory->id,
            ]);
        }
        return response($region->loadMissing(['subcategories', 'points']), 201);
    }

    public function destroy(Region $region, RegionSubcategory $regionSubcategory)
    {
        $regionSubcategory->userRegionSubcategories()->delete();
        $regionSubcategory->delete();
        return response($region->loadMissing(['subcategories', 'points']), 200);
    }


    public function destroyAll(Region $region, Request $request)	{
        
        foreach($region->regionSubcategories as $subcategory) {
            //elimino todos los user_region_subcategory asociados a la subcategoria
            // foreach($subcategory->userRegionSubcategories as $userRegionSub){
                $subcategory->userRegionSubcategories()->delete();
            // }
            $subcategory->delete();
        }
        return response($region->loadMissing(['subcategories', 'points']), 200);
    }
}
