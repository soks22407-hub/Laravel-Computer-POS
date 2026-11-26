@extends('layouts.admin')
@section('content')

<style>
    body {
        background: #f0f2f5;
    }

    .filter-card {
        background: #fff;
        border-radius: 14px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
        display: flex;
        align-items: flex-end;
        gap: 12px;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .filter-card label {
        font-size: 11px;
        font-weight: 600;
        color: #999;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .filter-card .form-control,
    .filter-card .form-select {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13px;
        padding: 7px 12px;
        color: #333;
        background: #fafafa;
        min-width: 140px;
    }

    .btn-apply,
    .btn-clear {
        min-width: 100px;
        height: 38px;
        padding: 0 20px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 999px;
        /* full pill */
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
    }

    .btn-apply {
        background: #4f46e5;
        color: #fff;
        border: none;
    }

    .btn-clear {
        background: #fff;
        color: #555;
        border: 1px solid #e5e7eb;
    }

    .report-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
        overflow: hidden;
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
        white-space: nowrap;
    }

    .rt tbody tr {
        border-bottom: 1px solid #f7f7f7;
    }

    .rt tbody tr:hover {
        background: #f9fafe;
    }

    .rt tbody td {
        padding: 12px 16px;
        font-size: 13px;
        color: #333;
        vertical-align: middle;
    }

    .table-responsive {
        padding: 8px 24px 20px;
    }

    .badge-type {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-in {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-out {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-adjustment {
        background: #ede9fe;
        color: #5b21b6;
    }

    .qty-change {
        font-weight: 700;
    }

    .qty-pos {
        color: #16a34a;
    }

    .qty-neg {
        color: #dc2626;
    }

    .variant-tag {
        font-size: 11px;
        color: #888;
        display: block;
        margin-top: 2px;
    }

    .empty-state {
        text-align: center;
        padding: 48px 20px;
        color: #bbb;
    }

    .empty-state .empty-icon {
        font-size: 40px;
        margin-bottom: 12px;
    }

    .empty-state p {
        font-size: 13px;
        margin: 0;
    }

    .pagination-wrap {
        padding: 16px 24px;
    }
</style>

<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding:5px 0;margin-top:0;">
            <h5>Stock History</h5>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">Stock History</div>
                </li>
            </ul>
        </div>

        {{-- Filter Card --}}
        <form method="GET" action="{{ route('admin.stock-history') }}" class="filter-card">
            <div class="filter-group">
                <label>Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Stock In</option>
                    <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Stock Out</option>
                    <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                </select>
            </div>
            <div class="filter-group">
                <label>From Date</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="filter-group">
                <label>To Date</label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>
            <button type="submit" class="btn-apply">Apply</button>
            <a href="{{ route('admin.stock-history') }}" class="btn-clear">Clear</a>
        </form>

        {{-- History Table --}}
        <div class="report-card">
            <div class="report-card-header">
                <h6 class="report-card-title">📋 Stock Transaction History</h6>
                <span style="font-size:12px; color:#aaa;">{{ $movements->total() }} records</span>
            </div>

            <div class="table-responsive">
                <table class="rt">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Product</th>
                            <th class="text-end">Before</th>
                            <th class="text-end">Change</th>
                            <th class="text-end">After</th>
                            <th class="text-end">Unit Cost</th>
                            <th>Reason</th>
                            <th>By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $m)
                        <tr>
                            <td>{{ $m->transaction_date->format('d M Y') }}</td>
                            <td>
                                @if($m->type === 'in')
                                <span class="badge-type badge-in">↓ Stock In</span>
                                @elseif($m->type === 'out')
                                <span class="badge-type badge-out">↑ Stock Out</span>
                                @else
                                <span class="badge-type badge-adjustment">↻ Adjustment</span>
                                @endif
                            </td>
                            <td>
                                {{ $m->product->name ?? '— Deleted Product —' }}
                                @if($m->variant)
                                <span class="variant-tag">
                                    {{ trim(($m->variant->color ?? '') . ' / ' . ($m->variant->storage ?? ''), ' /') }}
                                </span>
                                @endif
                            </td>
                            <td class="text-end">{{ $m->quantity_before }}</td>
                            <td class="text-end qty-change {{ $m->quantity_change > 0 ? 'qty-pos' : ($m->quantity_change < 0 ? 'qty-neg' : '') }}">
                                {{ $m->quantity_change > 0 ? '+' : '' }}{{ $m->quantity_change }}
                            </td>
                            <td class="text-end"><strong>{{ $m->quantity_after }}</strong></td>
                            <td class="text-end">${{ number_format($m->unit_cost ?? 0, 2) }}</td>
                            <td>{{ $m->reason ?? '—' }}</td>
                            <td>{{ $m->user->name ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <div class="empty-icon">📭</div>
                                    <p>No stock transactions found for the selected filters.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($movements->hasPages())
            <div class="pagination-wrap">
                {{ $movements->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

    </div>
</div>

@endsection