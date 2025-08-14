<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 
        'lat', 
        'lng', 
        'comment', 
        'private', 
        'subcategory_id',
        'location_short',           // llenables desde un listener
        'location_long',            // llenables desde un listener
        'valid_until',              // valido hasta un período de tiempo dado por la subcategoría 
        'incident_id',              // si es un incidente, se llena con el id del incidente
        'city_id',
    ];
    protected $table = "post";

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(PostImage::class, 'post_id', 'id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }
    
    public function category(){
        return $this->hasOneThrough(Category::class, Subcategory::class, 'id', 'id', 'subcategory_id', 'category_id');
    }

    public function city(){
        return $this->belongsTo(City::class);
    }
}
