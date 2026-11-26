@extends('layouts.admin')
@section('content')
<main class="pt-90">
    <div class="container">
        <h3 class="page-title">📦 Order Notifications</h3>

        @if(session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
        @endif

        <div class="card mt-4 p-3">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($notifications as $key => $n)
                    <tr class="{{ $n->is_read ? '' : 'table-warning' }}">
                        <td>{{ $key + 1 }}</td>
                        <td><strong>{{ $n->title }}</strong></td>
                        <td>{{ $n->message }}</td>
                        <td>{{ strtoupper($n->type) }}</td>
                        <td>
                            @if($n->is_read)
                            <span class="badge bg-success">Read</span>
                            @else
                            <span class="badge bg-danger">Unread</span>
                            @endif
                        </td>
                        <td>{{ $n->created_at->diffForHumans() }}</td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center gap-3 flex-nowrap">
                                @if(!$n->is_read)
                                <a href="{{ route('admin.inbox.show', $n->id) }}"
                                    class="btn btn-outline-primary rounded-circle d-flex align-items-center justify-content-center"
                                    style="width:30px; height:30px; font-size:18px;">
                                    <i class="icon-eye"></i>
                                </a>
                                @endif
                                <form action="{{ route('admin.inbox.delete', $n->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <!-- <button class="btn btn-sm btn-danger">Delete</button> -->
                                    <button type="button" class="btn btn-outline-danger rounded-circle d-flex align-items-center justify-content-center delete-btn"
                                        style="width:30px; height:30px; font-size:18px;">
                                        <i class="icon-trash-2"></i>
                                    </button>
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
            {{ $notifications->links('pagination::bootstrap-5') }}
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    $(function() {
        $(".delete-btn").on("click", function(e) {
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
</script>
@endpush

