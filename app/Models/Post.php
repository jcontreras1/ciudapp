<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'lat', 'lng', 'comment', 'private'];
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
}
