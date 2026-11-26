@extends('layouts.ceo')
@section('content')
<style>
    .badge-cash {
        background: #28a745;
    }

    .badge-aba {
        background: #0066cc;
    }

    .badge-wing {
        background: #ff6600;
    }

    .badge-aceleda {
        background: #cc0000;
    }

    .badge-amret {
        background: #009966;
    }

    .badge-card {
        background: #6f42c1;
    }

    .badge-credit {
        background: #6f42c1;
    }


    .table> :not(caption)>*>* {
        padding: 10px 8px !important;
        font-size: 0.95rem;
    }

    .table th {
        font-weight: 600;
        background-color: #f8f9fa;
    }


    .table-responsive table {
        min-width: 1100px;
        table-layout: auto;
    }

    .customer-cell {
        word-break: break-word;
        display: block;
    }
</style>

<div class="main-content-inner">
    <div class="main-content-wrap">

        {{-- Header --}}
        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding: 5px 0; margin-top: 0;">
            <h6>📋 POS Sale History</h6>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('ceo.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('ceo.pos') }}">
                        <div class="text-tiny">POS</div>
                    </a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">History</div>
                </li>
            </ul>
        </div>

        {{-- Summary Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="wg-chart-default">
                    <div class="flex items-center gap14">
                        <div class="image ic-bg"><i class="icon-shopping-bag"></i></div>
                        <div>
                            <div class="body-text mb-2">Total Sales</div>
                            <h4>{{ $orders->total() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="wg-chart-default">
                    <div class="flex items-center gap14">
                        <div class="image ic-bg"><i class="icon-dollar-sign"></i></div>
                        <div>
                            <div class="body-text mb-2">Today's Revenue</div>
                            <h4>${{ number_format($todayRevenue, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="wg-chart-default">
                    <div class="flex items-center gap14">
                        <div class="image ic-bg"><i class="icon-dollar-sign"></i></div>
                        <div>
                            <div class="body-text mb-2">This Month</div>
                            <h4>${{ number_format($monthRevenue, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="wg-chart-default">
                    <div class="flex items-center gap14">
                        <div class="image ic-bg"><i class="icon-dollar-sign"></i></div>
                        <div>
                            <div class="body-text mb-2">Total Revenue</div>
                            <h4>${{ number_format($totalRevenue, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="wg-box mb-4" style="margin-top: 16px;">
            <form method="GET" action="{{ route('ceo.pos.history') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Search Customer</label>
                    <input type="text" name="search" class="form-control" placeholder="Name or phone..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Payment</label>
                    <select name="payment" class="form-select">
                        <option value="">All</option>
                        @foreach(['cash','aba','wing','aceleda','amret','card','credit'] as $p)
                        <option value="{{ $p }}" {{ request('payment') == $p ? 'selected' : '' }}>
                            {{ strtoupper($p) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label d-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">🔍 Filter</button>
                        <a href="{{ route('ceo.pos.history') }}" class="btn btn-outline-secondary flex-fill d-flex align-items-center justify-content-center">Clear</a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="wg-box" style="margin-top: 16px;">
            @if(session('status'))
            <p class="alert alert-success">{{ session('status') }}</p>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle text-center">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 120px;">Date & Time</th>
                            <th style="min-width: 140px;" class="text-start">Customer</th>
                            <th style="width: 90px;">Items</th>
                            <th>Subtotal</th>
                            <th>Discount</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Paid</th>
                            <th>Change / Owed</th>
                            <th>Returned</th>
                            <th>Cashier</th>
                            <th>Status</th>
                            <th style="width: 90px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $key => $order)
                        <tr>
                            <td>{{ $orders->firstItem() + $key }}</td>
                            <td class="text-nowrap text-start" style="width: 120px;">
                                <div class="fw-medium text-dark" style="font-size: 1.2rem;">{{ $order->created_at->format('d M Y') }}</div>
                                <div class="text-muted small" style="font-size: 1.2rem;">{{ $order->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="text-start" style="min-width: 140px;">
                                <div class="customer-cell">
                                    @if($order->customer_name)
                                    <div class="fw-bold text-dark text-capitalize" style="font-size: 1.2rem;">{{ $order->customer_name }}</div>
                                    @if($order->customer_phone)
                                    <span class="text-muted d-block small" style="font-size: 1rem;">{{ $order->customer_phone }}</span>
                                    @endif
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary px-2 py-1">
                                    {{ $order->items->count() }} item(s)
                                </span>
                            </td>
                            <td class="text-nowrap">${{ number_format($order->subtotal, 2) }}</td>
                            <td class="text-nowrap">
                                @if($order->discount > 0)
                                <span class="text-danger">-${{ number_format($order->discount, 2) }}</span>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-nowrap"><strong>${{ number_format($order->total, 2) }}</strong></td>
                            <td>
                                <span class="badge badge-{{ $order->payment_method }} text-white px-2 py-1">
                                    {{ strtoupper($order->payment_method) }}
                                </span>
                            </td>
                            <td class="text-nowrap">${{ number_format($order->amount_paid, 2) }}</td>
                            {{-- Change / Owed Column --}}
                            <td class="text-nowrap">
                                @if($order->status == 'credit' && $order->amount_owed > 0)
                                <span class="text-danger fw-bold">-${{ number_format($order->amount_owed, 2) }}</span>
                                @elseif($order->status == 'completed' && $order->change_amount > 0)
                                <span class="text-secondary fw-bold">${{ number_format($order->change_amount, 2) }}</span>
                                @else
                                <span class="text-secondary fw-bold">$0.00</span>
                                @endif
                            </td>

                            {{-- Returned Column --}}
                            <td class="text-nowrap">
                                @if($order->status == 'cancelled' && $order->amount_paid > 0)
                                <span class="text-danger fw-bold">${{ number_format($order->amount_paid, 2) }}</span>
                                @elseif($order->amount_returned > 0)
                                <span class="text-secondary fw-bold">+${{ number_format($order->amount_returned, 2) }}</span>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $order->cashier->name ?? '—' }}</td>
                            <td>
                                @if($order->status == 'completed')
                                <span class="badge bg-success px-2 py-1">Completed</span>
                                @elseif($order->status == 'credit')
                                <span class="badge bg-warning text-dark px-2 py-1">Credit</span>
                                @else
                                <span class="badge bg-danger px-2 py-1">Cancelled</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown action-dropdown">
                                    <button class="btn btn-outline-secondary btn-action dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Action
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <a class="dropdown-item text-success" href="{{ route('ceo.pos.payment.receipts', $order->id) }}">
                                                <i class="icon-eye text-success"></i> View
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-primary" href="{{ route('ceo.pos.edit', $order->id) }}">
                                                <i class="icon-edit-3 text-primary"></i> Edit Order
                                            </a>
                                        </li>

                                        @if($order->amount_owed > 0 && $order->status != 'cancelled')
                                        <li>
                                            <a class="dropdown-item text-info fw-semibold" href="{{ route('ceo.pos.payment', $order->id) }}">
                                                <i class="icon-dollar-sign text-info"></i> Add Payment
                                            </a>
                                        </li>
                                        @endif

                                        @if($order->status != 'cancelled')
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form action="{{ route('ceo.pos.cancel', $order->id) }}" method="POST" class="m-0 form-cancel-shortcut">
                                                @csrf @method('PUT')
                                                <button type="submit" class="dropdown-item text-danger cancel-btn">
                                                    <i class="icon-trash-2 text-danger"></i> Delete / Cancel
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="14" class="text-center text-muted py-4">No sales records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $orders->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $(".cancel-btn").on("click", function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            swal({
                title: "Cancel this sale?",
                text: "This will restore the stock back.",
                type: "warning",
                buttons: ["No", "Yes, Cancel it"],
            }).then(function(result) {
                if (result) form.submit();
            });
        });
    });
</script>
@endpush