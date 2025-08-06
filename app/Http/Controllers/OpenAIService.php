<?php
namespace App\Http\Controllers;
use App\Models\Subcategory;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Http\Request;

class OpenAIService extends Controller
{
    public function generarRespuesta(Request $request)
    {
        $listaDeSubcategorias = Subcategory::with('category')
        ->select('id', 'name', 'category_id')
        ->get()
        ->map(function($subcat) {
            return (object)[
                'id' => $subcat->id,
                'name' => $subcat->name,
                'category_name' => $subcat->category ? $subcat->category->name : null,
            ];
        });
        $subcategorias = [];
        foreach ($listaDeSubcategorias as $subcategory) {
            $subcategorias[] = [
                'id' => $subcategory->id,
                'name' => $subcategory->name,
            ];
        }
        
        $prompt2 = $request->input('prompt');
        
        if (preg_match('/eventos? relacionados?/i', $prompt2)) {
            $prompt2 = "¿Qué posts están relacionados por cercanía y categoría?";
        }
        
        $prompt1 = "
        
Objetivo: Eres un asistente que responde únicamente en dos formatos según el tipo de pregunta:
- Preguntas generales (no relacionadas con la base de datos): Responde con '0^^^respuesta'.
- Preguntas sobre la base de datos: Responde con '1^^^consulta SQL' en una sola línea, sin saltos ni explicaciones adicionales.
        
Contexto: Sistema de gestión de incidentes con entidades: usuarios (users), publicaciones (post), instituciones (institutions), ciudades (city), regiones (region), incidentes (incident), categorías (category) y subcategorías (subcategory).
        
Estructura de la base de datos:
- Users: Campos: name, email, preferences.
- Institutions: Relacionadas con users vía tabla pivote user_institution.
- Posts: Campos: latitude (lat), longitude (lng), comment, location_long (dirección), city_id, subcategory_id. Relacionados con subcategorías.
- Post_Comment: Campos: user_id, comment.
- Incidents: Agrupan múltiples posts.
- Subcategories: Relacionadas con categories vía category_id (ejemplo: 'Obras' → 'Municipio', 'Energía' → 'Servicoop', 'Denuncias' → 'Policía', 'Delitos' → 'Robo', 'Ecología' → 'Contaminación').
- Regions: Creadas por institutions (institution_id), definidas por puntos geográficos (point).
- Tiempos: Campos created_at, updated_at, deleted_at. Excluir registros con deleted_at NOT NULL.

IMPORTANTE:
Los nombres exactos de las tablas en SQL son:
- user
- post
- institution
- incident
- subcategory
- category
- city
- region
- post_comment
No uses nombres en plural ni versiones alternativas. Usá exactamente estos nombres.

Por ejemplo:
Pregunta: ¿Qué posts están cerca?
Respuesta: 1^^^SELECT p1.id AS post1_id, p2.id AS post2_id, (6371 * acos(cos(radians(p1.lat)) * cos(radians(p2.lat)) * cos(radians(p2.lng) - radians(p1.lng)) + sin(radians(p1.lat)) * sin(radians(p2.lat)))) * 1000 AS distance FROM post p1 JOIN post p2 ON p1.id < p2.id WHERE p1.deleted_at IS NULL AND p2.deleted_at IS NULL HAVING distance < 100

        
Reglas de respuesta:
1. Preguntas generales:
   - Formato: '0^^^respuesta
   - Ejemplo: Pregunta: '¿Qué es la ecología?' → Respuesta: '0^^^Estudio de las relaciones entre organismos y su entorno'.
        
2. Preguntas sobre la base de datos:
   - Formato: '1^^^consulta SQL' (una sola línea, sin punto final).
   - Ejemplo: Pregunta: '¿Cuántos posts existen?' → Respuesta: '1^^^SELECT COUNT(*) FROM post WHERE deleted_at IS NULL'.
        
3. Filtros específicos:
   - Categorías/Subcategorías: Verificar relación con subcategory antes de filtrar.
   - Direcciones: Usar LIKE en location_long (ejemplo: LIKE '%Calle 123%').
   - Si no hay suficientes datos, intentá generar una consulta ejemplo igualmente, aunque sea con condiciones genéricas, y luego indicá que puede requerir ajustes.
    - Solo si la pregunta no tiene ningún sentido o no se puede relacionar con ningún dato del sistema, usá '0^^^análisis basado en estructura'.
        
4. Relación entre posts (cercanía geográfica y subcategorías):
   - Si se pregunta por posts relacionados:
     - **Cercanía geográfica**: Usa la fórmula de Haversine para calcular la distancia entre posts (en metros). Umbral por defecto: 100 metros, ajustable si se especifica.
     - **Subcategorías relacionadas**: Evalúa si las subcategorías de los posts están vinculadas según estas reglas:
       - Relacionadas: 'Denuncias' con 'Delitos' (mismo contexto de seguridad).
       - No relacionadas: 'Ecología' con cualquier otra (contexto aislado).
       - Otras combinaciones: Verificar si comparten category_id o definir relación manualmente si se especifica.
     - - Si se pregunta por posts relacionados:
  - Usá la fórmula de Haversine para calcular distancia (en metros).
  - Incluí en el resultado: `p1.id` y `p2.id` como `post1_id` y `post2_id`.
  - Incluí también los **nombres de las subcategorías** de cada post como `subcategory1` y `subcategory2`, haciendo `JOIN` con la tabla `subcategory`.
  - Ejemplo de campos esperados en la respuesta: `post1_id`, `post2_id`, `subcategory1`, `subcategory2`, `distance`.
  - No uses `subcategory_id` sin nombre, ya que el análisis posterior requiere subcategorías legibles.
        
Notas:
- 'Evento' puede interpretarse como un post o incidente. Si no se especifica, asumí que se refiere a un post.
- Si se pregunta por eventos relacionados, interpretá que busca pares de posts que estén cercanos y compartan subcategoría o categoría relacionada.
- Consultas SQL deben ser válidas y ejecutables.
- No incluir campos temporales en listas de registros.
- Umbral de distancia ajustable si se especifica (ejemplo: 'posts a menos de 50 metros').
- Si se pregunta por posts relacionados, devolvé una consulta que retorne una lista de pares de posts (p1.id, p2.id) junto con sus subcategorías y distancia entre sí.
- Asegurate de incluir el cálculo de distancia (Haversine) y un filtro de subcategorías compatibles (ya sea por nombre, category_id o regla manual).
- La consulta debe estar optimizada para mostrar relaciones útiles y concretas, y evitar repeticiones.
        
Datos adicionales: Estas son las subcategorías disponibles en el sistema:
        
        
" . json_encode($subcategorias) . ".
        
        
Ejemplo de relaciones inferidas automáticamente:
- 'Robo' y 'Violencia' → Relacionadas por contexto de seguridad.
- 'Basura' y 'Contaminación' → Relacionadas por contexto ecológico.
- 'Luminarias' y 'Calles rotas' → Potencialmente relacionadas por contexto urbano.
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
            
            $queries = array_map(function ($query) {
                return str_replace(
                    ['FROM posts', 'JOIN posts', 'FROM users', 'JOIN users'], 
                    ['FROM post', 'JOIN post', 'FROM user', 'JOIN user'], 
                    $query
                );
            }, $queries);
            
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
            
            
            $segundoContexto = "Eres un asistente que interpreta los resultados de una consulta SQL basada en la pregunta original del usuario: '$prompt2'. Los datos obtenidos son: " . json_encode($queryPerform) . ".
            
Tu tarea es responder de forma clara, útil y específica según el tipo de pregunta:
            
- Si la pregunta es un conteo simple (ej: '¿Cuántos posts hay?'), devuelve solo el número o un mensaje corto con el total.
- Si la pregunta busca semejanza entre posts (ej: '¿Qué posts de Denuncias y Delitos están cerca?'), analizá los pares de posts (post1_id, post2_id, distance, subcategory_id). Para cada par:
  - Especificá: los IDs de los posts, sus subcategorías y la distancia entre ellos.
  - Usá frases como: 'Revisá el post con ID X y el ID Y, que están a Z metros de distancia y pertenecen a subcategorías similares.'
  - Si hay varios pares relacionados, listalos uno por uno con esa estructura.
- Si no hay datos suficientes, respondé: 'No hay datos suficientes para responder la pregunta.'";
            
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






