<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GetBoundsFromAddressesService extends Controller
{
    public static function getBounds($address1, $address2, $address3, $address4, $city = 'Puerto Madryn', $state = 'Chubut', $country = 'Argentina')
    {
        $addresses = [
            self::normalizeStreet($address1),
            self::normalizeStreet($address2),
            self::normalizeStreet($address3),
            self::normalizeStreet($address4),
        ];
        
        $bounds = [];
        
        for ($i = 0; $i < count($addresses); $i++) {
            for ($j = $i + 1; $j < count($addresses); $j++) {
                $street1 = $addresses[$i];
                $street2 = $addresses[$j];
                
                $vertex = GetVertexFromStreetsService::getVertex($street1, $street2, $city, $state, $country);
                
                if ($vertex) {
                    $bounds[] = $vertex;
                } else {
                    \Log::warning("No se encontró intersección válida para: {$street1} y {$street2}");
                }
            }
        }
        
        return $bounds;
    }

    public static function getBoundsWithShifts($address1, $address2, $address3, $address4, $city = 'Puerto Madryn', $state = 'Chubut', $country = 'Argentina')
{
    $addresses = [
        self::normalizeStreet($address1),
        self::normalizeStreet($address2),
        self::normalizeStreet($address3),
        self::normalizeStreet($address4),
    ];

    $bounds = [];

    for ($shift = 0; $shift < count($addresses); $shift++) {
        // Generar pares de calles
        for ($i = 0; $i < count($addresses); $i++) {
            for ($j = $i + 1; $j < count($addresses); $j++) {
                $street1 = $addresses[$i];
                $street2 = $addresses[$j];

                $vertex = GetVertexFromStreetsService::getVertex($street1, $street2, $city, $state, $country);

                if ($vertex && !self::vertexExists($bounds, $vertex)) {
                    $bounds[] = $vertex;

                    // Si ya tenemos 4 vértices, cortamos
                    if (count($bounds) === 4) {
                        return $bounds;
                    }
                }
            }
        }

        // Rotar calles para probar nuevos pares
        array_push($addresses, array_shift($addresses));
    }

    return $bounds;
}

private static function vertexExists($bounds, $vertex, $tolerance = 0.00001)
{
    foreach ($bounds as $b) {
        if (abs($b['lat'] - $vertex['lat']) < $tolerance &&
            abs($b['lng'] - $vertex['lng']) < $tolerance) {
            return true;
        }
    }
    return false;
}
    
    /**
    * Limpia y normaliza nombres de calles
    */
    private static function normalizeStreet($street)
    {
        // Quitar espacios extra
        $street = trim($street);
        
        // Convertir a minúsculas y luego capitalizar
        $street = mb_convert_case($street, MB_CASE_TITLE, "UTF-8");
        
        // Opcional: normalizar acentos
        $street = self::normalizeAccents($street);
        
        return $street;
    }
    
    /**
    * Normaliza acentos y caracteres especiales
    */
    private static function normalizeAccents($string)
    {
        $normalizeChars = [
            'á' => 'á', 'à' => 'á', 'ä' => 'á', 'â' => 'á',
            'Á' => 'Á', 'À' => 'Á', 'Ä' => 'Á', 'Â' => 'Á',
            'é' => 'é', 'è' => 'é', 'ë' => 'é', 'ê' => 'é',
            'É' => 'É', 'È' => 'É', 'Ë' => 'É', 'Ê' => 'É',
            'í' => 'í', 'ì' => 'í', 'ï' => 'í', 'î' => 'í',
            'Í' => 'Í', 'Ì' => 'Í', 'Ï' => 'Í', 'Î' => 'Í',
            'ó' => 'ó', 'ò' => 'ó', 'ö' => 'ó', 'ô' => 'ó',
            'Ó' => 'Ó', 'Ò' => 'Ó', 'Ö' => 'Ó', 'Ô' => 'Ó',
            'ú' => 'ú', 'ù' => 'ú', 'ü' => 'ú', 'û' => 'ú',
            'Ú' => 'Ú', 'Ù' => 'Ú', 'Ü' => 'Ú', 'Û' => 'Ú',
            'ñ' => 'ñ', 'Ñ' => 'Ñ'
        ];
        
        return strtr($string, $normalizeChars);
    }
    
}
