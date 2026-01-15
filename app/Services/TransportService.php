<?php

namespace App\Services;

use App\Models\Transport;
use Illuminate\Http\Request;

class TransportService
{
    // Vendor CRUD for Transports
    public function storeTransport(Request $request, $vendorId)
    {
        $validated = $request->validated();

        $images = $this->processArrayField($validated['images'] ?? null);

        $transport = Transport::create([
            'vendor_id' => $vendorId,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'transport_type' => $validated['transport_type'],
            'departure_location' => $validated['departure_location'],
            'arrival_location' => $validated['arrival_location'],
            'price_per_person' => $validated['price_per_person'],
            'capacity' => $validated['capacity'],
            'available_seats' => $validated['available_seats'],
            'departure_time' => $validated['departure_time'],
            'arrival_time' => $validated['arrival_time'],
            'images' => $images,
        ]);

        return [
            'message' => 'Transport created successfully',
            'transport' => $transport,
            'status' => 201,
        ];
    }

    public function indexTransports($vendorId)
    {
        $transports = Transport::where('vendor_id', $vendorId)->get();

        return [
            'message' => 'Vendor transports retrieved successfully',
            'transports' => $transports,
        ];
    }

    public function updateTransport(Request $request, $transportId, $vendorId)
    {
        $transport = Transport::where('id', $transportId)->where('vendor_id', $vendorId)->first();

        if (!$transport) {
            return [
                'error' => 'Transport not found',
                'status' => 404,
            ];
        }

        $validated = $request->validated();

        $data = $validated;

        if (isset($validated['images'])) {
            $data['images'] = $this->processArrayField($validated['images']);
        }

        $transport->update($data);

        return [
            'message' => 'Transport updated successfully',
            'transport' => $transport,
        ];
    }

    public function destroyTransport($transportId, $vendorId)
    {
        $transport = Transport::where('id', $transportId)->where('vendor_id', $vendorId)->first();

        if (!$transport) {
            return [
                'error' => 'Transport not found',
                'status' => 404,
            ];
        }

        $transport->delete();

        return [
            'message' => 'Transport deleted successfully',
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