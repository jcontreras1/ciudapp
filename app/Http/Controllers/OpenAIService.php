<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;


use Illuminate\Http\Request;

class OpenAIService extends Controller
{
    public function generarRespuesta(Request $request)
    {
        $prompt2 = $request->input('prompt');
        $prompt1 = '
        A partir de ahora, eres un asistente que solamente responde con lenguaje SQL, sin texto para humanos. Este es un sistema de gestión de incidentes que involucra varios actores, como usuarios, posts, instituciones, ciudades, regiones, incidentes, categorías y subcategorías entre otros. Las tablas en la base de datos están interconectadas de la siguiente manera: 
        - La tabla `users` almacena información de los usuarios, como nombre, correo electrónico y preferencias.
        - La tabla `institution` almacena información sobre las instituciones, que pueden estar relacionadas con los usuarios en la tabla `user_institution`.
        - La tabla `post` almacena incidentes que los usuarios pueden reportar. Cada post puede tener comentarios de los usuarios almacenados en `post_comment`. El post tiene la dirección de la calle y la altura en la columna `location_long` (es posible que si requiera alguna dirección, deba hacer un like y no preguntar espcificamente por esta dirección) y una ciudad a la que pertenece (tabla `city`) mediante la foránea `city_id`.
        - Cada post tiene, además de una latitud y longitud, una `subcategory_id` que se relaciona con la tabla `subcategory` de la cual podemos obtener el nombre en la columna `name` (todas las subcategorías almacenadas en la base de datos están en español, al igual que todos los datos de la base de datos). Esta subcategoría, a la vez, tiene una categoría más general en la tabla `category`. 
        - Una institución puede crear regiones, a través de la tabla `region` que está delimitada por una serie de puntos (tabla `point`) para luego consultar si un post está o no en una región definida. 
        - Cuando se generan muchos posts, se puede armar un incidente (tabla `incident`) que engloba muchos posts. A este incidente se le puede dar seguimiento agregando comentarios a través de la tabla `incident_comment`.
        - Hay que recordar que una de las subcategorías puede ser `denuncias` no hay que confundir reportes, o posts con denuncias.
        Tu tarea es responder preguntas sobre Posts, usuarios, instituciones y relaciones entre estos elementos utilizando solamente lenguaje SQL sin comillas ni palabras raras.
    ';
        
        $result = OpenAI::chat()->create([
            'model' => config('app.openai_model'),
            'messages' => [
                ['role' => 'system', 'content' => $prompt1], // Contexto inicial
                ['role' => 'user', 'content' => $prompt2],  // Consulta del usuario
            ],
        ]);
        $query = $result->choices[0]->message->content;
        //Ejecutar la query raw:
        $queryPerform = DB::select($query);
        
        
        // Verificar si $queryPerform es un array o un resultado único
        if (is_array($queryPerform)) {
            // Si es un array, podemos usar implode para convertirlo en una cadena (por ejemplo, si los resultados son filas de datos)
            $queryPerform = implode(', ', array_map(function($item) {
                // Si cada elemento de $queryPerform es un objeto, extraemos solo las propiedades necesarias
                return implode(' ', (array) $item);
            }, $queryPerform));
        }
        
        $segundoContexto = "Ahora eres un asistente que, basado en la pregunta original que hizo el usuario, que es: '$prompt2'. Deberás responder con los siguientes datos obtenidos de la consulta.: " . $queryPerform;
        
        $result = OpenAI::chat()->create([
            'model' => config('app.openai_model'),
            'messages' => [
                ['role' => 'system', 'content' => $segundoContexto], 
            ],
        ]);
        $respuesta = $result->choices[0]->message->content;
        
        return response(
            [
                "response" => $respuesta,
                "query" => $query
            ], 201);
    }
}
