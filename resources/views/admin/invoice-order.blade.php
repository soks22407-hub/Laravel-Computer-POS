<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

        h2,
        h3 {
            margin: 0 0 10px 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .order-info,
        .checkout__totals-wrapper {
            margin-top: 20px;
        }

        .order-info__item {
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 5px;
        }

        table th {
            background: #f5f5f5;
        }

        .checkout-totals td {
            text-align: right;
        }

        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }

        .product-image {
            max-width: 50px;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ asset('uploads/setting/' . $globalSettings->logo) }}" class="logo" alt="Logo">
        <h2>Invoice #{{ $order->id }}</h2>
        <p>{{ $order->created_at->format('d M Y') }}</p>
    </div>

    <h3>Customer Info</h3>
    <div class="order-info">
        <div class="order-info__item"><strong>Name:</strong> {{ $order->name }}</div>
        <div class="order-info__item"><strong>Phone:</strong> {{ $order->phone }}</div>
        <div class="order-info__item"><strong>Address:</strong> {{ $order->address }}, {{ $order->locality }}, {{ $order->province }}, {{ $order->country }}</div>
        <div class="order-info__item"><strong>Payment Method:</strong> {{ $order->transaction->mode ?? 'N/A' }}</div>
    </div>

    <h3>Order Details</h3>
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderItems as $item)
            <tr>
                <td>
                    <img src="{{ asset('uploads/products/Thumbnails/' . $item->product->image) }}" class="product-image" alt="{{ $item->product->name }}">
                </td>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="checkout-totals">
        <tbody>
            <tr>
                <th>Subtotal</th>
                <td>${{ number_format($order->subtotal, 2) }}</td>
            </tr>
            <tr>
                <th>Shipping</th>
                <td>${{ number_format($order->shipping, 2) }}</td>
            </tr>
            <tr>
                <th>Total</th>
                <td>${{ number_format($order->total, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>

<script>
    window.print();
</script>