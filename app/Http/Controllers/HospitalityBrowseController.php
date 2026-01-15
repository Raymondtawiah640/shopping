<?php

namespace App\Http\Controllers;

use App\Services\HospitalityService;
use Illuminate\Http\Request;

class HospitalityBrowseController extends Controller
{
    protected $hospitalityService;

    public function __construct(HospitalityService $hospitalityService)
    {
        $this->hospitalityService = $hospitalityService;
    }

    // Get all hotels with optional filtering
    public function getHotels(Request $request)
    {
        $result = $this->hospitalityService->getHotels($request);
        return response()->json($result);
    }

    // Get all restaurants
    public function getRestaurants(Request $request)
    {
        $result = $this->hospitalityService->getRestaurants($request);
        return response()->json($result);
    }

    // Get all transport services
    public function getTransports(Request $request)
    {
        $result = $this->hospitalityService->getTransports($request);
        return response()->json($result);
    }

    // Get all tours
    public function getTours(Request $request)
    {
        $result = $this->hospitalityService->getTours($request);
        return response()->json($result);
    }
}