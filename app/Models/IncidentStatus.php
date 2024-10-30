<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentStatus extends Model
{
    use HasFactory;

    protected $table = "incident_status";
    protected $fillable = [
        'description',
        'code',
        'icon',
        'color',
    ];
}
