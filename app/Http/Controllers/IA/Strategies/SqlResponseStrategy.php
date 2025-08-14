<?php

namespace App\Http\Controllers\IA\Strategies;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;

class SqlResponseStrategy implements ResponseStrategy
{
    public function handle(array $parts, string $originalPrompt): array
    {
        $queries = array_filter(array_map('trim', explode(";", $parts[1])));
        $results = [];

        foreach ($queries as $query) {
            try {
                $fixedQuery = str_replace(
                    ['FROM posts', 'JOIN posts', 'FROM users', 'JOIN users'], 
                    ['FROM post', 'JOIN post', 'FROM user', 'JOIN user'], 
                    $query
                );
                $result = DB::select($fixedQuery);
                $results[] = $result;
            } catch (\Exception $e) {
                return [
                    "response" => "No ha sido posible ejecutar la query: $query. Por favor intenta redactar la pregunta de otra manera.",
                    "query" => $query
                ];
            }
        }

        $contexto = "
        Eres un asistente que interpreta los resultados de una consulta SQL ejecutada en base a la pregunta original del usuario: '$originalPrompt'. 
        Los datos obtenidos son: " . json_encode($results) . ".
            
        Reglas de interpretación:
        - Nunca menciones IDs internos de base de datos.
        - Si la pregunta es un conteo simple, responde únicamente con el número correspondiente, sin texto extra.
        - Si la pregunta busca relación o semejanza entre posts:
            - Describe cada evento usando calles, altura, ciudad, fecha, hora y subcategoría.
            - Indica distancias aproximadas y orden cronológico.
            - Usa un tono natural, como un informe breve, no técnico.
            - Si hay más de un par relacionado, menciona cada uno de forma clara.
        - Si no hay datos suficientes, explica de forma simple que no se encontraron coincidencias o registros.
            
        Siempre responde como si estuvieras explicando a una persona que no conoce detalles técnicos.
        ";

        $interpretacion = OpenAI::chat()->create([
            'model' => config('app.openai_model'),
            'messages' => [
                ['role' => 'system', 'content' => $contexto]
            ]
        ]);

        return [
            "response" => $interpretacion->choices[0]->message->content,
            "query" => implode('; ', $queries)
        ];
    }
}
