<?php

namespace App\Http\Controllers;

use App\Http\Requests\Institution\UpdateInstitutionRequest;
use App\Http\Requests\StoreInstitutionRequest;
use App\Models\Institution;
use App\Models\UserInstitution;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;


class InstitutionController extends Controller
{
    
    public function __construct()
    {
        $this->middleware(\App\Http\Middleware\isAdmin::class)
        ->only([
            'destroy',
            'store',
            'create',
        ]);
    }
    
    public function index(){
        $instituciones = null;
        //Instituciones del usuario si no es admin
        if(!auth()->user()->is_admin){
            $instituciones = auth()->user()->institutions()->with('city.province')->orderBy('name', 'asc')->paginate(10);
        }else{
            $instituciones = Institution::with('city.province')->orderBy('name', 'asc')->paginate(10);
        }
        return Inertia::render('Institucion/Index', ['instituciones' => $instituciones]);
    }
    
    public function store(StoreInstitutionRequest $request){
        Institution::create($request->validated());
        return redirect()->route('institution.index')->with('message', 'Institución creada correctamente');
    }
    
    public function edit(Institution $institution){
        $amIAdmin = true;
        if(!auth()->user()->isAdmin()){
            $ui = UserInstitution::where('institution_id', $institution->id)->where('user_id', auth()->id())->firstOrFail();
            $amIAdmin = boolval($ui->is_admin);
        }
        $regions = $institution->loadMissing('regions.points');
        return Inertia::render('Institucion/Edit',
        [
            'institucion' => $institution->loadMissing('city.province'),
            'regiones' => $institution->regions,
            'users' => $institution->users,
            'amIAdmin' => $amIAdmin
            ]
        );
    }
    
    public function create(){
        return Inertia::render('Institucion/Create');
    }
    
    public function update(Institution $institution, UpdateInstitutionRequest $request){
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
