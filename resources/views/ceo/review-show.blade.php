@extends('layouts.ceo')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Review Detail</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('ceo.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('ceo.reviews') }}">
                        <div class="text-tiny">Reviews</div>
                    </a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">Detail</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="row gap20">

                {{-- Left: info --}}
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width:160px;">Customer</th>
                            <td>{{ $review->user->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Product</th>
                            <td>{{ $review->product->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Rating</th>
                            <td>
                                @for($i = 1; $i <= 5; $i++)
                                    <span style="color: {{ $i <= $review->rating ? '#f5a623' : '#ccc' }}; font-size:20px;">★</span>
                                    @endfor
                                    <span class="text-muted ms-1">({{ $review->rating }} / 5)</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Comment</th>
                            <td>{{ $review->comment ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <td>{{ $review->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    </table>

                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('ceo.reviews') }}" class="btn btn-outline-secondary">
                            ← Back
                        </a>
                        <form action="{{ route('ceo.review.delete', $review->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-danger delete">
                                <i class="icon-trash-2"></i> Delete Review
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Right: photo --}}
                <div class="col-md-4">
                    @if($review->photo)
                    <div class="wg-box text-center p-3">
                        <p class="fw-bold mb-2">Review Photo</p>
                        <img src="{{ asset('uploads/reviews/' . $review->photo) }}"
                            style="max-width:100%; border-radius:8px;">
                    </div>
                    @else
                    <div class="wg-box text-center p-3 text-muted">
                        No photo uploaded.
                    </div>
                    @endif
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
</script>
@endpush