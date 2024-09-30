<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInstitutionRequest;
use App\Http\Requests\StoreNewUserToInstitutionRequest;
use App\Models\Institution;
use App\Models\User;
use App\Models\UserInstitution;
use App\Notifications\Register\CollaborateWithInstitutionNotification;
use App\Notifications\Register\NewUserWithInstitutionNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;



class InstitutionController extends Controller
{
    public function index(){
        
        $instituciones = Institution::orderBy('name', 'asc')->paginate(10);
        return Inertia::render('Institucion/Index', ['instituciones' => $instituciones]);
        
    }
    
    public function store(StoreInstitutionRequest $request){
        Institution::create($request->validated());
        return redirect()->route('institution.index')->with('message', 'Institución creada correctamente');
    }
    
    public function edit(Institution $institution){
        return Inertia::render('Institucion/Edit', 
        [
            'institucion' => $institution,
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
        return redirect()->route('institution.index')->with('message', 'Institución actualizada correctamente');
    }
    
    public function destroy(Institution $institution){
        $institution->delete();
        return redirect()->route('institution.index')->with('message', 'Institución eliminada correctamente');
    }
    
    /**
     * Primero verifico si el usuario existe en la base de datos por su email
     * Si existe => le envio una notificación de que ahora puede colaborar con la institución
     * Si no existe => lo creo, le pongo una clave temporal, le agrego la relación con la institución, y luego le envio una ntoificación
     */
    public function newUser(Institution $institution, StoreNewUserToInstitutionRequest $request){
        
        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if ($user) {
            UserInstitution::updateOrCreate([
                'user_id' => $user->id,
                'institution_id' => $institution->id,
            ],[
                'is_admin' => $request->isAdmin
            ]);
            Notification::send($user, new CollaborateWithInstitutionNotification($institution, $user));
        } else {
            $password = Str::random(20);
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            $token = \Illuminate\Support\Facades\Password::getRepository()->create($user);

            UserInstitution::create([
                'user_id' => $user->id,
                'institution_id' => $institution->id,
            ]);
            Notification::send($user, new NewUserWithInstitutionNotification($institution, $user, $token));
        }

        return redirect()->route('institution.edit', $institution)->with('message', 'Usuario añadido correctamente');
    }
}
