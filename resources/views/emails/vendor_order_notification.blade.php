<!DOCTYPE html>
<html>
<head>
    <title>New Order Notification</title>
</head>
<body>
    <h1>New Order for Your Products</h1>
    <p>Dear {{ $vendor->vendor_name }},</p>
    <p>You have received a new order for your products. Here are the details:</p>

    <h2>Order ID: {{ $order->order_id }}</h2>
    <p><strong>Customer:</strong> {{ $customer->full_name }}</p>
    <p><strong>Customer Email:</strong> {{ $customer->email }}</p>
    <p><strong>Shipping Address:</strong> {{ $order->shipping_address }}</p>
    <p><strong>Phone Number:</strong> {{ $order->phone_number }}</p>
    <p><strong>Payment Method:</strong> {{ $order->payment_method }}</p>
    <p><strong>Order Status:</strong> {{ $order->status }}</p>

    <h3>Your Products in This Order:</h3>
    <ul>
        @foreach($vendorItems as $item)
            <li>
                {{ $item['name'] }} - Quantity: {{ $item['quantity'] }} - Price: GHS {{ number_format($item['price'], 2) }} - Total: GHS {{ number_format($item['total'], 2) }}
            </li>
        @endforeach
    </ul>

    <p><strong>Total for Your Products:</strong> GHS {{ number_format(collect($vendorItems)->sum('total'), 2) }}</p>

    <p>Please log in to your vendor dashboard to manage this order.</p>
    <p>If you have any questions, please contact us.</p>
    <p>Best regards,<br>Kiln Enterprise</p>
</body>
</html>