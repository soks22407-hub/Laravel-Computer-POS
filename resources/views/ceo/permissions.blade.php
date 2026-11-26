@extends('layouts.ceo')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding: 5px 0; margin-top: 0;">
            <h4>Employee Permissions</h4>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('ceo.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">Permissions</div>
                </li>
            </ul>
        </div>

        @if (session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif
        @if (session('error'))
        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
        @endif

        @if ($employeeCount === 0)
        <div class="wg-box">
            <p class="text-muted mb-0">Add an employee (utype = EMP) first to assign permissions.</p>
        </div>
        @else
        <form method="POST" action="{{ route('ceo.permissions.update') }}" id="permissions-form">
            @csrf

            <div class="wg-box">
                <p class="mb-0">
                    Set what employees can do below. Saving applies these permissions to
                    <strong>all {{ $employeeCount }} employee{{ $employeeCount === 1 ? '' : 's' }}</strong>
                    — it overwrites any permissions they currently have.
                </p>
            </div>

            <div class="wg-box mt-3">
                <div class="wg-table table-all-user">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 30%">Page / Module</th>
                                    <th class="text-center">View</th>
                                    <th class="text-center">Add</th>
                                    <th class="text-center">Edit</th>
                                    <th class="text-center">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($modules as $key => $label)
                                    @php $perm = $existing->get($key); @endphp
                                    <tr>
                                        <td class="body-title-2">{{ $label }}</td>
                                        <td class="text-center">
                                            <input type="checkbox" name="permissions[{{ $key }}][view]" @checked($perm && $perm->can_view)>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="permissions[{{ $key }}][add]" @checked($perm && $perm->can_add)>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="permissions[{{ $key }}][edit]" @checked($perm && $perm->can_edit)>
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" name="permissions[{{ $key }}][delete]" @checked($perm && $perm->can_delete)>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save &amp; Apply to All Employees
                </button>
            </div>
        </form>
        @endif

        <p class="text-muted mt-3 small">
            Note: the CEO account automatically has full access to every page and is not listed here.
        </p>
    </div>
</div>
@endsection