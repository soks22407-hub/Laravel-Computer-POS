@extends('layouts.employee')
@section('content')
<style>
    .table td {
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        max-width: 150px;
    }

    .table th {
        white-space: normal;
        max-width: unset;
    }
    .contact-id {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        transition: background 0.2s ease;
        cursor: pointer;
    }

    .contact-id:hover {
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

    
    #box-content-search li {
        list-style: none;
    }

    #box-content-search .user-id {
        margin-bottom: 10px;
    }
</style>
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>All Messages</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('employee.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">All Messages</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search flex-grow" method="GET">
                        <fieldset class="name">
                            <input type="text" placeholder="Search here..." class="show-search" name="name" id="message-search-input"
                                tabindex="2" value="{{ request('name') }}" aria-required="true" required="" data-search-url="{{ route('employee.search.contact') }}" autocomplete="off">
                        </fieldset>
                        <div class="button-submit">
                            <button class="" type="submit"><i class="icon-search"></i></button>
                        </div>
                        <div class="box-content-search">
                            <ul id="message-box-content-search"></ul>
                        </div>
                    </form>
                </div>
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
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($contacts as $contact)
                            <tr>
                                <td>{{ $contacts->firstItem() + $loop->index }}</td>
                                <td>{{$contact->name}}</td>
                                <td>{{$contact->email}}</td>
                                <td>{{$contact->phone}}</td>
                                <td>{{$contact->comment}}</td>
                                <td>{{$contact->created_at}}</td>
                                <td>
                                    <div class="list-icon-function">
                                        <a href="{{ route('employee.contact.show', ['id' => $contact->id]) }}" class="text-primary">
                                            <i class="icon-eye"></i>
                                        </a>
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
                {{ $contacts->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>

    $(function() {
        let searchTimeout;
        let currentRequest = null;

        const $input = $("#message-search-input");
        const $results = $("#message-box-content-search");
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
                    success: function(contacts) {
                        $results.html('');
                        if (!contacts || contacts.length === 0) {
                            $results.append('<li>No contacts found</li>');
                        } else {
                            contacts.forEach(function(contact, index) {
                                const safeName = $('<div>').text(contact.name).html();
                                const safeEmail = contact.email ? $('<div>').text(contact.email).html() : '';
                                const contactUrl = "/employee/contact/" + contact.id;

                                $results.append(`
                                <li class="contact-id">
                                    <a href="${contactUrl}" class="d-flex items-center gap14 mb-10">
                                        <div class="flex items-center justify-between gap20 flex-grow">
                                            <div class="name">
                                                <span class="body-text">${safeName}</span>
                                                ${safeEmail ? `<div class="text-tiny">${safeEmail}</div>` : ''}
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                ${index < contacts.length - 1 ? '<li><div class="divider"></div></li>' : ''}
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

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#message-search-input, #message-box-content-search').length) {
                $results.html('');
            }
        });
    });
</script>
@endpush