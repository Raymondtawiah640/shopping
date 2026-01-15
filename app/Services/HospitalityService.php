<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\Transport;
use App\Models\Tour;
use Illuminate\Http\Request;

class HospitalityService
{
    // Get all hotels with optional filtering
    public function getHotels(Request $request)
    {
        $query = Hotel::with('vendor')->where('is_active', true);

        // Location filters
        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        if ($request->has('country')) {
            $query->where('country', 'like', '%' . $request->country . '%');
        }

        // Price range
        if ($request->has('min_price')) {
            $query->where('price_per_night', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price_per_night', '<=', $request->max_price);
        }

        // Availability
        if ($request->has('check_in') && $request->has('check_out')) {
            $query->where('available_rooms', '>', 0);
        }

        $hotels = $query->paginate(20);

        return [
            'message' => 'Hotels retrieved successfully',
            'hotels' => $hotels,
        ];
    }

    // Get all restaurants
    public function getRestaurants(Request $request)
    {
        $query = Restaurant::with('vendor')->where('is_active', true);

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        if ($request->has('cuisine_type')) {
            $query->where('cuisine_type', $request->cuisine_type);
        }

        $restaurants = $query->paginate(20);

        return [
            'message' => 'Restaurants retrieved successfully',
            'restaurants' => $restaurants,
        ];
    }

    // Get all transport services
    public function getTransports(Request $request)
    {
        $query = Transport::with('vendor')->where('is_active', true);

        if ($request->has('departure_location')) {
            $query->where('departure_location', 'like', '%' . $request->departure_location . '%');
        }
        if ($request->has('arrival_location')) {
            $query->where('arrival_location', 'like', '%' . $request->arrival_location . '%');
        }
        if ($request->has('transport_type')) {
            $query->where('transport_type', $request->transport_type);
        }

        $transports = $query->paginate(20);

        return [
            'message' => 'Transport services retrieved successfully',
            'transports' => $transports,
        ];
    }

    // Get all tours
    public function getTours(Request $request)
    {
        $query = Tour::with('vendor')->where('is_active', true);

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        if ($request->has('country')) {
            $query->where('country', 'like', '%' . $request->country . '%');
        }

        $tours = $query->paginate(20);

        return [
            'message' => 'Tours retrieved successfully',
            'tours' => $tours,
        ];
    }
}