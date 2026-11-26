@extends('layouts.employee')
@section('content')
<h3>Products of Brand: {{ $brand->name }}</h3>

<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Quantity</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
        <tr>
            <td>{{ $product->id }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->category->name ?? '-' }}</td>
            <td>{{ $product->regular_price }}</td>
            <td>{{ $product->quantity }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $products->links('pagination::bootstrap-5') }}
@endsection
