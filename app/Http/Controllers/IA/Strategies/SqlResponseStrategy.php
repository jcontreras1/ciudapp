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

        // Segunda llamada a OpenAI para interpretar los resultados
        $contexto = "Eres un asistente que interpreta los resultados de una consulta SQL basada en la pregunta original del usuario: '$originalPrompt'. Los datos obtenidos son: " . json_encode($results) . ".
        
Tu tarea es responder de forma clara, útil y específica según el tipo de pregunta:

- Si la pregunta es un conteo simple, devuelve solo el número.
- Si la pregunta busca semejanza o relación entre posts, brindar un breve texto enfatizando la calle, altura, número, o datos de ambos 
    posts con distancias, subcategorías, tiempos relativos y una breve narración que ayude. Pero sin entrar en tecnisismos como ID. 
    Sería bueno dar todo el detalle posible de la ubicación donde sucedieron ambas cosas.
- Si no hay datos suficientes, responde con un mensaje claro.";

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
