<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\GetBoundsFromAddressesService;
use Illuminate\Http\Request;

class ApiMapController extends Controller
{
    public function bounds(Request $request)
    {
        $calle1 = $request->input('calle1');
        $calle2 = $request->input('calle2');
        $calle3 = $request->input('calle3');
        $calle4 = $request->input('calle4');

    //  return [$calle1, $calle2, $calle3, $calle4];

     $bounds = GetBoundsFromAddressesService::getBoundsWithShifts(
            $calle1,
            $calle2,
            $calle3,
            $calle4
        );

        return response()->json($bounds);
    }
}
