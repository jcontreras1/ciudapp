<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentHistory extends Model
{
    use HasFactory;

    protected $table = "incident_history";
    protected $fillable = [
        'incident_id',
        'user_id',
        'description',
        'status_id'
    ];

    public function incident(){
        return $this->belongsTo(Incident::class);
    }
}
