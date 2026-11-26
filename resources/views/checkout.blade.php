@extends('layouts.app')
@section('content')
<main class="pt-90">
  <div class="mb-4 pb-4"></div>
  <section class="shop-checkout container">
    <h2 class="page-title">Shipping and Checkout</h2>
    <div class="checkout-steps">
      <a href="{{ route('cart.index') }}" class="checkout-steps__item active">
        <span class="checkout-steps__item-number">01</span>
        <span class="checkout-steps__item-title">
          <span>Shopping Bag</span>
          <em>Manage Your Items List</em>
        </span>
      </a>
      <a href="javascript:void(0)" class="checkout-steps__item active">
        <span class="checkout-steps__item-number">02</span>
        <span class="checkout-steps__item-title">
          <span>Shipping and Checkout</span>
          <em>Checkout Your Items List</em>
        </span>
      </a>
      <a href="javascript:void(0)" class="checkout-steps__item">
        <span class="checkout-steps__item-number">03</span>
        <span class="checkout-steps__item-title">
          <span>Confirmation</span>
          <em>Review And Submit Your Order</em>
        </span>
      </a>
    </div>
    <form name="checkout-form" action="{{route('cart.place.an.order')}}" method="POST">
      @csrf
      <div class="checkout-form">
        <div class="billing-info__wrapper">
          <div class="row">
            <div class="col-6">
              <h4>SHIPPING DETAILS</h4>
            </div>
            <div class="col-6">
            </div>
          </div>
          @if ($address)
          <div class="row">
            <div class="col-md-12">
              <div class="my-account__address-list">
                <div class="my-account__address-list-item">
                  <div class="my-account__address-item__detail">
                    <p>{{$address->name}}</p>
                    <p>{{$address->address}}</p>
                    <p>{{$address->landmark}}</p>
                    <p>{{$address->province}}, {{$address->country}}</p>
                    <br />
                    <p>{{$address->phone}}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @else
          <div class="row mt-5">
            <div class="col-md-6">
              <div class="form-floating my-3">
                <input type="text" class="form-control" name="name" required="" value="{{old('name')}}">
                <label for="name">Full Name *</label>
                @error('name')
                <span class="text-danger">{{$message}}</span>
                @enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating my-3">
                <input type="text" class="form-control" name="phone" required="" value="{{old('phone')}}">
                <label for="phone">Phone Number *</label>
                @error('phone')
                <span class="text-danger">{{$message}}</span>
                @enderror
              </div>
            </div>
            <!-- <div class="col-md-4">
                <div class="form-floating my-3">
                  <input type="text" class="form-control" name="zip" required="" value="{{old('name')}}">
                  <label for="zip">Pincode *</label>
                  <span class="text-danger"></span>
                </div>
              </div> -->
            <!-- <div class="col-md-4">
                <div class="form-floating mt-3 mb-3">
                  <input type="text" class="form-control" name="province" required="" value="{{old('province')}}">
                  <label for="province">Province *</label>
                  <span class="text-danger"></span>
                </div>
              </div> -->
            <div class="col-md-4">
              <div class="form-floating my-3">
                <select class="form-select" name="province" required>
                  <option value="">-- Select Province --</option>
                  @foreach(\App\Models\ShippingZone::where('is_active', true)->orderBy('province')->get() as $z)
                  <option value="{{ $z->province }}" {{ old('province') == $z->province ? 'selected' : '' }}>
                    {{ $z->province }}
                  </option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating my-3">
                <input type="text" class="form-control" name="address" required="" value="{{old('address')}}">
                <label for="address">House no, Building Name *</label>
                @error('address')
                <span class="text-danger">{{$message}}</span>
                @enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating my-3">
                <input type="text" class="form-control" name="locality" required="" value="{{old('locality')}}">
                <label for="locality">Road Name, Area, Colony *</label>
                @error('locality')
                <span class="text-danger">{{$message}}</span>
                @enderror
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-floating my-3">
                <input type="text" class="form-control" name="landmark" required="" value="{{old('landmark')}}">
                <label for="landmark">Landmark *</label>
                @error('landmark')
                <span class="text-danger">{{$message}}</span>
                @enderror
              </div>
            </div>
          </div>
          @endif
        </div>
        @php
        $subtotal = floatval(Cart::instance('cart')->subtotal(0, '', ''));
        $shipping = 3;
        $zone = null;

        if ($address) {
        $zone = \App\Models\ShippingZone::whereRaw('LOWER(TRIM(province)) = ?',
        [strtolower(trim($address->province ?? ''))])
        ->where('is_active', true)
        ->first();

        if ($zone) {
        $shipping = ($zone->free_shipping_above && $subtotal >= $zone->free_shipping_above)
        ? 0
        : $zone->fee;
        }
        }
        $grandTotal = $subtotal + $shipping;
        @endphp
        <div class="checkout__totals-wrapper">
          <div class="sticky-content">
            <div class="checkout__totals">
              <h3>Your Order</h3>
              {{-- Show for new customers (no saved address) --}}
              @if(!$address)
              <div class="alert alert-info py-2 mb-3" style="font-size:13px;">
                💡 Select your province to see the delivery fee.
              </div>
              @endif
              @if($zone)
              <div class="d-inline-flex align-items-center gap-2 px-3 py-2 mb-3 rounded"
                style="background:#e8f4fd; border:1px solid #bee3f8; font-size:14px;">
                🚚
                <span>
                  Delivery to <strong>{{ $zone->province }}</strong>:
                  @if($shipping == 0)
                  <span class="text-success fw-bold">FREE Shipping! 🎉</span>
                  @else
                  <strong>${{ number_format($shipping, 2) }}</strong>
                  @if($zone->free_shipping_above)
                  — Free above <strong>${{ number_format($zone->free_shipping_above, 2) }}</strong>
                  @endif
                  @endif
                </span>
              </div>
              @endif
              {{-- Zone info updates when province selected --}}
              <div id="zone-info" class="d-flex align-items-center gap-2 mb-3 p-2 rounded"
                style="background:#f0f9ff; border:1px solid #bae6fd; font-size:13px; display:none;">
              </div>
              <table class="checkout-cart-items">
                <thead>
                  <tr>
                    <th>PRODUCT</th>
                    <th align="right">SUBTOTAL</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach (Cart::instance('cart') as $item)
                  <tr>
                    <td>
                      {{ $item->name }} x {{ $item->qty }}
                    </td>
                    <td align="right">
                      ${{ $item->subtotal() }}
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
              <table class="checkout-totals">
                <tbody>
                  <tr>
                    <th>SUBTOTAL</th>
                    <td align="right">${{ number_format($subtotal, 2) }}</td>
                  </tr>
                  <tr>
                    <th>SHIPPING</th>
                    <td align="right">
                      <span id="shipping-display">
                        @if($shipping == 0)
                        <span class="text-success fw-bold">FREE</span>
                        @else
                        ${{ number_format($shipping, 2) }}
                        @endif
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <th>TOTAL</th>
                    <td align="right">
                      <span id="total-display">${{ number_format($grandTotal, 2) }}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="checkout__payment-methods">
              <!-- <div class="form-check">
                  <input class="form-check-input form-check-input_fill" type="radio" name="checkout_payment_method"
                    id="checkout_payment_method_1" checked>
                  <label class="form-check-label" for="checkout_payment_method_1">
                    Direct bank transfer
                    <p class="option-detail">
                      Make your payment directly into our bank account. Please use your Order ID as the payment
                      reference.Your order will not be shipped until the funds have cleared in our account.
                    </p>
                  </label>
                </div> -->
              <div class="form-check">
                <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                  id="mode1" value="Cart">
                <label class="form-check-label" for="mode1">
                  Debit or Credit Card
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                  id="mode2" value="ABA">
                <label class="form-check-label" for="mode2">
                  ABA
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                  id="mode3" value="ACELEDA">
                <label class="form-check-label" for="mode3">
                  ACELEDA
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                  id="mode4" value="WING">
                <label class="form-check-label" for="mode4">
                  WING
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                  id="mode5" value="Amret">
                <label class="form-check-label" for="mode5">
                  Amret
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input form-check-input_fill" type="radio" name="mode"
                  id="mode6" value="cod">
                <label class="form-check-label" for="mode6">
                  Cash on delivery
                </label>
              </div>
              <div class="policy-text">
                Your personal data will be used to process your order, support your experience throughout this
                website, and for other purposes described in our <a href="{{ route('legal.privacy') }}" target="_blank">privacy
                  policy</a>.
              </div>
            </div>
            <button class="btn btn-primary btn-checkout">PLACE ORDER</button>
          </div>
        </div>
      </div>
    </form>
  </section>
</main>
@endsection

@push('scripts')
@php
$shippingData = \App\Models\ShippingZone::where('is_active', true)
->get(['province', 'fee', 'free_shipping_above'])
->mapWithKeys(fn($z) => [
$z->province => [
'fee' => (float) $z->fee,
'free_above' => $z->free_shipping_above ? (float) $z->free_shipping_above : null,
]
]);
$cartSubtotalJs = floatval(Cart::instance('cart')->subtotal(0, '', ''));
@endphp

<script>
  const shippingFees = @json($shippingData);
  const subtotal = {{ $cartSubtotalJs }};

  document.addEventListener("DOMContentLoaded", function() {
    const provinceSelect = document.querySelector('select[name="province"]');
    if (!provinceSelect) return;

    function updateShipping() {
      const selected = provinceSelect.value;
      const zone = shippingFees[selected];

      let shipping = 3;
      let label = '$3.00';

      if (zone) {
        if (zone.free_above && subtotal >= zone.free_above) {
          shipping = 0;
          label = '<span class="text-success fw-bold">FREE 🎉</span>';
        } else {
          shipping = zone.fee;
          label = '$' + shipping.toFixed(2);
        }
      }

      const total = subtotal + shipping;
      document.getElementById('shipping-display').innerHTML = label;
      document.getElementById('total-display').textContent = '$' + total.toFixed(2);

      const zoneInfo = document.getElementById('zone-info');
      if (zoneInfo && zone && selected) {
        let info = '🚚 <strong>' + selected + '</strong>: ';
        if (shipping === 0) {
          info += '<span class="text-success fw-bold">FREE Shipping! 🎉</span>';
        } else {
          info += '<strong>$' + zone.fee.toFixed(2) + '</strong>';
          if (zone.free_above) {
            info += ' · Free above <strong>$' + zone.free_above.toFixed(2) + '</strong>';
          }
        }
        zoneInfo.innerHTML = info;
        zoneInfo.style.display = 'flex';
      } else if (zoneInfo) {
        zoneInfo.style.display = 'none';
      }
    }

    provinceSelect.addEventListener('change', updateShipping);
    if (provinceSelect.value) {
      updateShipping();
    }
  });
</script>
@endpush