@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <h4 class="text-base font-semibold text-gray-600 mb-4 border-b pb-2">Edit Profile</h4>

        @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            {{-- Form Fields --}}
            <div class="wg-box">
                <div class="text-center mt-4 mb-4">
                    <div class="position-relative d-inline-block">

                        <!-- Profile Image -->
                        <img src="{{ $user->profile_photo ? Storage::url('profile_photos/' . $user->profile_photo) : asset('default_photo.png') }}"
                            alt="Photo"
                            class="rounded-circle"
                            style="width: 120px; height: 120px; object-fit: cover;"
                            id="profileImagePreview">

                        <!-- Camera Icon Button -->
                        <label
                            class="position-absolute bg-dark text-white rounded-circle p-2"
                            style="bottom: 0; right: 0; cursor: pointer;">

                            <i class="bi bi-camera-fill"></i> <!-- Bootstrap Icon -->

                            <input type="file" name="profile_photo" class="d-none" accept="image/*" onchange="previewImage(event)">
                        </label>
                    </div>

                    @error('profile_photo')
                    <p class="text-danger small mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <fieldset class="name">
                    <div class="body-title">Name <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="name" name="name"
                        tabindex="0" value="{{ old('name', Auth::user()->name) }}" aria-required="true" required="">
                </fieldset>
                @error('name') <span class="alert alert-danger text-center">{{$message}}</span> @enderror

                <fieldset class="email">
                    <div class="body-title">Email <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="email" name="email"
                        tabindex="0" value="{{ old('email', Auth::user()->email) }}" aria-required="true" required="">
                </fieldset>
                @error('email') <span class="alert alert-danger text-center">{{$message}}</span> @enderror

                <fieldset class="mobile">
                    <div class="body-title">Mobile <span class="tf-color-1">*</span></div>
                    <input class="flex-grow" type="text" placeholder="mobile" name="mobile"
                        tabindex="0" value="{{ old('mobile', Auth::user()->mobile) }}" aria-required="true" required="">
                </fieldset>
                @error('mobile') <span class="alert alert-danger text-center">{{$message}}</span> @enderror

                <fieldset class="password">
                    <div class="body-title">Old Password <span class="tf-color-1">* </span></div>
                    <input type="password" name="old_password" id="old_password" placeholder="Old password"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                </fieldset>
                <!-- @error('password') <span class="alert alert-danger text-center">{{$message}}</span> @enderror -->
                <fieldset class="password">
                    <div class="body-title">New Password * <span class="tf-color-1">* </span></div>
                    <input type="password" name="new_password" id="new_password" placeholder="New password"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                </fieldset>
                <!-- @error('new_password') <span class="alert alert-danger text-center">{{$message}}</span> @enderror -->
                <fieldset class="password">
                    <div class="body-title">Confirm Password *<span class="tf-color-1">* </span></div>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" placeholder="Confirm new password"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                </fieldset>


                {{-- Buttons --}}
                <div class="mt-6 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.profile') }}" class="btn btn-secondary btn-lg">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        // FIX: The ID must match the ID on the <img> tag above.
        const image = document.getElementById('profileImagePreview');

        const file = event.target.files[0];

        if (file) {
            // URL.createObjectURL creates a temporary URL for the file to display it immediately.
            image.src = URL.createObjectURL(file);
        }
    }
</script>
@endsection