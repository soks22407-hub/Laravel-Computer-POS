@extends('layouts.ceo')
@section('content')
<h3>Products in Category: {{ $category->name }}</h3>

<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Brand</th>
            <th>Price</th>
            <th>Quantity</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
        <tr>
            <td>{{ $product->id }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->brand->name ?? '-' }}</td>
            <td>{{ $product->regular_price }}</td>
            <td>{{ $product->quantity }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $products->links('pagination::bootstrap-5') }}
@endsection
