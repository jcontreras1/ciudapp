<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    

    // public function index(){
    //     return Inertia::render('Posts/Index', [
    //         'posts' => Post::all(),
    //     ]);
    // }

    public function store(Request $request){
        Post::create([
            'user_id' => auth()->id(),
            'lat' => 1,
            'lng' => 1,
            'comment' => $request->comment,
            'private' => false,
            'subcategory_id' => 1
        ]);

        return to_route('home');
    }


}
