<?php

namespace App\Http\Controllers;

use App\Services\RestaurantService;
use App\Http\Requests\RestaurantStoreRequest;
use App\Http\Requests\RestaurantUpdateRequest;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    protected $restaurantService;

    public function __construct(RestaurantService $restaurantService)
    {
        $this->restaurantService = $restaurantService;
    }

    public function store(RestaurantStoreRequest $request)
    {
        $vendorId = $request->header('vendor_id');

        $result = $this->restaurantService->storeRestaurant($request, $vendorId);

        return response()->json($result, $result['status']);
    }

    public function index(Request $request)
    {
        $vendorId = $request->header('vendor_id');

        $result = $this->restaurantService->indexRestaurants($vendorId);

        return response()->json($result);
    }

    public function update(RestaurantUpdateRequest $request, $id)
    {
        $vendorId = $request->header('vendor_id');

        $result = $this->restaurantService->updateRestaurant($request, $id, $vendorId);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json($result);
    }

    public function destroy(Request $request, $id)
    {
        $vendorId = $request->header('vendor_id');

        $result = $this->restaurantService->destroyRestaurant($id, $vendorId);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json($result);
    }
}