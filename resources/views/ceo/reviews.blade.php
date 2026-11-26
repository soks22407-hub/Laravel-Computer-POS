@extends('layouts.ceo')
@section('content')
<style>
    .review-id {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        transition: background 0.2s ease;
        cursor: pointer;
    }

    .review-id:hover {
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

    #review-box-content-search li {
        list-style: none;
    }
</style>

<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding: 5px 0; margin-top: 0;">
            <h4>Reviews</h4>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('ceo.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">Reviews</div>
                </li>
            </ul>
        </div>

        @if(session('status'))
        <p class="alert alert-success">{{ session('status') }}</p>
        @endif

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <form class="form-search flex-grow" method="GET" action="{{ route('ceo.reviews') }}">
                        <fieldset class="name">
                            <input type="text" placeholder="Search by customer or product..."
                                class="show-search" name="search" id="review-search-input"
                                tabindex="2" value="{{ request('search') }}"
                                autocomplete="off">
                        </fieldset>
                        <div class="button-submit">
                            <button type="submit"><i class="icon-search"></i></button>
                        </div>
                        <div class="box-content-search">
                            <ul id="review-box-content-search"></ul>
                        </div>
                    </form>
                </div>
            </div>

            <div class="wg-table table-all-user">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Photo</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reviews as $key => $review)
                            <tr>
                                <td>{{ $reviews->firstItem() + $key }}</td>
                                <td>{{ $review->user->name ?? 'N/A' }}</td>
                                <td>{{ Str::limit($review->product->name ?? 'N/A', 30) }}</td>
                                <td>
                                    @for($i = 1; $i <= 5; $i++)
                                        <span style="color: {{ $i <= $review->rating ? '#f5a623' : '#ccc' }}; font-size:16px;">★</span>
                                        @endfor
                                        <small class="text-muted">({{ $review->rating }}/5)</small>
                                </td>
                                <td>{{ Str::limit($review->comment, 60) }}</td>
                                <td>
                                    @if($review->photo)
                                    <img src="{{ asset('uploads/reviews/' . $review->photo) }}"
                                        style="width:50px; height:50px; object-fit:cover; border-radius:4px;">
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $review->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="list-icon-function">
                                        <a href="{{ route('ceo.review.show', $review->id) }}">
                                            <div class="item eye">
                                                <i class="icon-eye"></i>
                                            </div>
                                        </a>
                                        <form action="{{ route('ceo.review.delete', $review->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <div class="item text-danger delete">
                                                <i class="icon-trash-2"></i>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No reviews found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $reviews->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Delete confirmation
    $(function() {
        $(".delete").on("click", function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            swal({
                title: "Are you sure?",
                text: "Do you want to delete this review?",
                type: "warning",
                buttons: ["No", "Yes"],
                confirmButtonColor: '#dc3545'
            }).then(function(result) {
                if (result) form.submit();
            });
        });
    });

    // Live search
    $(function() {
        $("#review-search-input").on("keyup", function() {
            var searchQuery = $(this).val();
            if (searchQuery.length > 1) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('ceo.search.review') }}",
                    data: {
                        query: searchQuery
                    },
                    dataType: 'json',
                    success: function(data) {
                        $("#review-box-content-search").html('');
                        $.each(data, function(index, review) {
                            const customerName = $('<div>').text(review.user_name).html();
                            const productName = $('<div>').text(review.product_name).html();
                            $("#review-box-content-search").append(`
                                <li class="review-id">
                                    <div class="flex items-center justify-between gap20 flex-grow">
                                        <div class="name">
                                            <span class="body-text">${customerName}</span>
                                            <span class="text-muted"> — ${productName}</span>
                                        </div>
                                        <span style="color:#f5a623;">
                                            ${'★'.repeat(review.rating)}${'☆'.repeat(5 - review.rating)}
                                        </span>
                                    </div>
                                </li>
                                <li><div class="divider"></div></li>
                            `);
                        });
                    }
                });
            } else {
                $("#review-box-content-search").html('');
            }
        });
    });
</script>
@endpush