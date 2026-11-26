@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding: 5px 0; margin-top: 0;">
            <h4>Products Sold by {{ $staff->name }}</h4>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <a href="{{ route('admin.staff.reports') }}">
                        <div class="text-tiny">Staff Reports</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">{{ $staff->name }}</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="mb-3 d-flex justify-content-between">
                <a href="{{ route('admin.staff.reports') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Staff Reports
                </a>
                <button type="button" class="btn btn-primary" onclick="printProductsReport()">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>

            <div class="wg-table table-all-user">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="products-report-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th style="width: 400px;">Product</th>
                                <th class="text-center" style="width: 200px;">Qty Sold</th>
                                <th class="text-end">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                            <tr>
                                <td>{{ $products->firstItem() + $loop->index }}</td>
                                <td style="width: 400px;">{{ $product->product_name }}</td>
                                <td class="text-center">{{ $product->total_qty }}</td>
                                <td class="text-end">${{ number_format($product->total_revenue, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No products sold in this period.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@include('admin.staff.partials.print-script')

<script>
    function printProductsReport() {
        var table = document.getElementById('products-report-table').cloneNode(true);

        var rangeText = buildRangeText(
            "{{ request('from') }}",
            "{{ request('to') }}",
            "{{ request('month') }}",
            "{{ request('year') }}",
            "{{ request('preset') }}"
        );

        printExcelStyle({
            title: 'Dola Dola Phone Shop',
            subtitle: 'Products Sold by {{ $staff->name }}',
            meta: rangeText,
            tableHtml: table.outerHTML
        });
    }
</script>
@endsection