<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRegionSubcategory extends Model
{
    use HasFactory;

    protected $table = 'user_region_subcategory';
    protected $fillable = [
        'user_id',
        'region_subcategory_id',
    ];

    public function regionSubcategory(){
        return $this->belongsTo(RegionSubcategory::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
