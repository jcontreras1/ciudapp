<?php

namespace App\Http\Controllers;

use App\Http\Resources\IncidentResource;
use App\Models\Incident;
use App\Models\IncidentStatus;
use App\Models\Institution;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IncidentController extends Controller
{
    public function index(Institution $institution){

        return Inertia::render('Institucion/Incidents',
    
            [
                'institution' => $institution,
                'incidents' => $institution->incidents
            ]
        );

    }

    public function show(Institution $institution, Incident $incident)
    {
        //Si solamente tiene un post, podría revisar todos reportes que estén cerca
        $postsRelacionados = [];
        if($incident->posts->count() == 1){
            $post = $incident->posts->first();
            $postsRelacionados = postCercanos($post, $institution);
        }

        return Inertia::render(
            'Institucion/Incident',

            [
                'institution' => $institution,
                'postsRelacionados' => $postsRelacionados,
                'myIncident' => new IncidentResource($incident),
                'estados' => IncidentStatus::all(),
            ]
        );

    }
}
