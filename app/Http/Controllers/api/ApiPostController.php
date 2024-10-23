<?php

namespace App\Http\Controllers\api;

use App\Events\PostUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\PostImage;
use App\Events\NewPostEvent;
use Illuminate\Support\Str;



class ApiPostController extends Controller
{

    public function show(Post $post){
        return response(new PostResource($post), 200);
    }

    public function storeComment(Post $post, StoreCommentRequest $request){
       PostComment::create(
           array_merge($request->validated(), ['post_id' => $post->id, 'user_id' => auth()->id() ?? 1, ])
        );
        PostUpdatedEvent::dispatch($post);
        return response(
            new PostResource($post),
            201
        );
    }

    public function dropComment(PostComment $comment){
        $post = $comment->post;
        $comment->delete();
        PostUpdatedEvent::dispatch($post);
        return response(new PostResource($post), 201);
    }

    public function store(Request $request){
        $post = null;
        DB::beginTransaction();

        if($request->has('image')){
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ]);


            //$request->image->move(public_path('images'), $imageName);
            $post = Post::create([
                'user_id' => auth()->id(),
                'lat' => $request->latitud,
                'lng' => $request->longitud,
                'comment' => "",
                'private' => false,
                'subcategory_id' => $request->subcategory_id,
                'location_long' => $request->fullAddress ?? null
            ]);

            //Ver hasta cuando sería considerado valido el post
            $subcategory = $post->subcategory;
            $post->valid_until = now()->addMinutes($subcategory->relevance_minutes);
            $post->save();

            if(!$post){
                DB::rollBack();
                return back()->with('error', 'Post no pudo ser creado');
            }

            $imageName = 'post/' . $post->id . '/' . Str::uuid() . '.' . $request->image->extension();
            $request->image->storeAs('public', $imageName);

            $postImage = PostImage::create([
                'post_id' => $post->id,
                'file' => Storage::url($imageName),
            ]);

            if(!$postImage){
                DB::rollBack();
                return back()->with('error', 'Post no pudo ser creado');
            }
            //eventos relacionados con nuevo Post
            NewPostEvent::dispatch($post);

        }
        DB::commit();
        return response(new PostResource($post), 200);
    }

    public function like(Post $post)
    {
        $user = auth()->user();
        if ($post->likes()->where('user_id', $user->id)->exists()) {
            $post->likes()->where('user_id', $user->id)->delete();
        } else {
            $post->likes()->create(['user_id' => $user->id]);
        }
        PostUpdatedEvent::dispatch($post);
        return response(new PostResource($post) ,201);
    }

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
