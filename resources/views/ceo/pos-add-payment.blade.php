@extends('layouts.ceo')
@section('content')

<div class="main-content-inner">
    <div class="main-content-wrap">
        
        {{-- Header Section --}}
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <div>
                <h3>💵 Customer Return Payment</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10 mt-2">
                    <li><a href="{{ route('ceo.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li><a href="{{ route('ceo.pos.history') }}"><div class="text-tiny">POS History</div></a></li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li><div class="text-tiny">Add Payment</div></li>
                </ul>
            </div>
            <a href="{{ route('ceo.pos.history') }}" class="btn btn-outline-secondary">
                ⬅ Back to History
            </a>
        </div>

        <div class="row g-4">
            {{-- Left Side: Order Debt Summary Card --}}
            <div class="col-md-5">
                <div class="wg-box">
                    <h5>📋 Order Summary</h5>
                    <div class="divider my-3"></div>
                    
                    <table class="table table-sm table-borderless gap-y-2">
                        <tbody>
                            <tr>
                                <td class="text-muted fw-medium ps-0">Customer:</td>
                                <td class="text-end fw-bold text-dark text-capitalize">
                                    {{ $order->customer_name ?? 'Walk-in Customer' }}
                                </td>
                            </tr>
                            @if($order->customer_phone)
                            <tr>
                                <td class="text-muted fw-medium ps-0">Phone Number:</td>
                                <td class="text-end text-dark">{{ $order->customer_phone }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="text-muted fw-medium ps-0">Date & Time:</td>
                                <td class="text-end text-dark">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-medium ps-0">Cashier:</td>
                                <td class="text-end text-dark">{{ $order->cashier->name ?? '—' }}</td>
                            </tr>
                            <tr class="border-top">
                                <td class="text-muted fw-medium ps-0 pt-2">Order Grand Total:</td>
                                <td class="text-end fw-bold text-dark pt-2">${{ number_format($order->total, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-success fw-medium ps-0">Amount Previously Paid:</td>
                                <td class="text-end fw-bold text-success">${{ number_format($order->amount_paid, 2) }}</td>
                            </tr>
                            <tr class="bg-light shadow-sm rounded">
                                <td class="text-danger fw-bold ps-2 py-2">Total Owed Balance:</td>
                                <td class="text-end fw-bold text-danger pe-2 py-2" id="max-owed-display" data-balance="{{ $order->amount_owed }}">
                                    ${{ number_format($order->amount_owed, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Right Side: Payment Collection Input Form --}}
            <div class="col-md-7">
                <div class="wg-box">
                    <h5>💰 Collect Remaining Payment</h5>
                    <div class="divider my-3"></div>

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('ceo.pos.payment.submit', $order->id) }}" method="POST" id="payment-form">
                        @csrf
                        
                        {{-- Payment Input --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Payment Amount Received ($)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-primary text-white fw-bold">$</span>
                                <input type="number" 
                                       name="payment_received" 
                                       id="payment_received" 
                                       class="form-control form-control-lg @error('payment_received') is-invalid @enderror" 
                                       placeholder="0.00" 
                                       step="0.01" 
                                       min="0.01" 
                                       max="{{ $order->amount_owed }}" 
                                       value="{{ old('payment_received', $order->amount_owed) }}" 
                                       required>
                                @error('payment_received')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted mt-1 d-block">
                                Click the field to change if the customer is only making a partial installment payment.
                            </small>
                        </div>

                        {{-- Payment Method Selection --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Payment Method</label>
                            <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                <option value="cash" {{ old('payment_method', $order->payment_method) == 'cash' ? 'selected' : '' }}>CASH</option>
                                <option value="aba" {{ old('payment_method', $order->payment_method) == 'aba' ? 'selected' : '' }}>ABA</option>
                                <option value="wing" {{ old('payment_method', $order->payment_method) == 'wing' ? 'selected' : '' }}>WING</option>
                                <option value="aceleda" {{ old('payment_method', $order->payment_method) == 'aceleda' ? 'selected' : '' }}>ACELEDA</option>
                                <option value="amret" {{ old('payment_method', $order->payment_method) == 'amret' ? 'selected' : '' }}>AMRET</option>
                                <option value="card" {{ old('payment_method', $order->payment_method) == 'card' ? 'selected' : '' }}>CARD</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Order Notes Field --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Order Notes / Payment Remarks</label>
                            <textarea name="note" 
                                      class="form-control @error('note') is-invalid @enderror" 
                                      rows="3" 
                                      placeholder="Add details about this collection (e.g., 'Paid remaining balance via ABA swap' or 'First partial deposit installment'...)">{{ old('note', $order->note) }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted mt-1 d-block">
                                You can edit the existing text or append new details above.
                            </small>
                        </div>

                        {{-- Live Balance Calculation Update Widget --}}
                        <div class="p-3 mb-4 rounded border d-flex justify-content-between align-items-center bg-light">
                            <span class="text-muted fw-medium">Remaining Leftover Debt after this payment:</span>
                            <span class="h5 m-0 fw-bold text-dark" id="live-remaining-display">
                                $0.00
                            </span>
                        </div>

                        {{-- Action Control Buttons --}}
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1 py-3 fw-bold" id="submit-btn">
                                💾 Save Payment Record
                            </button>
                            <a href="{{ route('ceo.pos.history') }}" class="btn btn-outline-secondary btn-lg px-4 py-3">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const paymentInput = document.getElementById("payment_received");
        const liveDisplay = document.getElementById("live-remaining-display");
        const maxOwed = parseFloat(document.getElementById("max-owed-display").getAttribute("data-balance"));
        
        function updateRemainingLiveCalculations() {
            let received = parseFloat(paymentInput.value) || 0;
            
            // Cap inputs from typing more than what's actual debt manually
            if (received > maxOwed) {
                paymentInput.value = maxOwed.toFixed(2);
                received = maxOwed;
            }
            
            const remaining = Math.max(0, maxOwed - received);
            liveDisplay.textContent = "$" + remaining.toFixed(2);
            
            // Switch style color state indicator dynamically
            if (remaining === 0) {
                liveDisplay.className = "h5 m-0 fw-bold text-success";
            } else {
                liveDisplay.className = "h5 m-0 fw-bold text-warning";
            }
        }

        // Add tracking events
        paymentInput.addEventListener("input", updateRemainingLiveCalculations);
        
        // Initial window trigger
        updateRemainingLiveCalculations();
    });
</script>
@endpush