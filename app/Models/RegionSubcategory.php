<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionSubcategory extends Model
{
    use HasFactory;

    protected $table = 'region_subcategory';
    protected $fillable = [
        'region_id',
        'subcategory_id',
    ];

    public function region(){
        return $this->belongsTo(Region::class);
    }

    public function subcategory(){
        return $this->belongsTo(Subcategory::class);
    }

    public function users(){
        return $this->belongsToMany(User::class, 'user_region_subcategory')->withPivot('id');
    }

    public function userRegionSubcategories(){
        return $this->hasMany(UserRegionSubcategory::class);
    }

}
