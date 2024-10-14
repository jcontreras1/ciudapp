<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $table = 'region';
    protected $fillable = [
        'name',
        'institution_id',
        'user_id',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function points()
    {
        return $this->hasMany(Point::class);
    }

    public function regionSubcategories()
    {
        return $this->hasMany(RegionSubcategory::class);
    }

    public function subcategories()
    {
        return $this->belongsToMany(Subcategory::class, 'region_subcategory')->withPivot('id');
    }
}
