
<div class="text-center mb-3">
    <h5 class="mb-0">🏪 Dolla Dolla Phone Shop</h5>
    <small class="text-muted">Receipt #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</small><br>
    <small class="text-muted">{{ $order->created_at->format('d M Y, h:i A') }}</small>
    @if($order->cashier)
    <br><small class="text-muted">Cashier: {{ $order->cashier->name }}</small>
    @endif
</div>
<hr>
@if($order->customer_name)
<div><strong>Customer:</strong> {{ $order->customer_name }}</div>
@endif
@if($order->customer_phone)
<div><strong>Phone:</strong> {{ $order->customer_phone }}</div>
@endif
<hr>
<table class="w-100">
    <thead>
        <tr>
            <th>Item</th>
            <th class="text-center">Qty</th>
            <th class="text-end">Price</th>
            <th class="text-end">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
        <tr>
            <td style="font-size:12px;">{{ $item->product_name }}</td>
            <td class="text-center">{{ $item->quantity }}</td>
            <td class="text-end">${{ number_format($item->price, 2) }}</td>
            <td class="text-end">${{ number_format($item->subtotal, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<hr>
<table class="w-100">
    <tr>
        <td>Subtotal</td>
        <td class="text-end">${{ number_format($order->subtotal, 2) }}</td>
    </tr>
    @if($order->discount > 0)
    <tr>
        <td>Discount</td>
        <td class="text-end text-danger">-${{ number_format($order->discount, 2) }}</td>
    </tr>
    @endif
    <tr>
        <td><strong>TOTAL</strong></td>
        <td class="text-end"><strong>${{ number_format($order->total, 2) }}</strong></td>
    </tr>
    <tr>
        <td>Paid ({{ strtoupper($order->payment_method) }})</td>
        <td class="text-end">${{ number_format($order->amount_paid, 2) }}</td>
    </tr>

    @if($order->amount_owed > 0)
    {{-- Credit order: show amount owed, hide change --}}
    <tr>
        <td><strong class="text-danger">📒 Amount Owed</strong></td>
        <td class="text-end text-danger">
            <strong>${{ number_format($order->amount_owed, 2) }}</strong>
        </td>
    </tr>
    @else
    {{-- Normal order: show change --}}
    <tr>
        <td><strong>Change</strong></td>
        <td class="text-end text-success">
            <strong>${{ number_format($order->change_amount, 2) }}</strong>
        </td>
    </tr>
    @endif
</table>
<hr>
@if($order->note)
<div><small>Note: {{ $order->note }}</small></div>
<hr>
@endif
<div class="text-center">
    <small>Thank you for shopping with us! 🙏</small>
</div>


