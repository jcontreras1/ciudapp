<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInstitutionRequest;
use App\Models\Institution;
use Illuminate\Http\Request;
use Inertia\Inertia;




class InstitutionController extends Controller
{
    public function index(){

        $instituciones = Institution::with('city.province')->orderBy('name', 'asc')->paginate(10);
        return Inertia::render('Institucion/Index', ['instituciones' => $instituciones]);

    }

    public function store(StoreInstitutionRequest $request){
        Institution::create($request->validated());
        return redirect()->route('institution.index')->with('message', 'Institución creada correctamente');
    }

    public function edit(Institution $institution){
        return Inertia::render('Institucion/Edit',
        [
            'institucion' => $institution->loadMissing('city.province'),
            'regiones' => $institution->regions,
            'users' => $institution->users,
            ]
        );
    }

    public function create(){
        return Inertia::render('Institucion/Create');
    }

    public function update(Institution $institution, StoreInstitutionRequest $request){
        $institution->update($request->validated());
        return redirect()->route('institution.edit', $institution)->with('message', 'Institución actualizada correctamente');
    }

    public function destroy(Institution $institution){
        //eliminto todos los usuairos asociados
        $institution->userPivot()->delete();
        
        //eliminto todas las regiones y los puntos asociados
        $regiones = $institution->regions;
        foreach ($regiones as $region) {
            $region->points()->delete();
            $region->delete();
        }

        $institution->delete();
        return redirect()->route('institution.index')->with('message', 'Institución eliminada correctamente');
    }
}
