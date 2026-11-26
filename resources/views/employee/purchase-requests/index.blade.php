@extends('layouts.employee')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-3">
            <h4>📋 My Purchase Requests</h4>
            <a href="{{ route('employee.purchase-requests.create') }}" class="btn btn-primary">
                + New Request
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="wg-box">
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Total Items</th>
                            <th>Note</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $pr)
                        <tr>
                            <td>{{ $pr->id }}</td>
                            <td>{{ $pr->created_at->format('d/m/Y') }}</td>
                            <td>{{ $pr->items->count() }} items</td>
                            <td>{{ $pr->note ?? '—' }}</td>
                            <td>
                                @if($pr->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($pr->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('employee.purchase-requests.show', $pr->id) }}" 
                                   class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
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