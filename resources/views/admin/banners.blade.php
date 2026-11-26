@extends('layouts.admin')
@section('content')
<style>
    .table th,
    .table td {
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        max-width: 150px;
    }

    .banner-id {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 15px;
        transition: all 0.3s ease;
        padding-right: 5px;
    }

    .banner-id .image {
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

    #box-content-search .slide-id {
        margin-bottom: 10px;
    }
</style>

<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding: 5px 0; margin-top: 0;">
            <h3>Banners</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Banners</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search flex-grow" method="GET">
                        <fieldset class="name">
                            <input type="text" placeholder="Search here..." class="show-search" name="name" id="banner-search-input"
                                tabindex="2" value="{{ request('name') }}" aria-required="true" required="" data-search-url="{{ route('admin.search.banner') }}" autocomplete="off">
                        </fieldset>
                        <div class="button-submit">
                            <button class="" type="submit"><i class="icon-search"></i></button>
                        </div>
                        <div class="box-content-search">
                            <ul id="banner-box-content-search"></ul>
                        </div>
                    </form>
                </div>
                <a class="tf-button style-1 w208" href="{{ route('admin.banner.add') }}"><i
                        class="icon-plus"></i>Add new</a>
            </div>
            <div class="wg-table table-all-user">
                @if(Session::has('status'))
                <p class="alert alert-success">{{Session::get('status')}}</p>
                @endif
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Subtitle</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($banners as $banner)
                        <tr>
                            <td>{{ $banners->firstItem() + $loop->index }}</td>
                            <td class="pname">
                                <div class="image">
                                    <img src="{{ asset('uploads/banners') }}/{{$banner->image}}" alt="" class="{{ $banner->title }}">
                                </div>
                            </td>
                            <td>{{ $banner->title }}</td>
                            <td>{{ $banner->subtitle }}</td>
                            <td>{{ $banner->description }}</td>
                            <td>{{ $banner->type }}</td>
                            <td>
                                <div class="list-icon-function">
                                    <a href="{{route('admin.banner.edit', ['id'=>$banner->id ])}}">
                                        <div class="item edit">
                                            <i class="icon-edit-3"></i>
                                        </div>
                                    </a>
                                    <form action="{{route('admin.banner.delete', ['id'=>$banner->id ])}}" method="POST">
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
                {{ $banners->links('pagination::bootstrap-5') }}
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
        $("#banner-search-input").on("keyup", function() {
            var searchQuery = $(this).val();
            if (searchQuery.length > 2) {
                $.ajax({
                    type: "GET",
                    url: "{{route('admin.search.banner')}}",
                    data: {
                        query: searchQuery
                    },
                    dataType: 'json',
                    success: function(data) {
                        $("#banner-box-content-search").html('');
                        $.each(data, function(index, banner) {
                            var url = "{{route('admin.banner.edit', ['id' => 'banner_id'])}}";
                            var link = url.replace('banner_id', banner.id);

                            $("#banner-box-content-search").append(`
                                    <li class="banner-id">  
                                    <a href="${link}" class="d-flex items-center gap14 mb-10"> 
                                        <div class="image no-bg">
                                        <img src="{{ asset('uploads/banners') }}/${banner.image}" alt="${banner.title}">
                                        </div>
                                        <div class="flex items-center justify-between gap20 flex-grow">
                                        <div class="name">
                                            <span class="body-text">${banner.title}</span>
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
                $("#banner-box-content-search").html('');
            }
        });
    });
</script>
@endpush