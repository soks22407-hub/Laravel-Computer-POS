@extends('layouts.employee')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>📋 Purchase Request #{{ $pr->id }}</h4>
            <a href="{{ route('employee.purchase-requests.index') }}" class="btn btn-secondary btn-sm">
                ← Back
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Request Info --}}
        <div class="wg-box mb-3">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Request ID:</strong> #{{ $pr->id }}</p>
                    <p><strong>Date:</strong> {{ $pr->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Note:</strong> {{ $pr->note ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong>
                        @if($pr->status === 'pending')
                            <span class="badge bg-warning text-dark">⏳ Pending</span>
                        @elseif($pr->status === 'approved')
                            <span class="badge bg-success">✅ Approved</span>
                        @else
                            <span class="badge bg-danger">❌ Rejected</span>
                        @endif
                    </p>

                    @if($pr->status === 'approved')
                    <p><strong>Approved by:</strong> {{ $pr->approvedBy->name ?? '—' }}</p>
                    <p><strong>Approved at:</strong> 
                        {{ $pr->approved_at ? \Carbon\Carbon::parse($pr->approved_at)->format('d/m/Y H:i') : '—' }}
                    </p>
                    @endif

                    @if($pr->status === 'rejected')
                    <p><strong>Rejected by:</strong> {{ $pr->approvedBy->name ?? '—' }}</p>
                    <p><strong>Rejected at:</strong>
                        {{ $pr->approved_at ? \Carbon\Carbon::parse($pr->approved_at)->format('d/m/Y H:i') : '—' }}
                    </p>
                    <p><strong>Reason:</strong>
                        <span class="text-danger">{{ $pr->reject_reason ?? '—' }}</span>
                    </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Items Table --}}
        <div class="wg-box">
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
                        @forelse($pr->items as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->variant ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $item->current_quantity == 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                    {{ $item->current_quantity }}
                                </span>
                            </td>
                            <td>
                                <strong class="text-primary">{{ $item->requested_quantity }}</strong>
                            </td>
                            <td>{{ $item->reason ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No items found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Status Message --}}
            <div class="mt-3">
                @if($pr->status === 'pending')
                    <div class="alert alert-warning">
                        ⏳ Your request is <strong>waiting for CEO approval</strong>.
                    </div>
                @elseif($pr->status === 'approved')
                    <div class="alert alert-success">
                        ✅ Your request has been <strong>approved</strong>! 
                        Stock will be updated after delivery.
                    </div>
                @else
                    <div class="alert alert-danger">
                        ❌ Your request was <strong>rejected</strong>.
                        @if($pr->reject_reason)
                            <br>Reason: <strong>{{ $pr->reject_reason }}</strong>
                        @endif
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection