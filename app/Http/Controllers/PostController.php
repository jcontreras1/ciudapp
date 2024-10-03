<?php

namespace App\Http\Controllers;

use App\Events\NewPostEvent;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class PostController extends Controller
{

    public function store(Request $request){
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



    return to_route('home');
}


}
