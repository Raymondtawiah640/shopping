<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function register(Request $request)
    {
        $customerData = $request->all();

        $customer = $this->customerService->registerCustomer($customerData);

        if (!$customer) {
            return response()->json([
                'message' => 'Failed to register customer',
            ], 500);
        }

        // Merge session cart to database cart
        $sessionCart = Session::get('cart', []);
        foreach ($sessionCart as $item) {
            $this->customerService->addToCart($customer->customer_id, $item['product_id'], $item['quantity']);
        }
        Session::forget('cart');

        return response()->json([
            'message' => 'Customer registered successfully',
            'customer' => $customer,
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $customer = $this->customerService->loginCustomer($credentials);

        if (!$customer) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Merge session cart to database cart
        $sessionCart = Session::get('cart', []);
        foreach ($sessionCart as $item) {
            $this->customerService->addToCart($customer->customer_id, $item['product_id'], $item['quantity']);
        }
        Session::forget('cart');

        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'message' => 'Customer login successful',
            'customer' => $customer,
            'token' => $token,
        ]);
    }

    public function getCustomerProfile($customerId)
    {
        $customer = $this->customerService->getCustomerProfile($customerId);

        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Customer profile retrieved successfully',
            'customer' => $customer,
        ]);
    }

    public function updateCustomerProfile(Request $request, $customerId)
    {
        $customerData = $request->all();

        $customer = $this->customerService->updateCustomerProfile($customerId, $customerData);

        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Customer profile updated successfully',
            'customer' => $customer,
        ]);
    }

    public function getCustomerOrders($customerId)
    {
        $orders = $this->customerService->getCustomerOrders($customerId);

        if (!$orders) {
            return response()->json([
                'message' => 'Customer not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Customer orders retrieved successfully',
            'orders' => $orders,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Customer logged out successfully',
        ]);
    }

    public function store(Request $request, $customerId)
    {
        $productId = $request->input('product_id');

        $wishlistItem = $this->customerService->addToWishlist($customerId, $productId);

        if (!$wishlistItem) {
            return response()->json([
                'message' => 'Customer not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Product added to wishlist successfully',
            'wishlist_item' => $wishlistItem,
        ]);
    }

    public function destroy(Request $request, $customerId)
    {
        $productId = $request->input('product_id');

        $result = $this->customerService->removeFromWishlist($customerId, $productId);

        if (!$result) {
            return response()->json([
                'message' => 'Wishlist item not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Product removed from wishlist successfully',
        ]);
    }

    public function index($customerId)
    {
        $wishlist = $this->customerService->getCustomerWishlist($customerId);

        if ($wishlist === null) {
            return response()->json([
                'message' => 'Customer not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Customer wishlist retrieved successfully',
            'wishlist' => $wishlist,
        ]);
    }

    public function clearCustomerWishlist($customerId)
    {
        $result = $this->customerService->clearCustomerWishlist($customerId);

        if (!$result) {
            return response()->json([
                'message' => 'Customer not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Customer wishlist cleared successfully',
        ]);
    }

}