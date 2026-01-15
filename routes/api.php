a<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ShoppingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HospitalityBookingController;
use App\Http\Controllers\HospitalityBrowseController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\TransportController;
//login as a user
Route::post('/login', [ShoppingController::class, 'login']);

// Customer Management
Route::post('/customers/register', [CustomerController::class, 'register']);
Route::post('/customers/login', [CustomerController::class, 'login']);
Route::post('/customers/verify-code', [CustomerController::class, 'verifyCode']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/customers/profile/{customerId}', [CustomerController::class, 'getCustomerProfile']);
    Route::put('/customers/profile/{customerId}', [CustomerController::class, 'updateCustomerProfile']);
    Route::get('/customers/orders/{customerId}', [CustomerController::class, 'getCustomerOrders']);
    Route::post('/customers/wishlist', [CustomerController::class, 'store']);
    Route::get('/customers/wishlist', [CustomerController::class, 'index']);
    Route::delete('/customers/wishlist/{productId}', [CustomerController::class, 'destroy']);
    Route::delete('/customers/wishlist/clear/{customerId}', [CustomerController::class, 'clearCustomerWishlist']);

    // Cart Operations
    Route::apiResource('/customers/cart', CartController::class)
        ->only(['store', 'update', 'destroy', 'show'])
        ->parameter('cart', 'customerId');
    Route::delete('/customers/cart/clear/{customerId}', [CartController::class, 'clear']);

    Route::post('/customers/logout', [CustomerController::class, 'logout']);
    Route::post('/customers/place-order', [ShoppingController::class, 'placeOrder']);

    // Hospitality Bookings
    Route::post('/hospitality/bookings', [HospitalityBookingController::class, 'createBooking']);
    Route::get('/customers/hospitality-bookings', [HospitalityBookingController::class, 'getCustomerBookings']);
});
//submit vendor info
Route::post('/vendor/submit', [VendorController::class, 'submitVendorInfo']);
//get vendor profile
Route::get('/vendor/profile/{vendorId}', [VendorController::class, 'getVendorProfile']);
//login as a vendor
Route::post('/vendor/login', [VendorController::class, 'vendorLogin']);
Route::post('/vendor/verify-code', [VendorController::class, 'verifyCode']);
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

// Hospitality Management (Vendor)
Route::middleware(['vendor.auth'])->group(function () {
    Route::get('/vendor/hospitality-bookings', [HospitalityBookingController::class, 'getVendorBookings']);
    Route::put('/vendor/hospitality-bookings/{bookingId}/status', [HospitalityBookingController::class, 'updateBookingStatus']);

    // Hotel Management
    Route::apiResource('/vendor/hotels', HotelController::class)->parameters([
        'hotels' => 'hotelId'
    ])->only(['store', 'index', 'update', 'destroy']);

    // Restaurant Management
    Route::apiResource('/vendor/restaurants', RestaurantController::class)->parameters([
        'restaurants' => 'restaurantId'
    ])->only(['store', 'index', 'update', 'destroy']);

    // Tour Management
    Route::apiResource('/vendor/tours', TourController::class)->parameters([
        'tours' => 'tourId'
    ])->only(['store', 'index', 'update', 'destroy']);

    // Transport Management
    Route::apiResource('/vendor/transports', TransportController::class)->parameters([
        'transports' => 'transportId'
    ])->only(['store', 'index', 'update', 'destroy']);
});

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

// Hospitality Services (Public browsing)
Route::get('/hospitality/hotels', [HospitalityBrowseController::class, 'getHotels']);
Route::get('/hospitality/restaurants', [HospitalityBrowseController::class, 'getRestaurants']);
Route::get('/hospitality/transports', [HospitalityBrowseController::class, 'getTransports']);
Route::get('/hospitality/tours', [HospitalityBrowseController::class, 'getTours']);

// Order Management
Route::get('/vendor/orders/{vendorId}', [VendorController::class, 'getVendorOrders']);
