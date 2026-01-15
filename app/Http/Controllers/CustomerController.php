<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use App\Mail\CustomerVerificationCode;
use App\Models\Customer;
use App\Http\Requests\CustomerRegisterRequest;
use App\Http\Requests\CustomerLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function register(CustomerRegisterRequest $request)
    {
        $customerData = $request->validated();

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

    public function login(CustomerLoginRequest $request)
    {
        $credentials = $request->validated();

        $customer = $this->customerService->loginCustomer($credentials);

        if (!$customer) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Generate a 6-digit verification code
        $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store the code in cache for 10 minutes
        Cache::put('customer_verification_' . $customer->email, $code, now()->addMinutes(10));

        // Send the verification code via email
        Mail::to($customer->email)->send(new CustomerVerificationCode($customer, $code));

        return response()->json([
            'message' => 'Verification code sent to your email',
        ]);
    }

    public function verifyCode(Request $request)
    {
        $email = $request->input('email');
        $code = $request->input('code');

        $cachedCode = Cache::get('customer_verification_' . $email);

        if (!$cachedCode || $cachedCode !== $code) {
            return response()->json([
                'message' => 'Invalid or expired verification code',
            ], 400);
        }

        // Clear the code from cache
        Cache::forget('customer_verification_' . $email);

        $customer = Customer::where('email', $email)->first();

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

    public function store(Request $request)
    {
        $customerId = auth()->user()->customer_id;
        $productId = $request->input('product_id');

        $wishlistItem = $this->customerService->addToWishlist($customerId, $productId);

        if (!$wishlistItem) {
            return response()->json([
                'message' => 'Failed to add to wishlist',
            ], 400);
        }

        return response()->json([
            'message' => 'Product added to wishlist successfully',
            'wishlist_item' => $wishlistItem,
        ]);
    }

    public function destroy(Request $request, $productId)
    {
        $customerId = auth()->user()->customer_id;

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

    public function index()
    {
        $customerId = auth()->user()->customer_id;
        $wishlist = $this->customerService->getCustomerWishlist($customerId);

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