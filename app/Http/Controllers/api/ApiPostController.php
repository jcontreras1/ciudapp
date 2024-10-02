<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Http\Request;

class ApiPostController extends Controller
{
    /**
    * Display a listing of the resource.
    */
    public function index()
    {
        //
    }
    
    /**
    * Store a newly created resource in storage.
    */
    public function store(Post $post, StoreCommentRequest $request){
        PostComment::create(
            array_merge($request->validated(), ['post_id' => $post->id, 'user_id' => auth()->id() ?? 1, ])
        );
        return response(
            $post->loadMissing('comments.user'),
            201
        );
    }
    
    /**
    * Display the specified resource.
    */
    public function show(Post $post)
    {
        //
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
        //
    }
}
