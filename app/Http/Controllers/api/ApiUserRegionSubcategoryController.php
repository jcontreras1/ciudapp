<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\RegionSubcategory;
use App\Models\UserRegionSubcategory;
use Illuminate\Http\Request;

class ApiUserRegionSubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(RegionSubcategory $regionSubcategory)
    {
        return response($regionSubcategory->users, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegionSubcategory $regionSubcategory, Request $request)
    {
        // $user = auth()->id();
        $user = 1;

        UserRegionSubcategory::firstOrCreate([
            'user_id' => $user,
            'region_subcategory_id' => $regionSubcategory->id
        ]);

        return response([], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RegionSubcategory $regionSubcategory, UserRegionSubcategory $userRegionSubcategory)
    {
        $userRegionSubcategory->delete();
        return response([], 200);
    }
}
