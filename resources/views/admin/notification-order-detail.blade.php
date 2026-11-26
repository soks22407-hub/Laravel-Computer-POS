@extends('layouts.admin')
@section('content')
<main class="pt-90">
    <div class="container">

        <h4 class="page-title">📦 Order Detail #{{ $order->id }}</h4>

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
        <div class="card p-3" style="margin-top: 16px;">
            <h5>🛒 Products</h5>

            <table class="table table-bordered mt-2">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Product</th>
                        <th class="text-center">Image</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Price</th>
                        <th class="text-center">Total</th>
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
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td class="text-center">
                            @if($item->product->image)
                            <img src="{{ asset('uploads/products/Thumbnails') }}/{{ $item->product->image }}" alt="{{ $item->product->name }}" class="image" width="50">
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">${{ $item->price }}</td>
                        <td class="text-end">${{ $total }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <h5 class="text-end">
                💰 Grand Total: ${{ $grandTotal }}
            </h5>
        </div>

        <a href="{{ url()->previous() }}" class="btn btn-secondary btn-lg px-5 py-3" style="margin-top: 17px;">
            ⬅ Back
        </a>

    </div>
</main>
@endsection