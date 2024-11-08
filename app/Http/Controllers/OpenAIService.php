<?php

namespace App\Http\Controllers;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Completions\CreateResponse;


use Illuminate\Http\Request;

class OpenAIService extends Controller
{
    public function generarRespuesta(Request $request)
    {
        $prompt = $request->input('prompt');
        $prompt = '
                 Imagina que tienes una base de datos de una plataforma de incidentes y publicaciones. 
                 Necesitas generar un sistema que pueda obtener información sobre las publicaciones (post), 
                 incluidas la subcategoría a la que pertenece (a través de subcategory_id). Esta subcategoría pertenece a una categoría
                 que se encuentra en la tabla category y se relaciona mediante category_id dentro de subcategory. Además, la dirección del post 
                 se encuentra en la columna location_long, y la ciudad en la que se encuentra se relaciona con la tabla city
                 mediante la clave foranea city_id.
                ' . $prompt;
        
        $result = OpenAI::chat()->create([
            'model' => config('app.openai_model'),
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);
        
        return response($result->choices[0]->message->content,201);
        // return response($prompt,201);
    }
}
    