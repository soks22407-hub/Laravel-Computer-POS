@extends('layouts.admin')

@section('content')
<div class="container p-6">
    <h3 class="mb-4">Task Details</h3>

    <div class="card">
        <div class="card-body">
            <p><strong>Title:</strong> {{ $task->title }}</p>
            <p><strong>Description:</strong> {{ $task->description ?? 'N/A' }}</p>
            <p><strong>Status:</strong>
                <span class="badge bg-primary">
                    {{ ucfirst(str_replace('_',' ', $task->status)) }}
                </span>
            </p>
            <p><strong>Assigned To:</strong>
                {{ $task->user->name ?? 'Not assigned' }}
            </p>
        </div>
    </div>

    <a href="{{ route('admin.tasks') }}" class="btn btn-secondary mt-3">
        ← Back
    </a>
</div>
@endsection
