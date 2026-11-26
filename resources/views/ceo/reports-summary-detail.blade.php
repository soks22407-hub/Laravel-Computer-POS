@extends('layouts.ceo')
@section('content')

<style>
    body {
        background: #f0f2f5;
    }

    .report-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .report-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 22px 14px;
        border-bottom: 1.5px solid #f0f0f0;
    }

    .report-card-title {
        font-size: 15px;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0;
    }

    .rt {
        width: 100%;
        border-collapse: collapse;
    }

    .rt thead th {
        padding: 10px 16px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #aaa;
        background: #fafbfc;
    }

    .rt tbody tr {
        border-bottom: 1px solid #f7f7f7;
    }

    .rt tbody tr:hover {
        background: #f9fafe;
    }

    .rt tbody td {
        padding: 11px 16px;
        font-size: 13px;
        color: #333;
    }

    .rt tfoot td {
        padding: 12px 16px;
        font-size: 13px;
        font-weight: 700;
        background: #fafbfc;
        border-top: 2px solid #f0f0f0;
    }

    .badge-completed {
        background: #d1fae5;
        color: #065f46;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-canceled {
        background: #fee2e2;
        color: #991b1b;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-delivered {
        background: #dbeafe;
        color: #1e40af;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-pending {
        background: #fef9c3;
        color: #854d0e;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .text-neg {
        color: #dc2626;
    }

    .text-pos {
        color: #166534;
        font-weight: 600;
    }
</style>

<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding:5px 0;margin-top:0;">
            <h3>Sales Detail Report</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('ceo.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('ceo.reports.summary') }}">
                        <div class="text-tiny">Summary Report</div>
                    </a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">Detail</div>
                </li>
            </ul>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <span class="ms-3 text-muted" style="font-size:13px;">
                Period: <strong>{{ $from->format('d M Y') }}</strong> – <strong>{{ $to->format('d M Y') }}</strong>
            </span>
            <a href="{{ route('ceo.reports.summary') }}" class="btn btn-sm btn-outline-secondary">← Back to Summary</a>
        </div>

        {{-- POS Orders --}}
        <div class="report-card" style="margin-top: 18px;">
            <div class="report-card-header">
                <h6 class="report-card-title">🏪 POS Orders</h6>
                <span style="font-size:12px; color:#aaa;">{{ $posOrders->count() }} orders</span>
            </div>
            <div style="padding:16px 24px 24px;">
                <table class="rt">
                    <thead>
                        <tr>
                            <th style="width: 60px">#</th>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Discount</th>
                            <th class="text-end">Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posOrders as $i => $order)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><strong>INV{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                            <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                            <td>{{ $order->customer_name ?? 'Walk-in' }}</td>
                            <td class="text-end">${{ number_format($order->subtotal ?? $order->total, 2) }}</td>
                            <td class="text-end text-neg">
                                {{ $order->discount > 0 ? '-$'.number_format($order->discount,2) : '—' }}
                            </td>
                            <td class="text-end text-pos">${{ number_format($order->total, 2) }}</td>
                            <td><span class="badge-completed">Completed</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No POS orders in this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4"><strong>Total</strong></td>
                            <td class="text-end">${{ number_format($posOrders->sum(fn($o) => $o->subtotal ?? $o->total), 2) }}</td>
                            <td class="text-end text-neg">
                                @php $totalPosDisc = $posOrders->sum('discount'); @endphp
                                {{ $totalPosDisc > 0 ? '-$'.number_format($totalPosDisc, 2) : '$0.00' }}
                            </td>
                            <td class="text-end text-pos">${{ number_format($posOrders->sum('total'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Online Orders --}}
        <div class="report-card">
            <div class="report-card-header">
                <h6 class="report-card-title">🛒 Online Orders</h6>
                <span style="font-size:12px; color:#aaa;">{{ $onlineOrders->count() }} orders</span>
            </div>
            <div style="padding:16px 24px 24px;">
                <table class="rt">
                    <thead>
                        <tr>
                            <th style="width: 60px">#</th>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th class="text-end">Discount</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Shipping Fee</th>
                            <th class="text-end">Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($onlineOrders as $i => $order)
                        @php
                        $couponDisc = max(0, ($order->subtotal ?? 0) - $order->total);
                        $itemDisc = $itemDiscounts[$order->id] ?? 0;
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><strong>INV{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                            <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                            <td>{{ $order->name ?? '—' }}</td>
                            <td class="text-end text-neg">
                                {{ $itemDisc > 0 ? '-$'.number_format($itemDisc,2) : '—' }}
                            </td>
                            <td class="text-end">${{ number_format($order->subtotal ?? $order->total, 2) }}</td>
                            <td class="text-end">${{ number_format($order->shipping ?? 0, 2) }}</td>
                            <td class="text-end text-pos">${{ number_format($order->total, 2) }}</td>
                            <td>
                                <span class="badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No online orders in this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4"><strong>Total</strong></td>
                            <td class="text-end text-neg">
                                @php $totalItemDisc = $itemDiscounts->sum(); @endphp
                                {{ $totalItemDisc > 0 ? '-$'.number_format($totalItemDisc, 2) : '$0.00' }}
                            </td>
                            <td class="text-end">${{ number_format($onlineOrders->sum(fn($o) => $o->subtotal ?? $o->total), 2) }}</td>
                            <td class="text-end">${{ number_format($onlineOrders->sum('shipping'), 2) }}</td>
                            <td class="text-end text-pos">${{ number_format($onlineOrders->sum('total'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection