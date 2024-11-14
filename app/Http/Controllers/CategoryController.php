<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\StoreSubcategoryRelationshipRequest;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\SubcategoryRelationship;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{

    public function __construct()
{
    $this->middleware(\App\Http\Middleware\isAdmin::class);
        // ->only([
        //     'destroy',
        //     'store',
        //     'update',
        // ]);
}
public function relationships(){
    $subcategories = Subcategory::with('category')->with('relationships')->get();   
    return Inertia::render('Category/Relationships', 
[
    'subcategories' => $subcategories
]);
}


public function storeRelationship(StoreSubcategoryRelationshipRequest $request){
    SubcategoryRelationship::create([
        'origin_id' => $request->origin_id,
        'destiny_id' => $request->destiny_id,
        'percentage' => $request->percentage,
    ]);
    SubcategoryRelationship::create([
        'origin_id' => $request->destiny_id,
        'destiny_id' => $request->origin_id,
        'percentage' => $request->percentage,
    ]);
        $subcategories = Subcategory::with('category')->with('relationships')->get();
        return response($subcategories, 200);
}

    
    public function index(){
        $categories = Category::with('subcategories.relationships')->get();
        // return $categories;
        return Inertia::render('Category/Index', ['categorias' => $categories]);
    }


    
    public function create(){
        return Inertia::render('Category/Create');
    }
    
    public function store(StoreCategoryRequest $request){
        Category::withTrashed()->updateOrCreate([
            'name' => $request->name
        ],[
            'icon' => $request->icon,
            'deleted_at' => null
            ]
        );
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
    
    public function destroy(Category $category){
        $category->delete();
        return redirect()->route('category.index')->with('message', 'Categoria eliminada correctamente');
    }
    
    
}
