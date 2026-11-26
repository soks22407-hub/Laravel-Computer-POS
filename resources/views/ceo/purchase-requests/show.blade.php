@extends('layouts.ceo')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>📋 Purchase Request #{{ $pr->id }}</h4>
            <div class="d-flex gap-2">
                {{-- Export Buttons --}}
                <a href="{{ route('ceo.purchase-requests.export.excel', $pr->id) }}"
                   class="btn btn-success btn-sm">
                    📗 Export Excel
                </a>
                <a href="{{ route('ceo.purchase-requests.export.pdf', $pr->id) }}"
                   class="btn btn-danger btn-sm">
                    📕 Export PDF
                </a>
                <a href="{{ route('ceo.purchase-requests.index') }}"
                   class="btn btn-secondary btn-sm">← Back</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Request Info --}}
        <div class="wg-box mb-3">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Employee:</strong> {{ $pr->employee->name }}</p>
                    <p><strong>Date:</strong> {{ $pr->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Note:</strong> {{ $pr->note ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong>
                        @if($pr->status === 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($pr->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </p>
                    @if($pr->approvedBy)
                    <p><strong>Reviewed by:</strong> {{ $pr->approvedBy->name }}</p>
                    <p><strong>Reviewed at:</strong> {{ \Carbon\Carbon::parse($pr->approved_at)->format('d/m/Y H:i') }}</p>
                    @endif
                    @if($pr->reject_reason)
                    <p><strong>Reject Reason:</strong>
                        <span class="text-danger">{{ $pr->reject_reason }}</span>
                    </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Items Table --}}
        <div class="wg-box mb-3" style="margin-top: 16px;">
            <h6 class="mb-3">📦 Requested Products</h6>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Variant</th>
                            <th>Current Qty</th>
                            <th>Requested Qty</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pr->items as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->variant ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $item->current_quantity == 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                    {{ $item->current_quantity }}
                                </span>
                            </td>
                            <td><strong>{{ $item->requested_quantity }}</strong></td>
                            <td>{{ $item->reason ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Approve / Reject Buttons --}}
        @if($pr->status === 'pending')
        <div class="wg-box" style="margin-top: 16px;">
            <h6 class="mb-3">👑 Decision</h6>
            <div class="d-flex gap-3">

                {{-- Approve --}}
                <form action="{{ route('ceo.purchase-requests.approve', $pr->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-lg"
                        onclick="return confirm('Approve this request?')">
                        ✅ Approve
                    </button>
                </form>

                {{-- Reject --}}
                <button class="btn btn-danger btn-lg" data-bs-toggle="collapse" 
                    data-bs-target="#rejectForm">
                    ❌ Reject
                </button>
            </div>

            {{-- Reject Form --}}
            <div class="collapse mt-3" id="rejectForm">
                <form action="{{ route('ceo.purchase-requests.reject', $pr->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason for Rejection</label>
                        <textarea name="reject_reason" class="form-control" rows="3"
                            placeholder="Enter reason..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">
                        Confirm Reject
                    </button>
                </form>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection