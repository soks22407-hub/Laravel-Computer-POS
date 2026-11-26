@extends('layouts.employee')
@section('content')
<style>
    /* ===== KPI GROUP CARD ===== */
    .kpi-group {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
        overflow: hidden;
        height: 100%;
    }

    .kpi-half {
        padding: 18px 20px;
        position: relative;
    }

    .kpi-half:first-child {
        border-bottom: 1px solid #f1f5f9;
    }

    .kpi-half .kpi-label {
        font-size: 12px;
        color: #94a3b8;
        font-weight: 500;
        margin-bottom: 4px;
    }

    .kpi-half .kpi-value {
        font-size: 22px;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
        margin: 0;
    }

    .kpi-half .kpi-sub {
        font-size: 11px;
        color: #b0bec5;
        margin-top: 2px;
    }

    .kpi-half .kpi-icon {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .icon-blue {
        background: #edf4ff;
        color: #3b82f6;
    }

    .icon-green {
        background: #eafaf2;
        color: #10b981;
    }

    .icon-red {
        background: #ffecef;
        color: #ef4444;
    }

    .icon-purple {
        background: #f3ecff;
        color: #8b5cf6;
    }

    .icon-orange {
        background: #fff4e6;
        color: #f97316;
    }

    .icon-teal {
        background: #e0fdf4;
        color: #14b8a6;
    }

    .icon-indigo {
        background: #eef2ff;
        color: #6366f1;
    }

    .icon-sky {
        background: #e0f2fe;
        color: #0ea5e9;
    }

    /* ===== COMBINED REVENUE CARD ===== */
    .revenue-banner {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        border-radius: 18px;
        padding: 28px 30px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 8px 24px rgba(79, 70, 229, .28);
        min-height: 120px;
    }

    .revenue-banner .rev-label {
        font-size: 15px;
        opacity: .9;
        margin-bottom: 6px;
    }

    .revenue-banner .rev-value {
        font-size: 34px;
        font-weight: 800;
        margin: 0 0 4px;
    }

    .revenue-banner .rev-sub {
        font-size: 13px;
        opacity: .75;
    }

    .revenue-banner .rev-icon {
        font-size: 54px;
        opacity: .85;
    }

    /* ===== CHART BOX ===== */
    .wg-box {
        background: #fff;
        border-radius: 18px;
        padding: 22px 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
        overflow: visible !important;
    }

    .wg-box h5 {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 4px;
    }

    /* Legend */
    .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }

    .dot.t1 {
        background: #3b82f6;
    }

    .dot.t2 {
        background: #f97316;
    }

    .dot.t3 {
        background: #10b981;
    }

    .dot.t4 {
        background: #ef4444;
    }

    .dot.t5 {
        background: #a855f7;
    }

    .legend-row {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 18px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #64748b;
    }

    .legend-item strong {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
    }

    /* ===== TOP SELLERS ===== */
    .top-sellers-card {
        background: #fff;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
        height: 100%;
    }

    .top-sellers-card h6 {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 14px;
    }

    .seller-group-label {
        font-size: 11px;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin: 10px 0 6px;
    }

    .seller-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .seller-item:last-child {
        border-bottom: none;
    }

    .seller-img {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        object-fit: cover;
        background: #f1f5f9;
    }

    .seller-name {
        font-size: 13px;
        color: #334155;
        font-weight: 500;
        flex: 1;
    }

    .seller-badge {
        font-size: 11px;
        background: #f1f5f9;
        color: #64748b;
        border-radius: 6px;
        padding: 2px 7px;
        font-weight: 600;
    }

    /* ===== TOP PRODUCTS ===== */
    .top-products-card {
        background: #fff;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
        height: 100%;
    }

    .top-products-card h6 {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 14px;
    }

    .prod-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .prod-item:last-child {
        border-bottom: none;
    }

    .prod-thumb {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        object-fit: cover;
        background: #f1f5f9;
        flex-shrink: 0;
    }

    .prod-info {
        flex: 1;
        min-width: 0;
    }

    .prod-info .prod-name {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .prod-info .prod-units {
        font-size: 11px;
        color: #94a3b8;
    }

    .prod-price {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
        white-space: nowrap;
    }

    /* ===== ALERT ===== */
    .stock-alert {
        border-radius: 14px;
    }

    /* ===== TODAY SUMMARY ===== */
    .dash-card {
        background: #fff;
        border-radius: 18px;
        padding: 22px 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
        height: 100%;
    }

    #line-chart-8 .apexcharts-canvas,
    #line-chart-8 .apexcharts-canvas svg {
        overflow: visible !important;
    }
</style>

<div class="main-content-inner">
    <div class="main-content-wrap container-fluid py-4">

        @if($lowStockCount > 0)
        <div class="alert alert-warning stock-alert d-flex align-items-center gap-3 mb-4 border-0 shadow-sm">
            <i class="icon-package fs-4"></i>
            <div>
                <strong>{{ $lowStockCount }} product(s)</strong> are low on stock or out of stock!
                <a href="{{ route('employee.stock.alert') }}" class="ms-2 fw-bold text-decoration-none">View Now →</a>
            </div>
        </div>
        @endif

        {{-- ===== ROW 1: Revenue Banner + Chart ===== --}}
        <div class="row g-3 mb-4">

            {{-- Monthly Revenue Chart --}}
            <div class="col-xl-12 col-lg-12">
                <div class="wg-box" style="overflow:visible; padding:22px 8px 22px 14px;">
                    <h5 class="text-center">Monthly Revenue Overview <small class="text-muted" style="font-size:11px;">(Online + POS)</small></h5>
                    <div class="legend-row d-flex justify-content-center flex-wrap gap-3 mb-3">
                        <div class="legend-item"><span class="dot t1"></span> Online <strong>${{ number_format($TotalAmount, 0) }}</strong></div>
                        <div class="legend-item"><span class="dot t2"></span> Pending <strong>${{ number_format($TotalOrderedAmount, 0) }}</strong></div>
                        <div class="legend-item"><span class="dot t3"></span> Delivered <strong>${{ number_format($TotalDeliveredAmount, 0) }}</strong></div>
                        <div class="legend-item"><span class="dot t4"></span> Canceled <strong>${{ number_format($TotalCanceledAmount, 0) }}</strong></div>
                        <div class="legend-item"><span class="dot t5"></span> POS <strong>${{ number_format($posTotalRevenue, 0) }}</strong></div>
                    </div>
                    <div id="line-chart-8"></div>
                </div>
            </div>

        </div>

        {{-- ===== ROW 2: 4 grouped KPI columns ===== --}}
        <div class="row g-3 mb-3 mt-4">

            {{-- Col 1: Total Orders + Total Amount --}}
            <div class="col-xl-3 col-sm-6">
                <div class="kpi-group">
                    <div class="kpi-half">
                        <div class="kpi-icon icon-blue"><i class="icon-shopping-bag"></i></div>
                        <div class="kpi-label">Total Orders</div>
                        <div class="kpi-value">{{ $dashboardDatas[0]->Total }}</div>
                        <div class="kpi-sub">All orders placed</div>
                    </div>
                    <div class="kpi-half">
                        <div class="kpi-icon icon-purple"><i class="icon-dollar-sign"></i></div>
                        <div class="kpi-label">Total Amount</div>
                        <div class="kpi-value">${{ number_format($dashboardDatas[0]->TotalAmount, 2) }}</div>
                        <div class="kpi-sub">All orders revenue</div>
                    </div>
                </div>
            </div>

            {{-- Col 2: Delivered Orders + Delivered Amount --}}
            <div class="col-xl-3 col-sm-6">
                <div class="kpi-group">
                    <div class="kpi-half">
                        <div class="kpi-icon icon-green"><i class="icon-check-circle"></i></div>
                        <div class="kpi-label">Delivered Orders</div>
                        <div class="kpi-value">{{ $dashboardDatas[0]->TotalDelivered }}</div>
                        <div class="kpi-sub">Successfully delivered</div>
                    </div>
                    <div class="kpi-half">
                        <div class="kpi-icon icon-teal"><i class="icon-dollar-sign"></i></div>
                        <div class="kpi-label">Delivered Amount</div>
                        <div class="kpi-value">${{ number_format($dashboardDatas[0]->TotalDeliveredAmount, 2) }}</div>
                        <div class="kpi-sub">Total delivered revenue</div>
                    </div>
                </div>
            </div>

            {{-- Col 3: Canceled Orders + Canceled Amount --}}
            <div class="col-xl-3 col-sm-6">
                <div class="kpi-group">
                    <div class="kpi-half">
                        <div class="kpi-icon icon-red"><i class="icon-x-circle"></i></div>
                        <div class="kpi-label">Canceled Orders</div>
                        <div class="kpi-value">{{ $dashboardDatas[0]->TotalCanceled }}</div>
                        <div class="kpi-sub">Total canceled</div>
                    </div>
                    <div class="kpi-half">
                        <div class="kpi-icon icon-red"><i class="icon-dollar-sign"></i></div>
                        <div class="kpi-label">Canceled Amount</div>
                        <div class="kpi-value">${{ number_format($dashboardDatas[0]->TotalCanceledAmount ?? 0, 2) }}</div>
                        <div class="kpi-sub">Total canceled revenue</div>
                    </div>
                </div>
            </div>

            {{-- Col 4: POS Total Orders + POS Today + POS This Month + POS Total Revenue --}}
            <div class="col-xl-3 col-sm-6">
                <div class="kpi-group">
                    <div class="row g-0 h-100">
                        <div class="col-6" style="border-right:1px solid #f1f5f9; border-bottom:1px solid #f1f5f9;">
                            <div class="kpi-half" style="border-bottom:none;">
                                <div class="kpi-label">POS Orders</div>
                                <div class="kpi-value" style="font-size:18px;">{{ $posTotalOrders }}</div>
                                <div class="kpi-sub">Total</div>
                            </div>
                        </div>
                        <div class="col-6" style="border-bottom:1px solid #f1f5f9;">
                            <div class="kpi-half" style="border-bottom:none;">
                                <div class="kpi-label">POS Today</div>
                                <div class="kpi-value" style="font-size:18px; color:#10b981;">${{ number_format($posTodayRevenue, 0) }}</div>
                                <div class="kpi-sub">Revenue</div>
                            </div>
                        </div>
                        <div class="col-6" style="border-right:1px solid #f1f5f9;">
                            <div class="kpi-half" style="border-bottom:none;">
                                <div class="kpi-label">POS Month</div>
                                <div class="kpi-value" style="font-size:18px; color:#f97316;">${{ number_format($posMonthRevenue, 0) }}</div>
                                <div class="kpi-sub">This month</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="kpi-half" style="border-bottom:none;">
                                <div class="kpi-label">POS Total</div>
                                <div class="kpi-value" style="font-size:18px; color:#6366f1;">${{ number_format($posTotalRevenue, 0) }}</div>
                                <div class="kpi-sub">All time</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        {{-- ===== ROW 3: Top Sellers + POS Top Products ===== --}}
        <div class="row g-3 mb-3 mt-4">

            <div class="col-xl-6 col-lg-12">
                <div class="top-sellers-card h-100">

                    ```
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">🏆 Top Product</h6>

                        <button class="btn btn-sm btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#topProductsModal">
                            Details
                        </button>
                    </div>

                    @foreach($topOverall->take(3) as $index => $item)
                        <div class="seller-item">
                            <span class="me-2 fw-bold">
                                #{{ $index + 1 }}
                            </span>

                            @if($item->product && $item->product->image)
                                <img src="{{ asset('uploads/products/'.$item->product->image) }}"
                                    class="seller-img" alt="">
                            @endif

                            <span class="seller-name">
                                {{ Str::limit($item->product->name ?? 'N/A', 25) }}
                            </span>

                            <span class="seller-badge">
                                {{ $item->total_qty }} sold
                            </span>
                        </div>
                    @endforeach

                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Best selling product of all time
                        </small>
                    </div>
                    

                </div>
                ```

            </div>



            {{-- Today's Summary --}}
            <div class="col-xl-6 col-lg-12">
                <div class="dash-card" style="height:100%;">
                    <div style="font-size:13px;font-weight:700;color:#1e293b;margin-bottom:16px;">📅 Today's Summary</div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span style="font-size:13px;color:#64748b;">Orders Today</span>
                        <strong style="font-size:14px;color:#1e293b;">{{ $todayOrders }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span style="font-size:13px;color:#64748b;">Revenue Today</span>
                        <strong style="font-size:14px;color:#10b981;">${{ number_format($todayRevenue, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span style="font-size:13px;color:#64748b;">POS Today</span>
                        <strong style="font-size:14px;color:#3b82f6;">${{ number_format($posTodayRevenue, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span style="font-size:13px;color:#64748b;">Delivered Today</span>
                        <strong style="font-size:14px;color:#8b5cf6;">{{ $todayDelivered }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span style="font-size:13px;color:#64748b;">Pending Orders</span>
                        <strong style="font-size:14px;color:#f97316;">{{ $dashboardDatas[0]->TotalOrdered ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span style="font-size:13px;color:#64748b;">Combined Revenue</span>
                        <strong style="font-size:14px;color:#4f46e5;">${{ number_format($dashboardDatas[0]->TotalAmount + $posTotalRevenue, 2) }}</strong>
                    </div>
                </div>
            </div>

        </div>



    </div>
</div>
<div class="modal fade" id="topProductsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">🏆 Top Products Report</h5>
                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">

                <h6>Top Product - Overall</h6>

                @foreach($topOverall as $item)
                <div class="seller-item mb-2">
                    <span>{{ $item->product->name ?? 'N/A' }}</span>
                    <span class="badge bg-primary">
                        {{ $item->total_qty }} sold
                    </span>
                </div>
                @endforeach

                <hr>

                <h6>Top Product - This Month</h6>

                @foreach($topMonth as $item)
                <div class="seller-item mb-2">
                    <span>{{ $item->product->name ?? 'N/A' }}</span>
                    <span class="badge bg-success">
                        {{ $item->total_qty }} sold
                    </span>
                </div>
                @endforeach

                <hr>

                <h6>POS Top Products</h6>

                @foreach($posTop as $pi)
                <div class="seller-item mb-2">
                    <span>{{ $pi->product->name ?? 'N/A' }}</span>
                    <span class="badge bg-warning">
                        x{{ $pi->qty }}
                    </span>
                </div>
                @endforeach

            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        var amountData    = [{{ $AmountM }}];
        var deliveredData = [{{ $DeliveredAmountM }}];
        var orderedData   = [{{ $OrderedAmountM }}];
        var canceledData  = [{{ $CanceledAmountM }}];
        var posData       = [{{ $PosAmountM }}];

        var el = document.getElementById('line-chart-8');
        if (el && typeof ApexCharts !== 'undefined') {
            new ApexCharts(el, {
                series: [{
                        name: 'Online',
                        data: amountData
                    },
                    {
                        name: 'Pending',
                        data: orderedData
                    },
                    {
                        name: 'Delivered',
                        data: deliveredData
                    },
                    {
                        name: 'Canceled',
                        data: canceledData
                    },
                    {
                        name: 'POS',
                        data: posData
                    }
                ],
                chart: {
                    type: 'line',
                    height: 280,
                    width: '100%',
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    },
                    offsetX: 0
                },
                stroke: {
                    curve: 'smooth',
                    width: 2.5
                },
                markers: {
                    size: 1,
                    hover: {
                        size: 7
                    }
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: false
                },
                colors: ['#3b82f6', '#f97316', '#10b981', '#ef4444', '#a855f7'],
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '11px'
                        },
                        rotate: 0,
                        trim: false,
                        hideOverlappingLabels: false
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                },
                yaxis: {
                    show: false
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,
                    padding: {
                        left: 10,
                        right: 10,
                        top: 10,
                        bottom: 10
                    }
                },
                tooltip: {
                    y: {
                        formatter: v => '$ ' + v.toLocaleString()
                    }
                }
            }).render();
        }
    });
</script>
@endpush