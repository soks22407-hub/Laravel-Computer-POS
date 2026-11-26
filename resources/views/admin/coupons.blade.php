@extends('layouts.admin')
@section('content')
<style>
    .coup-id {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        transition: background 0.2s ease;
        cursor: pointer;
    }

    .coup-id:hover {
        background-color: #f5f5f5;
        cursor: pointer;
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

    #box-content-search .coup-id {
        margin-bottom: 10px;
    }
</style>
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding: 5px 0; margin-top: 0;">
            <h3>Coupons</h3>
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
                    <div class="text-tiny">Coupons</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search flex-grow" method="GET">
                        <fieldset class="name">
                            <input type="text" placeholder="Search here..." class="show-search" name="code" id="coup-search-input"
                                tabindex="2" value="{{ request('code') }}" aria-required="true" required="" data-search-url="{{ route('admin.search.coupon') }}" autocomplete="off">
                        </fieldset>
                        <div class="button-submit">
                            <button class="" type="submit"><i class="icon-search"></i></button>
                        </div>
                        <div class="box-content-search">
                            <ul id="coup-box-content-search"></ul>
                        </div>
                    </form>
                </div>
                <a class="tf-button style-1 w208" href="{{ route('admin.coupon.add') }}"><i
                        class="icon-plus"></i>Add new</a>
            </div>
            <div class="wg-table table-all-user">
                <div class="table-responsive">
                    @if (Session::has('status'))
                    <p class="alert alert-success">{{Session::get('status')}}</p>
                    @endif
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Cart Value</th>
                                <th>Expiry Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coupons as $coupon)
                            <tr>
                                <td>{{ $coupons->firstItem() + $loop->index }}</td>
                                <td>{{ $coupon -> code }}</td>
                                <td>{{ $coupon -> type }}</td>
                                <td>{{ $coupon -> value }}</td>
                                <td>${{ $coupon -> cart_value }}</td>
                                <td>{{ $coupon -> expiry_date }}</td>
                                <td>
                                    <div class="list-icon-function">
                                        <a href="{{route('admin.coupon.edit', ['id'=>$coupon->id])}}">
                                            <div class="item edit">
                                                <i class="icon-edit-3"></i>
                                            </div>
                                        </a>
                                        <form action="{{route('admin.coupon.delete', ['id'=>$coupon->id])}}" method="POST">
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
            </div>
            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $coupons->links('pagination::bootstrap-5') }}
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
        $("#coup-search-input").on("keyup", function() {
            var searchQuery = $(this).val();
            if (searchQuery.length > 2) {
                $.ajax({
                    type: "GET",
                    url: "{{route('admin.search.coupon')}}",
                    data: {
                        query: searchQuery
                    },
                    dataType: 'json',
                    success: function(data) {
                        $("#coup-box-content-search").html('');
                        $.each(data, function(index, coupon) {
                            var url = "{{route('admin.coupon.edit', ['id' => 'coup_id'])}}";
                            var link = url.replace('coup_id', coupon.id);

                            const safeName = $('<div>').text(coupon.code).html();

                            $("#coup-box-content-search").append(`
                                    <li class="coup-id">  
                                    <a href="${link}" class="d-flex items-center gap14 mb-10"> 
                                        <div class="flex items-center justify-between gap20 flex-grow">
                                            <div class="name">
                                                <span class="body-text">${safeName}</span>
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
                $("#coup-box-content-search").html('');
            }
        });
    });
</script>
@endpush