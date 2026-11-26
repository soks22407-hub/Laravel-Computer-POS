@extends('layouts.employee')
@section('content')
<main class="pt-90">
    <div class="container">

        <h3 class="page-title">📦 Order Detail #{{ $order->id }}</h3>

        {{-- Order Info --}}
        <div class="card mt-3 p-3">
            <h5>Customer Information</h5>
            <p><strong>Name:</strong> {{ $order->name ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $order->phone ?? 'N/A' }}</p>
            <p><strong>Address:</strong> {{ $order->address ?? 'N/A' }}</p>
            <p><strong>Status:</strong>
                <span class="badge bg-info">{{ $order->status ?? 'Pending' }}</span>
            </p>
        </div>

        {{-- Products --}}
        <div class="card mt-4 p-3">
            <h5>🛒 Products</h5>

            <table class="table table-bordered mt-2">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    @php $grandTotal = 0; @endphp

                    @foreach($order->orderItems as $key => $item)
                    @php
                    $total = $item->quantity * $item->price;
                    $grandTotal += $total;
                    @endphp

                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>
                            @if($item->product->image)
                            <img src="{{ asset('uploads/products/Thumbnails') }}/{{ $item->product->image }}" alt="{{ $item->product->name }}" class="image" width="50">
                            @endif
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>${{ $item->price }}</td>
                        <td>${{ $total }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <h5 class="text-end">
                💰 Grand Total: ${{ $grandTotal }}
            </h5>
        </div>

        <a href="{{ url()->previous() }}" class="btn btn-secondary btn-lg px-5 py-3 mt-3">
            ⬅ Back
        </a>

    </div>
</main>
@endsection