<?php

namespace App\Http\Controllers;

use App\Services\HotelService;
use App\Http\Requests\HotelStoreRequest;
use App\Http\Requests\HotelUpdateRequest;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    protected $hotelService;

    public function __construct(HotelService $hotelService)
    {
        $this->hotelService = $hotelService;
    }

    public function store(HotelStoreRequest $request)
    {
        $vendorId = $request->header('vendor_id');

        $result = $this->hotelService->storeHotel($request, $vendorId);

        return response()->json($result, $result['status']);
    }

    public function index(Request $request)
    {
        $vendorId = $request->header('vendor_id');

        $result = $this->hotelService->indexHotels($vendorId);

        return response()->json($result);
    }

    public function update(HotelUpdateRequest $request, $id)
    {
        $vendorId = $request->header('vendor_id');

        $result = $this->hotelService->updateHotel($request, $id, $vendorId);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json($result);
    }

    public function destroy(Request $request, $id)
    {
        $vendorId = $request->header('vendor_id');

        $result = $this->hotelService->destroyHotel($id, $vendorId);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json($result);
    }
}