<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;




class PostController extends Controller
{


    public function index(){
        return Inertia::render('Posts/Index', [

            'posts' => Post::with('images')->get(),
        ]);
    }

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
                'comment' => "asd",
                'private' => false,
                'subcategory_id' => 1,
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

        }
        DB::commit();



    return to_route('home');
}


}
