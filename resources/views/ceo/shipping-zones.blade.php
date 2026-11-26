@extends('layouts.ceo')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding: 7px 0; margin-top: 0;">
            <h4>🚚 Shipping Zones</h4>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('ceo.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">Shipping Zones</div>
                </li>
            </ul>
        </div>

        @if(session('status'))
        <p class="alert alert-success">{{ session('status') }}</p>
        @endif

        <div class="wg-box">
            <p class="text-muted mb-3">
                Set delivery fee per province. If order total exceeds <strong>Free Shipping Above</strong>, shipping is free.
                Leave <strong>Free Shipping Above</strong> empty to never give free shipping for that province.
            </p>

            <form method="POST" action="{{ route('ceo.shipping.zones.update') }}">
                @csrf
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th style="width: 400px;">Province</th>
                                <th class="text-end">Delivery Fee ($)</th>
                                <th class="text-end">Free Shipping Above ($)</th>
                                <th class="text-center">Active</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($zones as $key => $zone)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td style="width: 400px;"><strong>{{ $zone->province }}</strong></td>
                                <td class="text-end">
                                    <input type="number" step="0.01" min="0"
                                        name="zones[{{ $zone->id }}][fee]"
                                        value="{{ $zone->fee }}"
                                        class="form-control ms-auto text-end" style="width:100px;">
                                </td>
                                <td class="text-end">
                                    <input type="number" step="0.01" min="0"
                                        name="zones[{{ $zone->id }}][free_shipping_above]"
                                        value="{{ $zone->free_shipping_above }}"
                                        placeholder="No free shipping"
                                        class="form-control ms-auto text-end" style="width:150px;">
                                </td>
                                <td>
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox"
                                            name="zones[{{ $zone->id }}][is_active]"
                                            {{ $zone->is_active ? 'checked' : '' }}>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <button type="submit" class="tf-button style-1">
                        💾 Save All Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection