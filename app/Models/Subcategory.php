<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subcategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 
        'icon', 
        'category_id', 
        'deleted_at',
        'relevance_minutes',
    ];

    protected $hidden = ['created_at', 'updated_at'];
    protected $table = 'subcategory';

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function relationships(){
        return $this->belongsToMany(Subcategory::class, 'subcategory_relationship', 'origin_id', 'destiny_id')->withPivot('percentage');
    }
}
