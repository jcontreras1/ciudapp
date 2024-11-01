<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;
    protected $table = "incident";
    protected $fillable = [
        'description',
        'user_id',
        'institution_id',
        'post_original_id',
        'status_id'
    ];

    public function status()
    {
        return $this->belongsTo(IncidentStatus::class);
    }

    public function posts(){
        return $this->hasMany(Post::class);
    }

    public function comments(){
        return $this->hasMany(IncidentComment::class);
    }

    public function institution(){
        return $this->belongsTo(Institution::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function history(){
        return $this->hasMany(IncidentHistory::class);
    }

    public function postOriginal(){
        return $this->belongsTo(Post::class, 'post_original_id');
    }

}
