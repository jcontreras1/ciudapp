<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Post $post){
        //Debería ser incluido dentro del post
    }

    public function store(Post $post, PostComment $postComment, StoreCommentRequest $request){
        $comment = PostComment::create(
            array_merge(
                $request->validated(),
                [
                    'post_id' => $post->id,
                    'user_id' => auth()->id(),
                ]
            )
        );
        return back()->with('message', 'Comentario agregado');
    }
}
