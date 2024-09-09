<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    
    public function index(){
        $categories = Category::with('subcategories')->get();
        return Inertia::render('Category/Index', ['categorias' => $categories]);
    }

    public function create(){
        return Inertia::render('Category/Create');
    }


}
