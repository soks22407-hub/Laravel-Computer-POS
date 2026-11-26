@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>⚡ Edit Flash Sale</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('admin.flash.sales') }}"><div class="text-tiny">Flash Sales</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Edit</div></li>
            </ul>
        </div>

        @if(session('status'))
        <p class="alert alert-success">{{ session('status') }}</p>
        @endif

        <div class="wg-box">
            <form method="POST" action="{{ route('admin.flash.sales.update', $flashSale->id) }}">
                @csrf @method('PUT')

                <div class="row g-3">
                    {{-- Product --}}
                    <div class="col-md-8">
                        <label class="form-label fw-medium">Product *</label>
                        <select name="product_id" id="product_select" class="form-select form-select-lg" required>
                            <option value="">-- Select Product --</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                {{ $flashSale->product_id == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} — ${{ number_format($product->regular_price, 2) }}
                            </option>
                            @endforeach
                        </select>
                        @error('product_id')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- Variant --}}
                    <div class="col-md-4" id="variant_group" style="display:none;">
                        <label class="form-label fw-medium">Variant</label>
                        <select name="variant_id" id="variant_select" class="form-select form-select-lg">
                            <option value="">-- Whole Product --</option>
                        </select>
                        @error('variant_id')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- Flash Sale Price --}}
                    <div class="col-md-4">
                        <label class="form-label fw-medium">Flash Sale Price ($) *</label>
                        <input type="number" step="0.01" min="0" name="sale_price"
                            class="form-control form-control-lg"
                            value="{{ old('sale_price', $flashSale->sale_price) }}" required>
                        @error('sale_price')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- Start Date --}}
                    <div class="col-md-4">
                        <label class="form-label fw-medium">Start Date & Time *</label>
                        <input type="datetime-local" name="start_at"
                            class="form-control form-control-lg"
                            value="{{ old('start_at', $flashSale->start_at->format('Y-m-d\TH:i')) }}" required>
                        @error('start_at')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- End Date --}}
                    <div class="col-md-4">
                        <label class="form-label fw-medium">End Date & Time *</label>
                        <input type="datetime-local" name="end_at"
                            class="form-control form-control-lg"
                            value="{{ old('end_at', $flashSale->end_at->format('Y-m-d\TH:i')) }}" required>
                        @error('end_at')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- Current Status --}}
                    <div class="col-md-12">
                        <div class="alert alert-info py-2" style="font-size:13px;">
                            📋 Current Status:
                            @if($flashSale->isRunning())
                                <span class="badge bg-success">🔥 Live</span>
                            @elseif(now()->lt($flashSale->start_at))
                                <span class="badge bg-info">Upcoming</span>
                            @else
                                <span class="badge bg-secondary">Expired</span>
                            @endif
                            | Flash Price: <strong>${{ number_format($flashSale->sale_price, 2) }}</strong>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="col-md-12 d-flex gap-3">
                        <button type="submit" class="tf-button style-1 px-5">
                            💾 Update Flash Sale
                        </button>
                        <a href="{{ route('admin.flash.sales') }}" class="tf-button style-2 px-5">
                            <i class="icon-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </form>
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
    const productVariants = @json($variantsMap);
    const savedProductId  = {{ $flashSale->product_id }};
    const savedVariantId  = {{ $flashSale->variant_id ?? 'null' }};

    function populateVariants(productId, selectedVariantId = null) {
        const variants = productVariants[productId] || [];
        const $variantSelect = $('#variant_select');
        const $variantGroup  = $('#variant_group');

        $variantSelect.html('<option value="">-- Whole Product --</option>');

        if (variants.length > 0) {
            variants.forEach(v => {
                const selected = (selectedVariantId && v.id == selectedVariantId) ? 'selected' : '';
                $variantSelect.append(`<option value="${v.id}" ${selected}>${v.label}</option>`);
            });
            $variantGroup.show();
        } else {
            $variantGroup.hide();
        }
    }

    $(function() {
        populateVariants(savedProductId, savedVariantId);

        $('#product_select').on('change', function() {
            populateVariants(this.value);
        });
    });
</script>
@endpush