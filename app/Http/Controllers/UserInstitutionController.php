<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserInstitution;
use App\Notifications\Register\CollaborateWithInstitutionNotification;
use App\Notifications\Register\NewUserWithInstitutionNotification;
use App\Http\Requests\StoreNewUserToInstitutionRequest;
use App\Models\Institution;

class UserInstitutionController extends Controller
{
     /**
     * Primero verifico si el usuario existe en la base de datos por su email
     * Si existe => le envio una notificación de que ahora puede colaborar con la institución
     * Si no existe => lo creo, le pongo una clave temporal, le agrego la relación con la institución, y luego le envio una ntoificación
     */
    public function store(Institution $institution, StoreNewUserToInstitutionRequest $request){
        
        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if ($user) {
            UserInstitution::updateOrCreate([
                'user_id' => $user->id,
                'institution_id' => $institution->id,
            ],[
                'is_admin' => $request->is_admin
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
                'is_admin' => $request->is_admin,
            ]);
            Notification::send($user, new NewUserWithInstitutionNotification($institution, $user, $token));
        }

        return redirect()->route('institution.edit', $institution)->with('message', 'Usuario añadido correctamente');
    }

    public function destroy(Institution $institution, UserInstitution $userInstitution){
        $userInstitution->delete();
        return redirect()->route('institution.edit', $institution)->with('message', 'Usuario eliminado correctamente');
    }

    public function update(Institution $institution, UserInstitution $userInstitution, Request $request){
        $request->validate([
            'is_admin' => 'required|boolean'
        ]);
        $userInstitution->update([
            'is_admin' => $request->is_admin
        ]);
        return redirect()->route('institution.edit', $institution)->with('message', 'Usuario actualizado correctamente');
    }


}
