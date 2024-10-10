<?php

namespace App\Policies;

use App\Models\Institution;
use App\Models\User;

class InstitutionPolicy
{
    /**
    * Create a new policy instance.
    */
    public function __construct()
    {
        //
    }
    
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) {
            return true;
        }
        
        return null;
    }
    
    public function edit(User $user, Institution $institution)
    {
        //instituciones donde esta
        return $user->institutions->contains($institution);
    }
    
    public function update(User $user, Institution $institution)
    {
        //instituciones donde es admin
        $instituciones = $user->institutions->filter(function ($inst) {
            return $inst->pivot->is_admin; // Accede a la propiedad directamente
        });
        return $instituciones->contains($institution);
    }
}
