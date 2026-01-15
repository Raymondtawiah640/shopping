<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Vendor;

class VendorAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $vendorId = $request->header('vendor_id');
        
        if (!$vendorId) {
            return response()->json(['message' => 'Unauthorized: Vendor ID is required'], 401);
        }
        
        $vendor = Vendor::where('vendor_id', $vendorId)->first();
        
        if (!$vendor) {
            return response()->json(['message' => 'Unauthorized: Vendor not found'], 401);
        }
        
        return $next($request);
    }
}
