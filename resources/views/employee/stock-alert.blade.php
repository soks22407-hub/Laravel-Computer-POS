@extends('layouts.employee')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding: 5px 0; margin-top: 0;">
            <h4>📦 Stock Alert</h4>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('employee.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Stock Alert</div></li>
            </ul>
        </div>

        @php
            $outOfStockCount = 0;
            $lowStockCount = 0;
            foreach ($lowStockProducts as $p) {
                $p->quantity == 0 ? $outOfStockCount++ : $lowStockCount++;
            }
            foreach ($lowStockVariantProducts as $p) {
                foreach ($p->variants as $v) {
                    $v->quantity == 0 ? $outOfStockCount++ : $lowStockCount++;
                }
            }
            $totalNeedsRestock = $outOfStockCount + $lowStockCount;
        @endphp

        {{-- Summary Badges + Request Button --}}
        <div class="d-flex gap-3 mb-4 flex-wrap align-items-center">
            <div class="wg-box px-4 py-3 text-center" style="min-width:150px;">
                <div class="fs-4 fw-bold text-danger">{{ $outOfStockCount }}</div>
                <div class="text-muted text-tiny">Out of Stock</div>
            </div>
            <div class="wg-box px-4 py-3 text-center" style="min-width:150px;">
                <div class="fs-4 fw-bold text-warning">{{ $lowStockCount }}</div>
                <div class="text-muted text-tiny">Low Stock (1–5)</div>
            </div>
            <div class="wg-box px-4 py-3 text-center" style="min-width:150px;">
                <div class="fs-4 fw-bold text-secondary">{{ $totalNeedsRestock }}</div>
                <div class="text-muted text-tiny">Total Needs Restock</div>
            </div>

            {{-- 👇 Request Restock Button for Employee --}}
            @if($totalNeedsRestock > 0)
            <div class="ms-auto">
                <a href="{{ route('employee.purchase-requests.create') }}"
                   class="btn btn-primary btn-lg">
                    📤 Request Restock
                </a>
            </div>
            @endif
        </div>

        {{-- My Recent Purchase Requests --}}
        @php
            $myRequests = \App\Models\PurchaseRequest::where('employee_id', auth()->id())
                ->with('items')
                ->latest()
                ->take(3)
                ->get();
        @endphp

        @if($myRequests->count() > 0)
        <div class="wg-box mb-4" style="margin-top: 16px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">📋 My Recent Purchase Requests</h6>
                <a href="{{ route('employee.purchase-requests.index') }}"
                   class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Note</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myRequests as $pr)
                        <tr>
                            <td>{{ $pr->id }}</td>
                            <td>{{ $pr->created_at->format('d/m/Y') }}</td>
                            <td>{{ $pr->items->count() }} items</td>
                            <td>{{ Str::limit($pr->note, 30) ?? '—' }}</td>
                            <td>
                                @if($pr->status === 'pending')
                                    <span class="badge bg-warning text-dark">⏳ Pending</span>
                                @elseif($pr->status === 'approved')
                                    <span class="badge bg-success">✅ Approved</span>
                                @else
                                    <span class="badge bg-danger">❌ Rejected</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('employee.purchase-requests.show', $pr->id) }}"
                                   class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Stock Alert Table --}}
        <div class="wg-box" style="margin-top: 16px;">
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Variant</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rowNum = 1; @endphp

                        {{-- Simple products --}}
                        @foreach($lowStockProducts as $product)
                        <tr>
                            <td>{{ $rowNum++ }}</td>
                            <td>
                                <img src="{{ asset('uploads/products/Thumbnails/' . $product->image) }}"
                                    style="width:50px; height:50px; object-fit:cover; border-radius:4px;">
                            </td>
                            <td>
                                <a href="{{ route('employee.product.show', $product->id) }}">
                                    {{ $product->name }}
                                </a>
                            </td>
                            <td>{{ $product->SKU }}</td>
                            <td>{{ $product->category->name ?? '—' }}</td>
                            <td>{{ $product->brand->name ?? '—' }}</td>
                            <td>—</td>
                            <td>
                                <span class="fw-bold {{ $product->quantity == 0 ? 'text-danger' : 'text-warning' }}">
                                    {{ $product->quantity }}
                                </span>
                            </td>
                            <td>
                                @if($product->quantity == 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                @elseif($product->quantity <= 3)
                                    <span class="badge bg-danger">Critical</span>
                                @else
                                    <span class="badge bg-warning text-dark">Low Stock</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('employee.product.edit', $product->id) }}">
                                    <div class="item edit">
                                        <i class="icon-edit-3"></i>
                                    </div>
                                </a>
                            </td>
                        </tr>
                        @endforeach

                        {{-- Variant products --}}
                        @foreach($lowStockVariantProducts as $product)
                            @foreach($product->variants as $variant)
                            <tr>
                                <td>{{ $rowNum++ }}</td>
                                <td>
                                    @if($variant->image)
                                    <img src="{{ asset('uploads/variants/Thumbnails/' . $variant->image) }}"
                                        style="width:50px; height:50px; object-fit:cover; border-radius:4px;">
                                    @else
                                    <img src="{{ asset('uploads/products/Thumbnails/' . $product->image) }}"
                                        style="width:50px; height:50px; object-fit:cover; border-radius:4px;">
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('employee.product.show', $product->id) }}">
                                        {{ $product->name }}
                                    </a>
                                </td>
                                <td>{{ $product->SKU }}</td>
                                <td>{{ $product->category->name ?? '—' }}</td>
                                <td>{{ $product->brand->name ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $variant->color ?? '' }}
                                        @if($variant->color && $variant->storage) / @endif
                                        {{ $variant->storage ?? '' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold {{ $variant->quantity == 0 ? 'text-danger' : 'text-warning' }}">
                                        {{ $variant->quantity }}
                                    </span>
                                </td>
                                <td>
                                    @if($variant->quantity == 0)
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @elseif($variant->quantity <= 3)
                                        <span class="badge bg-danger">Critical</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Low Stock</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('employee.product.edit', $product->id) }}">
                                        <div class="item edit">
                                            <i class="icon-edit-3"></i>
                                        </div>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        @endforeach

                        @if($rowNum === 1)
                        <tr>
                            <td colspan="10" class="text-center text-success py-4">
                                ✅ All products are well stocked!
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection