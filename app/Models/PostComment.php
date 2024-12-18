<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'post_id',
        'user_id',
        'comment',
        'deleted_at', // por si quisiera revivir el comentario
    ];

    protected $table = 'post_comment';

    //belongs to post
    public function post(){
        return $this->belongsTo(Post::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function commentLikes(){
        return $this->hasMany(PostCommentLike::class);
    }

    public function likes(){
        return $this->belongsToMany(User::class, 'post_comment_like', 'post_comment_id', 'user_id')->withPivot('id');
    }
}
