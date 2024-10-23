<?php

namespace App\Http\Controllers\api;

use App\Events\PostUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\PostComment;
use App\Models\PostCommentLike;
use Illuminate\Http\Request;

class ApiCommentLikeController extends Controller
{
    public function store(PostComment $postComment, Request $request)
    {
        PostCommentLike::firstOrCreate([
            'user_id' => auth()->id(),
            'post_comment_id' => $postComment->id,
        ]);

        PostUpdatedEvent::dispatch($postComment->post);
        
        return response(new PostResource($postComment->post), 201);
    }
    
    public function destroy(PostComment $postComment, PostCommentLike $like, Request $request)
    {
        $like->delete();
        PostUpdatedEvent::dispatch($postComment->post);
        return response(new PostResource($postComment->post), 200);
    }
}
