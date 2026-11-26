@extends('layouts.admin')

@section('content')
<main class="main-content-inner">
    <div class="main-content-wrap">
        <section class="my-account">
            <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding: 5px 0; margin-top: 0;">
                <h5>Add User</h5>
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
                        <div class="text-tiny">Add User</div>
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

                        <form action="{{ route('admin.account.store') }}" method="POST" class="needs-validation" novalidate>
                            @csrf

                            <div class="mb-4">
                                <label class="form-label fw-bold" for="name">Full Name *</label>
                                <input type="text" class="form-control form-control-lg" id="name" name="name"
                                    value="{{ old('name') }}" placeholder="Full Name" required>
                                @error('name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold" for="mobile">Mobile Number *</label>
                                <input type="text" class="form-control form-control-lg" id="mobile" name="mobile"
                                    value="{{ old('mobile') }}" placeholder="Mobile Number" required>
                                @error('mobile') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold" for="email">Email Address *</label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email"
                                    value="{{ old('email') }}" placeholder="Email Address" required>
                                @error('email') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold" for="utype">Account Type *</label>
                                <select class="form-select form-select-lg" id="utype" name="utype" required>
                                    <option value="USR" {{ old('utype') === 'USR' ? 'selected' : '' }}>User</option>
                                    <option value="ADM" {{ old('utype') === 'ADM' ? 'selected' : '' }}>Admin</option>
                                    <option value="EMP" {{ old('utype', 'EMP') === 'EMP' ? 'selected' : '' }}>Employee</option>
                                    <option value="CEO" {{ old('utype') === 'CEO' ? 'selected' : '' }}>CEO</option>
                                </select>
                                @error('utype') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" for="password">Password *</label>
                                    <input type="password" class="form-control form-control-lg" id="password"
                                        name="password" placeholder="Password" required>
                                    @error('password') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" for="password_confirmation">Confirm Password *</label>
                                    <input type="password" class="form-control form-control-lg" id="password_confirmation"
                                        name="password_confirmation" placeholder="Confirm password" required>
                                </div>
                            </div>

                            <div class="d-grid mt-4" style="margin-top: 16px;">
                                <button type="submit" class="btn btn-primary btn-lg">Create User</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
@endsection