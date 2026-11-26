@extends('layouts.ceo')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding: 7px 0; margin-top: 0;">
            <h4>⚡ Flash Sales</h4>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('ceo.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">Flash Sales</div>
                </li>
            </ul>
        </div>

        @if(session('status'))
        <p class="alert alert-success">{{ session('status') }}</p>
        @endif

        {{-- Create Form --}}
        <div class="wg-box mb-4">
            <h6 class="mb-3">➕ Create New Flash Sale</h6>
            <form method="POST" action="{{ route('ceo.flash.sales.store') }}">
                @csrf
                <div class="row g-3">

                    {{-- Product --}}
                    <div class="col-md-8">
                        <label class="form-label fw-medium">Product *</label>
                        <select name="product_id" id="product_select" class="form-select form-select-lg" required>
                            <option value="">-- Select Product --</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}">
                                {{ $product->name }} — ${{ number_format($product->regular_price, 2) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Variant --}}
                    <div class="col-md-4" id="variant_group" style="display:none;">
                        <label class="form-label fw-medium">Variant</label>
                        <select name="variant_id" id="variant_select" class="form-select form-select-lg">
                            <option value="">-- Whole Product --</option>
                        </select>
                    </div>

                    {{-- Flash Sale Price --}}
                    <div class="col-md-4">
                        <label class="form-label fw-medium">Flash Sale Price ($) *</label>
                        <input type="number" step="0.01" min="0" name="sale_price"
                            class="form-control form-control-lg"
                            placeholder="e.g. 899.00" required>
                    </div>

                    {{-- Start Date --}}
                    <div class="col-md-4">
                        <label class="form-label fw-medium">Start Date & Time *</label>
                        <input type="datetime-local" name="start_at"
                            class="form-control form-control-lg" required>
                    </div>

                    {{-- End Date --}}
                    <div class="col-md-4">
                        <label class="form-label fw-medium">End Date & Time *</label>
                        <input type="datetime-local" name="end_at"
                            class="form-control form-control-lg" required>
                    </div>

                    {{-- Discount Preview --}}
                    <div class="col-md-12">
                        <div class="alert alert-warning py-2" style="font-size:13px;">
                            💡 The flash sale price will <strong>override</strong> the regular price during the sale period.
                            Make sure the flash price is <strong>lower</strong> than the regular price.
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="col-md-12">
                        <button type="submit" class="tf-button style-1 w-100 py-3" style="font-size:16px;">
                            ⚡ Create Flash Sale
                        </button>
                    </div>

                </div>
            </form>
        </div>

        {{-- List --}}
        <div class="wg-box" style="margin-top: 16px;">
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Product</th>
                            <th>Variant</th>
                            <th>Original Price</th>
                            <th>Flash Price</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($flashSales as $key => $sale)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $sale->product->name ?? '—' }}</td>
                            <td>
                                @if($sale->variant)
                                    <span class="text-muted">{{ trim(($sale->variant->color ?? '') . ' ' . ($sale->variant->storage ?? '')) }}</span>
                                @else
                                    <span class="text-muted">Whole product</span>
                                @endif
                            </td>
                            <td>${{ number_format(($sale->variant->price ?? $sale->product->regular_price) ?? 0, 2) }}</td>
                            <td class="text-danger fw-bold">${{ number_format($sale->sale_price, 2) }}</td>
                            <td>{{ $sale->start_at->format('d M Y H:i') }}</td>
                            <td>{{ $sale->end_at->format('d M Y H:i') }}</td>
                            <td>
                                @if(!$sale->is_active)
                                <span class="badge bg-secondary">Disabled</span>
                                @elseif($sale->isRunning())
                                <span class="badge bg-success">🔥 Live</span>
                                @elseif(now()->lt($sale->start_at))
                                <span class="badge bg-info">Upcoming</span>
                                @else
                                <span class="badge bg-secondary">Expired</span>
                                @endif
                            </td>
                            <td>
                                <div class="list-icon-function">
                                    {{-- Edit button --}}
                                    <a href="{{ route('ceo.flash.sales.edit', $sale->id) }}">
                                        <div class="item edit">
                                            <i class="icon-edit-3"></i>
                                        </div>
                                    </a>
                                    {{-- Toggle active --}}
                                    <form action="{{ route('ceo.flash.sales.toggle', $sale->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn btn-sm {{ $sale->is_active ? 'btn-warning' : 'btn-success' }}">
                                            {{ $sale->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                    {{-- Delete --}}
                                    <form action="{{ route('ceo.flash.sales.delete', $sale->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <div class="item text-danger delete">
                                            <i class="icon-trash-2"></i>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No flash sales yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $flashSales->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@php
    $variantsMap = [];
    foreach ($products as $p) {
        $variantsMap[$p->id] = $p->variants->map(function ($v) {
            return [
                'id'    => $v->id,
                'label' => trim(($v->color ?? '') . ' ' . ($v->storage ?? '')) . ' - $' . number_format($v->price, 2),
            ];
        })->values();
    }
@endphp
<script>
    $(function() {
        $(".delete").on("click", function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            swal({
                title: "Are you sure?",
                text: "Delete this flash sale?",
                type: "warning",
                buttons: ["No", "Yes"],
            }).then(function(result) {
                if (result) form.submit();
            });
        });
    });

    const productVariants = @json($variantsMap);
    
    $(function() {
        const $productSelect = $('#product_select');
        const $variantGroup  = $('#variant_group');
        const $variantSelect = $('#variant_select');

        $productSelect.on('change', function() {
            const variants = productVariants[this.value] || [];
            $variantSelect.html('<option value="">-- Whole Product --</option>');

            if (variants.length > 0) {
                variants.forEach(v => {
                    $variantSelect.append(`<option value="${v.id}">${v.label}</option>`);
                });
                $variantGroup.show();
            } else {
                $variantGroup.hide();
            }
        });
    });
</script>
@endpush