<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\GetBoundsFromAddressesService;
use Illuminate\Http\Request;

class ApiMapController extends Controller
{
    public function bounds(Request $request)
    {
        
        $calles = $request->input('calles');
        
        if (!is_array($calles) || count($calles) < 3) {
            return response()->json(['error' => 'Se requieren al menos 4 calles.'], 422);
        }
        
        $bounds = GetBoundsFromAddressesService::getBoundsWithShifts($calles);
        
        return response()->json($bounds);
    }
}
