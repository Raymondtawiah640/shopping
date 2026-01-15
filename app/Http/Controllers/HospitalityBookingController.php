<?php

namespace App\Http\Controllers;

use App\Services\HospitalityBookingService;
use App\Http\Requests\HospitalityBookingCreateRequest;
use App\Http\Requests\HospitalityBookingUpdateStatusRequest;
use App\Models\Vendor;
use Illuminate\Http\Request;

class HospitalityBookingController extends Controller
{
    protected $hospitalityBookingService;

    public function __construct(HospitalityBookingService $hospitalityBookingService)
    {
        $this->hospitalityBookingService = $hospitalityBookingService;
    }

    public function createBooking(HospitalityBookingCreateRequest $request)
    {
        $user = auth()->user();

        $result = $this->hospitalityBookingService->createBooking($request, $user);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json($result, $result['status']);
    }

    public function getCustomerBookings()
    {
        $user = auth()->user();

        $result = $this->hospitalityBookingService->getCustomerBookings($user);

        return response()->json($result);
    }

    public function getVendorBookings(Request $request)
    {
        $vendorId = $request->header('vendor_id');
        $vendor = Vendor::where('vendor_id', $vendorId)->first();

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        $result = $this->hospitalityBookingService->getVendorBookings($vendor);

        return response()->json($result);
    }

    public function updateBookingStatus(HospitalityBookingUpdateStatusRequest $request, $bookingId)
    {
        $vendorId = $request->header('vendor_id');
        $vendor = Vendor::where('vendor_id', $vendorId)->first();

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        $result = $this->hospitalityBookingService->updateBookingStatus($request, $bookingId, $vendor);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json($result);
    }
}