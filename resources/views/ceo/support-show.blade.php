@extends('layouts.ceo')

@section('content')
<div class="container mt-4">
    <h4 class="mb-4 fw-bold">User Details</h4>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="row mb-3">
                <div class="col-sm-2 text-muted fs-5">User ID :</div>
                <div class="col-sm-9 fw-semibold fs-5">{{ $user->id }}</div>
            </div>
            <hr>

            <div class="row mb-3">
                <div class="col-sm-2 text-muted fs-5">Name :</div>
                <div class="col-sm-9 fw-semibold fs-5">{{ $user->name }}</div>
            </div>
            <hr>

            <div class="row mb-3">
                <div class="col-sm-2 text-muted fs-5">Email :</div>
                <div class="col-sm-9 fw-semibold fs-5">{{ $user->email }}</div>
            </div>
            <hr>

            <div class="row">
                <div class="col-sm-2 text-muted fs-5">Registered At :</div>
                <div class="col-sm-9 fw-semibold fs-5">{{ $user->created_at->format('d M Y H:i') }}</div>
            </div>
        </div>
    </div>

    <div style="margin-top: 16px;">
        <a href="{{ route('ceo.support') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Back to Users
        </a>
    </div>
</div>
@endsection