@extends('layouts.admin')
@section('content')
<style>
    .user-id {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        transition: background 0.2s ease;
        cursor: pointer;
    }

    .user-id:hover {
        background-color: #f5f5f5;
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

    .filter-toggle-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: #fff;
        color: #374151;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .filter-toggle-btn:hover,
    .filter-toggle-btn.active {
        border-color: #6366f1;
        color: #6366f1;
        background: #eef2ff;
    }

    .filter-badge {
        background: #6366f1;
        color: #fff;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 600;
    }

    .filter-panel {
        display: none;
        margin-top: 10px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .filter-panel.open {
        display: block;
    }

    .active-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eef2ff;
        color: #4338ca;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 13px;
    }

    .filter-chip a {
        color: #4338ca;
        text-decoration: none;
        font-weight: bold;
    }

    /* Remove default list styling */
    #box-content-search li {
        list-style: none;
    }

    #box-content-search .user-id {
        margin-bottom: 10px;
    }
</style>
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding: 5px 0; margin-top: 0;">
            <h4>Users</h4>
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
                    <div class="text-tiny">All User</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            @php
            $activeCount = collect(['utype','orders','from','to'])
            ->filter(fn($k) => request($k) !== null && request($k) !== '')
            ->count();
            @endphp
            <div class="wg-filter flex-grow" style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <form class="form-search flex-grow" method="GET">
                    <fieldset class="name">
                        <input type="text" placeholder="Search here..." class="show-search" name="name"
                            id="user-search-input" tabindex="2" value="{{ request('name') }}"
                            autocomplete="off" data-search-url="{{ route('admin.search.user') }}">
                    </fieldset>
                    <div class="button-submit">
                        <button type="submit"><i class="icon-search"></i></button>
                    </div>
                    <div class="box-content-search">
                        <ul id="user-box-content-search"></ul>
                    </div>
                </form>

                <button type="button" id="filter-toggle-btn"
                    class="filter-toggle-btn {{ $activeCount > 0 ? 'active' : '' }}">
                    <i class="icon-filter" style="font-size:14px;"></i>
                    Filters
                    @if($activeCount > 0)
                    <span class="filter-badge">{{ $activeCount }}</span>
                    @endif
                </button>

                <a href="{{ route('admin.account.create') }}" class="tf-button style-1 w208" style="margin-left: auto;">
                    <i class="icon-plus""></i>
                    Add User
                </a>
            </div>

            {{-- Filter Panel --}}
            <div id="filter-panel" class="filter-panel {{ $activeCount > 0 ? 'open' : '' }}">
                <form method="GET" action="{{ route('admin.user.account') }}" class="row g-3 align-items-end p-3">
                    @if(request('name'))
                    <input type="hidden" name="name" value="{{ request('name') }}">
                    @endif
                    <div class="col-md-2">
                        <label class="form-label text-uppercase small fw-bold text-muted">User Type</label>
                        <select name="utype" class="form-select">
                            <option value="">All Types</option>
                            @foreach(['USR','ADM','EMP'] as $type)
                            <option value="{{ $type }}" {{ request('utype') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-uppercase small fw-bold text-muted">Total Orders</label>
                        <select name="orders" class="form-select">
                            <option value="">Any</option>
                            <option value="0" {{ request('orders') == '0' ? 'selected' : '' }}>0</option>
                            <option value="1-5" {{ request('orders') == '1-5' ? 'selected' : '' }}>1 - 5</option>
                            <option value="6-10" {{ request('orders') == '6-10' ? 'selected' : '' }}>6 - 10</option>
                            <option value="10+" {{ request('orders') == '10+' ? 'selected' : '' }}>10+</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-uppercase small fw-bold text-muted">From Date</label>
                        <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-uppercase small fw-bold text-muted">To Date</label>
                        <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                    </div>
                    <div class="col-md-2 d-flex">
                        <button type="submit" class="btn btn-primary flex-fill">Apply</button>
                    </div>
                    <div class="col-md-2 d-flex">
                        <a href="{{ route('admin.user.account') }}" class="btn btn-outline-secondary flex-fill text-center">Clear</a>
                    </div>
                </form>
            </div>
            {{-- ── Active Filter Chips ── --}}
            @if($activeCount > 0)
            <div class="active-filters">
                @if(request('utype'))
                <span class="filter-chip">
                    Type: {{ ucfirst(request('utype')) }}
                    <a href="{{ url()->current() . '?' . http_build_query(request()->except(['utype','page'])) }}">&times;</a>
                </span>
                @endif
                @if(request('orders'))
                <span class="filter-chip">
                    Orders: {{ request('orders') }}
                    <a href="{{ url()->current() . '?' . http_build_query(request()->except(['orders','page'])) }}">&times;</a>
                </span>
                @endif
                @if(request('from'))
                <span class="filter-chip">
                    From: {{ request('from') }}
                    <a href="{{ url()->current() . '?' . http_build_query(request()->except(['from','page'])) }}">&times;</a>
                </span>
                @endif
                @if(request('to'))
                <span class="filter-chip">
                    To: {{ request('to') }}
                    <a href="{{ url()->current() . '?' . http_build_query(request()->except(['to','page'])) }}">&times;</a>
                </span>
                @endif
            </div>
            @endif

            <div class="wg-table table-all-user">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>User</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Total Orders</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                            <tr>
                                <td>{{ $users->firstItem() + $loop->index }}</td>
                                <td class="pname">
                                    <div class="name">
                                        <a href="#" class="body-title-2">{{ $user->name}}</a>
                                    </div>
                                    <div class="text-tiny mt-1">
                                        {{ $user->utype }}
                                    </div>
                                </td>
                                <td>{{ $user->mobile }}</td>
                                <td>{{ $user->email}}</td>
                                <td>{{ $user->orders_count }}</td>

                                <td class="text-center">
                                    <div class="list-icon-function">
                                        <a href="{{route('admin.account.edit', ['id'=>$user->id])}}">
                                            <div class="item edit">
                                                <i class="icon-edit-3"></i>
                                            </div>
                                        </a>
                                        <form action="{{route('admin.account.delete', ['id'=>$user->id])}}" method="POST">
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
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        let searchTimeout;
        let currentRequest = null;

        const $input = $("#user-search-input");
        const $results = $("#user-box-content-search");
        const searchUrl = $input.data('search-url');

        $input.on("input", function() {
            let query = $(this).val().trim();

            clearTimeout(searchTimeout);
            if (currentRequest) currentRequest.abort();

            if (query.length < 2) {
                $results.html('');
                return;
            }

            $results.html('<li class="loading">Searching...</li>');

            searchTimeout = setTimeout(function() {
                currentRequest = $.ajax({
                    url: searchUrl,
                    type: 'GET',
                    data: {
                        query: query
                    },
                    dataType: 'json',
                    success: function(users) {
                        $results.html('');
                        if (!users || users.length === 0) {
                            $results.append('<li>No users found</li>');
                        } else {
                            users.forEach(function(user, index) {
                                const safeName = $('<div>').text(user.name).html();
                                const safeEmail = user.email ? $('<div>').text(user.email).html() : '';
                                const userUrl = "/admin/account/edit/" + user.id;

                                $results.append(`
                                <li class="user-id">
                                    <a href="${userUrl}" class="d-flex items-center gap14 mb-10">
                                        <div class="flex items-center justify-between gap20 flex-grow">
                                            <div class="name">
                                                <span class="body-text">${safeName}</span>
                                                ${safeEmail ? `<div class="text-tiny">${safeEmail}</div>` : ''}
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                ${index < users.length - 1 ? '<li><div class="divider"></div></li>' : ''}
                            `);
                            });
                        }
                    },
                    error: function(xhr, status) {
                        if (status !== 'abort') {
                            $results.html('<li class="error">Error loading results</li>');
                        }
                    },
                    complete: function() {
                        currentRequest = null;
                    }
                });
            }, 300);
        });

        // Hide dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#user-search-input, #user-box-content-search').length) {
                $results.html('');
            }
        });
    });

    $(function() {
        $("#filter-toggle-btn").on("click", function() {
            $("#filter-panel").toggleClass("open");
            $(this).toggleClass("active");
        });
    });
</script>
@endpush