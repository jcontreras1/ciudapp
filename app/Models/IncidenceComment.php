<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidenceComment extends Model
{
    use HasFactory;

    protected $table = "incidence_comment";
    protected $fillable = [
        'comment',
        'user_id',
        'incidence_id'
    ];

    public function incidence(){
        return $this->belongsTo(Incidence::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
