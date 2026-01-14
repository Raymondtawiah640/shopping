<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Wishlist;
use App\Models\Cart;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CustomerService
{
    public function registerCustomer(array $customerData)
    {
        $customerId = $this->generateUniqueCustomerId();

        $customer = Customer::create([
            'full_name' => $customerData['full_name'],
            'email' => $customerData['email'],
            'phone_number' => $customerData['phone_number'] ?? null,
            'password' => Hash::make($customerData['password']),
            'customer_id' => $customerId,
        ]);

        return $customer;
    }

    public function loginCustomer(array $credentials)
    {
        $customer = Customer::where('email', $credentials['email'])->first();

        if (!$customer || !Hash::check($credentials['password'], $customer->password)) {
            return null;
        }

        return $customer;
    }

    public function getCustomerProfile($customerId)
    {
        return Customer::where('customer_id', $customerId)->first();
    }

    public function updateCustomerProfile($customerId, array $customerData)
    {
        $customer = Customer::where('customer_id', $customerId)->first();

        if (!$customer) {
            return null;
        }

        $customer->update([
            'full_name' => $customerData['full_name'] ?? $customer->full_name,
            'email' => $customerData['email'] ?? $customer->email,
            'phone_number' => $customerData['phone_number'] ?? $customer->phone_number,
        ]);

        return $customer;
    }

    public function getCustomerOrders($customerId)
    {
        $customer = Customer::where('customer_id', $customerId)->first();

        if (!$customer) {
            return null;
        }

        return $customer->orders;
    }

    public function addToWishlist($customerId, $productId)
    {
        $customer = Customer::where('customer_id', $customerId)->first();

        if (!$customer) {
            return null;
        }

        $existingWishlistItem = Wishlist::where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->first();

        if ($existingWishlistItem) {
            return $existingWishlistItem;
        }

        return Wishlist::create([
            'customer_id' => $customerId,
            'product_id' => $productId,
        ]);
    }

    public function removeFromWishlist($customerId, $productId)
    {
        $wishlistItem = Wishlist::where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->first();

        if (!$wishlistItem) {
            return false;
        }

        return $wishlistItem->delete();
    }

    public function getCustomerWishlist($customerId)
    {
        $customer = Customer::where('customer_id', $customerId)->first();

        if (!$customer) {
            return null;
        }

        return $customer->wishlist()->with('product')->get();
    }

    public function clearCustomerWishlist($customerId)
    {
        $customer = Customer::where('customer_id', $customerId)->first();

        if (!$customer) {
            return false;
        }

        return $customer->wishlist()->delete();
    }

    public function addToCart($customerId, $productId, $quantity = 1)
    {
        $customer = Customer::where('customer_id', $customerId)->first();

        if (!$customer) {
            return null;
        }

        $existingCartItem = Cart::where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->first();

        if ($existingCartItem) {
            $existingCartItem->update([
                'quantity' => $existingCartItem->quantity + $quantity
            ]);
            return $existingCartItem;
        }

        return Cart::create([
            'customer_id' => $customerId,
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);
    }

    public function updateCartItem($customerId, $productId, $quantity)
    {
        $cartItem = Cart::where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->first();

        if (!$cartItem) {
            return null;
        }

        if ($quantity <= 0) {
            return $cartItem->delete();
        }

        $cartItem->update([
            'quantity' => $quantity
        ]);

        return $cartItem;
    }

    public function removeFromCart($customerId, $productId)
    {
        $cartItem = Cart::where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->first();

        if (!$cartItem) {
            return false;
        }

        return $cartItem->delete();
    }

    public function getCustomerCart($customerId)
    {
        $customer = Customer::where('customer_id', $customerId)->first();

        if (!$customer) {
            return null;
        }

        return $customer->cart()->with('product')->get();
    }

    public function clearCustomerCart($customerId)
    {
        $customer = Customer::where('customer_id', $customerId)->first();

        if (!$customer) {
            return false;
        }

        return $customer->cart()->delete();
    }

    public function getCartTotal($customerId)
    {
        $cartItems = $this->getCustomerCart($customerId);

        if (!$cartItems) {
            return 0;
        }

        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->product->price * $item->quantity;
        }

        return $total;
    }

    protected function generateUniqueCustomerId()
    {
        return 'CUSTOMER-' . Str::upper(Str::random(8));
    }
}