<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ShoppingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CategoryController;
//login as a user
Route::post('/login', [ShoppingController::class, 'login']);

// Customer Management
Route::post('/customers/register', [CustomerController::class, 'register']);
Route::post('/customers/login', [CustomerController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/customers/profile/{customerId}', [CustomerController::class, 'getCustomerProfile']);
    Route::put('/customers/profile/{customerId}', [CustomerController::class, 'updateCustomerProfile']);
    Route::get('/customers/orders/{customerId}', [CustomerController::class, 'getCustomerOrders']);
    Route::apiResource('/customers/wishlist', CustomerController::class)
        ->only(['store', 'destroy', 'index'])
        ->parameter('wishlist', 'customerId');
    Route::delete('/customers/wishlist/clear/{customerId}', [CustomerController::class, 'clearCustomerWishlist']);

    // Cart Operations
    Route::apiResource('/customers/cart', CartController::class)
        ->only(['store', 'update', 'destroy', 'show'])
        ->parameter('cart', 'customerId');
    Route::delete('/customers/cart/clear/{customerId}', [CartController::class, 'clear']);

    Route::post('/customers/logout', [CustomerController::class, 'logout']);
    Route::post('/customers/place-order', [ShoppingController::class, 'placeOrder']);
});
//submit vendor info
Route::post('/vendor/submit', [VendorController::class, 'submitVendorInfo']);
//get vendor profile
Route::get('/vendor/profile/{vendorId}', [VendorController::class, 'getVendorProfile']);
//login as a vendor
Route::post('/vendor/login', [VendorController::class, 'vendorLogin']);
//get vendor dashboard
Route::get('/vendor/dashboard/{vendorId}', [VendorController::class, 'vendorDashboard']);
//check if vendor exists
Route::post('/vendor/check', [VendorController::class, 'checkVendorExists']);

// Product Management
Route::middleware(['vendor.auth'])->group(function () {
    Route::apiResource('/vendor/products', VendorController::class)
        ->only(['store', 'update', 'destroy'])
        ->parameters(['products' => 'productId']);
});
Route::get('/vendor/products/{vendorId}', [VendorController::class, 'getVendorProducts']);

// Category Management
Route::middleware(['vendor.auth'])->group(function () {
    Route::apiResource('/vendor/categories', CategoryController::class);
    Route::get('/vendor/categories/{category}/products', [VendorController::class, 'getProductsByCategory']);
});

// Customer Product Browsing
Route::get('/products/search', [ShoppingController::class, 'searchProducts']);
Route::get('/products/category/{categoryId}', [ShoppingController::class, 'getProductsByCategory']);
Route::apiResource('/products', ShoppingController::class)->only(['index', 'show']);
Route::get('/categories', [ShoppingController::class, 'getAllCategories']);

// Order Management
Route::get('/vendor/orders/{vendorId}', [VendorController::class, 'getVendorOrders']);
