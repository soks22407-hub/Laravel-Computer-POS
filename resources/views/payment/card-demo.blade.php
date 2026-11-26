@extends('layouts.app')

@section('title', 'Card Payment Demo')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6">Debit / Credit Card Payment</h2>

    {{-- Display Order Info --}}
    <div class="mb-4 p-4 border rounded bg-gray-50">
        <p><strong>Order ID:</strong> {{ $order->id }}</p>
        <p><strong>Total Amount:</strong> ${{ number_format($order->total, 2) }}</p>
    </div>

    {{-- Payment Form --}}
    <form action="{{ route('payment.card.demo.pay', $order->id) }}" method="POST" class="space-y-4">
        @csrf

        <div class="form-group">
            <label for="card_number" class="block font-medium mb-1">Card Number</label>
            <input type="text" name="card_number" id="card_number" class="form-input w-full border p-2 rounded" placeholder="1234 5678 9012 3456" required>
        </div>

        <div class="flex gap-4">
            <div class="form-group flex-1">
                <label for="expiry" class="block font-medium mb-1">Expiry Date</label>
                <input type="text" name="expiry" id="expiry" class="form-input w-full border p-2 rounded" placeholder="MM/YY" required>
            </div>
            <div class="form-group flex-1">
                <label for="cvv" class="block font-medium mb-1">CVV</label>
                <input type="text" name="cvv" id="cvv" class="form-input w-full border p-2 rounded" placeholder="123" required>
            </div>
        </div>

        <div class="form-group">
            <label for="name" class="block font-medium mb-1">Cardholder Name</label>
            <input type="text" name="name" id="name" class="form-input w-full border p-2 rounded" placeholder="John Doe" required>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Pay ${{ number_format($order->total, 2) }}
        </button>
    </form>
</div>
@endsection
