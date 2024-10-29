<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvolutionIncidence extends Model
{
    use HasFactory;

    protected $table = "evolution_incidence";
    protected $fillable = [
        'description',
        'user_id',
        'incidence_id',
        'incidence_status_id'
    ];

    public function incidence(){
        return $this->belongsTo(Incidence::class);
    }

    public function status(){
        return $this->belongsTo(IncidenceStatus::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
