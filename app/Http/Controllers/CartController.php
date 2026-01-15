<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function store(Request $request)
    {
        $customer = auth()->user();
        $customerId = $customer->customer_id;

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $cartItem = $this->customerService->addToCart($customerId, $productId, $quantity);

        if (!$cartItem) {
            return response()->json([
                'message' => 'Failed to add to cart',
            ], 400);
        }

        return response()->json([
            'message' => 'Product added to cart successfully',
            'cart_item' => $cartItem,
        ]);
    }

    public function update(Request $request, $customerId)
    {
        $customer = auth()->user();
        if ($customer->customer_id != $customerId) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        $cartItem = $this->customerService->updateCartItem($customerId, $productId, $quantity);

        if ($cartItem === null) {
            return response()->json([
                'message' => 'Cart item not found',
            ], 404);
        }

        if ($cartItem === true) {
            return response()->json([
                'message' => 'Cart item removed successfully',
            ]);
        }

        return response()->json([
            'message' => 'Cart item updated successfully',
            'cart_item' => $cartItem,
        ]);
    }

    public function destroy(Request $request, $customerId)
    {
        $customer = auth()->user();
        if ($customer->customer_id != $customerId) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $productId = $request->input('product_id');

        $result = $this->customerService->removeFromCart($customerId, $productId);

        if (!$result) {
            return response()->json([
                'message' => 'Cart item not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Product removed from cart successfully',
        ]);
    }

    public function show($customerId)
    {
        $customer = auth()->user();
        if ($customer->customer_id != $customerId) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $cart = $this->customerService->getCustomerCart($customerId);

        $total = $this->customerService->getCartTotal($customerId);

        return response()->json([
            'message' => 'Customer cart retrieved successfully',
            'cart' => $cart,
            'cart_total' => $total,
        ]);
    }

    public function clear($customerId)
    {
        $customer = auth()->user();
        if ($customer->customer_id != $customerId) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $result = $this->customerService->clearCustomerCart($customerId);

        if (!$result) {
            return response()->json([
                'message' => 'Failed to clear cart',
            ], 400);
        }

        return response()->json([
            'message' => 'Customer cart cleared successfully',
        ]);
    }
}