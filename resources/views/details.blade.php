@extends('layouts.app')
@section('content')
<style>
  .specs-table {
    border-collapse: collapse;
    font-size: 14px;
  }

  .specs-group-title {
    background-color: #f0f0f0;
    padding: 8px 12px;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.05em;
    color: #333;
    border-top: 2px solid #ddd;
    text-align: left;
  }

  .specs-table tbody tr {
    border-bottom: 1px solid #eee;
  }

  .specs-table tbody tr:hover {
    background-color: #fafafa;
  }

  .specs-label {
    padding: 8px 12px;
    font-weight: 600;
    color: #555;
    width: 200px;
    min-width: 150px;
    vertical-align: top;
    white-space: nowrap;
  }

  .specs-value {
    padding: 8px 12px;
    color: #333;
    vertical-align: top;
  }

  .filled-heart {
    color: orange;
  }

  .breadcrumb a {
    font-size: 16px;
    font-weight: 600;
    text-transform: uppercase;
  }

  .breadcrumb-separator {
    font-size: 18px;
    font-weight: 600;
    padding: 0 6px;
  }

  /* Fix image appearing under logo/header */
  .pt-90 {
    padding-top: 110px !important;
  }

  .stock-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 4px;
  }

  .stock-badge.out-of-stock {
    background-color: #ffe5e5;
    color: #c0392b;
    border: 1px solid #f5c6cb;
  }

  .stock-badge.low-stock {
    background-color: #fff4e0;
    color: #e67e22;
    border: 1px solid #ffd8a8;
  }

  /* thumbnail overlay wrapper */
  .variant-thumb-wrapper {
    position: relative;
    display: inline-block;
  }

  .variant-thumb-overlay {
    position: absolute;
    bottom: 4px;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 9px;
    font-weight: 700;
    padding: 2px 0;
    border-radius: 0 0 4px 4px;
    pointer-events: none;
  }

  .variant-thumb-overlay.out {
    background: rgba(192, 57, 43, 0.82);
    color: #fff;
  }

  .variant-thumb-overlay.low {
    background: rgba(230, 126, 34, 0.82);
    color: #fff;
  }

  /* Bottom variant thumbnails - uniform size */
  #thumbnailWrapper {
    margin-top: 16px;
  }

  #thumbnailWrapper .product-single__image-item {
    width: 104px;
    height: 104px;
    overflow: hidden;
    border-radius: 6px;
    flex-shrink: 0;
  }

  #thumbnailWrapper .variant-thumb {
    width: 104px !important;
    height: 104px !important;
    object-fit: cover !important;
    border-radius: 4px;
    display: block;
    border: 2px solid transparent;
    transition: border-color 0.2s;
  }

  /* Left vertical thumbnails */
  .product-single__thumbnail .swiper-slide.product-single__image-item {
    overflow: hidden;
  }

  .product-single__thumbnail .swiper-slide img {
    width: 104px !important;
    height: 104px !important;
    object-fit: cover !important;
    display: block;
  }

  /* Main large image - fit the whole image */
  .product-single__image img {
    width: 100% !important;
    height: 550px !important;
    object-fit: contain !important;
    display: block;
    background: #f8f8f8;
  }

  /* Fix the swiper container height too */
  .product-single__image .swiper-container {
    height: 550px;
  }

  .product-single__image .swiper-slide {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 550px;
    overflow: hidden;
  }

  .product-single__image-item img {
    max-width: 100%;
    max-height: 550px;
    width: auto !important;
    height: auto !important;
    object-fit: contain !important;
    display: block;
    margin: 0 auto;
  }

  /* Star rating picker */
  .star-pick-wrap {
    display: flex !important;
    flex-direction: row !important;
    gap: 4px !important;
    width: auto !important;
    max-width: 160px !important;
    align-items: center !important;
  }

  .star-pick-wrap .star-btn {
    font-size: 26px !important;
    color: #ccc !important;
    background: none !important;
    border: none !important;
    padding: 0 !important;
    margin: 0 !important;
    line-height: 1 !important;
    width: 26px !important;
    height: 26px !important;
    flex: 0 0 26px !important;
    cursor: pointer !important;
    display: inline-block !important;
  }

  .star-pick-wrap .star-btn.active {
    color: #f5a623 !important;
  }

  .rev-star-item {
    fill: #ccc !important;
  }
</style>
<main class="pt-90">
  <div class="mb-md-1 pb-md-3"></div>
  <section class="product-single container">
    <div class="row">
      <div class="col-lg-7">
        <div class="product-single__media" data-media-type="vertical-thumbnail">
          <div class="product-single__image">
            <div class="swiper-container">
              <div class="swiper-wrapper">

                <div class="swiper-slide product-single__image-item">
                  <img id="mainProductImage" loading="lazy" class="h-auto" src="{{ asset('uploads/products') }}/{{$product->image}}" width="674"
                    height="674" alt="" />
                  <a id="mainProductLink" data-fancybox="gallery" href="{{ asset('uploads/products') }}/{{$product->image}}" data-bs-toggle="tooltip"
                    data-bs-placement="left" title="Zoom">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <use href="#icon_zoom" />
                    </svg>
                  </a>
                </div>

                @foreach (explode(",", $product->images) as $gimg)
                <div class="swiper-slide product-single__image-item">
                  <img loading="lazy" class="h-auto" src="{{ asset('uploads/products') }}/{{$gimg}}" width="674"
                    height="674" alt="" />
                  <a data-fancybox="gallery" href="{{ asset('uploads/products') }}/{{$gimg}}" data-bs-toggle="tooltip"
                    data-bs-placement="left" title="Zoom">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <use href="#icon_zoom" />
                    </svg>
                  </a>
                </div>
                @endforeach

              </div>
              <div class="swiper-button-prev"><svg width="7" height="11" viewBox="0 0 7 11"
                  xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_prev_sm" />
                </svg></div>
              <div class="swiper-button-next"><svg width="7" height="11" viewBox="0 0 7 11"
                  xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_next_sm" />
                </svg></div>
            </div>
          </div>

          <div class="product-single__thumbnail">
            <div class="swiper-container">
              <div class="swiper-wrapper" id="variantSwiperWrapper">
                @foreach ($product->variants as $variant)

                <!-- Main variant image -->
                <div class="swiper-slide product-single__image-item">
                  <img loading="lazy"
                    class="h-auto"
                    src="{{ asset('uploads/variants/Thumbnails/'.$variant->image) }}"
                    width="104"
                    height="104"
                    alt="">
                </div>

                <!-- Variant gallery images -->
                @foreach ($variant->images ?? [] as $gimg)
                <div class="swiper-slide product-single__image-item">
                  <img loading="lazy"
                    class="h-auto"
                    src="{{ asset('uploads/variants/Thumbnails/'.$gimg) }}"
                    width="104"
                    height="104"
                    alt="">
                </div>
                @endforeach

                @endforeach

              </div>
            </div>
          </div>
        </div>
        <div id="thumbnailWrapper" class="product-single__thumbnail d-flex flex-wrap gap-4 justify-content-center">
          @foreach ($product->variants as $variant)
          <div class="product-single__image-item">
            <div class="variant-thumb-wrapper">
              <img
                class="variant-thumb h-auto"
                src="{{ asset('uploads/variants/'.$variant->image) }}"
                width="104" height="104"
                data-variant-id="{{ $variant->id }}"
                data-price="{{ $variant->price }}"
                data-image="{{ asset('uploads/variants/'.$variant->image) }}"
                data-gallery='@json($variant->images ?? [])'
                data-quantity="{{ $variant->quantity }}" />
              @if($variant->quantity == 0)
              <div class="variant-thumb-overlay out">OUT OF STOCK</div>
              @elseif($variant->quantity <= 5)
                <div class="variant-thumb-overlay low">LOW STOCK
            </div>
            @endif
          </div>
        </div>
        @endforeach
      </div>
    </div>

    <div class="col-lg-5">
      <h1 class="product-single__name">{{ $product->name }}</h1>
      <div class="product-single__rating">
        <div class="reviews-group d-flex">
          @for($i = 1; $i <= 5; $i++)
            <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg"
            style="fill: {{ $i <= round($avgRating) ? '#f5a623' : '#ccc' }} !important;">
            <use href="#icon_star" />
            </svg>
            @endfor
        </div>
        @if($reviews->count() > 0)
        <span class="reviews-note text-lowercase text-secondary ms-1">
          {{ $reviews->count() }} {{ Str::plural('review', $reviews->count()) }}
          · {{ $avgRating }}/5
        </span>
        @else
        <span class="reviews-note text-lowercase text-secondary ms-1">No reviews yet</span>
        @endif
      </div>
      <div class="product-single__price">
        <span class="current-price">
          @if ($product->sale_price)
          <s>${{$product->regular_price}}</s> ${{ $product->sale_price }}
          @else
          ${{$product->regular_price}}
          @endif
        </span>
      </div>
      @if($flashSale)
      <div class="flash-sale-box mb-3 p-3 rounded"
        style="background: linear-gradient(135deg, #ff6b6b, #ee5a24); color:white;">
        <div class="d-flex align-items-center gap-2 mb-2">
          <span style="font-size:20px;">⚡</span>
          <strong style="font-size:16px;">FLASH SALE!</strong>
          <span class="ms-2 text-white">
            Original: <s>${{ number_format($product->regular_price, 2) }}</s>
            → <strong>${{ number_format($flashSale->sale_price, 2) }}</strong>
          </span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <span style="font-size:13px;">Ends in:</span>
          <div id="flash-countdown" class="d-flex gap-2">
            <div class="countdown-block text-center px-2 py-1 rounded" style="background:rgba(0,0,0,0.3); min-width:45px;">
              <div id="cd-hours" style="font-size:20px; font-weight:bold;">00</div>
              <div style="font-size:10px;">HRS</div>
            </div>
            <div class="countdown-block text-center px-2 py-1 rounded" style="background:rgba(0,0,0,0.3); min-width:45px;">
              <div id="cd-minutes" style="font-size:20px; font-weight:bold;">00</div>
              <div style="font-size:10px;">MIN</div>
            </div>
            <div class="countdown-block text-center px-2 py-1 rounded" style="background:rgba(0,0,0,0.3); min-width:45px;">
              <div id="cd-seconds" style="font-size:20px; font-weight:bold;">00</div>
              <div style="font-size:10px;">SEC</div>
            </div>
          </div>
        </div>
      </div>
      @endif
      <div class="mb-4">
        <!-- <label class="fw-bold">Color</label>
          <div class="d-flex gap-3">
            @foreach($product->variants->pluck('color')->unique() as $color)
            <button type="button" class="btn btn-outline-dark color-btn" data-color="{{ $color }}">
              {{ $color }}
            </button>
            @endforeach
          </div> -->
        <div class="d-flex flex-wrap align-items-start gap-5 mb-4">

          <div>
            <label class="fw-bold d-block mb-2">Storage</label>
            <div class="d-flex flex-wrap gap-2">
              @foreach($product->variants->pluck('storage')->filter()->unique() as $storage)
              <button type="button" class="btn btn-outline-primary storage-btn" data-storage="{{ $storage }}">
                {{ $storage }}
              </button>
              @endforeach
            </div>
          </div>

          <div>
            <label class="fw-bold d-block mb-2">Ram</label>
            <div class="d-flex flex-wrap gap-2">
              @foreach($product->variants->pluck('ram')->filter()->unique() as $ram)
              <button type="button" class="btn btn-outline-primary ram-btn" data-ram="{{ $ram }}">
                {{ $ram }}
              </button>
              @endforeach
            </div>
          </div>

        </div>

        <div class="product-single__short-desc">
          <p>{{$product->short_description}}</p>
        </div>
        @if (Cart::instance('cart')->content()->where('id', $product->id)->count()>0)
        <a href="{{route('cart.index')}}" class="btn btn-warning mb-3">Go to Cart</a>
        @else
        <form name="addtocart-form" method="post" action="{{route('cart.add')}}">
          @csrf
          {{-- Stock Status Display --}}
          <div id="variantStockStatus" class="mb-3"></div>
          <div class="product-single__addtocart">
            <div class="qty-control position-relative">
              <input type="number" name="quantity" value="1" min="1" class="qty-control__number text-center">
              <div class="qty-control__reduce">-</div>
              <div class="qty-control__increase">+</div>
            </div><!-- .qty-control -->
            <input type="hidden" name="id" value="{{$product->id}}" />
            <input type="hidden" name="name" value="{{$product->name}}" />
            <input type="hidden" name="price"
              value="{{ $flashSale ? $flashSale->sale_price : ($product->sale_price ?: $product->regular_price) }}" />
            <input type="hidden" name="variant_id" id="selected_variant_id"
              value="{{ $product->variants->first()->id ?? '' }}" />
            <button type="submit" class="btn btn-primary btn-addtocart " data-aside="cartDrawer">Add to Cart</button>
          </div>
        </form>
        @endif
        <div class="product-single__addtolinks">
          @if (Cart::instance('wishlist')->content()->where('id', $product->id)->count()>0)
          <form method="POST" action="{{route('wishlist.item.remove', ['rowId'=>Cart::instance('wishlist')->content()->where('id', $product->id)->first()->rowId])}}" id="frm-remove-item">
            @csrf
            @method('DELETE')
            <a href="javascript:void(0)" class="menu-link menu-link_us-s add-to-wishlist filled-heart" onclick="document.getElementById('frm-remove-item').submit();"><svg width="16" height="16" viewBox="0 0 20 20"
                fill="none" xmlns="http://www.w3.org/2000/svg">
                <use href="#icon_heart" />
              </svg>
              <span>Remove from Wishlist</span>
            </a>
          </form>
          @else
          <form method="POST" action="{{route('wishlist.add')}}" id="wishlist-form">
            @csrf
            <input type="hidden" name="id" value="{{$product->id}}" />
            <input type="hidden" name="name" value="{{$product->name}}" />
            <input type="hidden" name="price" value="{{$product->sale_price == '' ? $product->regular_price : $product->sale_price }}" />
            <input type="hidden" name="quantity" value="1" />
            <a href="javascript:void(0)" class="menu-link menu-link_us-s add-to-wishlist" onclick="document.getElementById('wishlist-form').submit();"><svg width="16" height="16" viewBox="0 0 20 20"
                fill="none" xmlns="http://www.w3.org/2000/svg">
                <use href="#icon_heart" />
              </svg><span>Add to Wishlist</span></a>
          </form>
          @endif

          <share-button class="share-button">
            <button class="menu-link menu-link_us-s to-share border-0 bg-transparent d-flex align-items-center">
              <svg width="16" height="19" viewBox="0 0 16 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                <use href="#icon_sharing" />
              </svg>
              <span>Share</span>
            </button>
            <details id="Details-share-template__main" class="m-1 xl:m-1.5" hidden="">
              <summary class="btn-solid m-1 xl:m-1.5 pt-3.5 pb-3 px-5">+</summary>
              <div id="Article-share-template__main"
                class="share-button__fallback flex items-center absolute top-full left-0 w-full px-2 py-4 bg-container shadow-theme border-t z-10">
                <div class="field grow mr-4">
                  <label class="field__label sr-only" for="url">Link</label>
                  <input type="text" class="field__input w-full" id="url"
                    value="https://uomo-crystal.myshopify.com/blogs/news/go-to-wellness-tips-for-mental-health"
                    placeholder="Link" onclick="this.select();" readonly="">
                </div>
                <button class="share-button__copy no-js-hidden">
                  <svg class="icon icon-clipboard inline-block mr-1" width="11" height="13" fill="none"
                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" viewBox="0 0 11 13">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M2 1a1 1 0 011-1h7a1 1 0 011 1v9a1 1 0 01-1 1V1H2zM1 2a1 1 0 00-1 1v9a1 1 0 001 1h7a1 1 0 001-1V3a1 1 0 00-1-1H1zm0 10V3h7v9H1z"
                      fill="currentColor"></path>
                  </svg>
                  <span class="sr-only">Copy link</span>
                </button>
              </div>
            </details>
          </share-button>
          <script src="js/details-disclosure.html" defer="defer"></script>
          <script src="js/share.html" defer="defer"></script>
        </div>

        <div class="product-single__meta-info">
          <div class="meta-item">
            <label>SKU:</label>
            <span>{{$product->SKU}}</span>
          </div>
          <div class="meta-item">
            <label>Categories:</label>
            <span>{{$product->category->name}}</span>
          </div>
          <div class="meta-item">
            <label>Tags:</label>
            <span>NA</span>
          </div>
          <br /> <br /> <br /> <br /> <br />
          <div class="d-flex justify-content-between mb-4 pb-md-2">
            <div class="breadcrumb mb-0 d-none d-md-block flex-grow-1">
              <a href="{{ route('home.index') }}" class="menu-link menu-link_us-s text-uppercase fw-medium">Home</a>
              <span class="breadcrumb-separator menu-link fw-medium ps-1 pe-1">/</span>
              <a href="{{ route('shop.index') }}" class="menu-link menu-link_us-s text-uppercase fw-medium">The Shop</a>
            </div>

            <div class="product-single__prev-next d-flex align-items-center justify-content-between justify-content-md-end flex-grow-1">

              @if($prevProduct)
              <a href="{{ url('/shop/' . $prevProduct->slug) }}"
                class="text-uppercase fw-medium">
                <svg width="10" height="10" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_prev_md" />
                </svg>
                <span class="menu-link menu-link_us-s">Prev</span>
              </a>
              @else
              <span class="text-muted">No Prev</span>
              @endif


              @if($nextProduct)
              <a href="{{ url('/shop/' . $nextProduct->slug) }}"
                class="text-uppercase fw-medium">
                <span class="menu-link menu-link_us-s">Next</span>
                <svg width="10" height="10" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_next_md" />
                </svg>
              </a>
              @else
              <span class="text-muted">No Next</span>
              @endif

            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="product-single__details-tab">
      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <a class="nav-link nav-link_underscore active" id="tab-description-tab" data-bs-toggle="tab"
            href="#tab-description" role="tab" aria-controls="tab-description" aria-selected="true">Description</a>
        </li>
        <li class="nav-item" role="presentation">
          <a class="nav-link nav-link_underscore" id="tab-additional-info-tab" data-bs-toggle="tab"
            href="#tab-additional-info" role="tab" aria-controls="tab-additional-info"
            aria-selected="false">Additional Information</a>
        </li>
        <li class="nav-item" role="presentation">
          <a class="nav-link nav-link_underscore" id="tab-reviews-tab" data-bs-toggle="tab"
            href="#tab-reviews" role="tab" aria-controls="tab-reviews"
            aria-selected="false">Reviews ({{ $reviews->count() }})</a>
        </li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane fade show active" id="tab-description" role="tabpanel"
          aria-labelledby="tab-description-tab">
          <div class="product-single__description">
            {{$product->description}}
          </div>
        </div>
        <div class="tab-pane fade" id="tab-additional-info" role="tabpanel">
          <div class="product-specs">
            @php
            // Group attributes by prefix (e.g. "launch.announced" → group "launch")
            $grouped = [];
            foreach($product->attributes as $attr) {
            if (str_contains($attr->name, '.')) {
            [$group, $field] = explode('.', $attr->name, 2);
            $grouped[$group][$field] = $attr->value;
            } else {
            $grouped['General'][$attr->name] = $attr->value;
            }
            }
            @endphp

            @foreach($grouped as $group => $fields)
            <table class="specs-table w-100 mb-4">
              <thead>
                <tr>
                  <th colspan="2" class="specs-group-title">
                    {{ strtoupper(str_replace('_', ' ', $group)) }}
                  </th>
                </tr>
              </thead>
              <tbody>
                @foreach($fields as $name => $value)
                <tr>
                  <td class="specs-label">{{ ucfirst(str_replace('_', ' ', $name)) }}</td>
                  <td class="specs-value">{{ $value }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
            @endforeach
          </div>
        </div>
        <div class="tab-pane fade" id="tab-reviews" role="tabpanel" aria-labelledby="tab-reviews-tab">

          {{-- ✅ Session messages --}}
          @if(session('success'))
          <div class="alert alert-success mt-3">{{ session('success') }}</div>
          @endif
          @if(session('error'))
          <div class="alert alert-danger mt-3">{{ session('error') }}</div>
          @endif

          {{-- ✅ Average Rating Summary --}}
          <div class="d-flex align-items-center gap-3 mt-3 mb-4">
            <h2 class="product-single__reviews-title mb-0">
              Reviews ({{ $reviews->count() }})
            </h2>
            @if($reviews->count())
            <div class="reviews-group d-flex align-items-center gap-1" style="flex-wrap:nowrap;">
              @for($i = 1; $i <= 5; $i++)
                <svg class="rev-star-item" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg"
                style="width:14px; height:14px; flex-shrink:0; fill: {{ $i <= round($avgRating) ? '#f5a623' : '#ccc' }} !important;">
                <use href="#icon_star" />
                </svg>
                @endfor
                <span class="text-muted ms-1" style="white-space:nowrap;">{{ $avgRating }} / 5</span>
            </div>
            @endif
          </div>

          {{-- ✅ Existing Reviews List --}}
          <div class="product-single__reviews-list">
            @forelse($reviews as $review)
            <div class="product-single__reviews-item">
              <div class="customer-avatar">
                @if($review->user->profile_photo)
                <img loading="lazy" src="{{ asset('storage/profile_photos/' . $review->user->profile_photo) }}" alt="" />
                @else
                <img loading="lazy" src="{{ asset('assets/images/avatar.jpg') }}" alt="" />
                @endif
              </div>
              <div class="customer-review">
                <div class="customer-name">
                  <h6>{{ $review->user->name }}</h6>
                  <div class="d-flex" style="gap:2px;">
                    @for($i = 1; $i <= 5; $i++)
                      <svg class="rev-star-item" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg"
                      style="width:12px; height:12px; flex-shrink:0; fill: {{ $i <= $review->rating ? '#f5a623' : '#ccc' }} !important;">
                      <use href="#icon_star" />
                      </svg>
                      @endfor
                  </div>
                </div>
                <div class="review-date">{{ $review->created_at->format('F d, Y') }}</div>
                <div class="review-text">
                  @if($review->comment)
                  <p>{{ $review->comment }}</p>
                  @endif
                  @if($review->photo)
                  <img src="{{ asset('uploads/reviews/' . $review->photo) }}"
                    style="max-width:140px; border-radius:8px; margin-top:8px;" alt="Review photo">
                  @endif
                </div>
                {{-- ✅ Delete own review --}}
                @auth
                @if(Auth::id() == $review->user_id)
                <form action="{{ route('review.delete', $review->id) }}" method="POST" class="mt-2">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Delete my review</button>
                </form>
                @endif
                @endauth
              </div>
            </div>
            @empty
            <p class="text-muted">No reviews yet. Be the first to review!</p>
            @endforelse
          </div>

          {{-- ✅ Review Form --}}
          <div class="product-single__review-form">
            @auth
            @if(!$userReview)
            <form method="POST" action="{{ route('review.store', $product->id) }}" enctype="multipart/form-data">
              @csrf
              <h5>Write a Review for "{{ $product->name }}"</h5>
              <p>Your email address will not be published. Required fields are marked *</p>

              {{-- Star Rating --}}
              <div class="select-star-rating mb-3">
                <label class="d-block mb-2">Your rating *</label>
                <div style="display:block !important; line-height:1;">
                  <span id="starRatingGroup" style="display:inline-flex !important; flex-direction:row !important; gap:2px !important; width:auto !important;">
                    <button type="button" class="star-btn" data-value="1"
                      style="font-size:28px; color:#ccc; background:none; border:none; padding:0; margin:0; width:32px !important; min-width:32px !important; max-width:32px !important; flex:0 0 32px !important; cursor:pointer; line-height:1; display:inline-block !important;">★</button>
                    <button type="button" class="star-btn" data-value="2"
                      style="font-size:28px; color:#ccc; background:none; border:none; padding:0; margin:0; width:32px !important; min-width:32px !important; max-width:32px !important; flex:0 0 32px !important; cursor:pointer; line-height:1; display:inline-block !important;">★</button>
                    <button type="button" class="star-btn" data-value="3"
                      style="font-size:28px; color:#ccc; background:none; border:none; padding:0; margin:0; width:32px !important; min-width:32px !important; max-width:32px !important; flex:0 0 32px !important; cursor:pointer; line-height:1; display:inline-block !important;">★</button>
                    <button type="button" class="star-btn" data-value="4"
                      style="font-size:28px; color:#ccc; background:none; border:none; padding:0; margin:0; width:32px !important; min-width:32px !important; max-width:32px !important; flex:0 0 32px !important; cursor:pointer; line-height:1; display:inline-block !important;">★</button>
                    <button type="button" class="star-btn" data-value="5"
                      style="font-size:28px; color:#ccc; background:none; border:none; padding:0; margin:0; width:32px !important; min-width:32px !important; max-width:32px !important; flex:0 0 32px !important; cursor:pointer; line-height:1; display:inline-block !important;">★</button>
                  </span>
                </div>
                <input type="hidden" name="rating" id="ratingInput" value="">
                <div id="ratingError" class="text-danger small mt-1" style="display:none;">Please select a rating.</div>
              </div>

              {{-- Comment --}}
              <div class="mb-4">
                <textarea name="comment" class="form-control form-control_gray"
                  placeholder="Your Review" cols="30" rows="8"></textarea>
              </div>

              {{-- Photo Upload --}}
              <div class="form-label-fixed mb-4">
                <label class="form-label">Photo (optional)</label>
                <input type="file" name="photo" class="form-control form-control_gray" accept="image/*">
              </div>

              <div class="form-action">
                <button type="submit" class="btn btn-primary">Submit Review</button>
              </div>
            </form>
            @else
            <div class="alert alert-info mt-3">
              ✅ You have already reviewed this product.
            </div>
            @endif
            @else
            <p class="mt-3">
              <a href="{{ route('login') }}">Login</a> to write a review.
            </p>
            @endauth
          </div>

        </div>
      </div>
    </div>
  </section>
  <section class="products-carousel container">
    <h2 class="h3 text-uppercase mb-4 pb-xl-2 mb-xl-4">Related <strong>Products</strong></h2>

    <div id="related_products" class="position-relative">
      <div class="swiper-container js-swiper-slider" data-settings='{
            "autoplay": false,
            "slidesPerView": 4,
            "slidesPerGroup": 4,
            "effect": "none",
            "loop": true,
            "pagination": {
              "el": "#related_products .products-pagination",
              "type": "bullets",
              "clickable": true
            },
            "navigation": {
              "nextEl": "#related_products .products-carousel__next",
              "prevEl": "#related_products .products-carousel__prev"
            },
            "breakpoints": {
              "320": {
                "slidesPerView": 2,
                "slidesPerGroup": 2,
                "spaceBetween": 14
              },
              "768": {
                "slidesPerView": 3,
                "slidesPerGroup": 3,
                "spaceBetween": 24
              },
              "992": {
                "slidesPerView": 4,
                "slidesPerGroup": 4,
                "spaceBetween": 30
              }
            }
          }'>
        <div class="swiper-wrapper">
          @foreach ($rproducts as $rproduct)
          <div class="swiper-slide product-card">
            <div class="pc__img-wrapper">
              <a href="{{ route('shop.product.details',['product_slug'=>$rproduct->slug] ) }}">
                <img loading="lazy" src="{{ asset('uploads/products') }}/{{$rproduct->image}}" width="330" height="400"
                  alt="{{ $rproduct->name }}" class="pc__img">
                @if($rproduct->images)
                @foreach ((is_array($rproduct->images) ? $rproduct->images : json_decode($rproduct->images, true) ?? explode(',', $rproduct->images)) as $gimg)
                <img loading="lazy" src="{{ asset('uploads/products') }}/{{ trim($gimg) }}" width="330" height="400"
                  alt="{{ $rproduct->name }}" class="pc__img pc__img-second">
                @endforeach
                @endif
              </a>
              @if (Cart::instance('cart')->content()->where('id', $product->id)->count()>0)
              <a href="{{route('cart.index')}}" class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium btn-warning mb-3">Go to Cart</a>
              @else
              <form name="addtocart-form" method="post" action="{{route('cart.add')}}">
                @csrf
                <input type="hidden" name="id" value="{{$rproduct->id}}" />
                <input type="hidden" name="quantity" value="1" />
                <input type="hidden" name="name" value="{{$rproduct->name}}" />
                <input type="hidden" name="price" value="{{$rproduct->sale_price == '' ? $rproduct->regular_price : $rproduct->sale_price}}" />
                <button
                  type="submit"
                  class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium "
                  data-aside="cartDrawer" title="Add To Cart">Add To Cart
                </button>
              </form>
              @endif
            </div>

            <div class="pc__info position-relative">
              <p class="pc__category">{{ $rproduct->category->name }}</p>
              <h6 class="pc__title"><a href="{{ route('shop.product.details',['product_slug'=>$rproduct->slug] ) }}">{{ $rproduct->name }}</a></h6>
              <div class="product-card__price d-flex">
                <span class="money price">
                  @if ($rproduct->sale_price)
                  <s>${{$rproduct->regular_price}}</s> ${{ $rproduct->sale_price }}
                  @else
                  ${{$rproduct->regular_price}}
                  @endif
                </span>
              </div>

              @php
              $wishItem = Cart::instance('wishlist')->content()->where('id', $rproduct->id)->first();
              @endphp

              @if ($wishItem)
              <form method="POST" action="{{ route('wishlist.item.remove', ['rowId' => $wishItem->rowId]) }}">
                @csrf
                @method('DELETE')
                <button type="submit"
                  class="pc__btn-wl position-absolute top-0 end-0 bg-transparent border-0 filled-heart"
                  title="Remove From Wishlist">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <use href="#icon_heart" />
                  </svg>
                </button>
              </form>
              @else
              <form method="POST" action="{{ route('wishlist.add') }}">
                @csrf
                <input type="hidden" name="id" value="{{ $rproduct->id }}" />
                <input type="hidden" name="name" value="{{ $rproduct->name }}" />
                <input type="hidden" name="price" value="{{ $rproduct->sale_price == '' ? $rproduct->regular_price : $rproduct->sale_price }}" />
                <input type="hidden" name="quantity" value="1" />
                <button type="submit"
                  class="pc__btn-wl position-absolute top-0 end-0 bg-transparent border-0"
                  title="Add To Wishlist">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <use href="#icon_heart" />
                  </svg>
                </button>
              </form>
              @endif
            </div>
          </div>
          @endforeach
        </div><!-- /.swiper-wrapper -->
      </div><!-- /.swiper-container js-swiper-slider -->

      <div class="products-carousel__prev position-absolute top-50 d-flex align-items-center justify-content-center">
        <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
          <use href="#icon_prev_md" />
        </svg>
      </div><!-- /.products-carousel__prev -->
      <div class="products-carousel__next position-absolute top-50 d-flex align-items-center justify-content-center">
        <svg width="25" height="25" viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
          <use href="#icon_next_md" />
        </svg>
      </div><!-- /.products-carousel__next -->

      <div class="products-pagination mt-4 mb-5 d-flex align-items-center justify-content-center"></div>
      <!-- /.products-pagination -->
    </div><!-- /.position-relative -->

  </section><!-- /.products-carousel container -->
</main>
@endsection

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const shareButton = document.querySelector(".to-share");

    if (!shareButton) return;

    shareButton.addEventListener("click", async function() {
      const url = window.location.href;
      if (navigator.share) {
        try {
          await navigator.share({
            title: document.title,
            text: "Check this product",
            url: url
          });
        } catch (err) {
          console.log("Share cancelled");
        }
      } else {
        try {
          await navigator.clipboard.writeText(url);
          alert("Link copied to clipboard!");
        } catch (err) {
          prompt("Copy this link:", url);
        }
      }
    });
  });

  document.addEventListener("DOMContentLoaded", function() {

    // Storage
    document.querySelectorAll('.storage-btn').forEach(btn => {
      btn.addEventListener('click', function() {

        document.querySelectorAll('.storage-btn')
          .forEach(b => b.classList.remove('active'));

        this.classList.add('active');
      });
    });

  });





  const VARIANT_BASE_URL = "{{ asset('uploads/variants') }}";
  const VARIANT_THUMB_BASE_URL = "{{ asset('uploads/variants/Thumbnails') }}";

  function updateVerticalThumbnails(mainImage, galleryImages) {
    const wrapper = document.getElementById('variantSwiperWrapper');
    if (!wrapper) return;
    wrapper.innerHTML = '';

    const mainSlide = document.createElement('div');
    mainSlide.className = 'swiper-slide product-single__image-item';
    mainSlide.innerHTML = `<img loading="lazy" class="h-auto" src="${mainImage}" width="104" height="104" alt="">`;
    wrapper.appendChild(mainSlide);

    galleryImages.forEach(img => {
      const slide = document.createElement('div');
      slide.className = 'swiper-slide product-single__image-item';
      const src = img.startsWith('http') ? img : `${VARIANT_THUMB_BASE_URL}/${img}`;
      slide.innerHTML = `<img loading="lazy" class="h-auto" src="${src}" width="104" height="104" alt="">`;
      wrapper.appendChild(slide);
    });

    if (typeof variantSwiper !== 'undefined' && variantSwiper) {
      variantSwiper.update();
    }
  }

  function updateMainImageSwiper(mainImage, galleryImages) {
    const mainWrapper = document.querySelector('.product-single__image .swiper-wrapper');
    if (!mainWrapper) return;
    mainWrapper.innerHTML = '';

    const allImages = [
      mainImage,
      ...galleryImages.map(img =>
        img.startsWith('http') ? img : `${VARIANT_BASE_URL}/${img}`
      )
    ];

    allImages.forEach(src => {
      const slide = document.createElement('div');
      slide.className = 'swiper-slide product-single__image-item';
      slide.innerHTML = `
        <img loading="lazy" class="h-auto" src="${src}" width="674" height="674" alt="" />
        <a data-fancybox="gallery" href="${src}" data-bs-toggle="tooltip" data-bs-placement="left" title="Zoom">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <use href="#icon_zoom" />
          </svg>
        </a>`;
      mainWrapper.appendChild(slide);
    });

    if (typeof productSwiper !== 'undefined' && productSwiper) {
      productSwiper.slideTo(0);
      productSwiper.update();
    }

    const mainImg = document.getElementById('mainProductImage');
    const mainLink = document.getElementById('mainProductLink');
    if (mainImg) mainImg.src = mainImage;
    if (mainLink) mainLink.href = mainImage;
  }

  document.addEventListener("DOMContentLoaded", function() {

    const shareButton = document.querySelector(".to-share");
    if (shareButton) {
      shareButton.addEventListener("click", async function() {
        const url = window.location.href;
        if (navigator.share) {
          try {
            await navigator.share({
              title: document.title,
              text: "Check this product",
              url
            });
          } catch (err) {
            console.log("Share cancelled");
          }
        } else {
          try {
            await navigator.clipboard.writeText(url);
            alert("Link copied to clipboard!");
          } catch (err) {
            prompt("Copy this link:", url);
          }
        }
      });
    }

    document.querySelectorAll('.storage-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.storage-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
      });
    });

    const allThumbs = document.querySelectorAll('.variant-thumb');

    allThumbs.forEach(img => {
      img.style.cursor = 'pointer';
      img.style.border = '2px solid transparent';
      img.style.borderRadius = '4px';
      img.style.transition = 'border-color 0.2s';
    });

    // ── safe gallery parser ──────────────────────────────
    function parseGallery(datasetGallery) {
      try {
        const parsed = JSON.parse(datasetGallery || '[]');
        if (Array.isArray(parsed)) return parsed;
        if (typeof parsed === 'string') return parsed.split(',').map(s => s.trim()).filter(Boolean);
        return [];
      } catch (e) {
        return (datasetGallery || '').split(',').map(s => s.trim()).filter(Boolean);
      }
    }

    const LOW_STOCK_THRESHOLD = 5;

    function updateStockStatus(quantity) {
      const el = document.getElementById('variantStockStatus');
      if (!el) return;
      quantity = parseInt(quantity);
      if (quantity === 0) {
        el.innerHTML = '<span class="stock-badge out-of-stock">⛔ Out of Stock</span>';
        // Disable add to cart
        const btn = document.querySelector('button.btn-addtocart');
        if (btn) {
          btn.disabled = true;
          btn.style.opacity = '0.5';
        }
      } else if (quantity <= LOW_STOCK_THRESHOLD) {
        el.innerHTML = `<span class="stock-badge low-stock">⚠️ Low Stock — only ${quantity} left</span>`;
        const btn = document.querySelector('button.btn-addtocart');
        if (btn) {
          btn.disabled = false;
          btn.style.opacity = '1';
        }
      } else {
        el.innerHTML = '';
        const btn = document.querySelector('button.btn-addtocart');
        if (btn) {
          btn.disabled = false;
          btn.style.opacity = '1';
        }
      }
    }

    // Init first variant
    const firstThumb = allThumbs[0];
    if (firstThumb) {
      firstThumb.style.borderColor = '#007bff';
      const mainImage = firstThumb.dataset.image;
      const gallery = parseGallery(firstThumb.dataset.gallery);
      updateVerticalThumbnails(mainImage, gallery);
      updateMainImageSwiper(mainImage, gallery);
      updateStockStatus(firstThumb.dataset.quantity);
    }

    // Click handler
    allThumbs.forEach(img => {
      img.addEventListener('click', function() {
        allThumbs.forEach(t => t.style.borderColor = 'transparent');
        this.style.borderColor = '#007bff';
        const mainImage = this.dataset.image;
        const gallery = parseGallery(this.dataset.gallery);
        updateVerticalThumbnails(mainImage, gallery);
        updateMainImageSwiper(mainImage, gallery);
        const variantId = this.dataset.variantId;
        const price = this.dataset.price;
        const quantity = this.dataset.quantity;

        if (variantId) {
          document.getElementById('selected_variant_id').value = variantId;
        }
        if (price) {
          document.querySelector('input[name="price"]').value = price;
        }
        updateStockStatus(quantity);
      });
    });

  });

  //star & rating
  document.addEventListener("DOMContentLoaded", function() {
    const group = document.getElementById('starRatingGroup');
    if (!group) return;

    const stars = group.querySelectorAll('.star-btn');
    const input = document.getElementById('ratingInput');
    const errMsg = document.getElementById('ratingError');
    let selected = 0;

    function paintStars(count) {
      stars.forEach((s, i) => {
        s.style.color = i < count ? '#f5a623' : '#ccc';
      });
    }

    // Hover
    stars.forEach((star, idx) => {
      star.addEventListener('mouseenter', () => paintStars(idx + 1));
    });

    // Mouse leaves group → restore selected
    group.addEventListener('mouseleave', () => paintStars(selected));

    // Click → lock
    stars.forEach((star, idx) => {
      star.addEventListener('click', () => {
        selected = idx + 1;
        input.value = selected;
        paintStars(selected);
        if (errMsg) errMsg.style.display = 'none';
      });
    });

    // Validate on submit
    const reviewForm = document.querySelector('form[action*="review"]');
    if (reviewForm) {
      reviewForm.addEventListener('submit', function(e) {
        if (!input.value) {
          e.preventDefault();
          if (errMsg) errMsg.style.display = 'block';
        }
      });
    }
  });
</script>

{{-- Flash Sale Countdown - SEPARATE script block --}}
@if($flashSale)
<script>
  const flashEndTime = {
    {
      $flashSale - > end_at - > setTimezone('Asia/Phnom_Penh') - > timestamp
    }
  }* 1000;

  function updateCountdown() {
    const now = new Date().getTime();
    const diff = flashEndTime - now;

    if (diff <= 0) {
      const el = document.getElementById('flash-countdown');
      if (el) el.innerHTML = '<span style="color:white; font-weight:bold;">Sale Ended!</span>';
      return;
    }

    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    const h = document.getElementById('cd-hours');
    const m = document.getElementById('cd-minutes');
    const s = document.getElementById('cd-seconds');

    if (h) h.textContent = String(hours).padStart(2, '0');
    if (m) m.textContent = String(minutes).padStart(2, '0');
    if (s) s.textContent = String(seconds).padStart(2, '0');
  }

  updateCountdown();
  setInterval(updateCountdown, 1000);

  // Debug - remove after fixing
  console.log('End time:', new Date(flashEndTime));
  console.log('Now:', new Date());
  console.log('Diff seconds:', (flashEndTime - new Date().getTime()) / 1000);
</script>
@endif