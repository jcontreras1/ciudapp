<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
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
    
    public function store(StoreCategoryRequest $request){
        Category::create($request->validated());
        $categories = Category::with('subcategories')->get();
        return redirect()->route('category.index')->with('message', 'Categoria creada correctamente');
    }
    
    public function edit(Category $category){
        return Inertia::render('Category/Edit', ['category' => $category]);
    }
    
    public function update(Category $category, StoreCategoryRequest $request){
        $category->update($request->validated());
        return redirect()->route('category.index')->with('message', 'Categoria actualizada correctamente');
    }
    
    
}
