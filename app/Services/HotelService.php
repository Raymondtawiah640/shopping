<?php

namespace App\Services;

use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelService
{
    // Vendor CRUD for Hotels
    public function storeHotel(Request $request, $vendorId)
    {
        $validated = $request->validated();

        $amenities = $this->processAmenities($validated['amenities'] ?? null);
        $images = $this->processAmenities($validated['images'] ?? null); // reuse the method

        $hotel = Hotel::create([
            'vendor_id' => $vendorId,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'city' => $validated['city'],
            'country' => $validated['country'],
            'price_per_night' => $validated['price_per_night'],
            'total_rooms' => $validated['total_rooms'],
            'available_rooms' => $validated['available_rooms'],
            'amenities' => $amenities,
            'images' => $images,
        ]);

        return [
            'message' => 'Hotel created successfully',
            'hotel' => $hotel,
            'status' => 201,
        ];
    }

    public function indexHotels($vendorId)
    {
        $hotels = Hotel::where('vendor_id', $vendorId)->get();

        return [
            'message' => 'Vendor hotels retrieved successfully',
            'hotels' => $hotels,
        ];
    }

    public function updateHotel(Request $request, $hotelId, $vendorId)
    {
        $hotel = Hotel::where('id', $hotelId)->where('vendor_id', $vendorId)->first();

        if (!$hotel) {
            return [
                'error' => 'Hotel not found',
                'status' => 404,
            ];
        }

        $validated = $request->validated();

        $data = $validated;

        if (isset($validated['amenities'])) {
            $data['amenities'] = $this->processAmenities($validated['amenities']);
        }

        if (isset($validated['images'])) {
            $data['images'] = $this->processAmenities($validated['images']);
        }

        $hotel->update($data);

        return [
            'message' => 'Hotel updated successfully',
            'hotel' => $hotel,
        ];
    }

    public function destroyHotel($hotelId, $vendorId)
    {
        $hotel = Hotel::where('id', $hotelId)->where('vendor_id', $vendorId)->first();

        if (!$hotel) {
            return [
                'error' => 'Hotel not found',
                'status' => 404,
            ];
        }

        $hotel->delete();

        return [
            'message' => 'Hotel deleted successfully',
        ];
    }

    private function processAmenities($amenities)
    {
        if (is_array($amenities)) {
            return $amenities;
        }

        if (is_string($amenities) && !empty($amenities)) {
            return array_map('trim', explode(',', $amenities));
        }

        return [];
    }
}