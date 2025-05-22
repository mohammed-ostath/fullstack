<?php

use App\Http\Controllers\Api\CheckoutController;
use App\Models\User;
use App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CartController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\Auth\AdminAuthController;
use App\Http\Controllers\Api\Auth\CustomerAuthController;
use App\Http\Controllers\Api\Auth\DeliveryAuthController;

Route::prefix('admin')->group(function(){
    Route::post('/register', [AdminAuthController::class, 'register']);
    Route::post('/login', [AdminAuthController::class, 'login']);


Route::middleware(['auth:sanctum','isAdmin'])->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout']);
    Route::get('/me', [AdminAuthController::class, 'me']);
    Route::get('/token', [AdminAuthController::class, 'getAccessToken']);
    });
});

Route::prefix('customer')->group(function(){
    Route::post('/register', [CustomerAuthController::class, 'register']);
    Route::post('/login', [CustomerAuthController::class, 'login']);


Route::middleware(['auth:sanctum','isCustomer'])->group(function () {
    Route::post('/logout', [CustomerAuthController::class, 'logout']);
    Route::get('/me', [CustomerAuthController::class, 'me']);
    Route::get('/token', [CustomerAuthController::class, 'getAccessToken']);
    });
});


Route::prefix('delivery')->group(function(){
    Route::post('/register', [DeliveryAuthController::class, 'register']);
    Route::post('/login', [DeliveryAuthController::class, 'login']);


Route::middleware(['auth:sanctum','isDelivery'])->group(function () {
    Route::post('/logout', [DeliveryAuthController::class, 'logout']);
    Route::get('/me', [DeliveryAuthController::class, 'me']);
    Route::get('/token', [DeliveryAuthController::class, 'getAccessToken']);
    });
});


Route::apiResource('/products',ProductController::class)->only('index','show');


Route::middleware(['auth:sanctum','permission:create products'])->group(function(){
    Route::apiResource('/products',ProductController::class)->except('index','show');
    });
Route::apiResource('/categories',CategoryController::class)->only('index','show');


Route::middleware(['auth:sanctum','permission:create categories'])->group(function(){
    Route::apiResource('/categories',CategoryController::class)->except('index','show');
    });

Route::middleware(['auth:sanctum','permission:create orders'])->group(function(){
    Route::apiResource('/cart',CartController::class)->except('show');
    });

Route::get('/categories/{category}/products', [CategoryController::class, 'products'])->name('categories.products');


Route::middleware(['auth:sanctum','permission:create orders'])->group(function(){
    Route::post('/checkout', [CheckoutController::class,'checkout']);
    Route::get('/orders', [CheckoutController::class,'orderHistory']);
    Route::get('/orders/{orderId}', [CheckoutController::class,'orderDetails']);
});


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// // route group
//  Route::prefix('v1')->group(function () {
//     // get to get all data
//     // get posts
//     Route::get('/posts', function () {
//         // return posts data
//         $data = [
//             [
//                 'id' => 1,
//                 'title' => 'Post 1',
//                 'content' => 'Content of post 1'
//             ],
//             [
//                 'id' => 2,
//                 'title' => 'Post 2',
//                 'content' => 'Content of post 2'
//             ]
//         ];
//         return response()->json([
//             'message' => 'Get all posts',
//             'data' => $data
//         ]);
//     });

//     // post create new data
//     Route::post('/posts', function (Request $request) {
//         return response()->json([
//             'message' => 'Create new post',
//             'data' => $request->all()
//         ]);
//     });
//     // put to update exist data
//     Route::put('/posts/{id}', function (Request $request, $id) {
//         return response()->json([
//             'message' => 'Update post',
//             'data' => $request->all()
//         ]);
//     });
//     // delete to delete data
//     Route::delete('/posts/{id}', function ($id) {
//         return response()->json([
//             'message' => 'Delete post: ' . $id,
//         ]);
//     });

//     // get to get single data
//     Route::get('/posts/{id}', function ($id) {
//         return response()->json([
//             'message' => 'Get post: ' . $id,
//             'data' => [
//                 'id' => $id,
//                 'title' => 'Post ' . $id,
//                 'content' => 'Content of post ' . $id
//             ]
//         ]);
//     });
//  });


//  // parameterized route
// // required parameter
// Route::get('/posts/{id}/comments/{commentId}', function ($id, $slug) {
//     return response()->json([
//         'message' => 'Get post: ' . $id,
//         'data' => [
//             'id' => $id,
//             'slug' => $slug,
//             'title' => 'Post ' . $id,
//             'content' => 'Content of post ' . $id
//         ]
//     ]);
// });

// // optional parameter
// Route::get('/users/{id?}', function ($id = null) {
//     if ($id) {
//         return response()->json([
//             'message' => 'Get user: ' . $id,
//             'data' => [
//                 'id' => $id,
//                 'name' => 'User ' . $id
//             ]
//         ]);
//     } else {
//         return response()->json([
//             'message' => 'Get all users',
//             'data' => [
//                 [
//                     'id' => 1,
//                     'name' => 'User 1'
//                 ],
//                 [
//                     'id' => 2,
//                     'name' => 'User 2'
//                 ]
//             ]
//         ]);
//     }
// });

// Route::get('test-header', fn() => 'allowed')->middleware('custom_header');

// Route::middleware('auth:sanctum')->get('/profile', function (Request $request) {
//     return $request->user()->profile();
// });

// Route::post('login', function(Request $request){
//     $user = User::where('email', $request->email)->firstOrFail();
//     $token = $user->createToken('auth_token')->plainTextToken;
//     return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
// });

// Route::middleware('throttle:custom')->get('limited' , function(){
//     return 'not limited yet';
// });


// Route::apiResource('posts', PostController::class);
// Route::get('user-profile', [ProfileController::class, 'index']);
// Route::post('posts/{id}/comments', [PostController::class, 'addcomment']);
// Route::post('users/{id}/roles', [RoleController::class, 'store']);
// Route::get('roles', [RoleController::class, 'index']);


// Route::get('user-role', function(){
//     $user = User::find(1);
//     if (!$user->hasRole('editor')) {
//         return response()->json([
//             'message' => 'You are not authorized to edit posts.',
//             'data' => $user->roles
//         ], 403);
//     }
//     return response()->json([
//         'message' => 'You are authorized to edit posts.',
//         'data' => $user->roles
//     ]);
// });


// include_once __DIR__.'/auth.php';
