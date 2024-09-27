<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use HasFactory;

    protected $table = 'point';
    protected $fillable = [
        'lat',
        'lng',
        'region_id',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
