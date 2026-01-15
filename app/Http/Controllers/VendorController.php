<?php

namespace App\Http\Controllers;

use App\Http\Requests\VendorRequest;
use App\Http\Requests\VendorLoginRequest;
use App\Mail\VendorVerificationCode;
use App\Models\Vendor;
use App\Services\VendorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class VendorController extends Controller
{
    protected $vendorService;
    
    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }
    
    public function submitVendorInfo(VendorRequest $request)
    {
        $validatedData = $request->validated();
        
        $result = $this->vendorService->submitVendorInfo($validatedData);
        
        return response()->json($result);
    }
    
    public function checkVendorExists(Request $request)
    {
        $email = $request->input('email');
        $phoneNumber = $request->input('phone_number');
        
        $existingVendor = $this->vendorService->checkVendorExists($email, $phoneNumber);
        
        if ($existingVendor) {
            return response()->json([
                'exists' => true,
                'vendor_id' => $existingVendor->vendor_id,
                'message' => 'Vendor already exists',
            ]);
        }
        
        return response()->json([
            'exists' => false,
            'message' => 'Vendor does not exist',
        ]);
    }
    
    public function getVendorProfile($vendorId)
    {
        $vendor = Vendor::where('vendor_id', $vendorId)->first();
        
        if (!$vendor) {
            return response()->json([
                'message' => 'Vendor not found',
            ], 404);
        }
        
        return response()->json([
            'message' => 'Vendor profile retrieved successfully',
            'vendor' => $vendor,
        ]);
    }
    
    public function vendorLogin(VendorLoginRequest $request)
    {
        $vendorId = $request->input('vendor_id');
        $vendor = Vendor::where('vendor_id', $vendorId)->first();

        // Generate a 6-digit verification code
        $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store the code in cache for 10 minutes using email as key
        Cache::put('vendor_verification_' . $vendor->email, $code, now()->addMinutes(10));

        // Send the verification code via email
        Mail::to($vendor->email)->send(new VendorVerificationCode($vendor, $code));

        return response()->json([
            'message' => 'Verification code sent to your email',
        ]);
    }

    public function verifyCode(Request $request)
    {
        $vendorId = $request->input('vendor_id');
        $code = $request->input('code');

        $vendor = Vendor::where('vendor_id', $vendorId)->first();

        if (!$vendor) {
            return response()->json([
                'message' => 'Vendor not found',
            ], 404);
        }

        $cachedCode = Cache::get('vendor_verification_' . $vendor->email);

        if (!$cachedCode || $cachedCode !== $code) {
            return response()->json([
                'message' => 'Invalid or expired verification code',
            ], 400);
        }

        // Clear the code from cache
        Cache::forget('vendor_verification_' . $vendor->email);

        return response()->json([
            'message' => 'Verification successful',
            'vendor' => $vendor,
        ]);
    }
    
    public function vendorDashboard($vendorId)
    {
        $vendor = Vendor::where('vendor_id', $vendorId)->first();
        
        if (!$vendor) {
            return response()->json([
                'message' => 'Vendor not found',
            ], 404);
        }
        
        $dashboardData = $this->vendorService->getVendorDashboardData($vendorId);
        
        return response()->json([
            'message' => 'Vendor dashboard retrieved successfully',
            'vendor' => $vendor,
            'dashboard_data' => $dashboardData,
        ]);
    }
    
    public function store(Request $request)
    {
        $productData = $request->all();
        $image = $request->file('image');
        
        if ($request->isJson()) {
            $productData = $request->json()->all();
        }
        
        // Extract vendor_id from the header
        $vendorId = $request->header('vendor_id');
        $productData['vendor_id'] = $vendorId;
        
        // Handle the image upload
        $product = $this->vendorService->addProduct($productData, $image);
        
        if (!$product) {
            return response()->json([
                'message' => 'Failed to add product',
            ], 500);
        }
        
        return response()->json([
            'message' => 'Product added successfully',
            'product' => $product,
        ]);
    }
    
    public function update(Request $request, $productId)
    {
        $productData = $request->all();
        $image = $request->file('image');
        $product = $this->vendorService->updateProduct($productId, $productData, $image);
        
        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }
        
        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
        ]);
    }
    
    public function destroy($productId)
    {
        $result = $this->vendorService->deleteProduct($productId);
        
        if (!$result) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }
        
        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }
    
    public function getVendorProducts($vendorId)
    {
        $products = $this->vendorService->getVendorProducts($vendorId);
        
        return response()->json([
            'message' => 'Products retrieved successfully',
            'products' => $products,
        ]);
    }
    
    public function getVendorOrders($vendorId)
    {
        $orders = $this->vendorService->getVendorOrders($vendorId);
        
        return response()->json([
            'message' => 'Orders retrieved successfully',
            'orders' => $orders,
        ]);
    }

    public function getProductsByCategory($categoryId)
    {
        $products = $this->vendorService->getProductsByCategory($categoryId);

        return response()->json([
            'message' => 'Products by category retrieved successfully',
            'products' => $products,
        ]);
    }
}
