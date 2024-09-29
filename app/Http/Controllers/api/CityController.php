<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function search(Request $request){
        $query = $request->query('query');

        if (empty($query) || strlen($query) < 3) {
            return response()->json([]);
        }

        $cities = City::where('name', 'LIKE', '%' . $query . '%')->with('province')->get();

        return response()->json($cities);
    }
}
