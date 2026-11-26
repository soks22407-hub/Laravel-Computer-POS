@extends('layouts.admin')
@section('content')
<style>
    .brand-id {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        transition: background 0.2s ease;
        cursor: pointer;
    }

    .brand-id:hover {
        background-color: #f5f5f5;
    }

    .brand-id .image {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        gap: 10px;
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

    #box-content-search .brand-id {
        margin-bottom: 10px;
    }
</style>
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Brands</h3>
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
                    <div class="text-tiny">Brands</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search flex-grow" method="GET">
                        <fieldset class="name">
                            <input type="text" placeholder="Search here..." class="show-search" name="name" id="brand-search-input"
                                tabindex="2" value="{{ request('name') }}" aria-required="true" required="" data-search-url="{{ route('admin.search.brand') }}" autocomplete="off">
                        </fieldset>
                        <div class="button-submit">
                            <button class="" type="submit"><i class="icon-search"></i></button>
                        </div>
                        <div class="box-content-search">
                            <ul id="brand-box-content-search"></ul>
                        </div>
                    </form>
                </div>
                <a class="tf-button style-1 w208" href="{{route ('admin.brand.add')}}"><i
                        class="icon-plus"></i>Add new</a>
            </div>
            <div class="wg-table table-all-user">
                <div class="table-responsive">
                    @if(Session::has('status'))
                    <p class="alert alert-success">{{Session::get('status')}}</p>
                    @endif
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th style="width: 435px;">Name</th>
                                <th>Slug</th>
                                <th>Products</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($brands as $brand)
                            <tr>
                                <td>{{ $brands->firstItem() + $loop->index }}</td>
                                <td class="pname" style="width: 435px;">
                                    <div class="image">
                                        <img src="{{asset('uploads/brands')}}/{{$brand->image}}" alt="{{$brand->name}}" class="image">
                                    </div>
                                    <div class="name">
                                        <a href="#" class="body-title-2">{{ $brand->name}}</a>
                                    </div>
                                </td>
                                <td>{{ $brand->slug}}</td>
                                <td><a href="{{ route('admin.products.byBrand', $brand->id) }}">
                                        {{ $brand->products_count }}
                                    </a>
                                </td>
                                <td>
                                    <div class="list-icon-function">
                                        <a href="{{route('admin.brand.edit', ['id'=>$brand->id])}}">
                                            <div class="item edit">
                                                <i class="icon-edit-3"></i>
                                            </div>
                                        </a>
                                        <form action="{{route('admin.brand.delete', ['id'=>$brand->id])}}" method="POST">
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
                    {{$brands->links('pagination::bootstrap-5')}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
    $(function() {
        $("#brand-search-input").on("keyup", function() {
            var searchQuery = $(this).val();
            if (searchQuery.length > 2) {
                $.ajax({
                    type: "GET",
                    url: "{{route('admin.search.brand')}}",
                    data: {
                        query: searchQuery
                    },
                    dataType: 'json',
                    success: function(data) {
                        $("#brand-box-content-search").html('');
                        $.each(data, function(index, brand) {
                            var url = "{{route('admin.brand.edit', ['id' => 'brand_id'])}}";
                            var link = url.replace('brand_id', brand.id);

                            const safeName = $('<div>').text(brand.name).html();
                            const safeSlug = $('<div>').text(brand.slug).html();

                            $("#brand-box-content-search").append(`
                                    <li class="brand-id">  
                                    <a href="${link}" class="d-flex items-center gap14 mb-10"> 
                                        <div class="image no-bg">
                                        <img src="{{ asset('uploads/brands') }}/${brand.image}" alt="${safeName}">
                                        </div>
                                        <div class="flex items-center justify-between gap20 flex-grow">
                                            <div class="name">
                                                <span class="body-text">${safeName}</span>
                                                <div class="text-tiny text-muted">${safeSlug}</div>
                                            </div>
                                        </div>
                                    </a>
                                    </li>
                                    <li>
                                    <div class="divider"></div>
                                    </li>
                                `);
                        });
                    }
                });
            } else {
                $("#brand-box-content-search").html('');
            }
        });
    });
</script>
@endpush