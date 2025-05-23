<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Cache::remember('products', 300, function () {
            return Product::with('categories')->get();
        });

        // $products = Product::all();
        // $products = Product::withoutGlobalScope('active')->get();
        // $products = Product::priceBetween(1,50)->get();
        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully',
            'data' => $product->formatted_name,
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // validate the request

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'integer|min:0',
            'sku' => 'required|string|max:255|unique:products',
            'is_active' => 'boolean',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        // check if image is uploaded
        if ($request->hasFile('image')){
            $data['image'] = $request->file('image')->storeAs('products',$data['slug'] ,'public');
        }

        $product = Product::create($data);
        // attach categories
        if (isset($data['categories'])) {
            $product->categories()->attach($data['categories']);
        }
        $product->load('categories');
          //clear the cache
        Cache::forget('products');
        return response()->json([
            'success' => true,
            'message' => "Product Added Successfully",
            'data' => $product,
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
         $productCached = Cache::remember('product_' . $product->id, 300, function () use ($product)  {
            return $product::load('categories');
        });
        return response()->json([
            'success' => true,
            'message' => "Product Showed Successfully",
            'data' => $product,
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
         $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:products' . $product->id,
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'sku' => 'sometimes|required|string|max:255|unique:products' . $product->id,
            'is_active' => 'sometimes|boolean',
            'categories' => 'sometimes|array',
            'categories.*' => 'sometimes|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if($request->has('name')){
            $product->name = $request->name;
            $product->slug = Str::slug($request->name, '-');
        }

        if($request->has('description')){
            $product->description = $request->description;
        }

        if($request->has('price')){
            $product->price = $request->price;
        }

        if($request->has('stock')){
            $product->stock = $request->stock;
        }

        if($request->has('sku')){
            $product->sku = $request->sku;
        }

        if($request->has('is_active')){
            $product->is_active = $request->is_active;
        }
        // check if image is uploaded
        if ($request->hasFile('image')){
            $product->image = $request->file('image')->storeAs('products',$product->slug ,'public');
        }

        $product->save();

        if ($request->has('categories')) {
            $product->categories()->sync($request->categories);
        }
        $product->load('categories');
         //clear the cache
        Cache::forget('product_' . $product->id);

        Cache::forget('products');
        return response()->json([
            'success' => true,
            'message' => "Product updated Successfully",
            'data' => $product,
        ],200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //check if product has image delete it from storage
        if($product->image){
            Storage::disk('public')->delete($product->image);
        }
        Cache::forget('product_' . $product->id);
        Cache::forget('products');
        $product->delete();
        return response()->json([
            'success' => true,
            'message' => "Product deleted Successfully",
        ],200);
    }

    public function undoDelete(Request $request,Product $product)
    {
        if($request->user()->hasRole('admin')){
        $product->restore();
        return response()->json([
            'success' => true,
            'message' => "Product restored Successfully",
        ],200);
        }
        return response()->json([
            'success' => false,
            'message' => "You are not authorized to restore this product",
        ],403);
    }


    public function permenantDelete(Request $request,Product $product)
    {
        if($request->user()->hasRole('admin')){
        $product->forceDelete();
        return response()->json([
            'success' => true,
            'message' => "Product force delete Successfully",
        ],200);
        }
        return response()->json([
            'success' => false,
            'message' => "You are not authorized to force delete this product",
        ],403);
    }

    public function adminIndex(Request $request)
    {

            if($request->user()->hasRole('admin')){
            $products = Product::withTrashed()->get();

            return response()->json([
                'success' => true,
                'message' => "Products retrieved successfully",
                'data' => $products,
            ],200);
        }
        return response()->json([
            'success' => false,
            'message' => "You are not authorized to view this product",
        ],403);
    }

    //filter products by name,description,price just
    public function filter(Request $request)
    {
        $products = Product::query()
        ->when($request->price_min,fn($query) => $query->where('price', '>=', $request->price_min))
        ->when($request->price_max,fn($query) => $query->where('price', '<=', $request->price_max))
        ->when($request->q,function($query) use ($request){
            $query->where(function($query) use ($request){
                $query->where('name', 'like', '%'.$request->q.'%')
                ->orWhere('description', 'like', '%'.$request->q.'%');
            });
        })->get();



        return response()->json([
            'success' => true,
            'message' => "Products retrieved successfully",
            'data' => $products,
        ], 200);
    }

}