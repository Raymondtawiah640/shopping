<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ShoppingController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(Request $request)
    {
        $query = Product::query();

        // Search by name
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
        }

        // Filter by category
        if ($request->has('category_id')) {
            $categoryId = $request->input('category_id');
            $query->where('category_id', $categoryId);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        // Filter by vendor
        if ($request->has('vendor_id')) {
            $query->where('vendor_id', $request->input('vendor_id'));
        }

        $products = $query->get();

        return response()->json([
            'message' => 'Products retrieved successfully',
            'products' => $products,
        ]);
    }

    public function searchProducts(Request $request)
    {
        $query = Product::query();

        // Search by name or description
        if ($request->has('q')) {
            $searchTerm = $request->input('q');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by category
        if ($request->has('category')) {
            $categoryId = $request->input('category');
            $query->where('category_id', $categoryId);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        $products = $query->get();

        return response()->json([
            'message' => 'Products search results',
            'products' => $products,
            'count' => $products->count(),
        ]);
    }

    public function getProductsByCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)->get();

        return response()->json([
            'message' => 'Products by category retrieved successfully',
            'products' => $products,
        ]);
    }

    public function getAllCategories()
    {
        $categories = Category::all();

        return response()->json([
            'message' => 'All categories retrieved successfully',
            'categories' => $categories,
        ]);
    }

    public function show($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Product retrieved successfully',
            'product' => $product,
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken('user-token', ['*'], now()->addDays(30))->plainTextToken;

            return response()->json([
                'message' => 'User login successful',
                'user' => $user,
                'token' => $token,
            ]);
        }

        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }

    public function placeOrder(Request $request)
    {
        $orderData = $request->all();

        $customerId = $orderData['customer_id'];

        // Get customer cart
        $cart = $this->customerService->getCustomerCart($customerId);

        if (!$cart || $cart->isEmpty()) {
            return response()->json([
                'message' => 'Cart is empty',
            ], 400);
        }

        // Prepare items from cart
        $items = $cart->map(function ($cartItem) {
            return [
                'product_id' => $cartItem->product_id,
                'name' => $cartItem->product->name,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->product->price,
                'total' => $cartItem->product->price * $cartItem->quantity,
            ];
        });

        // Calculate total amount
        $totalAmount = $items->sum('total');

        $orderId = 'ORDER-' . strtoupper(Str::random(8));

        $order = Order::create([
            'order_id' => $orderId,
            'customer_id' => $customerId,
            'vendor_id' => null, // Orders can have items from multiple vendors
            'shipping_address' => $orderData['shipping_address'],
            'payment_method' => $orderData['payment_method'],
            'phone_number' => $orderData['phone_number'] ?? auth()->user()->phone_number,
            'total_amount' => $totalAmount,
            'status' => 'confirmed',
            'items' => $items->toArray(),
        ]);

        if (!$order) {
            return response()->json([
                'message' => 'Failed to place order',
            ], 500);
        }

        // Clear the cart after successful order
        $this->customerService->clearCustomerCart($customerId);

        // Get customer for email
        $customer = Customer::where('customer_id', $customerId)->first();

        // Send order confirmation email
        if ($customer) {
            Mail::to($customer->email)->send(new OrderConfirmation($order, $customer));
        }

        return response()->json([
            'message' => 'Order placed successfully',
            'order' => $order,
        ]);
    }

    public function getOrderHistory($customerId)
    {
        $orders = $this->customerService->getCustomerOrders($customerId);

        if (!$orders) {
            return response()->json([
                'message' => 'Customer not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Order history retrieved successfully',
            'orders' => $orders,
        ]);
    }
}
