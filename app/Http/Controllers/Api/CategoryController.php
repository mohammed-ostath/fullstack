<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json([
            'data' => $categories,
            'message' => 'Categories retrieved successfully',
            'status' => 200,
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        //create slug with seperator
        $slug = Str::slug($request->name, '-');
        // check if slug is unique
        $count = Category::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }
        // create the category
        $category = Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'is_active' => $request->is_active,
            'parent_id' => $request->parent_id ?? true,
        ]);
        return response()->json([
            'data' => $category,
            'message' => 'Category created successfully',
            'status' => 201,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load('parent', 'children');
        return response()->json([
            'data' => $category,
            'message' => 'Category retrieved successfully',
            'status' => 200,
        ]);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        // validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'parent_id' => 'nullable|exists:categories,id',
        ]);
    if($request->has('name') && $request->name != $category->name){
        //create slug with seperator
        $slug = Str::slug($request->name, '-');
        // check if slug is unique
        $count = Category::where('slug', $slug)->where('id','!=',$category->id,)->count();
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }
        $category->name = $request->name;
        $category->slug = $slug;
    }
    if($request->has('parent_id') && $request->parent_id != $category->parent_id){}{
        $category->parent_id = $request->parent_id;
    }

    if($request->has('description') && $request->description = $category->description){}

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // check if category has children
        if ($category->children()->count() > 0) {
            return response()->json([
                'message' => 'Category has children, cannot delete',
                'status' => 400,
            ]);
        }
        // delete the category
        $category->delete();
        return response()->json([
            'message' => 'Category deleted successfully',
            'status' => 200,
        ]);
    }

    public function products(){
        $category->load('products');
        return response()->json([
            'data' => $categories,
            'message' => 'products Categories retrieved successfully',
            'status' => 200,
        ]);
    }
}