@extends('layouts.ceo')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding: 5px 0; margin-top: 0;">
            <h4>Payment Details - {{ $staff->name }}</h4>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('ceo.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <a href="{{ route('ceo.staff.reports') }}">
                        <div class="text-tiny">Staff Reports</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">{{ $staff->name }}</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="mb-3 d-flex justify-content-between">
                <a href="{{ route('ceo.staff.reports') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Staff Reports
                </a>
                <button type="button" class="btn btn-primary" onclick="printPaymentsReport()">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>

            <div class="wg-table table-all-user">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="payments-report-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;" class="text-center">#</th>
                                <th class="text-center" style="width: 180px;">Date</th>
                                <th style="width: 120px;">Invoice No</th>
                                <th style="width: 200px;">Customer</th>
                                <th>Phone</th>
                                <th class="text-center">Source</th>
                                <th class="text-center">Payment Method</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)
                            <tr class="align-middle">
                                <td class="text-center">{{ $payments->firstItem() + $loop->index }}</td>
                                <td class="text-center">{{ $payment->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    INV{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                                </td>
                                <td>{{ $payment->customer_name ?? '-' }}</td>
                                <td>{{ $payment->customer_phone ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $payment->source == 'POS' ? 'bg-primary' : 'bg-info' }}">
                                        {{ $payment->source }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">
                                        {{ ucfirst($payment->payment_method) }}
                                    </span>
                                </td>
                                <td class="text-end">${{ number_format($payment->total, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No payment records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $payments->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@include('ceo.staff.partials.print-script')

<script>
    function printPaymentsReport() {
        var table = document.getElementById('payments-report-table').cloneNode(true);

        // strip badges -> plain text for excel-style output
        table.querySelectorAll('.badge').forEach(function(el) {
            el.outerHTML = el.textContent.trim();
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
            subtitle: 'Payment Details - {{ $staff->name }}',
            meta: rangeText,
            tableHtml: table.outerHTML
        });
    }
</script>
@endsection