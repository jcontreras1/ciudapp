<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GetBoundsFromAddressesService extends Controller
{
    public static function getbounds($address1, $address2, $address3, $address4, $city = 'Puerto Madryn', $state = 'Chubut', $country = 'Argentina')
    {
        $addresses = [$address1, $address2, $address3, $address4];
        $bounds = [];
        
        for ($i = 0; $i < count($addresses); $i++) {
            for ($j = $i + 1; $j < count($addresses); $j++) {
                $street1 = $addresses[$i];
                $street2 = $addresses[$j];
                
                $vertex = GetVertexFromStreetsService::getVertex($street1, $street2, $city, $state, $country);
                
                if ($vertex) {
                    $bounds[] = $vertex;
                }
            }
        }
        if(count($bounds) !== 4) {
            throw new \Exception('Entre esas calles no hay un polígono válido. Revisar si las calles son correctas.');
        }
        return $bounds;
    }
}
