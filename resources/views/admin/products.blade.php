@extends('layouts.admin')
@section('content')
<style>
    .product-id {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        transition: background 0.2s ease;
        cursor: pointer;
    }

    .product-id:hover {
        background-color: #f5f5f5;
    }

    .product-id .image {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        flex-shrink: 0;
        padding: 5px;
        border-radius: 10px;
        background: #EFF4F8;
    }

    .box-content-search {
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 999;
        background: #fff;
        border-radius: 4px;
        width: 100%;
        max-height: 300px;
        overflow-y: auto;
        margin-top: 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    #box-content-search li {
        list-style: none;
    }

    #box-content-search .product-id {
        margin-bottom: 10px;
    }

    .variant-list {
        max-height: 70px;
        overflow-y: auto;
    }

    /* ── Filter Panel ── */
    .filter-toggle-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        background: #fff;
        color: #374151;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        white-space: nowrap;
    }

    .filter-toggle-btn:hover {
        border-color: #6366f1;
        color: #6366f1;
    }

    .filter-toggle-btn.active {
        border-color: #6366f1;
        background: #eef2ff;
        color: #6366f1;
    }

    .filter-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #6366f1;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
    }

    .filter-panel {
        display: none;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 16px 20px;
        margin-top: 12px;
        gap: 16px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .filter-panel.open {
        display: flex;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 160px;
        flex: 1;
    }

    .filter-group label {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .filter-group select {
        padding: 8px 10px;
        border: 1.5px solid #d1d5db;
        border-radius: 7px;
        font-size: 13.5px;
        color: #1f2937;
        background: #fff;
        transition: border-color 0.2s;
        width: 100%;
    }

    .filter-group select:focus {
        outline: none;
        border-color: #6366f1;
    }

    .filter-actions {
        display: flex;
        gap: 8px;
        align-items: flex-end;
        flex-shrink: 0;
    }

    .btn-apply-filter {
        padding: 8px 18px;
        background: #6366f1;
        color: #fff;
        border: none;
        border-radius: 7px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-apply-filter:hover {
        background: #4f46e5;
    }

    .btn-clear-filter {
        padding: 8px 14px;
        background: #fff;
        color: #6b7280;
        border: 1.5px solid #d1d5db;
        border-radius: 7px;
        font-size: 13.5px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: border-color 0.2s, color 0.2s;
    }

    .btn-clear-filter:hover {
        border-color: #ef4444;
        color: #ef4444;
    }

    .active-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 10px;
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        background: #eef2ff;
        border: 1px solid #c7d2fe;
        border-radius: 20px;
        font-size: 12px;
        color: #4338ca;
        font-weight: 500;
    }

    .filter-chip a {
        color: #818cf8;
        text-decoration: none;
        font-size: 15px;
        line-height: 1;
    }

    .filter-chip a:hover {
        color: #ef4444;
    }
</style>

<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding: 5px 0; margin-top: 0;">
            <h3>All Products</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{route('admin.index')}}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">All Products</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">

            @php
            $activeCount = collect(['category_id','brand_id','stock_status'])
            ->filter(fn($k) => request($k) !== null && request($k) !== '')
            ->count();
            @endphp

            {{-- ── Top Bar ── --}}
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow" style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <form class="form-search flex-grow" method="GET">
                        <fieldset class="name">
                            <input type="text" placeholder="Search here..." class="show-search" name="name"
                                id="product-search-input" tabindex="2" value="{{ request('name') }}"
                                autocomplete="off" data-search-url="{{ route('admin.search.product') }}">
                        </fieldset>
                        <div class="button-submit">
                            <button type="submit"><i class="icon-search"></i></button>
                        </div>
                        <div class="box-content-search">
                            <ul id="product-box-content-search"></ul>
                        </div>
                    </form>

                    <button type="button" id="filter-toggle-btn"
                        class="filter-toggle-btn {{ $activeCount > 0 ? 'active' : '' }} tf-button style-1 w150">
                        <i class="icon-filter" style="font-size:14px;"></i>
                        Filters
                        @if($activeCount > 0)
                        <span class="filter-badge">{{ $activeCount }}</span>
                        @endif
                    </button>
                </div>

                <a class="tf-button style-1 w208" href="{{route('admin.product.add')}}">
                    <i class="icon-plus"></i>Add new
                </a>
            </div>

            {{-- ── Filter Panel ── --}}
            <form method="GET" action="{{ route('admin.products') }}" id="filter-form">
                @if(request('name'))
                <input type="hidden" name="name" value="{{ request('name') }}">
                @endif

                <div class="filter-panel {{ $activeCount > 0 ? 'open' : '' }}" id="filter-panel">

                    {{-- Category --}}
                    <div class="filter-group">
                        <label>Category</label>
                        <select name="category_id">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Brand --}}
                    <div class="filter-group">
                        <label>Brand</label>
                        <select name="brand_id">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Stock Status --}}
                    <div class="filter-group">
                        <label>Stock Status</label>
                        <select name="stock_status">
                            <option value="">Any Status</option>
                            <option value="instock" {{ request('stock_status') == 'instock'    ? 'selected' : '' }}>In Stock</option>
                            <option value="outofstock" {{ request('stock_status') == 'outofstock' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>

                    {{-- Actions --}}
                    <div class="filter-actions">
                        <button type="submit" class="btn-apply-filter">Apply</button>
                        <a href="{{ route('admin.products') }}" class="btn-clear-filter">Clear</a>
                    </div>
                </div>
            </form>

            {{-- ── Active Filter Chips ── --}}
            @if($activeCount > 0)
            <div class="active-filters">
                @if(request('category_id'))
                @php $catName = $categories->firstWhere('id', request('category_id'))?->name; @endphp
                <span class="filter-chip">
                    Category: {{ $catName }}
                    <a href="{{ url()->current() . '?' . http_build_query(request()->except(['category_id','page'])) }}">&times;</a>
                </span>
                @endif
                @if(request('brand_id'))
                @php $brandName = $brands->firstWhere('id', request('brand_id'))?->name; @endphp
                <span class="filter-chip">
                    Brand: {{ $brandName }}
                    <a href="{{ url()->current() . '?' . http_build_query(request()->except(['brand_id','page'])) }}">&times;</a>
                </span>
                @endif
                @if(request('stock_status'))
                <span class="filter-chip">
                    Stock: {{ request('stock_status') == 'instock' ? 'In Stock' : 'Out of Stock' }}
                    <a href="{{ url()->current() . '?' . http_build_query(request()->except(['stock_status','page'])) }}">&times;</a>
                </span>
                @endif
            </div>
            @endif

            <div class="table-responsive" style="margin-top:16px;">
                @if(Session::has('status'))
                <p class="alert alert-success">{{Session::get('status')}}</p>
                @endif
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="width:100px;" class="text-center">Code</th>
                            <th style="min-width:220px;" class="text-center">Name</th>
                            <th class="text-center">RegularPrice</th>
                            <th class="text-center">SalePrice</th>
                            <th class="text-center">Cost</th>
                            <th style="min-width:200px;" class="text-center">SKU</th>
                            <th class="text-center">Category</th>
                            <th class="text-center">Brand</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Quantity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                        <tr>
                            <td class="text-center">{{ $product->code ?? '-' }}</td>
                            <td class="pname" style="min-width:220px;">
                                <div class="image">
                                    <img src="{{asset('uploads/products/Thumbnails')}}/{{$product->image}}"
                                        alt="{{ $product->name }}" class="image">
                                </div>
                                <div class="name">
                                    <a href="#" class="body-title-2">{{ $product->name }}</a>
                                    <div class="text-tiny mt-3">{{ $product->slug }}</div>
                                </div>
                            </td>
                            <td class="text-end">${{ number_format($product->regular_price, 2) }}</td>
                            <td class="text-end">${{ number_format($product->sale_price, 2) }}</td>
                            <td class="text-end">${{ number_format($product->cost_price, 2) }}</td>
                            <td>{{ $product->SKU }}</td>
                            <td class="text-center">{{ $product->category->name }}</td>
                            <td class="text-center">{{ $product->brand->name }}</td>
                            <td class="text-center">
                                @if($product->stock_status == 'instock')
                                <span class="badge bg-success">In Stock</span>
                                @else
                                <span class="badge bg-danger">Out of Stock</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{ $product->quantity }}
                                @if($product->variants->count() > 0)
                                <br>
                                <div class="variant-list">
                                    <small class="text-muted">
                                        @foreach($product->variants as $v)
                                        <span class="badge bg-secondary mb-1">
                                            {{ $v->color }} {{ $v->storage }}: {{ $v->quantity }}
                                        </span><br>
                                        @endforeach
                                    </small>
                                </div>
                                @endif
                            </td>
                            <td>
                                <div class="list-icon-function">
                                    <a href="{{route('admin.product.show', $product->id)}}">
                                        <div class="item eye"><i class="icon-eye"></i></div>
                                    </a>
                                    <a href="{{route('admin.product.edit', ['id'=>$product->id])}}">
                                        <div class="item edit"><i class="icon-edit-3"></i></div>
                                    </a>
                                    <form action="{{route('admin.product.delete', ['id'=>$product->id])}}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="item text-danger delete">
                                            <i class="icon-trash-2"></i>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ── Delete confirm ──
    $(function() {
        $(".delete").on("click", function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            swal({
                title: "Are you sure ?",
                text: "Do you want to delete this record ?",
                type: "warning",
                buttons: ["No", "Yes"],
                confirmButtonColor: '#dc3545'
            }).then(function(result) {
                if (result) {
                    form.submit();
                }
            });
        });
    });

    // ── Live search ──
    $(function() {
        $("#product-search-input").on("keyup", function() {
            var searchQuery = $(this).val();
            if (searchQuery.length > 2) {
                $.ajax({
                    type: "GET",
                    url: "{{route('admin.search.product')}}",
                    data: {
                        query: searchQuery
                    },
                    dataType: 'json',
                    success: function(data) {
                        $("#product-box-content-search").html('');
                        $.each(data, function(index, product) {
                            var url = "{{route('admin.product.edit', ['id' => 'product_id'])}}";
                            var link = url.replace('product_id', product.id);
                            $("#product-box-content-search").append(`
                                <li class="product-id">
                                    <a href="${link}" class="d-flex items-center gap14 mb-10">
                                        <div class="image no-bg">
                                            <img src="{{ asset('uploads/products/Thumbnails') }}/${product.image}" alt="${product.name}">
                                        </div>
                                        <div class="flex items-center justify-between gap20 flex-grow">
                                            <div class="name">
                                                <span class="body-text">${product.name}</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li><div class="divider"></div></li>
                            `);
                        });
                    }
                });
            } else {
                $("#product-box-content-search").html('');
            }
        });
    });

    // ── Filter panel toggle ──
    $(function() {
        $("#filter-toggle-btn").on("click", function() {
            $("#filter-panel").toggleClass("open");
            $(this).toggleClass("active");
        });
    });
</script>
@endpush