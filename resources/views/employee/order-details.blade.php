@extends('layouts.employee')
@section('content')
<style>
    .table-transaction>tbody>tr:nth-of-type(odd) {
        --bs-table-accent-bg: #fff !important;
    }
</style>
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding: 5px 0; margin-top: 0;"​>
            <h4 class="mb-0">Order Details</h4>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{route('employee.index')}}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">Order Details</div>
                </li>
            </ul>
        </div>

        <div class="wg-box mt-3">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <h5>Ordered Details</h5>
                </div>
                <a class="tf-button style-2 w208" href="{{ route('employee.orders') }}">Back</a>
            </div>
            <div class="table-responsive">
                @if (Session::has('status'))
                <p class="alert alert-success">{{Session::get('status')}}</p>
                @endif
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="width:100px;">Order No</th>
                            <th>Customer Name</th>
                            <th>Mobile</th>
                            <th>Order Date</th>
                            <th>Delivered Date</th>
                            <th>Canceled Date</th>
                            <th>Order Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->name }}</td>
                            <td>{{ $order->phone }}</td>
                            <td>{{ $order->created_at }}</td>
                            <td>{{ $order->delivered_date }}</td>
                            <td>{{ $order->canceled_date }}</td>
                            <td>
                                @if ($order->status == 'delivered')
                                <span class="badge bg-success">Delivered</span>
                                @elseif ($order->status == 'canceled')
                                <span class="badge bg-danger">Canceled</span>
                                @elseif ($order->status == 'ordered')
                                <span class="badge bg-warning text-dark">Ordered</span>
                                @elseif ($order->status == 'shipped')
                                <span class="badge bg-info text-dark">Shipped</span>
                                @elseif ($order->status == 'out_for_delivery')
                                <span class="badge bg-primary">Out For Delivery</span>
                                @else
                                <span class="badge bg-secondary">Unknown</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="wg-box mt-5">
            <div class="flex items-center justify-between gap10 flex-wrap">
                <div class="wg-filter flex-grow">
                    <h5>Ordered Items</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="width:26%;" class="text-center">Name</th>
                            <th style="width:15%;" class="text-center">Category</th>
                            <th style="width:15%;" class="text-center">Brand</th>
                            <th style="width:7%;" class="text-center">Quantity</th>
                            <!-- <th style="width:19%;">SKU</th> -->
                            <th style="width:10%;" class="text-center">Price</th>
                            <th class="text-center">Return Status</th>
                            <th class="text-center">Options</th>
                            
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orderItems as $item)
                        <tr>
                            <td class="pname">
                                <div class="image">
                                    <img src="{{ asset('uploads/products/Thumbnails') }}/{{ $item->product->image }}" alt="{{ $item->product->name }}" class="image">
                                </div>
                                <div class="name">
                                    <a href="{{ route('shop.product.details', ['product_slug' =>$item->product->slug])}}" target="_blank"
                                        class="body-title-2">{{ $item->product->name }} <br/> 
                                        <small class="text-muted">{{ $item->product->SKU }}</small>
                                    </a>
                                </div>
                            </td>
                            <td class="text-center">{{ $item->product->category->name }}</td>
                            <td class="text-center">{{ $item->product->brand->name }}</td>       
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">${{ $item->price }}</td>
                            <td class="text-center">{{ $item->rstatus == 0 ? "No":"Yes"}}</td>
                            <td class="text-center">{{ $item->options }}</td>
                            <!-- <td class="text-center">
                                <div class="list-icon-function view-icon">
                                    <div class="item eye">
                                        <i class="icon-eye"></i>
                                    </div>
                                </div>
                            </td> -->
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $orderItems->links('pagination::bootstrap-5') }}
            </div>
        </div>

        <div class="wg-box mt-5">
            <h5>Shipping Address</h5>
            <div class="my-account__address-item col-md-6">
                <div class="my-account__address-item__detail">
                    <p><strong>Name : </strong> {{ $order->name }}</p>
                    <p><strong>Address : </strong> {{ $order->address }}</p>
                    <p><strong>Locality : </strong> {{ $order->locality }}</p>
                    <p><strong>Province : </strong> {{ $order->province }}, {{ $order->country }}</p>
                    <p><strong>Landmark : </strong> {{ $order->landmark }}</p>
                    <p><strong>Mobile : </strong>{{ $order->phone }}</p>
                </div>
            </div>
        </div>

        <div class="wg-box mt-5">
            <h5>Transactions</h5>
            <table class="table table-striped table-bordered table-transaction">
                <thead>
                    <tr>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">Shipping</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Payment Mode</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-end">${{ $order->subtotal }}</td>                       
                        <td class="text-end">${{ $order->shipping }}</td>                       
                        <td class="text-end">${{ $order->total }}</td>                      
                        <td class="text-center">{{ $transaction->mode }}</td>                       
                        <td class="text-center">
                            @if ($transaction->status == 'approved')
                            <span class="badge bg-success">Approved</span>
                            @elseif ($transaction->status == 'declined')
                            <span class="badge bg-danger">Declined</span>
                            @elseif ($transaction->status == 'refunded')
                            <span class="badge bg-secondary">Refunded</span>
                            @else
                            <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="wg-box mt-5">
            <h5>Update Order Status</h5>

            @if(session('error'))
            <div class="alert alert-danger mt-2">{{ session('error') }}</div>
            @endif

            @php
            $statuses = [
            'ordered' => ['label' => 'Ordered', 'icon' => 'fa-box', 'bg' => '#6c757d', 'border' => '#6c757d'],
            'shipped' => ['label' => 'Shipped', 'icon' => 'fa-truck', 'bg' => '#0dcaf0', 'border' => '#0dcaf0'],
            'out_for_delivery' => ['label' => 'Out for Delivery', 'icon' => 'fa-motorcycle', 'bg' => '#ffc107', 'border' => '#ffc107'],
            'delivered' => ['label' => 'Delivered', 'icon' => 'fa-circle-check', 'bg' => '#198754', 'border' => '#198754'],
            'canceled' => ['label' => 'Canceled', 'icon' => 'fa-circle-xmark', 'bg' => '#dc3545', 'border' => '#dc3545'],
            ];
            $isLocked = in_array($order->status, ['canceled', 'delivered']);
            @endphp

            <form action="{{ route('employee.order.status.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="order_id" value="{{ $order->id }}" />
                <input type="hidden" name="order_status" id="order_status" value="{{ $order->status }}" />

                <div class="row align-items-center g-2 mt-2">
                    @foreach($statuses as $value => $meta)
                    <div class="col-auto">
                        @php
                        $isActive = $order->status === $value;
                        $btnStyle = $isActive
                        ? "background-color:{$meta['bg']}; border: 2px solid {$meta['border']}; color:#fff;"
                        : "background-color:transparent; border: 2px solid {$meta['border']}; color:{$meta['bg']};";
                        @endphp
                        <button type="button"
                            id="btn-{{ $value }}"
                            onclick="selectStatus('{{ $value }}')"
                            {{ $isLocked ? 'disabled' : '' }}
                            class="btn tf-button w208 status-btn"
                            style="{{ $btnStyle }}">
                            <i class="fa-solid {{ $meta['icon'] }} me-1"></i>
                            {{ $meta['label'] }}
                        </button>
                    </div>
                    @endforeach
                </div>

                @if($isLocked)
                <p class="text-muted mt-2 mb-0">
                    <i class="fa-solid fa-lock me-1"></i>
                    This order is <strong>{{ $order->status }}</strong> and cannot be updated.
                </p>
                @endif

                <div class="row align-items-center g-2 mt-3">
                    <div class="col-auto">
                        <a href="{{ route('employee.orders.invoice', $order->id) }}"
                            target="_blank"
                            class="btn btn-warning tf-button w208">
                            <i class="fa-solid fa-print me-1"></i>
                            Print Invoice
                        </a>
                    </div>
                </div>
            </form>


            <div id="status-config"
                data-statuses='@json(collect($statuses)->map(fn($m) => ["bg" => $m["bg"], "border" => $m["border"]]))'
                data-locked="{{ $isLocked ? 'true' : 'false' }}"
                style="display:none">
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const config = document.getElementById('status-config');
    const statuses = JSON.parse(config.dataset.statuses);
    const isLocked = config.dataset.locked === 'true';
    const statusUpdateUrl = "{{ route('employee.order.status.update') }}";

    function selectStatus(value) {
        if (isLocked) return;

        document.getElementById('order_status').value = value;

        document.querySelectorAll('.status-btn').forEach(btn => {
            const key = btn.id.replace('btn-', '');
            const s = statuses[key];
            btn.style.backgroundColor = 'transparent';
            btn.style.border = '2px solid ' + s.bg;
            btn.style.color = s.bg;
        });

        const selected = document.getElementById('btn-' + value);
        const s = statuses[value];
        selected.style.backgroundColor = s.bg;
        selected.style.border = '2px solid ' + s.border;
        selected.style.color = '#fff';

        document.querySelector('form[action="' + statusUpdateUrl + '"]').submit();
    }
</script>
@endpush