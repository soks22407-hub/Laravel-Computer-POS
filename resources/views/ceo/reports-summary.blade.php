@extends('layouts.ceo')
@section('content')

<style>
    body {
        background: #f0f2f5;
    }

    /* ── Filter Card ── */
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

    /* ── KPI Grid ── */
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

    .kpi-icon.blue {
        background: #dbeafe;
    }

    .kpi-icon.orange {
        background: #ffedd5;
    }

    .kpi-icon.red {
        background: #fee2e2;
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

    /* ── Report Card ── */
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

    .report-card-meta {
        font-size: 12px;
        color: #aaa;
    }

    /* ── Table ── */
    .rt {
        width: 100%;
        border-collapse: collapse;
    }

    .rt thead tr {
        border-bottom: 1.5px solid #f0f0f0;
    }

    .rt thead th {
        padding: 10px 16px;
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
        padding: 12px 16px;
        font-size: 13px;
        color: #333;
        vertical-align: middle;
    }

    .rt tfoot tr {
        border-top: 2px solid #f0f0f0;
        background: #fafbfc;
    }

    .rt tfoot td {
        padding: 12px 16px;
        font-size: 13px;
        font-weight: 700;
        color: #1a1a2e;
    }

    /* Summary row highlight */
    .rt tbody tr.row-summary td {
        background: #f5f7ff;
        font-weight: 700;
        color: #1a1a2e;
        border-bottom: 2px solid #e0e5ff;
    }

    .rt tbody tr.row-summary:hover td {
        background: #eef0ff;
    }

    .rt tbody tr.row-summary td:first-child {
        color: #4361ee;
        font-size: 13.5px;
    }

    .text-neg {
        color: #dc2626;
    }

    .c-pos {
        color: #5b21b6;
        font-weight: 600;
    }

    .c-online {
        color: #c2410c;
        font-weight: 600;
    }

    .c-total {
        color: #166534;
        font-weight: 700;
    }

    .c-net {
        color: #1d4ed8;
        font-weight: 700;
    }

    .c-ship {
        color: #0891b2;
        font-weight: 600;
    }

    .text-muted {
        color: #bbb;
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

    @media print {
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        .no-print {
            display: none !important;
        }

        .print-header {
            display: block !important;
        }

        body {
            background: #fff !important;
        }

        .report-card {
            box-shadow: none;
            border: 1px solid #eee;
        }

        .rt thead th {
            font-size: 9px !important;
            padding: 5px 8px !important;
        }

        .rt tbody td {
            font-size: 11px !important;
            padding: 7px 8px !important;
        }

        .rt tfoot td {
            font-size: 11px !important;
            padding: 7px 8px !important;
        }
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<div class="main-content-inner">
    <div class="main-content-wrap">

        {{-- Breadcrumb --}}
        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding:5px 0;margin-top:0;">
            <h3>Sales Summary Report</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('ceo.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">Sales Summary Report</div>
                </li>
            </ul>
        </div>

        {{-- Print header (hidden on screen) --}}
        <div class="print-header">
            <h2>📊 Sales Summary Report</h2>
            <p>
                Period: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
                &nbsp;|&nbsp; Group by: {{ ucfirst($groupBy) }}
                &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, H:i') }}
            </p>
        </div>

        {{-- Filter Card --}}
        <form method="GET" action="{{ route('ceo.reports.summary') }}" class="filter-card no-print">
            <div class="filter-group">
                <label>From Date</label>
                <input type="date" name="from" class="form-control"
                    value="{{ \Carbon\Carbon::parse($from)->format('Y-m-d') }}">
            </div>
            <div class="filter-group">
                <label>To Date</label>
                <input type="date" name="to" class="form-control"
                    value="{{ \Carbon\Carbon::parse($to)->format('Y-m-d') }}">
            </div>
            <div class="filter-group">
                <label>Group By</label>
                <select name="group_by" class="form-select">
                    <option value="daily" {{ $groupBy === 'daily'   ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ $groupBy === 'weekly'  ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ $groupBy === 'monthly' ? 'selected' : '' }}>Monthly</option>
                </select>
            </div>
            <button type="submit" class="btn-apply">Apply</button>
            <a href="{{ route('ceo.reports.summary') }}" class="btn-clear text-decoration-none">Clear</a>
            <div class="export-group">
                <button type="button" class="btn-export btn-excel" onclick="exportExcel()">📊 Export Excel</button>
                <button type="button" class="btn-export btn-pdf" onclick="exportPDF()">📄 Export PDF</button>
            </div>
        </form>

        {{-- KPI Grid --}}
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon purple">💰</div>
                <div class="kpi-body">
                    <div class="kpi-label">Gross Sales</div>
                    <div class="kpi-value">${{ number_format($summary['gross_sales'], 2) }}</div>
                    <div class="kpi-sub">Before deductions</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon blue">🏪</div>
                <div class="kpi-body">
                    <div class="kpi-label">POS Sales</div>
                    <div class="kpi-value" style="color:#5b21b6">${{ number_format($summary['pos_sales'], 2) }}</div>
                    <div class="kpi-sub">In-store revenue</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon orange">🛒</div>
                <div class="kpi-body">
                    <div class="kpi-label">Online Sales</div>
                    <div class="kpi-value" style="color:#c2410c">${{ number_format($summary['online_sales'], 2) }}</div>
                    <div class="kpi-sub">E-commerce revenue</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon green">📈</div>
                <div class="kpi-body">
                    <div class="kpi-label">Net Sales</div>
                    <div class="kpi-value" style="color:#1d4ed8">${{ number_format($summary['net_sales'], 2) }}</div>
                    <div class="kpi-sub">After discounts & returns</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon red">↩️</div>
                <div class="kpi-body">
                    <div class="kpi-label">Returns & Discounts</div>
                    <div class="kpi-value" style="color:#dc2626">${{ number_format($summary['return_sales'] + $summary['discounts'], 2) }}</div>
                    <div class="kpi-sub">Total deductions</div>
                </div>
            </div>
        </div>

        {{-- Report Table Card --}}
        <div class="report-card">
            <div class="report-card-header">
                <h6 class="report-card-title">
                    📊 Sales Summary — {{ ucfirst($groupBy) }} Breakdown
                </h6>
                <div class="d-flex align-items-center gap-3">
                    <div class="legend">
                        <div class="legend-item">
                            <div class="legend-dot dot-pos"></div> POS
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot dot-online"></div> Online
                        </div>
                    </div>
                    <span class="report-card-meta">
                        {{ \Carbon\Carbon::parse($from)->format('d M Y') }} – {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
                    </span>
                </div>
            </div>

            <div style="padding: 16px 24px 24px;">
                <div class="table-responsive">
                    <table class="rt" id="tbl-summary">
                        <colgroup>
                            <col style="width:160px;">
                            <col style="width:120px;">
                            <col style="width:120px;">
                            <col style="width:120px;">
                            <col style="width:110px;">
                            <col style="width:120px;">
                            <col style="width:120px;">
                            <col style="width:120px;">
                            <col style="width:120px;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>{{ ucfirst($groupBy) }} ↑</th>
                                <th class="text-end">POS Sales</th>
                                <th class="text-end">Online Sales</th>
                                <th class="text-end">SubTotal Sales ↑</th>
                                <th class="text-end">Shipping Fee</th>
                                <th class="text-end">Return Sales</th>
                                <th class="text-end">Discounts</th>
                                <th class="text-end">Net Sales</th>
                                <th class="text-end">Total Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Summary totals row --}}
                            <tr class="row-summary"
                                data-e0="Summary"
                                data-e1="{{ $summary['gross_sales'] }}"
                                data-e2="{{ $summary['return_sales'] }}"
                                data-e3="{{ $summary['discounts'] }}"
                                data-e4="{{ $summary['pos_sales'] }}"
                                data-e5="{{ $summary['online_sales'] }}"
                                data-e6="{{ $summary['net_sales'] }}"
                                data-e7="{{ $summary['total_sales'] }}"
                                data-e8="{{ $summary['shipping_fee'] }}">
                                <td>Summary</td>
                                <td class="text-end c-pos">${{ number_format($summary['pos_sales'], 2) }}</td>
                                <td class="text-end c-online">${{ number_format($summary['online_sales'], 2) }}</td>
                                <td class="text-end" style="font-size:14px;">${{ number_format($summary['gross_sales'], 2) }}</td>
                                <td class="text-end c-ship">${{ number_format($summary['shipping_fee'], 2) }}</td>
                                <td class="text-end text-neg">
                                    {{ $summary['return_sales'] > 0 ? '-' : '' }}${{ number_format($summary['return_sales'], 2) }}
                                </td>
                                <td class="text-end text-neg">
                                    {{ $summary['discounts'] > 0 ? '-' : '' }}${{ number_format($summary['discounts'], 2) }}
                                </td>
                                <td class="text-end c-net" style="font-size:14px;">${{ number_format($summary['net_sales'], 2) }}</td>
                                <td class="text-end c-total" style="font-size:14px;">${{ number_format($summary['total_sales'], 2) }}</td>
                            </tr>

                            {{-- Per-period rows --}}
                            @forelse($rows as $row)
                            <tr
                                data-e0="{{ $row['label'] }}"
                                data-e1="{{ $row['gross_sales'] }}"
                                data-e2="{{ $row['return_sales'] }}"
                                data-e3="{{ $row['discounts'] }}"
                                data-e4="{{ $row['pos_sales'] }}"
                                data-e5="{{ $row['online_sales'] }}"
                                data-e6="{{ $row['net_sales'] }}"
                                data-e7="{{ $row['total_sales'] }}"
                                data-e8="{{ $row['shipping_fee'] }}">
                                <td style="font-weight:500;">
                                    <a href="{{ route('ceo.reports.summary.detail', [
                                                'from' => $row['period_from'],
                                                'to'   => $row['period_to'],
                                            ]) }}" style="color:#4361ee; text-decoration:none;">
                                        {{ $row['label'] }} 🔍
                                    </a>
                                </td>
                                <td class="text-end c-pos">${{ number_format($row['pos_sales'], 2) }}</td>
                                <td class="text-end c-online">${{ number_format($row['online_sales'], 2) }}</td>
                                <td class="text-end">${{ number_format($row['gross_sales'], 2) }}</td>
                                <td class="text-end c-ship">${{ number_format($row['shipping_fee'], 2) }}</td>
                                <td class="text-end {{ $row['return_sales'] > 0 ? 'text-neg' : 'text-muted' }}">
                                    {{ $row['return_sales'] > 0 ? '-' : '' }}${{ number_format($row['return_sales'], 2) }}
                                </td>
                                <td class="text-end {{ $row['discounts'] > 0 ? 'text-neg' : 'text-muted' }}">
                                    {{ $row['discounts'] > 0 ? '-' : '' }}${{ number_format($row['discounts'], 2) }}
                                </td>
                                <td class="text-end c-net">${{ number_format($row['net_sales'], 2) }}</td>
                                <td class="text-end c-total">${{ number_format($row['total_sales'], 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <div class="empty-icon">📭</div>
                                        <p>No sales data found for the selected date range.</p>
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
</div>

<script>
    // ── Export Excel ──────────────────────────────────────────────────────────
    function exportExcel() {
        const wb = XLSX.utils.book_new();
        const from = '{{ \Carbon\Carbon::parse($from)->format("d M Y") }}';
        const to = '{{ \Carbon\Carbon::parse($to)->format("d M Y") }}';
        const grp = '{{ ucfirst($groupBy) }}';

        function getRows() {
            const tbl = document.getElementById('tbl-summary');
            const out = [];
            tbl.querySelectorAll('tbody tr').forEach(tr => {
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

        function num(v) {
            const n = Number(String(v).replace(/[$,%]/g, '').trim());
            return isNaN(n) ? v : n;
        }

        const rows = getRows();
        // Order matches data-e0..e8: label, gross, return, discounts, pos, online, net, total, shipping
        const cols = [grp, 'Gross Sales ($)', 'Return Sales ($)', 'Net Discounts ($)',
            'POS Sales ($)', 'Online Sales ($)', 'Net Sales ($)', 'Total Sales ($)', 'Shipping Fee ($)'
        ];
        const body = rows.map(r => [r[0], num(r[1]), num(r[2]), num(r[3]), num(r[4]), num(r[5]), num(r[6]), num(r[7]), num(r[8])]);

        const wsData = [
            ['SALES SUMMARY REPORT', '', '', '', '', '', '', '', ''],
            [`Period: ${from} – ${to}  |  Group by: ${grp}`, '', '', '', '', '', '', '', ''],
            ['Generated: ' + new Date().toLocaleString('en-GB'), '', '', '', '', '', '', '', ''],
            [],
            cols,
            ...body,
        ];

        const ws = XLSX.utils.aoa_to_sheet(wsData);
        ws['!cols'] = [{
            wch: 22
        }, {
            wch: 16
        }, {
            wch: 16
        }, {
            wch: 16
        }, {
            wch: 16
        }, {
            wch: 16
        }, {
            wch: 16
        }, {
            wch: 16
        }, {
            wch: 16
        }];
        ws['!merges'] = [{
                s: {
                    r: 0,
                    c: 0
                },
                e: {
                    r: 0,
                    c: 8
                }
            },
            {
                s: {
                    r: 1,
                    c: 0
                },
                e: {
                    r: 1,
                    c: 8
                }
            },
            {
                s: {
                    r: 2,
                    c: 0
                },
                e: {
                    r: 2,
                    c: 8
                }
            },
        ];
        ws['!freeze'] = {
            xSplit: 0,
            ySplit: 5
        };

        // Format numeric columns as currency
        const numCols = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
        const dataStart = 6;
        const dataEnd = dataStart + body.length - 1;
        for (let row = dataStart; row <= dataEnd; row++) {
            numCols.forEach(col => {
                const cell = ws[`${col}${row}`];
                if (cell && typeof cell.v === 'number') cell.z = '"$"#,##0.00';
            });
        }

        XLSX.utils.book_append_sheet(wb, ws, '📊 Sales Summary');
        XLSX.writeFile(wb, `sales-summary-{{ \Carbon\Carbon::parse($from)->format("Ymd") }}-{{ \Carbon\Carbon::parse($to)->format("Ymd") }}.xlsx`);
    }

    // ── Export PDF via hidden iframe ──────────────────────────────────────────
    function exportPDF() {
        const btn = document.querySelector('.btn-pdf');
        const origText = btn.innerHTML;
        btn.innerHTML = '⏳ Generating...';
        btn.disabled = true;

        const from = '{{ \Carbon\Carbon::parse($from)->format("d M Y") }}';
        const to = '{{ \Carbon\Carbon::parse($to)->format("d M Y") }}';
        const grp = '{{ ucfirst($groupBy) }}';
        const now = new Date().toLocaleString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        function getRows() {
            const tbl = document.getElementById('tbl-summary');
            const out = [];
            tbl.querySelectorAll('tbody tr').forEach(tr => {
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

        function fmt(v) {
            const n = parseFloat(String(v).replace(/,/g, ''));
            return isNaN(n) ? (v || '$0.00') : '$' + n.toLocaleString('en', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function neg(v) {
            const n = parseFloat(v);
            return n > 0 ? `-${fmt(v)}` : fmt(v);
        }

        const rows = getRows();
        const summary = rows.find(r => r[0] === 'Summary') || {};
        const dataRows = rows.filter(r => r[0] !== 'Summary');

        // e0 label, e1 gross, e2 return, e3 discounts, e4 pos, e5 online, e6 net, e7 total, e8 shipping
        const tableRows = dataRows.map((r, i) => `
            <tr style="${i%2===0?'background:#fafbfc':''}">
                <td style="font-weight:500;color:#1a1a2e">${r[0]}</td>
                <td class="tr">${fmt(r[1])}</td>
                <td class="tr" style="color:#0891b2;font-weight:600">${fmt(r[8])}</td>
                <td class="tr" style="color:${parseFloat(r[2])>0?'#dc2626':'#bbb'}">${neg(r[2])}</td>
                <td class="tr" style="color:${parseFloat(r[3])>0?'#dc2626':'#bbb'}">${neg(r[3])}</td>
                <td class="tr" style="color:#5b21b6;font-weight:600">${fmt(r[4])}</td>
                <td class="tr" style="color:#c2410c;font-weight:600">${fmt(r[5])}</td>
                <td class="tr" style="color:#1d4ed8;font-weight:700">${fmt(r[6])}</td>
                <td class="tr" style="color:#166534;font-weight:700">${fmt(r[7])}</td>
            </tr>`).join('');

        const html = `<!DOCTYPE html>
        <html><head><meta charset="UTF-8">
        <title>Sales Summary — ${from} to ${to}</title>
        <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:Arial,sans-serif;background:#f0f2f5;padding:20px}
        .page{background:#fff;padding:28px 32px;border-radius:8px}
        .pdf-header{display:flex;justify-content:space-between;align-items:flex-start;
            padding-bottom:14px;border-bottom:2.5px solid #4361ee;margin-bottom:20px}
        .brand-name{font-size:17px;font-weight:700;color:#1a1a2e}
        .brand-sub{font-size:11px;color:#888;margin-top:2px}
        .meta-title{font-size:13px;font-weight:700;color:#1a1a2e;text-align:right}
        .meta-period{font-size:11px;color:#666;margin-top:3px;text-align:right}
        .meta-gen{font-size:10px;color:#bbb;margin-top:2px;text-align:right}
        .kpi-row{display:grid;grid-template-columns:repeat(5,1fr);gap:10px;margin-bottom:22px}
        .kpi-box{background:#fafbfc;border:1px solid #e8eaed;border-radius:8px;padding:11px 13px}
        .kpi-lbl{font-size:9px;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:.05em}
        .kpi-val{font-size:17px;font-weight:700;color:#1a1a2e;margin-top:2px}
        .kpi-hint{font-size:9px;color:#ccc;margin-top:1px}
        .section-title{font-size:10.5px;font-weight:700;color:#1a1a2e;text-transform:uppercase;
            letter-spacing:.06em;border-bottom:1.5px solid #f0f0f0;
            padding-bottom:7px;margin-bottom:12px;
            display:flex;justify-content:space-between;align-items:center}
        .leg-row{display:flex;gap:12px}
        .leg-item{display:flex;align-items:center;gap:4px;font-size:9px;color:#555}
        .leg-dot{width:8px;height:8px;border-radius:50%}
        table{width:100%;border-collapse:collapse;font-size:11px}
        thead th{padding:7px 10px;font-size:9px;font-weight:700;text-transform:uppercase;
            letter-spacing:.04em;color:#999;background:#fafbfc;
            border-bottom:1px solid #eee;white-space:nowrap}
        tbody td{padding:8px 10px;color:#333;border-bottom:.5px solid #f5f5f5}
        tbody tr:last-child td{border-bottom:none}
        .sum-row td{background:#eef0ff!important;font-weight:700;border-bottom:2px solid #c7d2fe}
        .sum-row td:first-child{color:#4361ee;font-size:12px}
        .tr{text-align:right}
        .pdf-footer{border-top:1px solid #eee;padding-top:10px;margin-top:18px;
            display:flex;justify-content:space-between}
        .pdf-footer p{font-size:9px;color:#bbb}
        </style>
        </head><body>
        <div class="page">
        <div class="pdf-header">
            <div>
                <div class="brand-name">📊 Dola Dola Phone Shop</div>
                <div class="brand-sub">Sales Summary Report</div>
            </div>
            <div>
                <div class="meta-title">Sales Summary</div>
                <div class="meta-period">${from} – ${to} · ${grp}</div>
                <div class="meta-gen">Generated: ${now}</div>
            </div>
        </div>

        <div class="kpi-row">
            <div class="kpi-box">
                <div class="kpi-lbl">Gross Sales</div>
                <div class="kpi-val">${fmt(summary[1])}</div>
                <div class="kpi-hint">Before deductions</div>
            </div>
            <div class="kpi-box">
                <div class="kpi-lbl">POS Sales</div>
                <div class="kpi-val" style="color:#5b21b6">${fmt(summary[4])}</div>
                <div class="kpi-hint">In-store revenue</div>
            </div>
            <div class="kpi-box">
                <div class="kpi-lbl">Online Sales</div>
                <div class="kpi-val" style="color:#c2410c">${fmt(summary[5])}</div>
                <div class="kpi-hint">E-commerce revenue</div>
            </div>
            <div class="kpi-box">
                <div class="kpi-lbl">Net Sales</div>
                <div class="kpi-val" style="color:#1d4ed8">${fmt(summary[6])}</div>
                <div class="kpi-hint">After deductions</div>
            </div>
            <div class="kpi-box">
                <div class="kpi-lbl">Total Sales</div>
                <div class="kpi-val" style="color:#166534">${fmt(summary[7])}</div>
                <div class="kpi-hint">Final figure</div>
            </div>
        </div>

        <div class="section-title">
            ${grp} Sales Breakdown &nbsp;·&nbsp; ${from} – ${to}
            <div class="leg-row">
                <div class="leg-item"><div class="leg-dot" style="background:#4361ee"></div> POS</div>
                <div class="leg-item"><div class="leg-dot" style="background:#f97316"></div> Online</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>${grp}</th>
                    <th class="tr">Gross Sales</th>
                    <th class="tr">Shipping Fee</th>
                    <th class="tr">Return Sales</th>
                    <th class="tr">Net Discounts</th>
                    <th class="tr">POS Sales</th>
                    <th class="tr">Online Sales</th>
                    <th class="tr">Net Sales</th>
                    <th class="tr">Total Sales</th>
                </tr>
            </thead>
            <tbody>
                <tr class="sum-row">
                    <td>Summary</td>
                    <td class="tr">${fmt(summary[1])}</td>
                    <td class="tr" style="color:#0891b2;font-weight:700">${fmt(summary[8])}</td>
                    <td class="tr" style="color:#dc2626">${neg(summary[2])}</td>
                    <td class="tr" style="color:#dc2626">${neg(summary[3])}</td>
                    <td class="tr" style="color:#5b21b6;font-weight:700">${fmt(summary[4])}</td>
                    <td class="tr" style="color:#c2410c;font-weight:700">${fmt(summary[5])}</td>
                    <td class="tr" style="color:#1d4ed8;font-weight:700">${fmt(summary[6])}</td>
                    <td class="tr" style="color:#166534;font-weight:700">${fmt(summary[7])}</td>
                </tr>
                ${tableRows}
            </tbody>
        </table>

        <div class="pdf-footer">
            <p>Sales Summary Report · Dola Dola Phone Shop</p>
            <p>${from} – ${to}</p>
        </div>
        </div>
        </body></html>`;

        const iframe = document.createElement('iframe');
        iframe.style.cssText = 'position:fixed;top:-9999px;left:-9999px;width:1px;height:1px;border:none;';
        document.body.appendChild(iframe);
        iframe.onload = () => {
            setTimeout(() => {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
                setTimeout(() => document.body.removeChild(iframe), 2000);
            }, 400);
        };
        iframe.srcdoc = html;

        btn.innerHTML = origText;
        btn.disabled = false;
    }
</script>

@endsection