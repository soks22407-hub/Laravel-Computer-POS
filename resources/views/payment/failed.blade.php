@extends('layouts.app')

@section('title', 'Payment Failed')

@section('content')
<div class="container mx-auto p-6 text-center">
    <h2 class="text-3xl font-bold text-red-600 mb-4">Payment Failed</h2>

    <p class="mb-6 text-lg">
        Unfortunately, your payment could not be processed. Please try again or choose another payment method.
    </p>

    <div class="flex justify-center gap-4">
        {{-- Retry payment --}}
        <a href="{{ url()->previous() }}" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Try Again
        </a>

        {{-- Go back to cart --}}
        <a href="{{ route('cart.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700">
            Back to Cart
        </a>
    </div>
</div>
@endsection
