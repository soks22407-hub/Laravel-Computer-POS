@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <h3>Contact Details</h3>
        <div class="wg-box p-20">
            <p><strong>ID:</strong> {{ $contact->id }}</p>
            <p><strong>Name:</strong> {{ $contact->name }}</p>
            <p><strong>Email:</strong> {{ $contact->email }}</p>
            <p><strong>Phone:</strong> {{ $contact->phone }}</p>
            <p><strong>Message:</strong> {{ $contact->comment }}</p>
            <p><strong>Date:</strong> {{ $contact->created_at }}</p>

            <a href="{{ route('admin.contacts') }}" class="btn btn-secondary mt-20">Back to All Contacts</a>
        </div>
    </div>
</div>
@endsection
