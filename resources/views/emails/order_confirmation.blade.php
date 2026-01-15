<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
</head>
<body>
    <h1>Order Confirmation</h1>
    <p>Dear {{ $customer->full_name }},</p>
    <p>Thank you for your order! Here are the details:</p>

    <h2>Order ID: {{ $order->order_id }}</h2>
    <p><strong>Shipping Address:</strong> {{ $order->shipping_address }}</p>
    <p><strong>Phone Number:</strong> {{ $order->phone_number }}</p>
    <p><strong>Payment Method:</strong> {{ $order->payment_method }}</p>
    <p><strong>Total Amount:</strong> GHS {{ number_format($order->total_amount, 2) }}</p>
    <p><strong>Status:</strong> {{ $order->status }}</p>

    <h3>Items Ordered:</h3>
    <ul>
        @foreach($order->items as $item)
            <li>
                {{ $item['name'] }} - Quantity: {{ $item['quantity'] }} - Price: GHS {{ number_format($item['price'], 2) }} - Total: GHS {{ number_format($item['total'], 2) }}
            </li>
        @endforeach
    </ul>

    <p>If you have any questions, please contact us.</p>
    <p>Best regards,<br>Kiln Enterprise</p>
</body>
</html>