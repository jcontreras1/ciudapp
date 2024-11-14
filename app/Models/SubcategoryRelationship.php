<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubcategoryRelationship extends Model
{
    use HasFactory;

    protected $fillable = [
        'percentage',
        'origin_id',
        'destiny_id'
    ];

    protected $table = 'subcategory_relationship';

    public function origin(){
        return $this->belongsTo(Subcategory::class, 'origin_id');
    }

    public function destiny(){
        return $this->belongsTo(Subcategory::class, 'destiny_id');
    }   



}
