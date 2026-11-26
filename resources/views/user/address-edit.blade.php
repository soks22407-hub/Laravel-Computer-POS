@extends('layouts.app')
@section('content')
<main class="pt-90">
  <div class="mb-4 pb-4"></div>
  <section class="my-account container">
    <h2 class="page-title">My Address</h2>

    <div class="row">
      <div class="col-lg-2">
        @include('user.account-nav')
      </div>

      <div class="col-lg-10">

        @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <div class="card p-4 shadow-sm">
          <h5 class="mb-4 fw-bold">📍 Edit Shipping Address</h5>

          <form method="POST" action="{{ route('user.address.update', ['id' => $address->id]) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" value="{{ $address->id }}">

            <div class="row">
              {{-- Full Name --}}
              <div class="col-md-6">
                <div class="form-floating mb-3">
                  <input type="text" class="form-control" name="name"
                    value="{{ old('name', $address->name) }}" placeholder="Full Name" required>
                  <label>Full Name *</label>
                  @error('name')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>
              </div>

              {{-- Phone --}}
              <div class="col-md-6">
                <div class="form-floating mb-3">
                  <input type="text" class="form-control" name="phone"
                    value="{{ old('phone', $address->phone) }}" placeholder="Phone" required>
                  <label>Phone Number *</label>
                  @error('phone')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>
              </div>

              {{-- Province Dropdown --}}
              <div class="col-md-4">
                <div class="mb-3">
                  <label class="form-label fw-medium">Province / City *</label>
                  <select class="form-select" name="province" required>
                    <option value="">-- Select Province --</option>
                    @foreach(\App\Models\ShippingZone::where('is_active', true)->orderBy('province')->get() as $z)
                    <option value="{{ $z->province }}"
                      {{ strtolower(trim(old('province', $address->province))) == strtolower(trim($z->province)) ? 'selected' : '' }}>
                      {{ $z->province }}
                      @if($z->free_shipping_above)@endif
                    </option>
                    @endforeach
                  </select>
                  @error('province')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>
              </div>

              {{-- House / Building --}}
              <div class="col-md-4">
                <div class="form-floating mb-3">
                  <input type="text" class="form-control" name="address"
                    value="{{ old('address', $address->address) }}" placeholder="House no" required>
                  <label>House no, Building Name *</label>
                  @error('address')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>
              </div>

              {{-- Locality --}}
              <div class="col-md-4">
                <div class="form-floating mb-3">
                  <input type="text" class="form-control" name="locality"
                    value="{{ old('locality', $address->locality) }}" placeholder="Road Name" required>
                  <label>Road Name, Area *</label>
                  @error('locality')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>
              </div>

              {{-- Landmark --}}
              <div class="col-md-12">
                <div class="form-floating mb-3">
                  <input type="text" class="form-control" name="landmark"
                    value="{{ old('landmark', $address->landmark) }}" placeholder="Landmark" required>
                  <label>Landmark *</label>
                  @error('landmark')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>
              </div>
            </div>

            {{-- Buttons --}}
            <div class="d-flex gap-3 mt-2">
              <button type="submit" class="btn btn-primary px-4">
                💾 Update Address
              </button>
              <a href="{{ route('user.address') }}" class="btn btn-outline-secondary px-4">
                ← Back
              </a>
            </div>

          </form>
        </div>

      </div>
    </div>
  </section>
</main>
@endsection