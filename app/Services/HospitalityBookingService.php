<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\Transport;
use App\Models\Tour;
use App\Models\HospitalityBooking;
use App\Mail\HospitalityBookingNotification;
use App\Mail\CustomerBookingConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class HospitalityBookingService
{
    // Create booking request
    public function createBooking(Request $request, $user)
    {
        $customerId = $user->customer_id;

        // Verify service exists and belongs to a hospitality vendor
        $service = null;
        $vendor = null;

        switch ($request->service_type) {
            case 'hotel':
                $service = Hotel::with('vendor')->find($request->service_id);
                break;
            case 'restaurant':
                $service = Restaurant::with('vendor')->find($request->service_id);
                break;
            case 'transport':
                $service = Transport::with('vendor')->find($request->service_id);
                break;
            case 'tour':
                $service = Tour::with('vendor')->find($request->service_id);
                break;
        }

        if (!$service || !$service->vendor) {
            return [
                'error' => 'Service not found or invalid',
                'status' => 404,
            ];
        }

        $vendor = $service->vendor;
        $vendorId = $service->vendor_id;

        // Calculate total amount
        $totalAmount = 0;
        switch ($request->service_type) {
            case 'hotel':
                if ($request->check_in_date && $request->check_out_date) {
                    $nights = Carbon::parse($request->check_in_date)->diffInDays($request->check_out_date);
                    $totalAmount = $service->price_per_night * $request->number_of_guests * $nights;
                }
                break;
            case 'restaurant':
                $totalAmount = $service->average_price * $request->number_of_guests;
                break;
            case 'transport':
                $totalAmount = $service->price_per_person * $request->number_of_guests;
                break;
            case 'tour':
                $totalAmount = $service->price_per_person * $request->number_of_guests;
                break;
        }

        $booking = HospitalityBooking::create([
            'customer_id' => $customerId,
            'vendor_id' => $vendorId,
            'service_type' => $request->service_type,
            'service_id' => $request->service_id,
            'number_of_guests' => $request->number_of_guests,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'booking_date' => $request->booking_date,
            'special_requests' => $request->special_requests,
            'total_amount' => $totalAmount,
            'status' => 'confirmed',
        ]);

        // Send email notification to vendor
        if ($vendor->email) {
            Mail::to($vendor->email)->send(new HospitalityBookingNotification($booking, $user, $vendor, $service));
        }

        // Send email confirmation to customer
        if ($user->email) {
            Mail::to($user->email)->send(new CustomerBookingConfirmation($booking, $user, $vendor, $service));
        }

        return [
            'message' => 'Booking request submitted successfully',
            'booking' => $booking,
            'status' => 201,
        ];
    }

    // Get customer bookings
    public function getCustomerBookings($user)
    {
        $customerId = $user->customer_id;

        $bookings = HospitalityBooking::with(['vendor', 'customer'])
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'message' => 'Customer bookings retrieved successfully',
            'bookings' => $bookings,
        ];
    }

    // Get vendor bookings
    public function getVendorBookings($vendor)
    {
        $vendorId = $vendor->vendor_id;

        $bookings = HospitalityBooking::with(['customer'])
            ->where('vendor_id', $vendorId)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'message' => 'Vendor bookings retrieved successfully',
            'bookings' => $bookings,
        ];
    }

    // Update booking status
    public function updateBookingStatus(Request $request, $bookingId, $vendor)
    {
        $booking = HospitalityBooking::find($bookingId);

        if (!$booking) {
            return [
                'error' => 'Booking not found',
                'status' => 404,
            ];
        }

        // Verify vendor owns this booking
        $vendorId = $vendor->vendor_id;
        if ($booking->vendor_id !== $vendorId) {
            return [
                'error' => 'Unauthorized',
                'status' => 403,
            ];
        }

        $booking->update([
            'status' => $request->status,
            'vendor_notes' => $request->vendor_notes,
        ]);

        return [
            'message' => 'Booking status updated successfully',
            'booking' => $booking,
        ];
    }
}