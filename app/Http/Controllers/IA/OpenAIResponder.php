<?php

namespace App\Http\Controllers\IA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\IA\Strategies\GeneralResponseStrategy;
use App\Http\Controllers\IA\Strategies\ResponseStrategy;
use App\Http\Controllers\IA\Strategies\SqlResponseStrategy;
use App\Http\Controllers\IA\Strategies\EntreCuadrasStrategy;
use OpenAI\Laravel\Facades\OpenAI;


class OpenAIResponder extends Controller
{
    protected $strategies = [];
    
    public function __construct()
    {
        $this->strategies = [
            "0" => new GeneralResponseStrategy(),
            "1" => new SqlResponseStrategy(),
            "2" => new EntreCuadrasStrategy(), // tercer caso de uso
        ];
    }
    
    public function generarRespuesta(string $promptUsuario, array $subcategorias)
    {
        $contexto = $this->generarContextoInicial($subcategorias);
        
        $result = OpenAI::chat()->create([
            'model' => config('app.openai_model'),
            'messages' => [
                ['role' => 'system', 'content' => $contexto],
                ['role' => 'user', 'content' => $this->normalizarPrompt($promptUsuario)],
            ],
        ]);
        
        $parts = explode("^^^", $result->choices[0]->message->content);
        $tipo = $parts[0] ?? "0";
        
        if (isset($this->strategies[$tipo])) {
            return $this->strategies[$tipo]->handle($parts, $promptUsuario);
        }
        
        return [
            "response" => "Tipo de respuesta desconocido",
            "query" => ""
        ];
    }
    
    private function generarContextoInicial(array $subcategorias)
    {
        return "
        
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
          - Inclui el created_at que da una relación del tiempo para evaluar cuando sucedió cada cosa y ver si hay similitudes
          - Inclui 'location_long' (cuidado que a veces es null) para obtener información de altura y calle de la ciudad.
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
    }
    
    private function normalizarPrompt(string $prompt)
    {
        if (preg_match('/eventos? relacionados?/i', $prompt)) {
            return "¿Qué posts están relacionados por cercanía y categoría?";
        }
        return $prompt;
    }
}
