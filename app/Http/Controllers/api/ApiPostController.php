<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ApiPostController extends Controller
{

    
    public function store(Post $post, StoreCommentRequest $request){
        PostComment::create(
            array_merge($request->validated(), ['post_id' => $post->id, 'user_id' => auth()->id() ?? 1, ])
        );
        return response(
            $post->loadMissing('comments.user'),
            201
        );
    }
    
    public function like(Post $post)
    {
        $user = auth()->user();
        if ($post->likes()->where('user_id', $user->id)->exists()) {
            $post->likes()->where('user_id', $user->id)->delete();
        } else {
            $post->likes()->create(['user_id' => $user->id]);
        }

        return response(new PostResource($post) ,201);
    }
    
    /**
    * Update the specified resource in storage.
    */
    public function update(Request $request, Post $post)
    {
        //
    }
    
    /**
    * Remove the specified resource from storage.
    */
    public function destroy(Post $post)
    {
        //eliminar imagenes del disco
        Storage::deleteDirectory('public/post/' . $post->id);
        $post->images()->delete();
        $post->comments()->delete();
        $post->likes()->delete();

        $post->delete();

        return response([], 201);
    }
}
