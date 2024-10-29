<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidenceStatus extends Model
{
    use HasFactory;

    protected $table = "incidence_status";
    protected $fillable = [
        'name'
    ];

    public function incidences(){
        return $this->belongsToMany(Incidence::class, 'evolution_incidence')->withPivot('description', 'created_at', 'user_id');
    }
}
