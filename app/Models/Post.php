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
        'deleted_at', 
        'subcategory_id',
        'location_short',           // llenables desde un listener
        'location_long',            // llenables desde un listener
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
        return $this->hasMany(PostComment::class)->withTrashed();
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }
    
    public function category(){
        return $this->hasOneThrough(Category::class, Subcategory::class, 'id', 'id', 'subcategory_id', 'category_id');
    }
}
