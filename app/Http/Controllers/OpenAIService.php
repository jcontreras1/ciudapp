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

        $prompt1 = "Objetivo: A partir de ahora, eres un asistente que responde únicamente de dos maneras:

Si la pregunta es general (no relacionada con la base de datos), responde con el formato 0^^^respuesta.
Si la pregunta está relacionada con la base de datos, responde exclusivamente en SQL válido con el formato 1^^^consulta SQL, sin explicaciones ni comentarios adicionales. Todas las sql devueltas, deberán estar escritas en una sola linea sin saltos de línea.
Contexto del sistema: Este es un sistema de gestión de incidentes que involucra usuarios, publicaciones (posts), instituciones, ciudades (city), regiones (region), incidentes (incident), categorías (category) y subcategorías (subcategory).

Estructura de la base de datos:

Users: Contiene nombre, correo y preferencias.
Institutions (institution): Se relacionan con usuarios mediante la tabla pivote user_institution.
Posts (post): Contienen latitud, longitud, comment, location_long (dirección) y city_id. Están relacionados con subcategorías mediante subcategory_id.
Los usuarios pueden comentar en post_comment (user_id, comment).
Incidents (incident): Varios posts pueden formar un incidente.
Subcategories (subcategory): Se relacionan con categories (category) mediante category_id.
Ejemplo: 'Obras' → 'Municipio', 'Energía' → 'ervicoop', 'Denuncias' → 'Policía'.
Regions (region): Creadas por instituciones (institution_id). Definidas por puntos geográficos (point).
Tiempos (created_at, updated_at, deleted_at): No mostrar registros con deleted_at distinto de NULL. No incluir estos campos en listas de registros.
Reglas de respuesta:

Si la pregunta es general (fuera del contexto del sistema):
Responde en el formato 0^^^respuesta.
Ejemplo:
Pregunta: '¿Cuánto es 1000 + 1000?'
Respuesta: 0^^^2000.

Si la pregunta es sobre la base de datos:
Responde en el formato 1^^^consulta SQL.
Ejemplo:
Pregunta: '¿Cuántos posts hay?'
Respuesta: 1^^^SELECT COUNT(*) FROM post WHERE deleted_at IS NULL.

Si se menciona una categoría, verificar su relación con una subcategoría antes de filtrar.

Si se menciona una dirección, usar LIKE en location_long.

Si preguntan por correlaciones:
Genera la consulta para obtener los datos necesarios. Si los datos no permiten calcular una correlación, explica la relación con base en la estructura del sistema.

Reglas adicionales para consultas de relación entre incidentes:

Si se pregunta por la relación entre Tránsito y Accidentes en una calle y altura específicas:

Obtener los datos filtrando los posts donde subcategory_id corresponda a 'Tránsito' o 'Accidentes'.
Usar location_long LIKE '%[calle] [altura]%' para encontrar coincidencias.
Responder con SQL:

1^^^SELECT COUNT(*) AS total_transito 
     FROM post 
     WHERE subcategory_id = (SELECT id FROM subcategory WHERE name = 'Tránsito') 
     AND location_long LIKE '%Manuel Belgrano 100%' 
     AND deleted_at IS NULL

SELECT COUNT(*) AS total_accidentes 
     FROM post 
     WHERE subcategory_id = (SELECT id FROM subcategory WHERE name = 'Accidentes') 
     AND location_long LIKE '%Manuel Belgrano 100%' 
     AND deleted_at IS NULL

 Si los resultados se proveen, calcular la correlación entre ambas variables.

Si la correlación no es viable, dar un análisis basado en la cantidad de reportes y su proximidad temporal.
"; 

        // Solicitar respuesta a OpenAI
        $result = OpenAI::chat()->create([
            'model' => config('app.openai_model'),
            'messages' => [
                ['role' => 'system', 'content' => $prompt1],    // Contexto inicial
                ['role' => 'user', 'content' => $prompt2],      // Consulta del usuario
            ],
        ]);
        
        // Primer respuesta de OpenAI
        $respuesta = $result->choices[0]->message->content;
        $respuesta = explode("^^^", $respuesta); // Dividimos la respuesta por el separador
        
        // Si la respuesta es general (no relacionada con la base de datos)
        if ($respuesta[0] == "0") {
            return response([
                "response" => $respuesta[1],
                "query" => ""
            ], 201);
        }

        // Si la respuesta está relacionada con la base de datos y contiene una o más queries
        if ($respuesta[0] == "1") {
            // Limpiamos el texto de la consulta eliminando saltos de línea y espacios innecesarios
            $queries = explode(";", trim($respuesta[1]));

            // Eliminamos los saltos de línea y dejamos las queries en una sola línea
            // $queries = array_map(function($query) {
            //     // Reemplazamos múltiples espacios por uno solo y eliminamos los saltos de línea
            //     return preg_replace('/\s+/', ' ', trim($query));
            // }, $queries);

            $results = [];
            foreach ($queries as $query) {
                // Validamos que la consulta no esté vacía
                if (empty($query)) {
                    continue; // Si la consulta está vacía, la saltamos
                }

                // Ejecutar la consulta SQL
                $queryPerform = null;
                try {
                    $queryPerform = DB::select($query);
                } catch (\Exception $e) {
                    return response([
                        "response" => "No ha sido posible ejecutar la query: $query. Por favor intenta redactar la pregunta de otra manera.",
                        "query" => $query
                    ], 500);  // Cambié el código de estado a 500, que es más adecuado para errores del servidor
                }

                // Verificar si $queryPerform es un array o un resultado único
                if (is_array($queryPerform)) {
                    // Si es un array, los unimos en un solo string
                    $queryPerform = implode(', ', array_map(function($item) {
                        return implode(' ', (array) $item);
                    }, $queryPerform));
                }

                $results[] = $queryPerform; // Guardamos el resultado de cada consulta
            }

            // Concatenamos los resultados obtenidos de las consultas
            $resultContent = implode("\n", $results);

            // Regenerar el contexto para OpenAI con los resultados obtenidos
            $segundoContexto = "Ahora eres un asistente que, basado en la pregunta original que hizo el usuario, que es: '$prompt2'. Deberás responder con los siguientes datos obtenidos de las consultas SQL: \n" . $resultContent;

            // Hacer una nueva llamada a OpenAI con los resultados de la consulta
            $result = OpenAI::chat()->create([
                'model' => config('app.openai_model'),
                'messages' => [
                    ['role' => 'system', 'content' => $segundoContexto], 
                ],
            ]);
            $respuestaFinal = $result->choices[0]->message->content;

            // Devolver la respuesta final con los resultados
            return response([
                "response" => $respuestaFinal,
                "query" => implode('; ', $queries)  // Concatenamos las queries y las devolvemos como una cadena única
            ], 201);
        }
    }
}


        
        


     