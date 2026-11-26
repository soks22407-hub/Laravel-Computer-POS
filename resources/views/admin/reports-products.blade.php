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

    .filter-card .filter-group {
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

    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, .1);
        outline: none;
    }

    .btn-apply {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 20px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 8px;
        border: 1px solid transparent;
        line-height: 1.5;
        box-sizing: border-box;
        cursor: pointer;
        background: #4f46e5;
        color: #fff;
    }

    .btn-apply:hover {
        background: #3451d1;
        color: #fff;
    }

    .btn-clear {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 20px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        line-height: 1.5;
        box-sizing: border-box;
        background: #fff;
        color: #555;
        cursor: pointer;
        transition: all .2s;
    }

    .btn-clear:hover {
        border-color: #aaa;
        color: #333;
    }

    .export-group {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-left: auto;
    }

    .btn-export {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 8px;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: 1.5px solid transparent;
        transition: all .2s;
        text-decoration: none;
        background: #fff;
    }

    .btn-excel {
        color: #16a34a;
        border-color: #16a34a;
    }

    .btn-excel:hover {
        background: #16a34a;
        color: #fff;
    }

    .btn-pdf {
        color: #dc2626;
        border-color: #dc2626;
    }

    .btn-pdf:hover {
        background: #dc2626;
        color: #fff;
    }

    .btn-print {
        color: #4361ee;
        border-color: #4361ee;
    }

    .btn-print:hover {
        background: #4361ee;
        color: #fff;
    }

    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    @media (max-width:1200px) {
        .kpi-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width:768px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .kpi-card {
        background: #fff;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .kpi-icon.purple {
        background: #ede9fe;
    }

    .kpi-icon.green {
        background: #d1fae5;
    }

    .kpi-icon.yellow {
        background: #fef3c7;
    }

    .kpi-icon.red {
        background: #fee2e2;
    }

    .kpi-icon.orange {
        background: #ffedd5;
    }

    .kpi-body {
        min-width: 0;
    }

    .kpi-label {
        font-size: 11px;
        color: #999;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: 2px;
    }

    .kpi-value {
        font-size: 22px;
        font-weight: 700;
        color: #1a1a2e;
        line-height: 1.1;
    }

    .kpi-sub {
        font-size: 11px;
        color: #bbb;
        margin-top: 2px;
    }

    .report-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
        overflow: hidden;
    }

    .report-tabs {
        display: flex;
        gap: 4px;
        padding: 16px 20px 0;
        border-bottom: 1.5px solid #f0f0f0;
        overflow-x: auto;
    }

    .report-tabs .tab-btn {
        padding: 10px 18px;
        border: none;
        background: none;
        font-size: 13px;
        font-weight: 600;
        color: #888;
        cursor: pointer;
        border-bottom: 2.5px solid transparent;
        margin-bottom: -1.5px;
        white-space: nowrap;
        transition: all .15s;
        border-radius: 6px 6px 0 0;
    }

    .report-tabs .tab-btn:hover {
        color: #4361ee;
        background: #f5f7ff;
    }

    .report-tabs .tab-btn.active {
        color: #4361ee;
        border-bottom-color: #4361ee;
        background: #f5f7ff;
    }

    .tab-content-area {
        padding: 24px 20px;
    }

    .tab-pane-custom {
        display: none;
    }

    .tab-pane-custom.active {
        display: block;
    }

    .tab-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .tab-header h6 {
        font-size: 15px;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0;
    }

    .tab-header small {
        font-size: 12px;
        color: #aaa;
    }

    .rt {
        width: 100%;
        border-collapse: collapse;
    }

    .rt thead tr {
        border-bottom: 1.5px solid #f0f0f0;
    }

    .rt thead th {
        padding: 10px 14px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #aaa;
        white-space: nowrap;
        background: #fafbfc;
    }

    .rt tbody tr {
        border-bottom: 1px solid #f7f7f7;
        transition: background .12s;
    }

    .rt tbody tr:hover {
        background: #f9fafe;
    }

    .rt tbody td {
        padding: 12px 14px;
        font-size: 13px;
        color: #333;
        vertical-align: middle;
    }

    .rt tfoot tr {
        border-top: 2px solid #f0f0f0;
        background: #fafbfc;
    }

    .rt tfoot td {
        padding: 12px 14px;
        font-size: 13px;
        font-weight: 700;
        color: #1a1a2e;
    }

    #tbl-revenue thead colgroup {
        font-size: 13px !important;
        text-align: center !important;
        white-space: nowrap !important;
        overflow: visible !important;
    }

    /* Split-bar column: visible on screen, hidden in print */
    .col-split {
        min-width: 150px;
        width: 150px;
    }

    .prog-wrap {
        height: 6px;
        background: #f0f0f0;
        border-radius: 4px;
        overflow: hidden;
        display: flex;
        width: 100%;
    }

    .print-hide {
        /* hidden only in print, visible on screen */
    }

    .prod-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .prod-thumb {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        object-fit: cover;
        border: 1px solid #f0f0f0;
        flex-shrink: 0;
    }

    .prod-name {
        font-size: 13px;
        font-weight: 600;
        color: #1a1a2e;
    }

    .prod-sku {
        font-size: 11px;
        color: #bbb;
        margin-top: 1px;
    }

    .rank {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
    }

    .rank-1 {
        background: #fef3c7;
        color: #92400e;
    }

    .rank-2 {
        background: #f1f5f9;
        color: #475569;
    }

    .rank-3 {
        background: #ffedd5;
        color: #9a3412;
    }

    .rank-n {
        background: #f4f4f4;
        color: #888;
    }

    .ch-pill {
        display: inline-block;
        padding: 2px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .ch-pos {
        background: #ede9fe;
        color: #5b21b6;
    }

    .ch-online {
        background: #fff7ed;
        color: #c2410c;
    }

    .ch-total {
        background: #f0fdf4;
        color: #166534;
    }

    .st-badge {
        display: inline-block;
        padding: 3px 11px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .st-ok {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .st-low {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
    }

    .st-out {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .prog-pos {
        background: #4361ee;
        flex-shrink: 0;
    }

    .prog-online {
        background: #f97316;
        flex-shrink: 0;
    }

    .v-pill {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        margin: 2px;
    }

    .v-ok {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .v-low {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
    }

    .v-out {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .oos-tag {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        margin: 2px;
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
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

    .legend {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        color: #666;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .dot-pos {
        background: #4361ee;
    }

    .dot-online {
        background: #f97316;
    }

    .print-header {
        display: none;
        text-align: center;
        margin-bottom: 20px;
    }

    .print-header h2 {
        font-size: 20px;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0 0 4px;
    }

    .print-header p {
        font-size: 12px;
        color: #666;
        margin: 0;
    }

    .charts-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }

    .chart-card {
        background: #fafbfc;
        border: 1px solid #f0f0f0;
        border-radius: 12px;
        padding: 16px 20px;
    }

    .chart-card-title {
        font-size: 12px;
        font-weight: 700;
        color: #888;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: 8px;
    }

    @media print {
        .charts-row {
            display: none !important;
        }
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        .no-print {
            display: none !important;
        }

        .print-hide {
            display: none !important;
        }

        .print-header {
            display: block !important;
        }

        body {
            background: #fff !important;
        }

        /* Show all tab sections */
        .tab-pane-custom {
            display: block !important;
        }

        .tab-pane-custom+.tab-pane-custom {
            border-top: 2px solid #e5e7eb;
            padding-top: 16px;
            margin-top: 20px;
            page-break-before: always;
        }

        .report-card {
            box-shadow: none;
            border: 1px solid #eee;
        }

        .report-tabs {
            display: none !important;
        }

        .kpi-grid {
            grid-template-columns: repeat(5, 1fr) !important;
        }

        .col-split {
            display: none !important;
        }

        /* Hide product images so they don't overlap text */
        .prod-thumb {
            display: none !important;
        }

        .prod-cell {
            display: block !important;
            gap: 0 !important;
        }

        .prod-name {
            font-size: 10px !important;
            font-weight: 700 !important;
        }

        .prod-sku {
            font-size: 9px !important;
        }

        /* Compact table for paper */
        .rt thead th {
            font-size: 8px !important;
            padding: 5px 6px !important;
        }

        .rt tbody td {
            font-size: 10px !important;
            padding: 6px 6px !important;
        }

        .rt tfoot td {
            font-size: 10px !important;
            padding: 6px 6px !important;
        }

        /* Keep badges readable */
        .st-badge,
        .v-pill,
        .oos-tag,
        .ch-pill,
        .rank {
            font-size: 8px !important;
            padding: 2px 5px !important;
        }

        /* Suppress progress bars (already hidden via col-split) */
        .prog-wrap {
            display: none !important;
        }

    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding: 5px 0; margin-top: 0;">
            <h3>Product Reports</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">Product Reports</div>
                </li>
            </ul>
        </div>

        <div class="print-header">
            <h2>📦 Product Reports — All Sections</h2>
            <p>Period: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }} &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, H:i') }}</p>
        </div>

        <form method="GET" action="{{ route('admin.reports.products') }}" class="filter-card no-print">
            <div class="filter-group">
                <label>From Date</label>
                <input type="date" name="from" class="form-control" value="{{ \Carbon\Carbon::parse($from)->format('Y-m-d') }}">
            </div>
            <div class="filter-group">
                <label>To Date</label>
                <input type="date" name="to" class="form-control" value="{{ \Carbon\Carbon::parse($to)->format('Y-m-d') }}">
            </div>
            <button type="submit" class="btn-apply">Apply</button>
            <a href="{{ route('admin.reports.products') }}" class="btn-clear text-decoration-none">Clear</a>
            <div class="export-group">
                <button type="button" class="btn-export btn-excel" onclick="exportExcel()">📊 Export Excel</button>
                <button type="button" class="btn-export btn-pdf" onclick="exportPDF()">📄 Export PDF</button>
            </div>
        </form>

        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon purple">📦</div>
                <div class="kpi-body">
                    <div class="kpi-label">Total Products</div>
                    <div class="kpi-value">{{ $summary['total_products'] }}</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon green">💵</div>
                <div class="kpi-body">
                    <div class="kpi-label">Total Revenue</div>
                    <div class="kpi-value">${{ number_format($summary['total_revenue'], 2) }}</div>
                    <div class="kpi-sub">Online + POS Combined</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon yellow">🛒</div>
                <div class="kpi-body">
                    <div class="kpi-label">Units Sold</div>
                    <div class="kpi-value">{{ number_format($summary['total_sold']) }}</div>
                    <div class="kpi-sub">Online + POS</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon red">⛔</div>
                <div class="kpi-body">
                    <div class="kpi-label">Out of Stock</div>
                    <div class="kpi-value">{{ $summary['out_of_stock'] }}</div>
                    <div class="kpi-sub">products</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon orange">⚠️</div>
                <div class="kpi-body">
                    <div class="kpi-label">Low Stock</div>
                    <div class="kpi-value">{{ $summary['low_stock'] }}</div>
                    <div class="kpi-sub">products (≤5 units)</div>
                </div>
            </div>
        </div>

        <div class="report-card">
            <div class="report-tabs no-print">
                <button class="tab-btn active" onclick="switchTab('chart',this)">📊 Chart Product Report(POS + Online)</button>
                <button class="tab-btn" onclick="switchTab('top',this)">🏆 Top Selling</button>
                <button class="tab-btn" onclick="switchTab('low',this)">📉 Low Selling</button>
                <button class="tab-btn" onclick="switchTab('stock',this)">📦 Stock Report</button>
                <button class="tab-btn" onclick="switchTab('out',this)">⛔ Out of Stock</button>
                <button class="tab-btn" onclick="switchTab('revenue',this)">💰 Revenue</button>
            </div>

            <div class="tab-content-area">
                {{-- TAB 0 — Chart product --}}
                <div id="pane-chart" class="tab-pane-custom active">
                    <div class="tab-header">
                        <h6>Chart Product Report (POS + Online)</h6>
                        <div class="d-flex align-items-center gap-3">
                            <div class="legend">
                                <div class="legend-item">
                                    <div class="legend-dot dot-pos"></div> POS
                                </div>
                                <div class="legend-item">
                                    <div class="legend-dot dot-online"></div> Online
                                </div>
                            </div>
                            <small>{{ \Carbon\Carbon::parse($from)->format('d M') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</small>
                        </div>
                    </div>

                    {{-- CHARTS ROW --}}
                    <div class="charts-row" id="charts-row">
                        <div class="chart-card">
                            <div class="chart-card-title">Revenue Share</div>
                            <div id="chart-revenue-share"></div>
                        </div>
                        <div class="chart-card">
                            <div class="chart-card-title">POS vs Online Sales</div>
                            <div id="chart-pos-online"></div>
                        </div>
                    </div>
                    {{-- Table Customer buy Product --}}
                    <div class="table-responsive">
                        <table class="rt" id="tbl-invoices"
                            data-export-headers="Invoice No,Date,Customer Name,Sales Channel,Total,Delivery Address,Status">
                            <thead>
                                <tr>
                                    <th style="width: 120px;">Invoice No</th>
                                    <th>Date</th>
                                    <th style="width: 220px;">Customer Name</th>
                                    <th class="text-center">Sales Channel</th>
                                    <th>Delivery Address</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $inv)
                                @php $invoiceNo = 'INV' . str_pad($inv->id, 6, '0', STR_PAD_LEFT); @endphp
                                <tr
                                    data-e0="{{ $invoiceNo }}"
                                    data-e1="{{ $inv->date }}"
                                    data-e2="{{ $inv->customer_name }}"
                                    data-e3="{{ $inv->channel }}"
                                    data-e4="{{ number_format($inv->total,2) }}"
                                    data-e5="{{ $inv->address }}"
                                    data-e6="{{ $inv->status }}">
                                    <td><strong>{{ $invoiceNo }}</strong></td>
                                    <td style="color:#666;">{{ \Carbon\Carbon::parse($inv->date)->format('d M Y, h:i A') }}</td>
                                    <td>{{ $inv->customer_name }}</td>
                                    <td class="text-center">
                                        <span class="ch-pill {{ $inv->channel==='POS' ? 'ch-pos' : 'ch-online' }}">
                                            {{ $inv->channel }}
                                        </span>
                                    </td>
                                    <td style="color:#666; font-size:12px;">{{ $inv->address ?: '—' }}</td>
                                    <td class="text-end"><strong>${{ number_format($inv->total,2) }}</strong></td>
                                    <td class="text-center">
                                        <span class="st-badge {{ $inv->status==='Paid' ? 'st-ok' : 'st-out' }}">
                                            {{ $inv->status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr class="empty-row">
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <div class="empty-icon">🧾</div>
                                            <p>No invoices found for this period.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- TAB 1 — TOP SELLING --}}
                <div id="pane-top" class="tab-pane-custom">
                    <div class="tab-header">
                        <h6>🏆 Top 10 Best-Selling Products</h6>
                        <div class="d-flex align-items-center gap-3">
                            <div class="legend">
                                <div class="legend-item">
                                    <div class="legend-dot dot-pos"></div> POS
                                </div>
                                <div class="legend-item">
                                    <div class="legend-dot dot-online"></div> Online
                                </div>
                            </div>
                            <small>{{ \Carbon\Carbon::parse($from)->format('d M') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</small>
                        </div>
                    </div>
                    @php $maxSold = $topSelling->max('total_sold') ?: 1; @endphp

                    <div class="table-responsive">
                        {{-- data-export-headers = columns that go to Excel (comma-separated, matching td[data-export] order) --}}
                        <table class="rt" id="tbl-top"
                            data-export-headers="#,Product,SKU,Category,POS Sold,Online Sold,Total Sold">
                            <thead>
                                <tr>
                                    <th style="width:60px;">#</th>
                                    <th style="width: 350px;">Product</th>
                                    <th>Category</th>
                                    <th class="text-center">POS Sold</th>
                                    <th class="text-center">Online Sold</th>
                                    <th class="text-center">Total Sold</th>
                                    <th class="col-split print-hide">Sales Split</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topSelling as $i => $p)
                                <tr
                                    data-e0="{{ $i+1 }}"
                                    data-e1="{{ $p->name }}"
                                    data-e2="{{ $p->sku }}"
                                    data-e3="{{ $p->category }}"
                                    data-e4="{{ $p->pos_sold }}"
                                    data-e5="{{ $p->online_sold }}"
                                    data-e6="{{ $p->total_sold }}">
                                    <td><span class="rank {{ $i===0?'rank-1':($i===1?'rank-2':($i===2?'rank-3':'rank-n')) }}">{{ $i+1 }}</span></td>
                                    <td>
                                        <div class="prod-cell">
                                            <img src="{{ asset('uploads/products/'.$p->image) }}" class="prod-thumb" alt="">
                                            <div>
                                                <div class="prod-name">{{ $p->name }}</div>
                                                <div class="prod-sku">{{ $p->sku }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="color:#666;">{{ $p->category }}</td>
                                    <td class="text-center"><span style="color:#5b21b6;font-weight:600;">{{ $p->pos_sold }}</span></td>
                                    <td class="text-center"><span style="color:#c2410c;font-weight:600;">{{ $p->online_sold }}</span></td>
                                    <td class="text-center"><strong style="font-size:14px;">{{ $p->total_sold }}</strong></td>
                                    <td class="col-split print-hide">
                                        @php $pct = $p->total_sold / $maxSold * 100; @endphp
                                        <div class="prog-wrap">
                                            <div class="prog-pos" style="width:{{ $p->total_sold?($p->pos_sold/$p->total_sold*$pct):0 }}%"></div>
                                            <div class="prog-online" style="width:{{ $p->total_sold?($p->online_sold/$p->total_sold*$pct):0 }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr class="empty-row">
                                    <td colspan="8">
                                        <div class="empty-state">
                                            <div class="empty-icon">📭</div>
                                            <p>No sales data for this period.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 2 — LOW SELLING --}}
                <div id="pane-low" class="tab-pane-custom">
                    <div class="tab-header">
                        <h6>📉 Bottom 10 Low-Selling Products</h6>
                        <small>Products with at least 1 sale · sorted lowest first</small>
                    </div>
                    <div class="table-responsive">
                        <table class="rt" id="tbl-low"
                            data-export-headers="#,Product,SKU,Category,POS Sold,Online Sold,Total Sold,Stock Left">
                            <thead>
                                <tr>
                                    <th style="width:60px;">#</th>
                                    <th style="width: 350px;">Product</th>
                                    <th>Category</th>
                                    <th class="text-center">POS Sold</th>
                                    <th class="text-center">Online Sold</th>
                                    <th class="text-center">Total Sold</th>
                                    <th>Stock Left</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowSelling as $i => $p)
                                <tr
                                    data-e0="{{ $i+1 }}"
                                    data-e1="{{ $p->name }}"
                                    data-e2="{{ $p->sku }}"
                                    data-e3="{{ $p->category }}"
                                    data-e4="{{ $p->pos_sold }}"
                                    data-e5="{{ $p->online_sold }}"
                                    data-e6="{{ $p->total_sold }}"
                                    data-e7="{{ $p->total_stock }} units">
                                    <td><span class="rank rank-n">{{ $i+1 }}</span></td>
                                    <td>
                                        <div class="prod-cell">
                                            <img src="{{ asset('uploads/products/'.$p->image) }}" class="prod-thumb" alt="">
                                            <div>
                                                <div class="prod-name">{{ $p->name }}</div>
                                                <div class="prod-sku">{{ $p->sku }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="color:#666;">{{ $p->category }}</td>
                                    <td class="text-center"><span style="color:#5b21b6;font-weight:600;">{{ $p->pos_sold }}</span></td>
                                    <td class="text-center"><span style="color:#c2410c;font-weight:600;">{{ $p->online_sold }}</span></td>
                                    <td class="text-center"><strong>{{ $p->total_sold }}</strong></td>
                                    <td><span class="st-badge {{ $p->stock_status==='out'?'st-out':($p->stock_status==='low'?'st-low':'st-ok') }}">{{ $p->total_stock }} units</span></td>
                                </tr>
                                @empty
                                <tr class="empty-row">
                                    <td colspan="8">
                                        <div class="empty-state">
                                            <div class="empty-icon">📭</div>
                                            <p>No data available.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 3 — STOCK REPORT --}}
                <div id="pane-stock" class="tab-pane-custom">
                    <div class="tab-header">
                        <h6>📦 Stock Report — All Products</h6>
                        <div class="d-flex gap-2">
                            <span class="st-badge st-ok">In Stock</span>
                            <span class="st-badge st-low">Low Stock ≤5</span>
                            <span class="st-badge st-out">Out of Stock</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="rt" id="tbl-stock"
                            data-export-headers="Product,SKU,Category,Variants,Total Stock,Status,Variant Breakdown">
                            <thead>
                                <tr>
                                    <th style="width: 350px;">Product</th>
                                    <th>Category</th>
                                    <th class="text-center">Variants</th>
                                    <th class="text-center">Total Stock</th>
                                    <th>Status</th>
                                    <th>Variant Breakdown</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockReport as $p)
                                @php
                                $varBreakdown = $p->variants->map(fn($v) => "{$v->color} {$v->storage}: {$v->quantity}")->implode(', ');
                                $stockLabel = $p->stock_status==='out'?'Out of Stock':($p->stock_status==='low'?'Low Stock':'In Stock');
                                @endphp
                                <tr
                                    data-e0="{{ $p->name }}"
                                    data-e1="{{ $p->sku }}"
                                    data-e2="{{ $p->category }}"
                                    data-e3="{{ $p->variants_count }}"
                                    data-e4="{{ $p->total_stock }}"
                                    data-e5="{{ $stockLabel }}"
                                    data-e6="{{ $varBreakdown }}">
                                    <td>
                                        <div class="prod-cell">
                                            <img src="{{ asset('uploads/products/'.$p->image) }}" class="prod-thumb" alt="">
                                            <div>
                                                <div class="prod-name">{{ $p->name }}</div>
                                                <div class="prod-sku">{{ $p->sku }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="color:#666;">{{ $p->category }}</td>
                                    <td class="text-center"><span style="font-weight:600;color:#444;">{{ $p->variants_count }}</span></td>
                                    <td class="text-center"><strong style="font-size:15px;color:#1a1a2e;">{{ $p->total_stock }}</strong></td>
                                    <td><span class="st-badge {{ $p->stock_status==='out'?'st-out':($p->stock_status==='low'?'st-low':'st-ok') }}">{{ $stockLabel }}</span></td>
                                    <td>
                                        @foreach($p->variants as $v)
                                        <span class="v-pill {{ $v->quantity==0?'v-out':($v->quantity<=5?'v-low':'v-ok') }}">
                                            {{ $v->color }} {{ $v->storage }}: {{ $v->quantity }}
                                        </span>
                                        @endforeach
                                    </td>
                                </tr>
                                @empty
                                <tr class="empty-row">
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <div class="empty-icon">📦</div>
                                            <p>No products found.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 4 — OUT OF STOCK --}}
                <div id="pane-out" class="tab-pane-custom">
                    <div class="tab-header">
                        <h6>⛔ Out of Stock Products</h6>
                        <span class="st-badge st-out">{{ $outOfStock->count() }} products</span>
                    </div>
                    @if($outOfStock->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon">✅</div>
                        <p>All products are currently in stock — great job!</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="rt" id="tbl-out"
                            data-export-headers="Product,SKU,Category,Variants Affected,Out of Stock Variants,Total Ever Sold">
                            <thead>
                                <tr>
                                    <th style="width: 350px;">Product</th>
                                    <th>Category</th>
                                    <th class="text-center">Variants Affected</th>
                                    <th>Out of Stock Variants</th>
                                    <th class="text-center">Total Ever Sold</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($outOfStock as $p)
                                @php
                                $outVars = $p->variants->where('quantity', 0);
                                $oosLabel = $outVars->map(fn($v)=>"{$v->color} {$v->storage}")->implode(', ');
                                @endphp
                                <tr
                                    data-e0="{{ $p->name }}"
                                    data-e1="{{ $p->sku }}"
                                    data-e2="{{ $p->category }}"
                                    data-e3="{{ $outVars->count() }} / {{ $p->variants_count }}"
                                    data-e4="{{ $oosLabel }}"
                                    data-e5="{{ $p->total_sold }}">
                                    <td>
                                        <div class="prod-cell">
                                            <img src="{{ asset('uploads/products/'.$p->image) }}" class="prod-thumb" alt="">
                                            <div>
                                                <div class="prod-name">{{ $p->name }}</div>
                                                <div class="prod-sku">{{ $p->sku }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="color:#666;">{{ $p->category }}</td>
                                    <td class="text-center"><span class="st-badge st-out">{{ $outVars->count() }} / {{ $p->variants_count }}</span></td>
                                    <td>@foreach($outVars as $v)<span class="oos-tag">{{ $v->color }} {{ $v->storage }}</span>@endforeach</td>
                                    <td class="text-center"><strong>{{ $p->total_sold }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

                {{-- TAB 5 — REVENUE --}}
                <div id="pane-revenue" class="tab-pane-custom">
                    <div class="tab-header">
                        <h6>💰 Product Revenue Report</h6>
                        <div class="legend">
                            <div class="legend-item">
                                <div class="legend-dot dot-pos"></div> POS
                            </div>
                            <div class="legend-item">
                                <div class="legend-dot dot-online"></div> Online
                            </div>
                        </div>
                    </div>
                    @php $totalRev = $revenueReport->sum('total_revenue') ?: 1; @endphp
                    <div class="table-responsive">
                        <table class="rt" id="tbl-revenue"
                            data-export-headers="Product,SKU,Category,POS Revenue,Online Revenue,Total Revenue,Units Sold,Share %">
                            <colgroup>
                                <col style="width:300px;"> {{-- Product --}}
                                <col> {{-- Category --}}
                                <col style="width:130px;"> {{-- POS Revenue --}}
                                <col style="width:130px;"> {{-- Online Revenue --}}
                                <col style="width:130px;"> {{-- Total Revenue --}}
                                <col style="width:90px;"> {{-- Qty POS --}}
                                <col style="width:90px;"> {{-- Units Sold --}}
                                <col style="width:90px;"> {{-- Share % --}}
                                <col style="width:150px;"> {{-- Revenue Split --}}
                            </colgroup>
                            <thead>

                                <tr>
                                    <th style="width: 350px;">Product</th>
                                    <th>Category</th>
                                    <th class="text-center">POS Revenue</th>
                                    <th style="white-space:nowrap; text-align:center; min-width:150px;">Online Revenue</th>
                                    <th class="text-center">Total Revenue</th>
                                    <th class="text-center">Qty POS</th>
                                    <th class="text-center">Units Sold</th>
                                    <th class="text-center">Share %</th>
                                    <th class="col-split print-hide">Revenue Split</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($revenueReport as $p)
                                @if($p->total_revenue > 0)
                                @php $share = $p->total_revenue / $totalRev * 100; @endphp
                                <tr
                                    data-e0="{{ $p->name }}"
                                    data-e1="{{ $p->sku }}"
                                    data-e2="{{ $p->category }}"
                                    data-e3="{{ number_format($p->pos_revenue,2) }}"
                                    data-e4="{{ number_format($p->online_revenue,2) }}"
                                    data-e5="{{ number_format($p->total_revenue,2) }}"
                                    data-e6="{{ $p->pos_sold }}"
                                    data-e7="{{ $p->total_sold }}"
                                    data-e8="{{ number_format($share,1) }}%">
                                    <td>
                                        <div class="prod-cell">
                                            <img src="{{ asset('uploads/products/'.$p->image) }}" class="prod-thumb" alt="">
                                            <div>
                                                <div class="prod-name">{{ $p->name }}</div>
                                                <div class="prod-sku">{{ $p->sku }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="color:#666;">{{ $p->category }}</td>
                                    <td class="text-end" style="color:#5b21b6;font-weight:600;">${{ number_format($p->pos_revenue,2) }}</td>
                                    <td class="text-end" style="color:#c2410c;font-weight:600; min-width:150px;">${{ number_format($p->online_revenue,2) }}</td>
                                    <td class="text-end"><strong style="font-size:14px;">${{ number_format($p->total_revenue,2) }}</strong></td>
                                    <td class="text-center">{{ $p->pos_sold }}</td>
                                    <td class="text-center">{{ $p->total_sold }}</td>
                                    <td class="text-center" style="color:#666;">{{ number_format($share,1) }}%</td>
                                    <td class="col-split print-hide">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="prog-wrap flex-grow-1">
                                                <div class="prog-pos" style="width:{{ $p->total_revenue?($p->pos_revenue/$p->total_revenue*$share):0 }}%"></div>
                                                <div class="prog-online" style="width:{{ $p->total_revenue?($p->online_revenue/$p->total_revenue*$share):0 }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                                @empty
                                <tr class="empty-row">
                                    <td colspan="9">
                                        <div class="empty-state">
                                            <div class="empty-icon">💰</div>
                                            <p>No revenue data for this period.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" style="color:#666;" class="text-end">TOTAL</td>
                                    <td class="text-end" style="color:#5b21b6;">${{ number_format($revenueReport->sum('pos_revenue'),2) }}</td>
                                    <td class="text-end" style="color:#c2410c; min-width:150px;">${{ number_format($revenueReport->sum('online_revenue'),2) }}</td>
                                    <td class="text-end">${{ number_format($revenueReport->sum('total_revenue'),2) }}</td>
                                    <td class="text-center">{{ $revenueReport->sum('pos_sold') }}</td>
                                    <td class="text-center">{{ $revenueReport->sum('total_sold') }}</td>
                                    <td class="text-center"><span class="ch-pill ch-total">100%</span></td>
                                    <td class="col-split print-hide"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@php
$chartProducts = $revenueReport->where('total_revenue', '>', 0)
->pluck('name')
->map(function($n) { return strlen($n) > 22 ? substr($n,0,22).'…' : $n; })
->values();
$chartPosRev = $revenueReport->where('total_revenue', '>', 0)->pluck('pos_revenue')->values();
$chartOnlRev = $revenueReport->where('total_revenue', '>', 0)->pluck('online_revenue')->values();
$chartTotRev = $revenueReport->where('total_revenue', '>', 0)->pluck('total_revenue')->values();
$chartFullNames = $revenueReport->where('total_revenue', '>', 0)->pluck('name')->values();
@endphp
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    // ── Website Charts (ApexCharts) ──────────────────────────────────────
    function initCharts() {
        // Data from Blade
        const products = @json($chartProducts);
        const posRev = @json($chartPosRev);
        const onlRev = @json($chartOnlRev);
        const totRev = @json($chartTotRev);
        const fullNames = @json($chartFullNames);


        // Donut — Revenue Share
        new ApexCharts(document.getElementById('chart-revenue-share'), {
            chart: {
                type: 'donut',
                height: 220,
                toolbar: {
                    show: false
                }
            },
            series: totRev,
            labels: fullNames,
            colors: ['#4361ee', '#f97316', '#10b981', '#f59e0b', '#6366f1', '#ec4899'],
            legend: {
                position: 'bottom',
                fontSize: '11px'
            },
            dataLabels: {
                enabled: true,
                formatter: (val) => val.toFixed(1) + '%'
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '60%'
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: v => '$' + Number(v).toLocaleString('en', {
                        minimumFractionDigits: 2
                    })
                }
            },
        }).render();

        // Bar — POS vs Online
        new ApexCharts(document.getElementById('chart-pos-online'), {
            chart: {
                type: 'bar',
                height: 220,
                toolbar: {
                    show: false
                },
                stacked: false
            },
            series: [{
                    name: 'POS',
                    data: posRev
                },
                {
                    name: 'Online',
                    data: onlRev
                },
            ],
            xaxis: {
                categories: fullNames,
                labels: {
                    style: {
                        fontSize: '10px'
                    }
                }
            },
            colors: ['#4361ee', '#f97316'],
            plotOptions: {
                bar: {
                    columnWidth: '50%',
                    borderRadius: 4
                }
            },
            legend: {
                position: 'bottom',
                fontSize: '11px'
            },
            yaxis: {
                labels: {
                    formatter: v => '$' + Number(v).toLocaleString('en', {
                        minimumFractionDigits: 0
                    })
                }
            },
            tooltip: {
                y: {
                    formatter: v => '$' + Number(v).toLocaleString('en', {
                        minimumFractionDigits: 2
                    })
                },
                x: {
                    formatter: (val, {
                        dataPointIndex
                    }) => fullNames[dataPointIndex] || val
                },
            },
            dataLabels: {
                enabled: false
            },
        }).render();
    }

    // Init on page load
    document.addEventListener('DOMContentLoaded', initCharts);
    // ── Tab switching ────────────────────────────────────────────────────────────
    let activeTab = 'top';

    function switchTab(name, btn) {
        document.querySelectorAll('.tab-pane-custom').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('pane-' + name).classList.add('active');
        btn.classList.add('active');
        activeTab = name;
    }

    // ── Export Excel ─────────────────────────────────────────────────────────────
    function exportExcel() {
        const wb = XLSX.utils.book_new();
        const from = '{{ \Carbon\Carbon::parse($from)->format("d M Y") }}';
        const to = '{{ \Carbon\Carbon::parse($to)->format("d M Y") }}';
        const period = `${from} – ${to}`;

        // ── Helper: collect rows from data-e* attributes ──────────────────────
        function getRows(tableId) {
            const tbl = document.getElementById(tableId);
            if (!tbl) return [];
            const out = [];
            tbl.querySelectorAll('tbody tr:not(.empty-row)').forEach(tr => {
                const row = {};
                let i = 0;
                while (tr.dataset['e' + i] !== undefined) {
                    row[i] = tr.dataset['e' + i];
                    i++;
                }
                if (Object.keys(row).length) out.push(row);
            });
            return out;
        }

        // ── Helper: number or string ──────────────────────────────────────────
        function val(v) {
            if (v === undefined || v === null || v === '') return '';
            const n = Number(String(v).replace(/[$,%]/g, '').trim());
            return isNaN(n) ? v : n;
        }

        const topRows = getRows('tbl-top');
        const lowRows = getRows('tbl-low');
        const stockRows = getRows('tbl-stock');
        const outRows = getRows('tbl-out');
        const revRows = getRows('tbl-revenue');

        // ════════════════════════════════════════════════════════════════════════
        // SHEET 1 — SUMMARY
        // ════════════════════════════════════════════════════════════════════════
        const summaryData = [
            ['PRODUCT REPORT — SUMMARY'],
            [''],
            ['Store', 'Dola Dola Phone Shop'],
            ['Period', period],
            ['Generated', new Date().toLocaleString('en-GB')],
            [''],
            ['METRIC', 'VALUE'],
            ['Total Products', val('{{ $summary["total_products"] }}')],
            ['Total Revenue', val('{{ $summary["total_revenue"] }}')],
            ['Units Sold', val('{{ $summary["total_sold"] }}')],
            ['Out of Stock', val('{{ $summary["out_of_stock"] }}')],
            ['Low Stock (≤5)', val('{{ $summary["low_stock"] }}')],
        ];
        const wsSummary = XLSX.utils.aoa_to_sheet(summaryData);
        wsSummary['!cols'] = [{
            wch: 22
        }, {
            wch: 30
        }];
        wsSummary['!merges'] = [{
            s: {
                r: 0,
                c: 0
            },
            e: {
                r: 0,
                c: 1
            }
        }];
        // Format revenue cell as currency
        const revCell = wsSummary['B9'];
        if (revCell) revCell.z = '"$"#,##0.00';
        XLSX.utils.book_append_sheet(wb, wsSummary, '📊 Summary');

        // ════════════════════════════════════════════════════════════════════════
        // SHEET 2 — TOP SELLING
        // ════════════════════════════════════════════════════════════════════════
        const topHeader = ['TOP 10 BEST-SELLING PRODUCTS', '', '', '', '', '', ''];
        const topSub = [`Period: ${period}`, '', '', '', '', '', ''];
        const topCols = ['#', 'Product Name', 'SKU', 'Category', 'POS Sold', 'Online Sold', 'Total Sold'];
        const topBody = topRows.map(r => [val(r[0]), r[1], r[2], r[3], val(r[4]), val(r[5]), val(r[6])]);
        // Totals row
        const topTotals = ['', 'TOTAL', '', '',
            topRows.reduce((s, r) => s + Number(r[4] || 0), 0),
            topRows.reduce((s, r) => s + Number(r[5] || 0), 0),
            topRows.reduce((s, r) => s + Number(r[6] || 0), 0),
        ];

        const wsTop = XLSX.utils.aoa_to_sheet([topHeader, topSub, topCols, ...topBody, topTotals]);
        wsTop['!cols'] = [{
            wch: 5
        }, {
            wch: 40
        }, {
            wch: 14
        }, {
            wch: 16
        }, {
            wch: 12
        }, {
            wch: 14
        }, {
            wch: 12
        }];
        wsTop['!merges'] = [{
                s: {
                    r: 0,
                    c: 0
                },
                e: {
                    r: 0,
                    c: 6
                }
            },
            {
                s: {
                    r: 1,
                    c: 0
                },
                e: {
                    r: 1,
                    c: 6
                }
            },
        ];
        wsTop['!freeze'] = {
            xSplit: 0,
            ySplit: 3
        };
        XLSX.utils.book_append_sheet(wb, wsTop, '🏆 Top Selling');

        // ════════════════════════════════════════════════════════════════════════
        // SHEET 3 — LOW SELLING
        // ════════════════════════════════════════════════════════════════════════
        const lowCols = ['#', 'Product Name', 'SKU', 'Category', 'POS Sold', 'Online Sold', 'Total Sold', 'Stock Left'];
        const lowBody = lowRows.map(r => [val(r[0]), r[1], r[2], r[3], val(r[4]), val(r[5]), val(r[6]), r[7]]);

        const wsLow = XLSX.utils.aoa_to_sheet([
            ['BOTTOM 10 LOW-SELLING PRODUCTS', '', '', '', '', '', '', ''],
            [`Period: ${period}`, '', '', '', '', '', '', ''],
            lowCols,
            ...lowBody,
        ]);
        wsLow['!cols'] = [{
            wch: 5
        }, {
            wch: 40
        }, {
            wch: 14
        }, {
            wch: 16
        }, {
            wch: 12
        }, {
            wch: 14
        }, {
            wch: 12
        }, {
            wch: 14
        }];
        wsLow['!merges'] = [{
                s: {
                    r: 0,
                    c: 0
                },
                e: {
                    r: 0,
                    c: 7
                }
            },
            {
                s: {
                    r: 1,
                    c: 0
                },
                e: {
                    r: 1,
                    c: 7
                }
            },
        ];
        wsLow['!freeze'] = {
            xSplit: 0,
            ySplit: 3
        };
        XLSX.utils.book_append_sheet(wb, wsLow, '📉 Low Selling');

        // ════════════════════════════════════════════════════════════════════════
        // SHEET 4 — STOCK REPORT
        // ════════════════════════════════════════════════════════════════════════
        const stockCols = ['Product Name', 'SKU', 'Category', 'Variants', 'Total Stock', 'Status', 'Variant Breakdown'];
        const stockBody = stockRows.map(r => [r[0], r[1], r[2], val(r[3]), val(r[4]), r[5], r[6]]);
        const totalStock = stockRows.reduce((s, r) => s + Number(r[4] || 0), 0);

        const wsStock = XLSX.utils.aoa_to_sheet([
            ['STOCK REPORT — ALL PRODUCTS', '', '', '', '', '', ''],
            [`Generated: ${new Date().toLocaleString('en-GB')}`, '', '', '', '', '', ''],
            stockCols,
            ...stockBody,
            ['', 'TOTAL STOCK', '', '', totalStock, '', ''],
        ]);
        wsStock['!cols'] = [{
            wch: 40
        }, {
            wch: 14
        }, {
            wch: 16
        }, {
            wch: 10
        }, {
            wch: 13
        }, {
            wch: 14
        }, {
            wch: 50
        }];
        wsStock['!merges'] = [{
                s: {
                    r: 0,
                    c: 0
                },
                e: {
                    r: 0,
                    c: 6
                }
            },
            {
                s: {
                    r: 1,
                    c: 0
                },
                e: {
                    r: 1,
                    c: 6
                }
            },
        ];
        wsStock['!freeze'] = {
            xSplit: 0,
            ySplit: 3
        };
        XLSX.utils.book_append_sheet(wb, wsStock, '📦 Stock Report');

        // ════════════════════════════════════════════════════════════════════════
        // SHEET 5 — OUT OF STOCK
        // ════════════════════════════════════════════════════════════════════════
        const outCols = ['Product Name', 'SKU', 'Category', 'Variants Affected', 'Out of Stock Variants', 'Total Ever Sold'];
        const outBody = outRows.length ?
            outRows.map(r => [r[0], r[1], r[2], r[3], r[4], val(r[5])]) : [
                ['✓ All products are currently in stock', '', '', '', '', '']
            ];

        const wsOut = XLSX.utils.aoa_to_sheet([
            ['OUT OF STOCK PRODUCTS', '', '', '', '', ''],
            [`Period: ${period}`, '', '', '', '', ''],
            outCols,
            ...outBody,
        ]);
        wsOut['!cols'] = [{
            wch: 40
        }, {
            wch: 14
        }, {
            wch: 16
        }, {
            wch: 18
        }, {
            wch: 35
        }, {
            wch: 16
        }];
        wsOut['!merges'] = [{
                s: {
                    r: 0,
                    c: 0
                },
                e: {
                    r: 0,
                    c: 5
                }
            },
            {
                s: {
                    r: 1,
                    c: 0
                },
                e: {
                    r: 1,
                    c: 5
                }
            },
        ];
        if (!outRows.length) {
            wsOut['!merges'].push({
                s: {
                    r: 3,
                    c: 0
                },
                e: {
                    r: 3,
                    c: 5
                }
            });
        }
        wsOut['!freeze'] = {
            xSplit: 0,
            ySplit: 3
        };
        XLSX.utils.book_append_sheet(wb, wsOut, '⛔ Out of Stock');

        // ════════════════════════════════════════════════════════════════════════
        // SHEET 6 — REVENUE
        // ════════════════════════════════════════════════════════════════════════
        const revCols = ['Product Name', 'SKU', 'Category', 'POS Revenue ($)', 'Online Revenue ($)', 'Total Revenue ($)', 'Units Sold', 'Share %'];
        const revBody = revRows.map(r => [
            r[0], r[1], r[2],
            val(r[3]), val(r[4]), val(r[5]),
            val(r[6]),
            parseFloat((r[7] || '0').replace('%', '')) / 100,
        ]);
        const posTotal = revRows.reduce((s, r) => s + Number((r[3] || '0').replace(/,/g, '')), 0);
        const onlTotal = revRows.reduce((s, r) => s + Number((r[4] || '0').replace(/,/g, '')), 0);
        const revTotal = revRows.reduce((s, r) => s + Number((r[5] || '0').replace(/,/g, '')), 0);
        const unitTotal = revRows.reduce((s, r) => s + Number(r[6] || 0), 0);

        const wsRev = XLSX.utils.aoa_to_sheet([
            ['PRODUCT REVENUE REPORT', '', '', '', '', '', '', ''],
            [`Period: ${period}`, '', '', '', '', '', '', ''],
            revCols,
            ...revBody,
            ['', 'TOTAL', '', posTotal, onlTotal, revTotal, unitTotal, 1],
        ]);

        // Format revenue + share columns
        const revRowCount = revBody.length + 4; // header rows + data + total
        for (let row = 3; row < revRowCount; row++) {
            ['D', 'E', 'F'].forEach(col => {
                const cell = wsRev[`${col}${row}`];
                if (cell) cell.z = '"$"#,##0.00';
            });
            const shareCell = wsRev[`H${row}`];
            if (shareCell) shareCell.z = '0.0%';
        }

        wsRev['!cols'] = [{
            wch: 40
        }, {
            wch: 14
        }, {
            wch: 16
        }, {
            wch: 18
        }, {
            wch: 20
        }, {
            wch: 18
        }, {
            wch: 13
        }, {
            wch: 10
        }];
        wsRev['!merges'] = [{
                s: {
                    r: 0,
                    c: 0
                },
                e: {
                    r: 0,
                    c: 7
                }
            },
            {
                s: {
                    r: 1,
                    c: 0
                },
                e: {
                    r: 1,
                    c: 7
                }
            },
        ];
        wsRev['!freeze'] = {
            xSplit: 0,
            ySplit: 3
        };
        XLSX.utils.book_append_sheet(wb, wsRev, '💰 Revenue');

        // ── Save ──────────────────────────────────────────────────────────────
        XLSX.writeFile(wb, `product-report-{{ \Carbon\Carbon::parse($from)->format("Ymd") }}-{{ \Carbon\Carbon::parse($to)->format("Ymd") }}.xlsx`);
    }

    // ── Export PDF — auto-download, no print dialog, no admin chrome ─────────────
    async function exportPDF() {
        const btn = document.querySelector('.btn-pdf');
        const origText = btn.innerHTML;
        btn.innerHTML = '⏳ Generating...';
        btn.disabled = true;

        const from = '{{ \Carbon\Carbon::parse($from)->format("d M Y") }}';
        const to = '{{ \Carbon\Carbon::parse($to)->format("d M Y") }}';
        const now = new Date().toLocaleString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        // ── Build PDF HTML ──────────────────────────────────────────────────────
        const css = `
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; padding: 20px; }
        .pdf-page {
            background: #fff; padding: 28px 32px; margin-bottom: 20px;
            border-radius: 8px; page-break-after: always;
        }
        .pdf-page:last-child { page-break-after: avoid; }

        /* Header */
        .pdf-header { display: flex; justify-content: space-between; align-items: flex-start;
            padding-bottom: 16px; border-bottom: 2.5px solid #4361ee; margin-bottom: 20px; }
        .brand-name { font-size: 17px; font-weight: 700; color: #1a1a2e; }
        .brand-sub  { font-size: 11px; color: #888; margin-top: 2px; }
        .meta-title  { font-size: 13px; font-weight: 700; color: #1a1a2e; text-align: right; }
        .meta-period { font-size: 11px; color: #666; margin-top: 3px; text-align: right; }
        .meta-gen    { font-size: 10px; color: #bbb; margin-top: 2px; text-align: right; }

        /* KPI */
        .kpi-row { display: grid; grid-template-columns: repeat(5,1fr); gap: 10px; margin-bottom: 24px; }
        .kpi-box { background: #fafbfc; border: 1px solid #e8eaed; border-radius: 8px; padding: 11px 13px; }
        .kpi-lbl { font-size: 9px; font-weight: 700; color: #aaa; text-transform: uppercase; letter-spacing: .05em; }
        .kpi-val { font-size: 18px; font-weight: 700; color: #1a1a2e; margin-top: 2px; }
        .kpi-hint { font-size: 9px; color: #ccc; margin-top: 1px; }

        /* Section */
        .section { margin-bottom: 26px; }
        .section-title {
            font-size: 10.5px; font-weight: 700; color: #1a1a2e;
            text-transform: uppercase; letter-spacing: .06em;
            border-bottom: 1.5px solid #f0f0f0; padding-bottom: 7px; margin-bottom: 11px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .section-title small { font-size: 10px; color: #aaa; font-weight: 500; text-transform: none; letter-spacing: 0; }

        /* Table */
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        thead th { padding: 6px 9px; font-size: 9px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .04em; color: #999; background: #fafbfc;
            border-bottom: 1px solid #eee; white-space: nowrap; }
        tbody td { padding: 8px 9px; color: #333; border-bottom: 0.5px solid #f5f5f5; vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        tfoot td { padding: 8px 9px; font-weight: 700; color: #1a1a2e;
            border-top: 1.5px solid #e5e7eb; background: #fafbfc; }

        .rank { width: 20px; height: 20px; border-radius: 50%; display: inline-flex;
            align-items: center; justify-content: center; font-size: 9px; font-weight: 700; }
        .r1 { background: #fef3c7; color: #92400e; }
        .r2 { background: #f1f5f9; color: #475569; }
        .r3 { background: #ffedd5; color: #9a3412; }
        .rn { background: #f4f4f4; color: #888; }

        .badge { display: inline-block; padding: 2px 7px; border-radius: 20px; font-size: 9px; font-weight: 700; }
        .b-ok  { background: #f0fdf4; color: #166534; }
        .b-low { background: #fff7ed; color: #c2410c; }
        .b-out { background: #fef2f2; color: #991b1b; }

        .pname { font-size: 11px; font-weight: 600; color: #1a1a2e; }
        .psku  { font-size: 9px;  color: #bbb; margin-top: 1px; }
        .c-pos    { color: #5b21b6; font-weight: 600; }
        .c-online { color: #c2410c; font-weight: 600; }
        .text-center { text-align: center; }
        .text-right  { text-align: right;  }
        .font-bold   { font-weight: 700; }

        .prog { 
            height: 5px; 
            background: #f0f0f0; 
            border-radius: 3px;
            overflow: hidden; 
            display: flex !important; 
            width: 80px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .prog-p { 
            background: #4361ee !important; 
            display: block;
            height: 5px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .prog-o { 
            background: #f97316 !important; 
            display: block;
            height: 5px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .all-ok-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px;
            padding: 14px; text-align: center; font-size: 12px; color: #166534; font-weight: 600; }

        .pdf-footer { border-top: 1px solid #eee; padding-top: 10px; margin-top: 4px;
            display: flex; justify-content: space-between; }
        .pdf-footer p { font-size: 9px; color: #bbb; }

        .page-cont-header { display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 18px; padding-bottom: 10px; border-bottom: 1.5px solid #f0f0f0; }
        .page-cont-title { font-size: 12px; font-weight: 700; color: #1a1a2e; }

        .charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
        }
        .chart-card {
            background: #fafbfc;
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            padding: 16px 20px;
        }
        .chart-card-title {
            font-size: 12px;
            font-weight: 700;
            color: #888;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 8px;
        }

        @media print {
            .charts-row { display: none !important; }
        }
        `;

        // ── Collect data from data-e* attributes ──────────────────────────────
        function getRows(tableId) {
            const tbl = document.getElementById(tableId);
            if (!tbl) return [];
            const out = [];
            tbl.querySelectorAll('tbody tr:not(.empty-row)').forEach(tr => {
                const row = {};
                let i = 0;
                while (tr.dataset['e' + i] !== undefined) {
                    row[i] = tr.dataset['e' + i];
                    i++;
                }
                if (Object.keys(row).length) out.push(row);
            });
            return out;
        }

        const topRows = getRows('tbl-top');
        const lowRows = getRows('tbl-low');
        const stockRows = getRows('tbl-stock');
        const outRows = getRows('tbl-out');
        const revRows = getRows('tbl-revenue');
        const maxSold = Math.max(...topRows.map(r => +r[6] || 0), 1);
        const totalRev = revRows.reduce((s, r) => s + parseFloat(r[5] || 0), 0) || 1;

        function rankClass(i) {
            return ['r1', 'r2', 'r3'][i] || 'rn';
        }

        function topTable() {
            if (!topRows.length) return '<p style="color:#bbb;font-size:11px;text-align:center;padding:16px;">No sales data for this period.</p>';
            return `<table>
            <thead><tr>
                <th style="width:32px">#</th>
                <th>Product</th>
                <th>Category</th>
                <th class="text-center">POS Sold</th>
                <th class="text-center">Online Sold</th>
                <th class="text-center">Total Sold</th>
                <th style="width:90px">Sales Split</th>
            </tr></thead>
            <tbody>${topRows.map((r,i) => {
                const pct = +r[6] / maxSold * 100;
                const posW  = r[6] > 0 ? (+r[4] / +r[6] * pct).toFixed(1) : 0;
                const onlW  = r[6] > 0 ? (+r[5] / +r[6] * pct).toFixed(1) : 0;
                return `<tr>
                    <td><span class="rank ${rankClass(i)}">${i+1}</span></td>
                    <td><div class="pname">${r[1]}</div><div class="psku">${r[2]}</div></td>
                    <td style="color:#666">${r[3]}</td>
                    <td class="text-center c-pos">${r[4]}</td>
                    <td class="text-center c-online">${r[5]}</td>
                    <td class="text-center font-bold">${r[6]}</td>
                    <td><div class="prog"><div class="prog-p" style="width:${posW}%"></div><div class="prog-o" style="width:${onlW}%"></div></div></td>
                </tr>`;
            }).join('')}</tbody></table>`;
        }

        function lowTable() {
            if (!lowRows.length) return '<p style="color:#bbb;font-size:11px;text-align:center;padding:16px;">No data available.</p>';
            return `<table>
            <thead><tr>
                <th style="width:32px">#</th><th>Product</th><th>Category</th>
                <th class="text-center">POS Sold</th><th class="text-center">Online Sold</th>
                <th class="text-center">Total Sold</th><th>Stock Left</th>
            </tr></thead>
            <tbody>${lowRows.map((r,i) => `<tr>
                <td><span class="rank rn">${i+1}</span></td>
                <td><div class="pname">${r[1]}</div><div class="psku">${r[2]}</div></td>
                <td style="color:#666">${r[3]}</td>
                <td class="text-center c-pos">${r[4]}</td>
                <td class="text-center c-online">${r[5]}</td>
                <td class="text-center font-bold">${r[6]}</td>
                <td><span class="badge ${r[7].includes('0 units') ? 'b-out' : parseInt(r[7]) <= 5 ? 'b-low' : 'b-ok'}">${r[7]}</span></td>
            </tr>`).join('')}</tbody></table>`;
        }

        function stockTable() {
            if (!stockRows.length) return '<p style="color:#bbb;font-size:11px;text-align:center;padding:16px;">No products found.</p>';
            return `<table>
            <thead><tr>
                <th>Product</th><th>Category</th>
                <th class="text-center">Variants</th><th class="text-center">Total Stock</th>
                <th>Status</th><th>Variant Breakdown</th>
            </tr></thead>
            <tbody>${stockRows.map(r => {
                const sc = r[5]==='Out of Stock'?'b-out':r[5]==='Low Stock'?'b-low':'b-ok';
                return `<tr>
                    <td><div class="pname">${r[0]}</div><div class="psku">${r[1]}</div></td>
                    <td style="color:#666">${r[2]}</td>
                    <td class="text-center font-bold">${r[3]}</td>
                    <td class="text-center" style="font-size:13px;font-weight:700;color:#1a1a2e">${r[4]}</td>
                    <td><span class="badge ${sc}">${r[5]}</span></td>
                    <td style="font-size:10px;color:#555">${r[6]}</td>
                </tr>`;
            }).join('')}</tbody></table>`;
        }

        function outSection() {
            if (!outRows.length) return '<div class="all-ok-box">✓ All products are currently in stock — great job!</div>';
            return `<table>
            <thead><tr>
                <th>Product</th><th>Category</th>
                <th class="text-center">Variants Affected</th>
                <th>Out of Stock Variants</th><th class="text-center">Total Ever Sold</th>
            </tr></thead>
            <tbody>${outRows.map(r => `<tr>
                <td><div class="pname">${r[0]}</div><div class="psku">${r[1]}</div></td>
                <td style="color:#666">${r[2]}</td>
                <td class="text-center"><span class="badge b-out">${r[3]}</span></td>
                <td style="font-size:10px">${r[4]}</td>
                <td class="text-center font-bold">${r[5]}</td>
            </tr>`).join('')}</tbody></table>`;
        }

        function revTable() {
            if (!revRows.length) return '<p style="color:#bbb;font-size:11px;text-align:center;padding:16px;">No revenue data.</p>';
            const posTotal = revRows.reduce((s, r) => s + parseFloat(r[3] || 0), 0);
            const onlTotal = revRows.reduce((s, r) => s + parseFloat(r[4] || 0), 0);
            const revTotal = revRows.reduce((s, r) => s + parseFloat(r[5] || 0), 0);
            const unitTotal = revRows.reduce((s, r) => s + parseInt(r[6] || 0), 0);
            return `<table>
            <thead><tr>
                <th>Product</th><th>Category</th>
                <th class="text-right">POS Revenue</th><th class="text-right">Online Revenue</th>
                <th class="text-right">Total Revenue</th>
                <th class="text-center">Units Sold</th><th class="text-center">Share %</th>
            </tr></thead>
            <tbody>${revRows.map(r => `<tr>
                <td><div class="pname">${r[0]}</div><div class="psku">${r[1]}</div></td>
                <td style="color:#666">${r[2]}</td>
                <td class="text-right c-pos">$${r[3]}</td>
                <td class="text-right c-online">$${r[4]}</td>
                <td class="text-right font-bold" style="font-size:12px">$${r[5]}</td>
                <td class="text-center">${r[6]}</td>
                <td class="text-center" style="color:#666">${r[7]}</td>
            </tr>`).join('')}</tbody>
            <tfoot><tr>
                <td colspan="2" style="color:#888;font-size:9px;text-transform:uppercase;letter-spacing:.04em">Total</td>
                <td class="text-right" style="color:#5b21b6">$${posTotal.toFixed(2)}</td>
                <td class="text-right" style="color:#c2410c">$${onlTotal.toFixed(2)}</td>
                <td class="text-right">$${revTotal.toFixed(2)}</td>
                <td class="text-center">${unitTotal}</td>
                <td class="text-center"><span class="badge b-ok">100%</span></td>
            </tr></tfoot></table>`;
        }

        const html = `<!DOCTYPE html>
        <html><head><meta charset="UTF-8">
        <title>Product Report — ${from} to ${to}</title>
        <style>${css}</style>
        </head><body>

        <!-- PAGE 1 -->
        <div class="pdf-page">
        <div class="pdf-header">
            <div>
            <div class="brand-name">📦 Dola Dola Phone Shop</div>
            <div class="brand-sub">Product Performance Report</div>
            </div>
            <div>
            <div class="meta-title">Product Reports</div>
            <div class="meta-period">${from} – ${to}</div>
            <div class="meta-gen">Generated: ${now}</div>
            </div>
        </div>

        <div class="kpi-row">
            <div class="kpi-box">
            <div class="kpi-lbl">Total Products</div>
            <div class="kpi-val">{{ $summary['total_products'] }}</div>
            </div>
            <div class="kpi-box">
            <div class="kpi-lbl">Total Revenue</div>
            <div class="kpi-val">\${{ number_format($summary['total_revenue'],2) }}</div>
            <div class="kpi-hint">Online + POS</div>
            </div>
            <div class="kpi-box">
            <div class="kpi-lbl">Units Sold</div>
            <div class="kpi-val">{{ number_format($summary['total_sold']) }}</div>
            <div class="kpi-hint">Online + POS</div>
            </div>
            <div class="kpi-box">
            <div class="kpi-lbl">Out of Stock</div>
            <div class="kpi-val">{{ $summary['out_of_stock'] }}</div>
            <div class="kpi-hint">products</div>
            </div>
            <div class="kpi-box">
            <div class="kpi-lbl">Low Stock</div>
            <div class="kpi-val">{{ $summary['low_stock'] }}</div>
            <div class="kpi-hint">≤5 units</div>
            </div>
        </div>

        ${chartsHTML()}

        <div class="section">
            <div class="section-title">Top 10 Best-Selling Products
            <small>● POS &nbsp; ● Online &nbsp; ${from} – ${to}</small>
            </div>
            ${topTable()}
        </div>

        <div class="section">
            <div class="section-title">Bottom 10 Low-Selling Products
            <small>Products with at least 1 sale · sorted lowest first</small>
            </div>
            ${lowTable()}
        </div>

        <div class="pdf-footer">
            <p>Product Reports · My Store Admin</p>
            <p>Page 1 of 2</p>
        </div>
        </div>

        <!-- PAGE 2 -->
        <div class="pdf-page">
        <div class="page-cont-header">
            <div class="page-cont-title">Product Reports (continued)</div>
            <div style="font-size:10px;color:#aaa">${from} – ${to}</div>
        </div>

        <div class="section">
            <div class="section-title">Stock Report — All Products
            <small>
                <span class="badge b-ok">In Stock</span>&nbsp;
                <span class="badge b-low">Low Stock ≤5</span>&nbsp;
                <span class="badge b-out">Out of Stock</span>
            </small>
            </div>
            ${stockTable()}
        </div>

        <div class="section">
            <div class="section-title">Out of Stock Products
            <span class="badge b-out">{{ $outOfStock->count() }} products</span>
            </div>
            ${outSection()}
        </div>

        <div class="section">
            <div class="section-title">Product Revenue Report
            <small>● POS &nbsp; ● Online</small>
            </div>
            ${revTable()}
        </div>

        <div class="pdf-footer">
            <p>Product Reports · My Store Admin</p>
            <p>Page 2 of 2</p>
        </div>
        </div>

        </body></html>`;

        // ── Print via hidden iframe (no new window) ───────────────────────────
        const iframe = document.createElement('iframe');
        iframe.style.cssText = 'position:fixed;top:-9999px;left:-9999px;width:1px;height:1px;border:none;';
        document.body.appendChild(iframe);

        iframe.onload = () => {
            setTimeout(() => {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
                // Remove iframe after print dialog closes
                setTimeout(() => document.body.removeChild(iframe), 2000);
            }, 500);
        };

        iframe.srcdoc = html;

        btn.innerHTML = origText;
        btn.disabled = false;

        function chartsHTML() {
            const names = revRows.map(r => r[0].length > 22 ? r[0].substring(0, 22) + '…' : r[0]);
            const totals = revRows.map(r => parseFloat((r[5] || '0').replace(/,/g, '')));
            const posR = revRows.map(r => parseFloat((r[3] || '0').replace(/,/g, '')));
            const onlR = revRows.map(r => parseFloat((r[4] || '0').replace(/,/g, '')));
            const grandTotal = totals.reduce((a, b) => a + b, 0) || 1;

            // Donut segments
            const COLORS = ['#4361ee', '#f97316', '#10b981', '#f59e0b', '#6366f1', '#ec4899'];
            const cx = 80,
                cy = 80,
                R = 65,
                r = 40;
            let startAngle = -Math.PI / 2;
            let donutPaths = '';
            let legendItems = '';

            totals.forEach((t, i) => {
                const angle = (t / grandTotal) * 2 * Math.PI;
                const endAngle = startAngle + angle;
                const x1 = cx + R * Math.cos(startAngle),
                    y1 = cy + R * Math.sin(startAngle);
                const x2 = cx + R * Math.cos(endAngle),
                    y2 = cy + R * Math.sin(endAngle);
                const ix1 = cx + r * Math.cos(startAngle),
                    iy1 = cy + r * Math.sin(startAngle);
                const ix2 = cx + r * Math.cos(endAngle),
                    iy2 = cy + r * Math.sin(endAngle);
                const large = angle > Math.PI ? 1 : 0;
                const pct = (t / grandTotal * 100).toFixed(1);
                const col = COLORS[i % COLORS.length];

                donutPaths += `<path d="M${x1},${y1} A${R},${R} 0 ${large},1 ${x2},${y2} L${ix2},${iy2} A${r},${r} 0 ${large},0 ${ix1},${iy1} Z" fill="${col}" stroke="#fff" stroke-width="2"/>`;

                // Label on segment if big enough
                if (angle > 0.3) {
                    const midAngle = startAngle + angle / 2;
                    const lx = cx + (R + r) / 2 * Math.cos(midAngle);
                    const ly = cy + (R + r) / 2 * Math.sin(midAngle);
                    donutPaths += `<text x="${lx}" y="${ly}" text-anchor="middle" dominant-baseline="middle" font-size="8" font-weight="700" fill="#fff">${pct}%</text>`;
                }

                legendItems += `<div style="display:flex;align-items:center;gap:5px;font-size:9px;color:#555;margin-bottom:3px;">
                    <div style="width:9px;height:9px;border-radius:50%;background:${col};flex-shrink:0"></div>
                    <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100px">${names[i]}</span>
                    <span style="color:#999;margin-left:auto">$${Number(totals[i]).toLocaleString('en',{minimumFractionDigits:0})}</span>
                </div>`;
                startAngle = endAngle;
            });

            // Bar chart
            const barW = 260,
                barH = 130,
                padL = 55,
                padB = 30,
                padT = 10;
            const plotW = barW - padL - 10;
            const plotH = barH - padB - padT;
            const maxVal = Math.max(...posR, ...onlR, 1);
            const groupW = plotW / (names.length || 1);
            const bw = Math.min(groupW * 0.35, 20);

            let bars = '';
            let xLabels = '';
            let yLabels = '';

            // Y axis labels
            for (let i = 0; i <= 4; i++) {
                const yv = maxVal * i / 4;
                const y = padT + plotH - (plotH * i / 4);
                yLabels += `<line x1="${padL}" y1="${y}" x2="${barW-10}" y2="${y}" stroke="#f0f0f0" stroke-width="0.5"/>`;
                yLabels += `<text x="${padL-4}" y="${y}" text-anchor="end" dominant-baseline="middle" font-size="7" fill="#bbb">$${yv>=1000?(yv/1000).toFixed(0)+'k':yv.toFixed(0)}</text>`;
            }

            names.forEach((name, i) => {
                const gx = padL + groupW * i + groupW / 2;
                const posH = (posR[i] / maxVal) * plotH;
                const onlH = (onlR[i] / maxVal) * plotH;
                const posX = gx - bw - 1;
                const onlX = gx + 1;
                const posY = padT + plotH - posH;
                const onlY = padT + plotH - onlH;

                bars += `<rect x="${posX}" y="${posY}" width="${bw}" height="${posH}" fill="#4361ee" rx="2"/>`;
                bars += `<rect x="${onlX}" y="${onlY}" width="${bw}" height="${onlH}" fill="#f97316" rx="2"/>`;

                const shortName = name.length > 10 ? name.substring(0, 10) + '…' : name;
                xLabels += `<text x="${gx}" y="${barH-padB+14}" text-anchor="middle" font-size="7" fill="#999">${shortName}</text>`;
            });

            return `
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">

            <!-- Donut -->
            <div style="background:#fafbfc;border:1px solid #f0f0f0;border-radius:8px;padding:12px 14px;">
                <div style="font-size:9px;font-weight:700;color:#999;text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px;">Revenue Share</div>
                <div style="display:flex;align-items:center;gap:12px;">
                <svg width="160" height="160" viewBox="0 0 160 160" style="flex-shrink:0">
                    ${donutPaths}
                </svg>
                <div style="flex:1;min-width:0">${legendItems}</div>
                </div>
            </div>

            <!-- Bar -->
            <div style="background:#fafbfc;border:1px solid #f0f0f0;border-radius:8px;padding:12px 14px;">
                <div style="font-size:9px;font-weight:700;color:#999;text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px;">POS vs Online Revenue</div>
                <svg width="${barW}" height="${barH}" viewBox="0 0 ${barW} ${barH}">
                ${yLabels}
                <line x1="${padL}" y1="${padT}" x2="${padL}" y2="${padT+plotH}" stroke="#e5e7eb" stroke-width="1"/>
                <line x1="${padL}" y1="${padT+plotH}" x2="${barW-10}" y2="${padT+plotH}" stroke="#e5e7eb" stroke-width="1"/>
                ${bars}
                ${xLabels}
                </svg>
                <div style="display:flex;gap:12px;margin-top:4px;">
                <div style="display:flex;align-items:center;gap:4px;font-size:8px;color:#555">
                    <div style="width:8px;height:8px;border-radius:2px;background:#4361ee"></div> POS
                </div>
                <div style="display:flex;align-items:center;gap:4px;font-size:8px;color:#555">
                    <div style="width:8px;height:8px;border-radius:2px;background:#f97316"></div> Online
                </div>
                </div>
            </div>

            </div>`;
        }
    }
</script>

@endsection