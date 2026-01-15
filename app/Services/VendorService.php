<?php

namespace App\Services;

use App\Models\Vendor;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Support\Str;

class VendorService
{
    public function submitVendorInfo(array $validatedData)
    {
        $email = $validatedData['email'] ?? null;
        $phoneNumber = $validatedData['phone_number'] ?? null;
        
        $existingVendor = $this->checkVendorExists($email, $phoneNumber);
        
        if ($existingVendor) {
            return [
                'message' => 'Vendor already exists',
                'vendor_id' => $existingVendor->vendor_id,
                'vendor' => $existingVendor,
            ];
        }
        
        $vendorId = $this->generateUniqueVendorId();
        
        $vendor = Vendor::create([
            'vendor_name' => $validatedData['vendor_name'],
            'business_name' => $validatedData['business_name'],
            'email' => $email,
            'phone_number' => $phoneNumber,
            'vendor_id' => $vendorId,
        ]);
        
        return [
            'message' => 'Vendor information submitted successfully',
            'vendor_id' => $vendorId,
            'vendor' => $vendor,
        ];
    }
    
    public function checkVendorExists($email, $phoneNumber)
    {
        $existingVendor = null;
        if ($email) {
            $existingVendor = Vendor::where('email', $email)->first();
        } elseif ($phoneNumber) {
            $existingVendor = Vendor::where('phone_number', $phoneNumber)->first();
        }
        
        return $existingVendor;
    }
    
    protected function generateUniqueVendorId()
    {
        return 'VENDOR-' . Str::upper(Str::random(8));
    }
    
    public function addProduct(array $productData, $image = null)
    {
        $imagePath = null;
        if ($image) {
            $imagePath = $image->store('product_images', 'public');
            // Get the full path of the uploaded image
            $imagePath = storage_path('app/public/' . $imagePath);
        }
        
        $product = Product::create([
            'name' => $productData['name'],
            'description' => $productData['description'],
            'price' => $productData['price'],
            'quantity' => $productData['quantity'],
            'image' => $imagePath,
            'vendor_id' => $productData['vendor_id'],
            'category_id' => $productData['category_id'] ?? null,
        ]);
        
        return $product;
    }
    
    public function updateProduct($productId, array $productData, $image = null)
    {
        $product = Product::find($productId);
        
        if (!$product) {
            return null;
        }
        
        // Only update the fields that are provided in the request
        $updateData = [];
        
        if (array_key_exists('name', $productData)) {
            $updateData['name'] = $productData['name'];
        }
        
        if (array_key_exists('description', $productData)) {
            $updateData['description'] = $productData['description'];
        }
        
        if (array_key_exists('price', $productData)) {
            $updateData['price'] = $productData['price'];
        }
        
        if (array_key_exists('quantity', $productData)) {
            $updateData['quantity'] = $productData['quantity'];
        }

        if (array_key_exists('category_id', $productData)) {
            $updateData['category_id'] = $productData['category_id'];
        }
        
        // Handle image update if provided
        if ($image) {
            $imagePath = $image->store('product_images', 'public');
            $updateData['image'] = storage_path('app/public/' . $imagePath);
        } elseif (array_key_exists('image', $productData) && $productData['image'] === null) {
            // Explicitly set image to null if provided as null in the request
            $updateData['image'] = null;
        }
        
        // Only perform the update if there are fields to update
        if (!empty($updateData)) {
            $product->update($updateData);
            // Refresh the product data from the database to ensure the latest data is returned
            $product->refresh();
            // Ensure the updated_at timestamp is updated
            $product->touch();
        }
        
        return $product;
    }
    
    public function deleteProduct($productId)
    {
        $product = Product::find($productId);
        
        if (!$product) {
            return false;
        }
        
        $product->delete();
        return true;
    }
    
    public function getVendorProducts($vendorId)
    {
        return Product::where('vendor_id', $vendorId)->get();
    }
    
    public function getVendorOrders($vendorId)
    {
        $orders = Order::all();
        $vendorOrders = $orders->filter(function ($order) use ($vendorId) {
            foreach ($order->items as $item) {
                $product = Product::find($item['product_id']);
                if ($product && $product->vendor_id == $vendorId) {
                    return true;
                }
            }
            return false;
        });
        return $vendorOrders;
    }
    
    public function getVendorDashboardData($vendorId)
    {
        $products = $this->getVendorProducts($vendorId);
        $orders = $this->getVendorOrders($vendorId);
        
        $totalOrders = $orders->count();
        $totalProducts = $products->count();
        $totalEarnings = $orders->sum('total_amount');
        
        return [
            'total_orders' => $totalOrders,
            'total_products' => $totalProducts,
            'total_earnings' => $totalEarnings,
            'recent_orders' => $orders->take(5),
        ];
    }

    public function getProductsByCategory($categoryId)
    {
        return Product::where('category_id', $categoryId)->get();
    }
}