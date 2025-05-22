<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CheckoutController extends Controller
{
    //validate the request data
    public function checkout()
    {
        $request->validate([
            'shopping_name' => 'required|string|max:255',
            'shopping_address' => 'required|string|max:255',
            'shopping_city' => 'required|string|max:255',
            'shopping_state' => 'required|string|max:255',
            'shopping_zipcode' => 'required|string|max:15',
            'shopping_country' => 'required|string|max:255',
            'shopping_phone' => 'required|string|max:10',
            'payment_method' => 'nullable|id:credit_card,paypal',
            'notes' => 'nullable|string|max:255',
        ]);
        $user = $request->user();
        $cartItems = Cart::where('user_id', $user->id)->with('product')->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'your cart is empty'], 400);
        }
        $subtotal = 0;
        $item = []; // order item array
        foreach ($cartItems as $item) {
            $product = $item->product;
            // Check if product is active
            if (!$product->is_active) {
                return response()->json([
                    'message' => "product {$product->name} is no longer available"
                ], 400);
            }
// check product stock
            if ($product->stock < $item->quantity) {
                return response()
                    ->json(['message' => "Not enough stock for product {$product->name}"], 400);
            }
            $itemSubTotal = round($product->price = $item->quantity, 2);
            $subtotal += $itemSubTotal;
            $items[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'quantity' => $item->quantity,
                'price' => $product->price,
                'subtotal' => $itemSubTotal,
            ];

        }

        $tax = round($subtotal * 0.08, 2);
        $shoppingCost = 5.00;
        $total = round($subtotal + $tax + $shoppingCost, 2);
        DB::beginTransaction();
        try {
            $order = new Order([
                'user_id' => $user->id,
                'status' => 'pending',
                'shopping_name' => $request->shopping_name,
                'shopping_address' => $request->shopping_address,
                'shopping_city' => $request->shopping_city,
                'shopping_state' => $request->shopping_state,
                'shopping_zipcode' => $request->shopping_zipcode,
                'shopping_country' => $request->shopping_country,
                'shopping_phone' => $request->shopping_phone,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shopping_cost' => $shoppingCost,
                'total' => $total,
                'payment_method' => $request->payment_method ?? 'cod',
                'payment_status' => 'pending',
                'order_number' => uniqid('order_'),
                'notes' => $request->notes,
            ]);
            $user->orders()->save($order);
            //save order items
            foreach ($items as $item) {
                $order->items()->create($item);
                Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']); //decremnt stock
            }
            //clear the user cart
            Cart::where('user_id', $user->id)->each(function ($cartItem) {
                $cartItem->delete();
            });
            DB::commit();
            return response()->json([
                'message' => 'order placed successfully',
                'order' => $order->load('items'),
                'status' => 'success'],
                201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'failed to place order: ' . $e . getMessage()], 500);
        }
        return response()->json(['message' => 'order placed successfully'], 201);
    }

    public function orderHistory(Request $request){
        $user = $request->user();
        $orders = $user->orders()->with('items')->get();
        return response()->json([
            'message' => 'order history retrieved successfully',
            'orders' => $orders,
            'status' => 'true',
        ]);
    }

    public function orderDetails(Request $request, $id){
        $user = $request->user();
        $orders = $user->orders()->with('items')->find($id);
        if(!$orders){
            return response()->json([
                'message' => 'order not found',
                'status' => 'false',
            ],404);
        }
        return response()->json([
            'message' => 'order details retrieved successfully',
            'orders' => $orders,
            'status' => 'true',
        ]);
    }

}
