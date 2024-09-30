<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    use HasFactory;

    protected $table="institution";
    protected $fillable = [
        'name',
        'city_id',
        'address',
        'mail',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function users()
    {
        // return $this->hasManyThrough(
        //     User::class,
        //     UserInstitution::class,
        //     'institution_id', // Foreign key on user_institution table...
        //     'id',             // Foreign key on users table...
        //     'id',             // Local key on institutions table...
        //     'user_id'        // Local key on user_institution table...
        // ); // Esto permite acceder a la columna is_admin

        return $this->belongsToMany(User::class, 'user_institution', 'institution_id', 'user_id')
                    ->withPivot(['is_admin', 'id']);
    }

    public function regions()
    {
        return $this->hasMany(Region::class);
    }
}
