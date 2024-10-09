<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostCommentLike extends Model
{
    use HasFactory;
    protected $table = 'post_comment_like';
    protected $fillable = [
        'user_id',
        'post_comment_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function postComment(){
        return $this->belongsTo(PostComment::class);
    }
}
