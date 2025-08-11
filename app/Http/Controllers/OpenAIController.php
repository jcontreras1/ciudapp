<?php

namespace App\Http\Controllers;

use App\Http\Controllers\IA\OpenAIResponder;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class OpenAIController extends Controller
{
     public function generarRespuesta(Request $request, OpenAIResponder $responder)
    {
        $subcategorias = Subcategory::select('id', 'name')->get()->toArray();
        $respuesta = $responder->generarRespuesta($request->input('prompt'), $subcategorias);

        return response($respuesta, 201);
    }
}
