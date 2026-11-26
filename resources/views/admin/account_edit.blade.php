@extends('layouts.admin')

@section('content')
<style>

</style>
<main class="main-content-inner">
    <div class="main-content-wrap">
        <section class="my-account">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Account Details</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{route ('admin.index')}}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <a href="{{route ('admin.user.account')}}">
                            <div class="text-tiny">User</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Edit User</div>
                    </li>
                </ul>
            </div>
            <div class="wg-box">
                <div class="col-lg-19">
                    <div class="card shadow-sm p-4">
                        {{-- Success message --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Error message --}}
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('admin.account.update', ['id' => $user->id]) }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" value="{{ $user->id }}">

                            <!-- <div class="form-group mt-2">
                                <label>Profile Image</label><br>

                                <img src="{{ $user->profile_photo ? asset('storage/profile/'.$user->profile_photo) : asset('default.png') }}"
                                    width="90" class="rounded mb-2">

                                <input type="file" name="profile_photo" class="form-control">
                            </div> -->

                            <div class="mb-4">
                                <label class="form-label fw-bold" for="name">Full Name *</label>
                                <input type="text" class="form-control form-control-lg" id="name" name="name"
                                    value="{{ old('name', $user->name) }}" placeholder="Full Name" required>
                                @error('name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold" for="mobile">Mobile Number *</label>
                                <input type="text" class="form-control form-control-lg" id="mobile" name="mobile"
                                    value="{{ old('mobile', $user->mobile) }}" placeholder="Mobile Number" required>
                                @error('mobile') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold" for="email">Email Address *</label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email"
                                    value="{{ old('email', $user->email) }}" placeholder="Email Address" required>
                                @error('email') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- Admin Only Role --}}
                            @if(auth()->user()->utype === 'ADM')
                                <div class="mb-4">
                                    <label class="form-label fw-bold" for="utype">Account Type *</label>
                                    <select class="form-select form-select-lg" id="utype" name="utype" required>
                                        <option value="USR" {{ $user->utype === 'USR' ? 'selected' : '' }}>User</option>
                                        <option value="ADM" {{ $user->utype === 'ADM' ? 'selected' : '' }}>Admin</option>
                                        <option value="EMP" {{ $user->utype === 'EMP' ? 'selected' : '' }}>Employee</option>
                                        <option value="CEO" {{ $user->utype === 'CEO' ? 'selected' : '' }}>CEO</option>
                                    </select>
                                </div>
                            @endif

                            <hr class="my-4">

                            <!-- <h5 class="mb-3 fw-bold">Change Password</h5> -->

                            <div class="mb-3">
                                <label class="form-label fw-bold" for="old_password">Old Password *</label>
                                <input type="password" class="form-control form-control-lg" id="old_password"
                                    name="old_password" placeholder="Old password">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" for="new_password">New Password *</label>
                                    <input type="password" class="form-control form-control-lg" id="new_password"
                                        name="new_password" placeholder="New password">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" for="new_password_confirmation">Confirm New Password *</label>
                                    <input type="password" class="form-control form-control-lg" id="new_password_confirmation"
                                        name="new_password_confirmation" placeholder="Confirm new password">
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
@endsection
