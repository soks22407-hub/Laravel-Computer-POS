@extends('layouts.ceo')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding: 5px 0; margin-top: 0;">
            <h4 class="mb-3">📋 Purchase Requests</h4>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('ceo.stock.alert') }}">
                        <div class="text-tiny">Stock Alert</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">Purchase Requests</div>
                </li>
            </ul>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Summary Badges --}}
        <div class="d-flex gap-3 mb-4 flex-wrap">
            <div class="wg-box px-4 py-3 text-center" style="min-width:140px;">
                <div class="fs-4 fw-bold text-warning">
                    {{ $requests->where('status','pending')->count() }}
                </div>
                <div class="text-muted text-tiny">Pending</div>
            </div>
            <div class="wg-box px-4 py-3 text-center" style="min-width:140px;">
                <div class="fs-4 fw-bold text-success">
                    {{ $requests->where('status','approved')->count() }}
                </div>
                <div class="text-muted text-tiny">Approved</div>
            </div>
            <div class="wg-box px-4 py-3 text-center" style="min-width:140px;">
                <div class="fs-4 fw-bold text-danger">
                    {{ $requests->where('status','rejected')->count() }}
                </div>
                <div class="text-muted text-tiny">Rejected</div>
            </div>
        </div>

        <div class="wg-box" style="margin-top: 16px;">
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('ceo.stock.alert') }}"
                    class="btn btn-secondary btn-sm">← Back</a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Note</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $pr)
                        <tr>
                            <td>{{ $pr->id }}</td>
                            <td>{{ $pr->employee->name }}</td>
                            <td>{{ $pr->created_at->format('d/m/Y') }}</td>
                            <td>{{ $pr->items->count() }} items</td>
                            <td>{{ Str::limit($pr->note, 40) ?? '—' }}</td>
                            <td>
                                @if($pr->status === 'pending')
                                <span style="color:#f59e0b;font-weight:600;font-size:13px;">
                                    <i class="icon-clock"></i> Pending
                                </span>
                                @elseif($pr->status === 'approved')
                                <span style="color:#10b981;font-weight:600;font-size:13px;">
                                    <i class="icon-check-circle"></i> Approved
                                </span>
                                @else
                                <span style="color:#f43f5e;font-weight:600;font-size:13px;">
                                    <i class="icon-x-circle"></i> Rejected
                                </span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{-- Change route prefix to ceo. for ceo view --}}
                                <a href="{{ route('ceo.purchase-requests.show', $pr->id) }}"
                                    class="btn btn-sm px-3"
                                    style="background:#0ea5e9;color:#fff;border:none;border-radius:6px;
                                        font-weight:500;letter-spacing:.3px;box-shadow:0 1px 3px rgba(14,165,233,.3);">
                                    <i class="icon-eye" style="font-size:13px;"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No purchase requests yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection