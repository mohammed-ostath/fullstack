<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //get authrize user
        $user = $request()->user();
        //get all carts of the user
        $cartItems = Cart::where('user_id', $user->id)->with('product')->get();
        //get total of cart items
        $total =$cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
        return response()->json([
            'success' => true,
            'message' => 'Cart items retrieved successfully',
            'cartItems' => $cartItems,
            'total' => $total
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request()->user();
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        //check if product already in cart
        $cartItems = Cart::where('user_id', $user->id)->where('product_id', $data['product_id'])->first();
        if ($cartItems) {
            //update quantity
            $cartItems->quantity += $data['quantity'];
            $cartItems->save();
            return response()->json([
                'success' => true,
                'message' => 'Cart item updated successfully',
                'cartItem' => $cartItems
            ],200);
        }else{
            //create new cart item
             $cartItems = Cart::create([
                'user_id' => $user->id,
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity']
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Cart item created successfully',
                'cartItem' => $cartItems
            ],201);

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);
        //update quantity
        $cart->quantity = $data['quantity'];
        $cart->save();
        return response()->json([
            'success' => true,
            'message' => 'Cart item updated successfully',
            'cartItem' => $cart
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        //delete cart item
        $cart->delete();
        return response()->json([
            'success' => true,
            'message' => 'Cart item deleted successfully'
        ],200);
    }
}