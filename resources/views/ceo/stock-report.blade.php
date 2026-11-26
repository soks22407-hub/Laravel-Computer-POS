@extends('layouts.ceo')
@section('content')

<style>
    body { background: #f0f2f5; }

    .filter-card {
        background: #fff;
        border-radius: 14px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
        display: flex;
        align-items: flex-end;
        gap: 12px;
        flex-wrap: wrap;
    }

    .filter-group { display: flex; flex-direction: column; gap: 4px; }

    .filter-card label {
        font-size: 11px;
        font-weight: 600;
        color: #999;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .filter-card .form-control {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13px;
        padding: 7px 12px;
        color: #333;
        background: #fafafa;
        min-width: 140px;
    }

    .btn-apply, .btn-clear {
        min-width: 100px;
        height: 38px;
        padding: 0 20px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
    }

    .btn-apply { background: #4f46e5; color: #fff; border: none; }
    .btn-clear { background: #fff; color: #555; border: 1px solid #e5e7eb; text-decoration: none; }

    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    @media (max-width:1200px) { .kpi-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width:768px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }

    .kpi-card {
        background: #fff;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .kpi-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }

    .kpi-icon.green { background: #d1fae5; }
    .kpi-icon.red { background: #fee2e2; }
    .kpi-icon.purple { background: #ede9fe; }
    .kpi-icon.blue { background: #dbeafe; }

    .kpi-label {
        font-size: 11px; color: #999; font-weight: 600;
        text-transform: uppercase; letter-spacing: .05em; margin-bottom: 2px;
    }

    .kpi-value { font-size: 20px; font-weight: 700; color: #1a1a2e; line-height: 1.1; }
    .kpi-sub { font-size: 11px; color: #bbb; margin-top: 2px; }

    .report-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
        overflow: hidden;
    }

    .report-card-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 18px 22px 14px; border-bottom: 1.5px solid #f0f0f0;
    }

    .report-card-title { font-size: 15px; font-weight: 700; color: #1a1a2e; margin: 0; }

    .table-responsive { padding: 8px 24px 20px; }

    .rt { width: 100%; border-collapse: separate; border-spacing: 0; border: 1px solid #f0f0f0; border-radius: 10px; overflow: hidden; }

    .rt thead th {
        padding: 10px 16px; font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .05em; color: #aaa;
        background: #fafbfc; white-space: nowrap;
    }

    .rt tbody tr { border-bottom: 1px solid #f7f7f7; }
    .rt tbody tr:hover { background: #f9fafe; }
    .rt tbody td { padding: 11px 16px; font-size: 13px; color: #333; vertical-align: middle; }

    .qty-in { color: #16a34a; font-weight: 600; }
    .qty-out { color: #dc2626; font-weight: 600; }
    .qty-net-pos { color: #16a34a; font-weight: 700; }
    .qty-net-neg { color: #dc2626; font-weight: 700; }
    .qty-net-zero { color: #999; }
    .text-muted { color: #bbb; }

    .empty-state { text-align: center; padding: 48px 20px; color: #bbb; }
    .empty-state .empty-icon { font-size: 40px; margin-bottom: 12px; }
    .empty-state p { font-size: 13px; margin: 0; }
</style>

<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding:5px 0;margin-top:0;">
            <h3 style="font-size:18px; font-weight:700; margin:0;">Stock Report</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('ceo.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Stock Report</div></li>
            </ul>
        </div>

        {{-- Filter Card --}}
        <form method="GET" action="{{ route('ceo.stock-report') }}" class="filter-card">
            <div class="filter-group">
                <label>From Date</label>
                <input type="date" name="from" class="form-control" value="{{ \Carbon\Carbon::parse($from)->format('Y-m-d') }}">
            </div>
            <div class="filter-group">
                <label>To Date</label>
                <input type="date" name="to" class="form-control" value="{{ \Carbon\Carbon::parse($to)->format('Y-m-d') }}">
            </div>
            <button type="submit" class="btn-apply">Apply</button>
            <a href="{{ route('ceo.stock-report') }}" class="btn-clear">Clear</a>
        </form>

        {{-- KPI Grid --}}
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon green">↓</div>
                <div class="kpi-body">
                    <div class="kpi-label">Stock In</div>
                    <div class="kpi-value">{{ number_format($summary['total_in_qty']) }} units</div>
                    <div class="kpi-sub">${{ number_format($summary['total_in_value'], 2) }} value</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon red">↑</div>
                <div class="kpi-body">
                    <div class="kpi-label">Stock Out</div>
                    <div class="kpi-value">{{ number_format($summary['total_out_qty']) }} units</div>
                    <div class="kpi-sub">${{ number_format($summary['total_out_value'], 2) }} value</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon purple">↻</div>
                <div class="kpi-body">
                    <div class="kpi-label">Adjustments</div>
                    <div class="kpi-value">{{ number_format($summary['total_adjustments']) }}</div>
                    <div class="kpi-sub">Corrections made</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon blue">📦</div>
                <div class="kpi-body">
                    <div class="kpi-label">Current Inventory Value</div>
                    <div class="kpi-value">${{ number_format($summary['current_inventory_value'], 2) }}</div>
                    <div class="kpi-sub">All products, at cost</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon {{ $summary['net_change'] >= 0 ? 'green' : 'red' }}">
                    {{ $summary['net_change'] >= 0 ? '▲' : '▼' }}
                </div>
                <div class="kpi-body">
                    <div class="kpi-label">Net Stock Change</div>
                    <div class="kpi-value" style="color:{{ $summary['net_change'] > 0 ? '#16a34a' : ($summary['net_change'] < 0 ? '#dc2626' : '#1a1a2e') }};">
                        {{ $summary['net_change'] > 0 ? '+' : '' }}{{ number_format($summary['net_change']) }} units
                    </div>
                    <div class="kpi-sub">In − Out + Adjustments</div>
                </div>
            </div>
        </div>

        {{-- Daily Breakdown Table --}}
        <div class="report-card">
            <div class="report-card-header">
                <h6 class="report-card-title">📊 Stock Movement — Daily Breakdown</h6>
                <span style="font-size:12px; color:#aaa;">
                    {{ \Carbon\Carbon::parse($from)->format('d M Y') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
                </span>
            </div>

            <div class="table-responsive">
                <table class="rt">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th class="text-end">Stock In (Qty)</th>
                            <th class="text-end">Stock In (Value)</th>
                            <th class="text-end">Stock Out (Qty)</th>
                            <th class="text-end">Stock Out (Value)</th>
                            <th class="text-end">Adjustments</th>
                            <th class="text-end">Net Change</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                        <tr>
                            <td>{{ $row['date'] }}</td>
                            <td class="text-end {{ $row['in_qty'] > 0 ? 'qty-in' : 'text-muted' }}">
                                {{ $row['in_qty'] > 0 ? '+' . number_format($row['in_qty']) : '0' }}
                            </td>
                            <td class="text-end {{ $row['in_value'] > 0 ? '' : 'text-muted' }}">
                                ${{ number_format($row['in_value'], 2) }}
                            </td>
                            <td class="text-end {{ $row['out_qty'] > 0 ? 'qty-out' : 'text-muted' }}">
                                {{ $row['out_qty'] > 0 ? '-' . number_format($row['out_qty']) : '0' }}
                            </td>
                            <td class="text-end {{ $row['out_value'] > 0 ? '' : 'text-muted' }}">
                                ${{ number_format($row['out_value'], 2) }}
                            </td>
                            <td class="text-end {{ $row['adjust_count'] > 0 ? '' : 'text-muted' }}">
                                {{ $row['adjust_count'] > 0 ? $row['adjust_count'] . ' change' . ($row['adjust_count'] > 1 ? 's' : '') : '—' }}
                            </td>
                            <td class="text-end {{ $row['net_change'] > 0 ? 'qty-net-pos' : ($row['net_change'] < 0 ? 'qty-net-neg' : 'qty-net-zero') }}">
                                {{ $row['net_change'] > 0 ? '+' : '' }}{{ number_format($row['net_change']) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-icon">📭</div>
                                    <p>No stock activity found for the selected date range.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@endsection