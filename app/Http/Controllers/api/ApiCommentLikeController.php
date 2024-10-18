<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\PostComment;
use App\Models\PostCommentLike;
use Illuminate\Http\Request;

class ApiCommentLikeController extends Controller
{
    public function store(PostComment $postComment, Request $request)
    {
        // $request->validate([
        //     'post_comment_id' => 'required|exists:post_comments,id',
        // ]);
        PostCommentLike::firstOrCreate([
            'user_id' => auth()->id(),
            'post_comment_id' => $postComment->id,
        ]);
        // $like = $postComment->likes()->create([
            // 'user_id' => auth()->id(),
        // ]);

        return response(new PostResource($postComment->post), 201);
    }

    public function destroy(PostComment $postComment, PostCommentLike $postCommentLike, Request $request)
    {
        $postCommentLike->delete();
        return response($postComment, 200);
    }
}
