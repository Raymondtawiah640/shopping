<?php

namespace App\Http\Controllers;

use App\Services\TourService;
use App\Http\Requests\TourStoreRequest;
use App\Http\Requests\TourUpdateRequest;
use Illuminate\Http\Request;

class TourController extends Controller
{
    protected $tourService;

    public function __construct(TourService $tourService)
    {
        $this->tourService = $tourService;
    }

    public function store(TourStoreRequest $request)
    {
        try {
            $vendorId = $request->header('vendor_id');

            $result = $this->tourService->storeTour($request, $vendorId);

            return response()->json($result, $result['status']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        $vendorId = $request->header('vendor_id');

        $result = $this->tourService->indexTours($vendorId);

        return response()->json($result);
    }

    public function update(TourUpdateRequest $request, $id)
    {
        $vendorId = $request->header('vendor_id');

        $result = $this->tourService->updateTour($request, $id, $vendorId);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json($result);
    }

    public function destroy(Request $request, $id)
    {
        $vendorId = $request->header('vendor_id');

        $result = $this->tourService->destroyTour($id, $vendorId);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json($result);
    }
}