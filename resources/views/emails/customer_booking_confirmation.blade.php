<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
</head>
<body>
    <h1>Booking Confirmation</h1>
    <p>Dear {{ $customer->full_name }},</p>
    <p>Thank you for your booking! Your request has been submitted successfully. Here are the details:</p>

    <h2>Booking ID: {{ $booking->booking_id }}</h2>
    <p><strong>Service Type:</strong> {{ ucfirst($booking->service_type) }}</p>
    <p><strong>Service:</strong> {{ $service->name }}</p>
    <p><strong>Vendor:</strong> {{ $vendor->vendor_name }}</p>

    <h3>Booking Details:</h3>
    <p><strong>Number of Guests:</strong> {{ $booking->number_of_guests }}</p>
    @if($booking->check_in_date)
    <p><strong>Check-in Date:</strong> {{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</p>
    @endif
    @if($booking->check_out_date)
    <p><strong>Check-out Date:</strong> {{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}</p>
    @endif
    @if($booking->booking_date)
    <p><strong>Booking Date:</strong> {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y H:i') }}</p>
    @endif
    @if($booking->special_requests)
    <p><strong>Special Requests:</strong> {{ $booking->special_requests }}</p>
    @endif
    <p><strong>Total Amount:</strong> GHS {{ number_format($booking->total_amount, 2) }}</p>
    <p><strong>Status:</strong> {{ ucfirst($booking->status) }}</p>

    <h3>Service Information:</h3>
    @if($booking->service_type === 'transport')
    <p><strong>Route:</strong> {{ $service->departure_location }} to {{ $service->arrival_location }}</p>
    @else
    <p><strong>Location:</strong> {{ $service->location }}, {{ $service->city }}, {{ $service->country }}</p>
    @endif
    <p><strong>Description:</strong> {{ $service->description }}</p>

    @if($booking->service_type === 'hotel')
    <p><strong>Price per Night:</strong> GHS {{ number_format($service->price_per_night, 2) }}</p>
    <p><strong>Available Rooms:</strong> {{ $service->available_rooms }}</p>
    @elseif($booking->service_type === 'restaurant')
    <p><strong>Cuisine Type:</strong> {{ $service->cuisine_type }}</p>
    <p><strong>Average Price:</strong> GHS {{ number_format($service->average_price, 2) }}</p>
    @elseif($booking->service_type === 'transport')
    <p><strong>Route:</strong> {{ $service->departure_location }} to {{ $service->arrival_location }}</p>
    <p><strong>Departure:</strong> {{ \Carbon\Carbon::parse($service->departure_time)->format('M d, Y H:i') }}</p>
    <p><strong>Price per Person:</strong> GHS {{ number_format($service->price_per_person, 2) }}</p>
    @elseif($booking->service_type === 'tour')
    <p><strong>Duration:</strong> {{ $service->duration_days }} days</p>
    <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($service->start_date)->format('M d, Y') }}</p>
    <p><strong>Price per Person:</strong> GHS {{ number_format($service->price_per_person, 2) }}</p>
    @endif

    <p>You will receive an update once the vendor confirms your booking. If you have any questions, please contact us.</p>
    <p>Best regards,<br>Kiln Enterprise</p>
</body>
</html>