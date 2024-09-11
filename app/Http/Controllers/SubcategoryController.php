<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subcategory\StoreSubcategoryRequest;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubcategoryController extends Controller
{
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
        Subcategory::create(array_merge($request->validated(), ['category_id' => $category->id]));
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
    public function destroy(string $id)
    {
        //
    }
}
