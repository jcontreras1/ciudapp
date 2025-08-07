<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GetVertexFromStreetsService extends Controller
{
    public static function getVertex($street1, $street2, $city = 'Puerto Madryn', $state = 'Chubut', $country = 'Argentina')
    {
        $apiKey = config('app.google_api_key');
        
        // Codificamos partes individualmente
        $s1 = urlencode($street1);
        $s2 = urlencode($street2);
        $city = urlencode($city);
        $state = urlencode($state);
        $country = urlencode($country);
        
        // Creamos la dirección con intersección explícita (%26)
        $address = "$s1%20%26%20$s2,$city,$state,$country";
        
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$address&key=$apiKey";
        
        // Hacemos la petición
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        
        // Validamos la respuesta
        if (
            isset($data['status']) &&
            $data['status'] === 'OK' &&
            isset($data['results'][0]['geometry']['location']) &&
            in_array('intersection', $data['results'][0]['types'] ?? [])
            ) {
                return [
                    'lat' => $data['results'][0]['geometry']['location']['lat'],
                    'lng' => $data['results'][0]['geometry']['location']['lng'],
                ];
            }
            
            // No se encontró una intersección válida
            return null;
        }
    }
    