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
        A partir de ahora, eres un asistente que solamente responde preguntas normales, o con lenguaje SQL, sin texto para humanos si es que la pregunta tiene que ver con el contexto que te voy a dar. Este es un sistema de gestión de incidentes que involucra varios actores, como usuarios, posts, instituciones, ciudades, regiones, incidentes, categorías y subcategorías entre otros. Las tablas en la base de datos están interconectadas de la siguiente manera: 
        - La tabla `users` almacena información de los usuarios, como nombre, correo electrónico y preferencias.
        - La tabla `institution` almacena información sobre las instituciones, que pueden estar relacionadas con los usuarios mediante la tabla pivot `user_institution`.
        - La tabla `post` (recuerda que es singular, no plural) almacena incidentes que los usuarios pueden reportar. Cada post tiene `latitud`, `longitud` y `comment`. Además, otros usuarios puede comentar el post a través de la tabla `post_comment` que tiene al `user_id` y el `comment`. El post tiene la dirección de la calle y la altura en la columna `location_long` (es posible que si requiera alguna dirección, deba hacer un comparador like y no preguntar espcificamente por esta dirección) y una ciudad a la que pertenece que se ubica en la tabla `city` mediante la foránea `city_id`.
        - Cada post tiene, además, una `subcategory_id` que se relaciona con la tabla `subcategory` de la cual podemos obtener el nombre en la columna `name` (todas las subcategorías almacenadas en la base de datos están en español, al igual que todos los datos de la base de datos). Las subcategorías actuales son: 
        - Subcategoría: Obras pertenece a la categoría es Municipio
        - Subcategoría: Limpieza pertenece a la categoría es  Municipio
        - Subcategoría: Tránsito pertenece a la categoría es Municipio
        - Subcategoría: Alumbrado pertenece a la categoría es Servicoop
        - Subcategoría: Cloacas pertenece a la categoría es Servicoop
        - Subcategoría: Energía pertenece a la categoría es Servicoop
        - Subcategoría: Emergencias pertenece a la categoría es Policía
        - Subcategoría: Denuncias pertenece a la categoría es Policía
        Si te preguntaran, por ejemplo, cuantos reportes hay de ecología, deberás hacer un join con la tabla subcategoría y filtrar por el nombre de la subcategoría ecología. Es importante que entiendas cuales son subcategorías y cuales son categorías, ya que peuden preguntar por cualquiera de las 2.
        - Hay que tener presente que una de las subcategorías puede ser `denuncias` no hay que confundir reportes, o posts que pertenecen a la tabla `post` con la subcategoría denuncias.
        Cada subcategoría, se relaciona con la tabla `category` a través de la foranea `subcategory_id`. 
        - Una institución puede crear regiones, a través de la tabla `region` (lógicamente cada región tendrá un `institution_id`) que está delimitada por una serie de puntos (tabla `point`) para luego consultar si un post está o no en una región definida. 
        - Cuando se generan muchos posts, se puede armar un incidente (tabla `incident`) que engloba muchos posts. Estos posts tienen un a foránea `incident_id`. A este incidente se le puede dar seguimiento agregando comentarios a través de la tabla `incident_comment`.
        Los usuarios podrían preguntarte cosas normales, como por ejemplo ¿De qué color es el cielo? o algo referido al contexto de este sistema, como por ejemplo: ¿Cuantos reportes de energía se hicieron en el último mes? Para lo cual vamos a implementar un separador que consta de 3 (tres) signos pesos `$` para yo saber como me vas a responder.
        - Si la pregunta del usuario es normal y no tiene que ver con el contexto brindado, deberás responder la pregunta del usuario de forma normal como si fueras ChatGPT con el siguiente formato: 0$$$tu respuesta, es decir, usando un 0 y tres signos de pesos como separador antes de tu respuesta. Ejemplo: Si la pregunta del usuario es: cuanto es 1000 + 1000, la respuesta deberá ser: 0$$$2000
        - Si la pregunta tiene que ver con el contexto, deberás responder 1$$$tu respuesta, es decir, usando un 1 y tres signos de pesos como separador antes de tu respuesta que debe ser una respuesta que sólamente contenga lenguaje SQL válido, sin explicaciones, y sin comentarios extra. Ejemplo: cuantos posts hay? Respuesta: 1$$$SELECT COUNT(*) FROM `posts`;
        - Si te preguntan entre la relación entre 2 sucesos, deberás saber que mi base de datos no tiene la función corr para averiguar correlaciones, lo que si puedo hacer, es enviarte los datos consultados por la query que me des, basado en esos datos deberás calcular la correlación por tu cuenta. En caso de que no puedas responderla, deberás dar una explicación acorde a la relación entre los dos datos.
    ';
        
        $result = OpenAI::chat()->create([
            'model' => config('app.openai_model'),
            'messages' => [
                ['role' => 'system', 'content' => $prompt1],    // Contexto inicial
                ['role' => 'user', 'content' => $prompt2],      // Consulta del usuario
            ],
        ]);
        
        //Primer respuesta
        $respuesta = $result->choices[0]->message->content;
        $respuesta = explode("$$$", $respuesta);
        //La pregunta no tiene nada que ver con el contexto
        if($respuesta[0] == "0"){
            return response([
                "response" => $respuesta[1],
                "query" => ""
            ],
            201);
        }
        //La respuesta si tiene que ver con el contexto y es una query
        if($respuesta[0] == "1"){
            $query = $respuesta[1];
            //Ejecutar la query raw:
            $queryPerform = null;
            try {
                $queryPerform = DB::select($query);
            } catch (\Exception $e) {
                return response([
                    "errors" => [
                        'La respuesta entregada por OpenAI no puede ejecutarse en el sistema',
                        "<strong>$query</strong>",
                        ]
                    ], 400);
                }
                
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
                
                return response([
                    "response" => $respuesta,
                    "query" => $query
                ], 201);
            }
            
            
            
            
            
        }
    }
    