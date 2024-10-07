<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Inertia\Inertia;

use RealRashid\SweetAlert\Facades\Alert;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $posts = Post::with('category', 'subcategory', 'images', 'comments')->orderBy('id', 'desc')->cursorPaginate(5);
        // $post = Post::where('id', 1003)->first();
        // return new PostResource($post);
        //Esto es solo api
        if($request->wantsJson()){
            return PostResource::collection($posts);
        }

        //esto es inertia
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canLogout' => Route::has('logout') && auth()->check(),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'posts' => PostResource::collection($posts),
            'categorias' => Category::with('subcategories')->get(),
        ]);
    }
}
