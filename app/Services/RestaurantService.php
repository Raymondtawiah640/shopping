<?php

namespace App\Services;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantService
{
    // Vendor CRUD for Restaurants
    public function storeRestaurant(Request $request, $vendorId)
    {
        $validated = $request->validated();

        $restaurant = Restaurant::create([
            'vendor_id' => $vendorId,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'city' => $validated['city'],
            'country' => $validated['country'],
            'cuisine_type' => $validated['cuisine_type'],
            'average_price' => $validated['average_price'],
            'capacity' => $validated['capacity'],
            'opening_hours' => $validated['opening_hours'] ?? null,
            'images' => $validated['images'] ?? null,
        ]);

        return [
            'message' => 'Restaurant created successfully',
            'restaurant' => $restaurant,
            'status' => 201,
        ];
    }

    public function indexRestaurants($vendorId)
    {
        $restaurants = Restaurant::where('vendor_id', $vendorId)->get();

        return [
            'message' => 'Vendor restaurants retrieved successfully',
            'restaurants' => $restaurants,
        ];
    }

    public function updateRestaurant(Request $request, $restaurantId, $vendorId)
    {
        $restaurant = Restaurant::where('id', $restaurantId)->where('vendor_id', $vendorId)->first();

        if (!$restaurant) {
            return [
                'error' => 'Restaurant not found',
                'status' => 404,
            ];
        }

        $validated = $request->validated();

        $restaurant->update($validated);

        return [
            'message' => 'Restaurant updated successfully',
            'restaurant' => $restaurant,
        ];
    }

    public function destroyRestaurant($restaurantId, $vendorId)
    {
        $restaurant = Restaurant::where('id', $restaurantId)->where('vendor_id', $vendorId)->first();

        if (!$restaurant) {
            return [
                'error' => 'Restaurant not found',
                'status' => 404,
            ];
        }

        $restaurant->delete();

        return [
            'message' => 'Restaurant deleted successfully',
        ];
    }
}