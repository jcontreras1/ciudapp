<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subcategory\StoreSubcategoryRequest;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubcategoryController extends Controller{

    public function __construct()
    {
        $this->middleware(\App\Http\Middleware\isAdmin::class);
            // ->only([
            //     'destroy',
            //     'store',
            //     'update',
            // ]);
    }
    /**
    * Display a listing of the resource.
    */
    public function index(Category $category)
    {
        return Inertia::render('Subcategory/Index', [
            'categoria' => $category,
            'subcategorias' => $category->subcategories
        ]);
    }
    
    /**
    * Show the form for creating a new resource.
    */
    public function create(Category $category)
    {
        return Inertia::render('Subcategory/Create', ['categoria' => $category]);
    }
    
    /**
    * Store a newly created resource in storage.
    */
    public function store(Category $category, StoreSubcategoryRequest $request)
    {
        Subcategory::withTrashed()->updateOrCreate([
            'name' => $request->name,
            'category_id' => $category->id,
        ],[
            'deleted_at' => null,
            'icon' => $request->icon,
            'relevance_minutes' => $request->relevance_minutes
            ]
        );
        return redirect()->route('subcategory.index', $category->id)->with('message', 'Subcategoría creada correctamente');
    }
    
    /**
    * Show the form for editing the specified resource.
    */
    public function edit(Category $category, Subcategory $subcategory)
    {
        return Inertia::render('Subcategory/Edit', [
            'category' => $category,
            'subcategory' => $subcategory
        ]);
    }
    
    /**
    * Update the specified resource in storage.
    */
    public function update(StoreSubcategoryRequest $request, Category $category, Subcategory $subcategory)
    {
        $subcategory->update($request->validated());
        return redirect()->route('subcategory.index', $category->id)->with('message', 'Subcategoría actualizada correctamente');
    }
    
    /**
    * Remove the specified resource from storage.
    */
    public function destroy(Category $category, Subcategory $subcategory){
        $subcategory->delete();
        return redirect()->back()->with('message', 'Subcategoría eliminada correctamente');
        
    }
}
