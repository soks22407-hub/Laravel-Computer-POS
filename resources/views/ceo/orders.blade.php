@extends('layouts.ceo')
@section('content')
<style>
    .order-id {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        transition: background 0.2s ease;
        cursor: pointer;
    }

    .order-id:hover {
        background-color: #f5f5f5;
    }

    .box-content-search {
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 999;
        background: #fff;
        border-radius: 4px;
        width: 100%;
        max-height: 300px;
        overflow-y: auto;
        margin-top: 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .filter-toggle-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: #fff;
        color: #374151;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .filter-toggle-btn:hover,
    .filter-toggle-btn.active {
        border-color: #6366f1;
        color: #6366f1;
        background: #eef2ff;
    }

    .filter-badge {
        background: #6366f1;
        color: #fff;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 600;
    }

    .filter-panel {
        display: none;
        margin-top: 10px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .filter-panel.open {
        display: block;
    }

    .active-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eef2ff;
        color: #4338ca;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 13px;
    }

    .filter-chip a {
        color: #4338ca;
        text-decoration: none;
        font-weight: bold;
    }


    #box-content-search li {
        list-style: none;
    }

    #box-content-search .user-id {
        margin-bottom: 10px;
    }
</style>
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding: 3px 0; margin-top: 0;">
            <h4>Orders</h4>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('ceo.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Orders</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            @php
            $activeCount = collect(['status','from','to'])
            ->filter(fn($k) => request($k) !== null && request($k) !== '')
            ->count();
            @endphp
            <div class="wg-filter flex-grow" style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <form class="form-search flex-grow" method="GET">
                    <fieldset class="name">
                        <input type="text" placeholder="Search here..." class="show-search" name="name"
                            id="order-search-input" tabindex="2" value="{{ request('name') }}"
                            autocomplete="off" data-search-url="{{ route('ceo.search.order') }}">
                    </fieldset>
                    <div class="button-submit">
                        <button type="submit"><i class="icon-search"></i></button>
                    </div>
                    <div class="box-content-search">
                        <ul id="order-box-content-search"></ul>
                    </div>
                </form>

                <button type="button" id="filter-toggle-btn"
                    class="filter-toggle-btn {{ $activeCount > 0 ? 'active' : '' }}">
                    <i class="icon-filter" style="font-size:14px;"></i>
                    Filters
                    @if($activeCount > 0)
                    <span class="filter-badge">{{ $activeCount }}</span>
                    @endif
                </button>
            </div>

            {{-- Filter Panel --}}
            <div id="filter-panel" class="filter-panel {{ $activeCount > 0 ? 'open' : '' }}">
                <form method="GET" action="{{ route('ceo.orders') }}" class="row g-3 align-items-end p-3">
                    @if(request('name'))
                    <input type="hidden" name="name" value="{{ request('name') }}">
                    @endif
                    <div class="col-md-3">
                        <label class="form-label text-uppercase small fw-bold text-muted">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            @foreach(['ordered','shipped','out_for_delivery','delivered','canceled'] as $s)
                            <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $s)) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-uppercase small fw-bold text-muted">From Date</label>
                        <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-uppercase small fw-bold text-muted">To Date</label>
                        <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">Apply</button>
                        <a href="{{ route('ceo.orders') }}" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </form>
            </div>
            {{-- ── Active Filter Chips ── --}}
            @if($activeCount > 0)
            <div class="active-filters">
                @if(request('status'))
                <span class="filter-chip">
                    Status: {{ ucwords(str_replace('_', ' ', request('status'))) }}
                    <a href="{{ url()->current() . '?' . http_build_query(request()->except(['status','page'])) }}">&times;</a>
                </span>
                @endif
                @if(request('from'))
                <span class="filter-chip">
                    From: {{ request('from') }}
                    <a href="{{ url()->current() . '?' . http_build_query(request()->except(['from','page'])) }}">&times;</a>
                </span>
                @endif
                @if(request('to'))
                <span class="filter-chip">
                    To: {{ request('to') }}
                    <a href="{{ url()->current() . '?' . http_build_query(request()->except(['to','page'])) }}">&times;</a>
                </span>
                @endif
            </div>
            @endif

            <div class="wg-table table-all-user">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="width:70px">OrderNo</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Phone</th>
                                <th class="text-center">Subtotal</th>
                                <th class="text-center">Shipping</th>
                                <th class="text-center">Total</th>

                                <th class="text-center">Status</th>
                                <th class="text-center">Order Date</th>
                                <th class="text-center">Total Items</th>
                                <th class="text-center">Delivered On</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                            <tr>
                                <td class="text-center">{{ $order->id }}</td>
                                <td class="text-center">{{ $order->name }}</td>
                                <td class="text-center">{{ $order->phone }}</td>
                                <td class="text-end">${{ $order->subtotal }}</td>
                                <td class="text-end">${{ $order->shipping }}</td>
                                <td class="text-end">${{ $order->total }}</td>

                                <td class="text-center">
                                    @if ($order->status == 'delivered')
                                    <span class="badge bg-success">Delivered</span>
                                    @elseif ($order->status == 'canceled')
                                    <span class="badge bg-danger">Canceled</span>
                                    @elseif ($order->status == 'ordered')
                                    <span class="badge bg-warning text-dark">Ordered</span>
                                    @elseif ($order->status == 'shipped')
                                    <span class="badge bg-info text-dark">Shipped</span>
                                    @elseif ($order->status == 'out_for_delivery')
                                    <span class="badge bg-primary">Out for Delivery</span>
                                    @else
                                    <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $order->created_at }}</td>
                                <td class="text-center">{{ $order->orderItems->count() }}</td>
                                <td class="text-center">{{ $order->delivered_date }}</td>
                                <td class="text-center">
                                    <a href="{{ route('ceo.order.details', ['order_id'=>$order->id]) }}">
                                        <div class="list-icon-function view-icon">
                                            <div class="item eye">
                                                <i class="icon-eye"></i>
                                            </div>
                                        </div>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        let searchTimeout;
        let currentRequest = null;

        const $input = $("#order-search-input");
        const $results = $("#order-box-content-search");
        const searchUrl = $input.data('search-url');

        $input.on("input", function() {
            let query = $(this).val().trim();

            clearTimeout(searchTimeout);
            if (currentRequest) currentRequest.abort();

            if (query.length < 2) {
                $results.html('');
                return;
            }

            $results.html('<li class="loading">Searching...</li>');

            searchTimeout = setTimeout(function() {
                currentRequest = $.ajax({
                    url: searchUrl,
                    type: 'GET',
                    data: {
                        query: query
                    },
                    dataType: 'json',
                    success: function(orders) {
                        $results.html('');
                        if (!orders || orders.length === 0) {
                            $results.append('<li>No orders found</li>');
                        } else {
                            orders.forEach(function(order, index) {
                                const safeName = $('<div>').text(order.name).html();
                                const safePhone = order.phone ? $('<div>').text(order.phone).html() : '';
                                const orderUrlTemplate = "{{ route('ceo.order.details', ['order_id' => ':id']) }}";
                                const orderUrl = orderUrlTemplate.replace(':id', order.id);

                                $results.append(`
                                <li class="order-id">
                                    <a href="${orderUrl}" class="d-flex items-center gap14 mb-10">
                                        <div class="flex items-center justify-between gap20 flex-grow">
                                            <div class="name">
                                                <span class="body-text">
                                                    <strong>Order #${order.id}</strong> – ${safeName}
                                                </span>
                                                ${safePhone ? `<div class="text-tiny">${safePhone}</div>` : ''}
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                ${index < orders.length - 1 ? '<li><div class="divider"></div></li>' : ''}
                            `);
                            });
                        }
                    },
                    error: function(xhr, status) {
                        if (status !== 'abort') {
                            $results.html('<li class="error">Error loading results</li>');
                        }
                    },
                    complete: function() {
                        currentRequest = null;
                    }
                });
            }, 300);
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#order-search-input, #order-box-content-search').length) {
                $results.html('');
            }
        });
    });

    $(function() {
        $("#filter-toggle-btn").on("click", function() {
            $("#filter-panel").toggleClass("open");
            $(this).toggleClass("active");
        });
    });
</script>
@endpush