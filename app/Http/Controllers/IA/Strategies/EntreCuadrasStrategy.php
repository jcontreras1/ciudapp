<?php

namespace App\Http\Controllers\IA\Strategies;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\GetBoundsFromAddressesService;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;

class EntreCuadrasStrategy implements ResponseStrategy
{
    public function handle(array $parts, string $originalPrompt): array
    {
        $parts= explode('!!!', $parts[1]);
        $sqlBase = $parts[1];
        $calles = array_filter(array_map('trim', explode("||", $parts[0])));
        $calles = array_unique($calles);
        
        if (count($calles) < 4) {
            return [
                "response" => "Se necesitan al menos 4 calles para procesar esta solicitud.",
                "query" => $sqlBase
            ];
        }
        
        
        
        $bounds =  GetBoundsFromAddressesService::getBoundsWithShifts($calles); 
        // $bounds = [
        //     ["lat"=> -42.76362169999999, "lng"=> -65.03483109999999],
        //     ["lat"=> -42.7671362, "lng"=> -65.0343207],
        //     ["lat"=> -42.7670002, "lng"=> -65.0375554],
        //     ["lat"=> -42.7675712, "lng"=> -65.03566599999999]
        // ];
        if (count($bounds) < 4) {
            return [
                "response" => "No se pudieron determinar los límites con las calles proporcionadas. Si una de las calles incluye rotondas, intenta abarcar más área.",
                "query" => $sqlBase
            ];
        }
        // 1. Ejecutar query baseç
        $postConfiltrosBase = DB::select($sqlBase);
        
        
        // 2. Si no hay resultados, corto
        if (empty($postConfiltrosBase)) {
            return [
                "response" => "No hay posts que cumplan los filtros iniciales.",
                "query" => $sqlBase
            ];
        }
        
        // 3. Filtrar por polígono
        $idsDentro = [];
        
        foreach ($postConfiltrosBase as $post) {
            $punto = ['lat' => $post->lat, 'lng' => $post->lng];
            
            if (pointInPolygon($punto, $bounds)) {
                $idsDentro[] = $post->id;
            }
        }
        
        // 4. Si no hay nada dentro
        if (empty($idsDentro)) {
            return [
                "response" => "No se encontraron posts dentro del área definida.",
                "query" => $sqlBase
            ];
        }
        
        // 5. Query final solo con los que están dentro
        $postsFinal = Post::whereIn('id', $idsDentro)->with('subcategory')->get();
        
        
     
            
            $contexto = "
                Eres un asistente que interpreta los resultados de una consulta SQL ejecutada en base a la pregunta original del usuario: '$originalPrompt'. 
                Los datos obtenidos son: " . json_encode($postsFinal) . ".
            
                Reglas de interpretación:
                - Nunca menciones IDs internos de base de datos.
                - Armar una narrativa con los posts que se encuentran dentro de los límites establecidos por las calles.
            
                Siempre responde como si estuvieras explicando a una persona que no conoce detalles técnicos.
                ";
            
            $interpretacion = OpenAI::chat()->create([
                'model' => config('app.openai_model'),
                'messages' => [
                    ['role' => 'system', 'content' => $contexto]
                    ]
                ]);
                
                
                $response = [
                    "response" => $interpretacion->choices[0]->message->content,
                    "query" => $sqlBase,
                    "calles" => $calles,
                    "bounds" => $bounds,
                    "posts" => $postsFinal->map(function($post) {
                        return [
                            'lat' => $post->lat,
                            'lng' => $post->lng
                        ];
                    })->values()->all(),
                ];
                
                return $response;
            }
            
        }
        