@php
    $layout = match($role) {
        'ADM' => 'layouts.admin',
        default => 'layouts.employee',
    };

    $storeRoute = match($role) {
        'ADM' => route('admin.purchase-requests.store'),
        default => route('employee.purchase-requests.store'),
    };

    $backRoute = match($role) {
        'ADM' => route('admin.purchase-requests.index'),
        default => route('employee.purchase-requests.index'),
    };
@endphp


@extends($layout)

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-3">
            <h4>➕ New Purchase Request</h4>
            <a href="{{ $backRoute }}" class="btn btn-secondary">
                ← Back
            </a>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ $storeRoute }}" method="POST">
            @csrf
            <div class="wg-box mb-3">
                <h6 class="mb-3">📝 Note to CEO</h6>
                <textarea name="note" class="form-control" rows="3" 
                    placeholder="Optional note...">{{ old('note') }}</textarea>
            </div>

            <div class="wg-box">
                <h6 class="mb-3">📦 Low Stock Products</h6>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Select</th>
                                <th>Product</th>
                                <th>Variant</th>
                                <th>Current Qty</th>
                                <th>Request Qty</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 0; @endphp

                            {{-- Simple Products --}}
                            @foreach($lowStockProducts as $product)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="items[{{ $i }}][selected]" value="1">
                                    <input type="hidden" name="items[{{ $i }}][product_id]" value="{{ $product->id }}">
                                    <input type="hidden" name="items[{{ $i }}][product_name]" value="{{ $product->name }}">
                                    <input type="hidden" name="items[{{ $i }}][current_quantity]" value="{{ $product->quantity }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ asset('uploads/products/Thumbnails/' . $product->image) }}"
                                            style="width:40px;height:40px;object-fit:cover;border-radius:4px;">
                                        {{ $product->name }}
                                    </div>
                                </td>
                                <td>—</td>
                                <td>
                                    <span class="badge {{ $product->quantity == 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                        {{ $product->quantity }}
                                    </span>
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $i }}][requested_quantity]"
                                        class="form-control form-control-sm" min="1" value="1" style="width:80px;">
                                </td>
                                <td>
                                    <input type="text" name="items[{{ $i }}][reason]"
                                        class="form-control form-control-sm" placeholder="Reason...">
                                </td>
                            </tr>
                            @php $i++; @endphp
                            @endforeach

                            {{-- Variant Products --}}
                            @foreach($lowStockVariants as $variant)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="items[{{ $i }}][selected]" value="1">
                                    <input type="hidden" name="items[{{ $i }}][product_id]" value="{{ $variant->product->id }}">
                                    <input type="hidden" name="items[{{ $i }}][product_name]" value="{{ $variant->product->name }}">
                                    <input type="hidden" name="items[{{ $i }}][variant]" 
                                        value="{{ $variant->color }} {{ $variant->storage }}">
                                    <input type="hidden" name="items[{{ $i }}][current_quantity]" value="{{ $variant->quantity }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ asset('uploads/variants/Thumbnails/' . $variant->image) }}"
                                            style="width:40px;height:40px;object-fit:cover;border-radius:4px;">
                                        {{ $variant->product->name }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $variant->color }} {{ $variant->storage }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $variant->quantity == 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                        {{ $variant->quantity }}
                                    </span>
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $i }}][requested_quantity]"
                                        class="form-control form-control-sm" min="1" value="1" style="width:80px;">
                                </td>
                                <td>
                                    <input type="text" name="items[{{ $i }}][reason]"
                                        class="form-control form-control-sm" placeholder="Reason...">
                                </td>
                            </tr>
                            @php $i++; @endphp
                            @endforeach

                            @if($lowStockProducts->isEmpty() && $lowStockVariants->isEmpty())
                            <tr>
                                <td colspan="6" class="text-center text-success py-4">
                                    ✅ All products are well stocked!
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary btn-lg">
                        📤 Submit Request
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection