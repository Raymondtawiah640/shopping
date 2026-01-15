<?php

namespace App\Services;

use App\Models\Tour;
use Illuminate\Http\Request;

class TourService
{
    // Vendor CRUD for Tours
    public function storeTour(Request $request, $vendorId)
    {
        $validated = $request->validated();

        $itinerary = $this->processArrayField($validated['itinerary'] ?? null);
        $images = $this->processArrayField($validated['images'] ?? null);

        $tour = Tour::create([
            'vendor_id' => $vendorId,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'city' => $validated['city'],
            'country' => $validated['country'],
            'price_per_person' => $validated['price_per_person'],
            'duration_days' => $validated['duration_days'],
            'max_participants' => $validated['max_participants'],
            'available_spots' => $validated['available_spots'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'itinerary' => $itinerary,
            'images' => $images,
        ]);

        return [
            'message' => 'Tour created successfully',
            'tour' => $tour,
            'status' => 201,
        ];
    }

    public function indexTours($vendorId)
    {
        $tours = Tour::where('vendor_id', $vendorId)->get();

        return [
            'message' => 'Vendor tours retrieved successfully',
            'tours' => $tours,
        ];
    }

    public function updateTour(Request $request, $tourId, $vendorId)
    {
        $tour = Tour::where('id', $tourId)->where('vendor_id', $vendorId)->first();

        if (!$tour) {
            return [
                'error' => 'Tour not found',
                'status' => 404,
            ];
        }

        $validated = $request->validated();

        $data = $validated;

        if (isset($validated['itinerary'])) {
            $data['itinerary'] = $this->processArrayField($validated['itinerary']);
        }

        if (isset($validated['images'])) {
            $data['images'] = $this->processArrayField($validated['images']);
        }

        $tour->update($data);

        return [
            'message' => 'Tour updated successfully',
            'tour' => $tour,
        ];
    }

    public function destroyTour($tourId, $vendorId)
    {
        $tour = Tour::where('id', $tourId)->where('vendor_id', $vendorId)->first();

        if (!$tour) {
            return [
                'error' => 'Tour not found',
                'status' => 404,
            ];
        }

        $tour->delete();

        return [
            'message' => 'Tour deleted successfully',
        ];
    }

    private function processArrayField($field)
    {
        if (is_array($field)) {
            return $field;
        }

        if (is_string($field) && !empty($field)) {
            return array_map('trim', explode(',', $field));
        }

        return [];
    }
}