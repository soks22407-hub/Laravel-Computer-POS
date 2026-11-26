@extends('layouts.employee')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-2">
            <h4 class="mb-0">Payment Receipt</h4>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('employee.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('employee.pos.history') }}">
                        <div class="text-tiny">Pos History</div>
                    </a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">Payment Receipt</div>
                </li>
            </ul>
        </div>

        @if(session('status'))
        <p class="alert alert-success">{{ session('status') }}</p>
        @endif

        <div class="wg-box mt-2">
            <div class="flex items-center justify-between gap10 flex-wrap mb-3">
                <div class="wg-filter flex-grow">
                    <h5>INV{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} — {{ $order->customer_name ?? 'Walk-in Customer' }}</h5>
                </div>
                <button type="button" class="tf-button style-2 w208" data-bs-toggle="modal" data-bs-target="#receiptModal">
                    <i class="icon-eye"></i> View Full Receipt
                </button>
                <a class="tf-button style-2 w208" href="{{ route('employee.pos.history') }}">Back</a>
            </div>

            <table class="table table-bordered table-striped" style="font-size:13px;">
                <thead>
                    <tr>
                        <th class="text-start">Date</th>
                        <th class="text-center">Method</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Received By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->payments as $payment)
                    <tr>
                        <td>{{ $payment->created_at->format('d M Y, h:i A') }}</td>
                        <td class="text-center">{{ strtoupper($payment->payment_method) }}</td>
                        <td class="text-end">${{ number_format($payment->amount, 2) }}</td>
                        <td class="text-end">{{ $payment->receivedBy->name ?? '—' }}</td>
                    </tr>
                    @if($payment->note)
                    <tr>
                        <td colspan="4" style="font-size:11px; color:#888;">Note: {{ $payment->note }}</td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No payment records found.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($order->payments->count() > 0)
                <tfoot>
                    <tr class="table-light">
                        <td colspan="2" class="text-end"><strong>Total Repaid</strong></td>
                        <td class="text-end"><strong>${{ number_format($order->payments->sum('amount'), 2) }}</strong></td>
                        <td></td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="2" class="text-end"><strong>Remaining Balance</strong></td>
                        <td class="text-end text-danger"><strong>${{ number_format($order->amount_owed, 2) }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <style>
                @media print {
                    body * {
                        visibility: hidden;
                    }

                    #receiptModal,
                    #receiptModal .modal-dialog,
                    #receiptModal .modal-content,
                    #receiptModal .modal-body,
                    #receiptModal .modal-body * {
                        visibility: visible;
                    }

                    #receiptModal {
                        display: block !important;
                        position: absolute;
                        left: 0;
                        top: 0;
                        width: 100%;
                        background: #fff !important;
                    }

                    #receiptModal .modal-dialog {
                        max-width: 100%;
                        margin: 0;
                    }

                    #receiptModal .modal-header,
                    #receiptModal .modal-footer {
                        display: none !important;
                    }
                }
            </style>

            <div class="modal-header">
                <h5 class="modal-title">🧾 Receipt #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="text-center mb-3">
                    <h5 class="mb-0">🏪 Dolla Dolla Phone Shop</h5>
                    <small class="text-muted">Receipt #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</small><br>
                    <small class="text-muted">{{ $order->created_at->format('d M Y, h:i A') }}</small>
                    @if($order->cashier)
                    <br><small class="text-muted">Cashier: {{ $order->cashier->name }}</small>
                    @endif
                </div>
                <hr>
                <div>
                    <strong>Invoice No:</strong>
                    INV{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                </div>
                @if($order->customer_name)
                <div><strong>Customer:</strong> {{ $order->customer_name }}</div>
                @endif
                @if($order->customer_phone)
                <div><strong>Phone:</strong> {{ $order->customer_phone }}</div>
                @endif
                <hr>
                <table class="w-100">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td style="font-size:12px;">{{ $item->product_name }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">${{ number_format($item->price, 2) }}</td>
                            <td class="text-end">${{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <hr>
                <table class="w-100">
                    <tr>
                        <td>Subtotal</td>
                        <td class="text-end">${{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    @if($order->discount > 0)
                    <tr>
                        <td>Discount</td>
                        <td class="text-end text-danger">-${{ number_format($order->discount, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>TOTAL</strong></td>
                        <td class="text-end"><strong>${{ number_format($order->total, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td>Paid ({{ strtoupper($order->payment_method) }})</td>
                        <td class="text-end">${{ number_format($order->amount_paid, 2) }}</td>
                    </tr>
                    @php
                    $firstPayment = $order->amount_paid - $order->payments->sum('amount');
                    @endphp
                    <tr>
                        <td>First Paid</td>
                        <td class="text-end">${{ number_format($firstPayment, 2) }}</td>
                    </tr>
                    @if($order->amount_owed > 0)
                    <tr>
                        <td><strong class="text-danger">📒 Amount Owed</strong></td>
                        <td class="text-end text-danger">
                            <strong>${{ number_format($order->amount_owed, 2) }}</strong>
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td><strong>Change</strong></td>
                        <td class="text-end text-success">
                            <strong>${{ number_format($order->change_amount, 2) }}</strong>
                        </td>
                    </tr>
                    @endif
                </table>
                @if($order->payments && $order->payments->count() > 0)
                <hr>
                <div class="mb-2"><strong>📒 Payment History</strong></div>
                <table class="w-100" style="font-size:12px;">
                    <thead>
                        <tr>
                            <th class="text-start">Date</th>
                            <th class="text-center">Method</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->payments as $payment)
                        <tr>
                            <td>{{ $payment->created_at->format('d M Y, h:i A') }}</td>
                            <td class="text-center">{{ strtoupper($payment->payment_method) }}</td>
                            <td class="text-end">${{ number_format($payment->amount, 2) }}</td>
                        </tr>
                        @if($payment->note)
                        <tr>
                            <td colspan="3" style="font-size:11px; color:#888;">Note: {{ $payment->note }}</td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
                <div class="text-end mt-1">
                    @if($order->amount_owed > 0)
                    <strong class="text-danger">Remaining Balance: ${{ number_format($order->amount_owed, 2) }}</strong>
                    @else
                    <strong class="text-success">✅ Fully Paid</strong>
                    @endif
                </div>
                @endif
                <hr>
                @if($order->note)
                <div><small>Note: {{ $order->note }}</small></div>
                <hr>
                @endif
                <div class="text-center">
                    <small>Thank you for shopping with us! 🙏</small>
                </div>
            </div>

            <div class="modal-footer">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fa-solid fa-print me-1"></i> Print
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
@endsection