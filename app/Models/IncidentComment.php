<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentComment extends Model
{
    use HasFactory;

    protected $table = "incident_comment";
    protected $fillable = [
        'comment',
        'user_id',
        'incident_id',
    ];

    public function incident(){
        return $this->belongsTo(Incident::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
