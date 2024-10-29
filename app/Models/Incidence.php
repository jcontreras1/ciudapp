<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incidence extends Model
{
    use HasFactory;
    protected $table = "incidence";
    protected $fillable = [
        'description',
        'user_id',
        'institution_id',
        'resolved'
    ];

    public function status()
    {
        return $this->belongsToMany(IncidenceStatus::class, 'evolution_incidence')->withPivot('description', 'created_at', 'user_id');
    }

    public function post(){
        return $this->belongsTo(Post::class);
    }

    public function comments(){
        return $this->hasMany(IncidenceComment::class);
    }

    public function institution(){
        return $this->belongsTo(Institution::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function evoIncidence(){
        return $this->hasMany(EvolutionIncidence::class);
    }

}
