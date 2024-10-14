<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\RegionSubcategory;
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

    public function destroy(Region $region, RegionSubcategory $regionSubcategory)
    {
        $regionSubcategory->delete();
        return response($region->loadMissing(['subcategories', 'points']), 200);
    }
}
