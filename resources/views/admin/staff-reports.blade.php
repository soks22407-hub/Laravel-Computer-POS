@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding: 5px 0; margin-top: 0;">
            <h4>Staff Reports</h4>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">Staff Reports</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            {{-- Filter form --}}
            <form method="GET" class="row g-3 align-items-end mb-3">
                <div class="col-md-2">
                    <label class="form-label text-uppercase small fw-bold text-muted">Quick Range</label>
                    <select name="preset" class="form-select" onchange="this.form.submit()">
                        <option value="">Custom</option>
                        <option value="today" {{ request('preset') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="this_week" {{ request('preset') == 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="this_month" {{ request('preset') == 'this_month' ? 'selected' : '' }}>This Month</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-uppercase small fw-bold text-muted">Month</label>
                    <select name="month" class="form-select">
                        <option value="">All</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                            @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-uppercase small fw-bold text-muted">Year</label>
                    <select name="year" class="form-select">
                        <option value="">All</option>
                        @for ($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-uppercase small fw-bold text-muted">From Date</label>
                    <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-uppercase small fw-bold text-muted">To Date</label>
                    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                </div>
                <div class="col-md-1 d-flex">
                    <button type="submit" class="btn btn-primary flex-fill">Apply</button>
                </div>
                <div class="col-md-1 d-flex">
                    <a href="{{ route('admin.staff.reports') }}" class="btn btn-outline-secondary flex-fill text-center">Clear</a>
                </div>
            </form>
            <div class="mb-3 d-flex justify-content-end">
                <button type="button" class="btn btn-primary" onclick="printStaffReport()">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>

            {{-- Table --}}
            <div class="wg-table table-all-user">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="staff-report-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;" class="text-center">#</th>
                                <th style="min-width: 200px;">Staff</th>
                                <th class="text-center">Online Orders</th>
                                <th class="text-center">Online Amount</th>
                                <th class="text-center">POS Orders</th>
                                <th class="text-center">POS Amount</th>
                                <th class="text-center no-print" style="width: 150px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($staff as $member)
                            <tr class="align-middle">
                                <td class="text-center">{{ $staff->firstItem() + $loop->index }}</td>
                                <td class="pname" style="min-width: 200px;">
                                    <div class="name">
                                        <span class="body-title-2">{{ $member->name }}</span>
                                    </div>
                                    <div class="text-tiny mt-1">{{ $member->utype }}</div>
                                </td>
                                <td class="text-center">{{ $member->online_orders_count }}</td>
                                <td class="text-end">${{ number_format($member->online_sales ?? 0, 2) }}</td>
                                <td class="text-center">{{ $member->pos_orders_count }}</td>
                                <td class="text-end">${{ number_format($member->pos_sales ?? 0, 2) }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 1rem;">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.staff.report.products', ['id' => $member->id] + request()->query()) }}">
                                                    <i class="icon-eye"></i> View Product
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.staff.report.payments', ['id' => $member->id] + request()->query()) }}">
                                                    <i class="icon-credit-card"></i> View Payment
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <!-- <td style="min-width: 220px;">
                                    @if(isset($paymentBreakdown[$member->id]))
                                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                                        @foreach($paymentBreakdown[$member->id] as $pm)
                                        <span class="badge bg-light text-dark border" style="font-size: 11px;">
                                            {{ ucfirst($pm->payment_method) }}: ${{ number_format($pm->amount, 2) }} ({{ $pm->count }})
                                        </span>
                                        @endforeach
                                    </div>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td> -->

                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No staff found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $staff->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@include('admin.staff.partials.print-script')

<script>
    function printStaffReport() {
        var table = document.getElementById('staff-report-table').cloneNode(true);
        table.querySelectorAll('.no-print').forEach(function(el) {
            el.remove();
        });

        var rangeText = buildRangeText(
            "{{ request('from') }}",
            "{{ request('to') }}",
            "{{ request('month') }}",
            "{{ request('year') }}",
            "{{ request('preset') }}"
        );

        printExcelStyle({
            title: 'Dola Dola Phone Shop',
            subtitle: 'Staff Sales Report',
            meta: rangeText,
            tableHtml: table.outerHTML
        });
    }
</script>
@endsection