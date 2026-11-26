@extends('layouts.app')
@section('content')
<style>
    .table> :not(caption)>tr>th {
        padding: 0.625rem 1.5rem .625rem !important;
        background-color: #6a6e51 !important;
    }

    .table>tr>td {
        padding: 0.625rem 1.5rem .625rem !important;
    }

    .table-bordered> :not(caption)>tr>th,
    .table-bordered> :not(caption)>tr>td {
        border-width: 1px 1px;
        border-color: #6a6e51;
    }

    .table> :not(caption)>tr>td {
        padding: .8rem 1rem !important;
    }

    .bg-success {
        background-color: #40c710 !important;
    }

    .bg-danger {
        background-color: #f44032 !important;
    }

    .bg-warning {
        background-color: #f5d700 !important;
        color: #000;
    }

    
</style>
<main class="pt-90" style="padding-top: 0px;">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
        <h2 class="page-title">Address</h2>
        <div class="row">
            <div class="col-lg-2">
                @include('user.account-nav')
            </div>
            <div class="col-lg-10">
                <div class="wg-table table-all-user">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Phone</th>
                                    <th class="text-center">Province</th>
                                    <th class="text-center">Address</th>
                                    <th class="text-center">Locality</th>
                                    <th class="text-center">Land Mark</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($addresses as $add)
                                <tr>
                                    <td class="text-center">{{ $add->name }}</td>
                                    <td class="text-center">{{ $add->phone }}</td>
                                    <td class="text-center">{{ $add->province }}</td>
                                    <td class="text-center">{{ $add->address }}</td>
                                    <td class="text-center">{{ $add->locality }}</td>
                                    <td class="text-center">{{ $add->landmark }}</td>

                                    <td class="text-center">
                                        <a href="{{ route('user.address.edit', ['id'=>$add->id]) }}">
                                            <div class="list-icon-function view-icon">
                                                <div class="item eye">
                                                    <i class="fa fa-eye"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                </tr>
                                
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{ $addresses->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>
    </section>
</main>
@endsection