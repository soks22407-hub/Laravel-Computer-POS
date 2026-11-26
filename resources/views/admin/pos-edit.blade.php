@extends('layouts.admin')
@section('content')
<style>
    .badge-cash {
        background: #28a745;
    }

    .badge-aba {
        background: #0066cc;
    }

    .badge-wing {
        background: #ff6600;
    }

    .badge-aceleda {
        background: #cc0000;
    }

    .badge-amret {
        background: #009966;
    }

    .badge-card {
        background: #6f42c1;
    }

    .badge-credit {
        background: #6f42c1;
    }

    .product-row {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
    }

    .product-row:hover {
        background: #e9ecef;
    }

    .remove-item-btn {
        cursor: pointer;
        color: #dc3545;
        background: none;
        border: none;
        font-size: 1.2rem;
        padding: 0 6px;
    }

    .remove-item-btn:hover {
        color: #a71d2a;
    }

    #summary-box {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 20px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 1px dashed #dee2e6;
    }

    .summary-row:last-child {
        border-bottom: none;
    }

    .summary-total {
        font-size: 1.2rem;
        font-weight: 700;
    }
</style>

<div class="main-content-inner">
    <div class="main-content-wrap">

        {{-- Header --}}
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <div>
                <h3>✏️ Edit POS Order #{{ $order->id }}</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10 mt-2">
                    <li><a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a></li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li><a href="{{ route('admin.pos.history') }}">
                            <div class="text-tiny">POS History</div>
                        </a></li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li>
                        <div class="text-tiny">Edit Order</div>
                    </li>
                </ul>
            </div>
            <a href="{{ route('admin.pos.history') }}" class="btn btn-outline-secondary">⬅ Back to History</a>
        </div>

        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form action="{{ route('admin.pos.update', $order->id) }}" method="POST" id="edit-order-form">
            @csrf
            @method('PUT')

            <div class="row g-4">

                {{-- LEFT: Customer + Items --}}
                <div class="col-lg-8">

                    {{-- Customer Info --}}
                    <div class="wg-box mb-4">
                        <h5>👤 Customer Information</h5>
                        <div class="divider my-3"></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Customer Name</label>
                                <input type="text" name="customer_name" class="form-control"
                                    placeholder="Walk-in Customer"
                                    value="{{ old('customer_name', $order->customer_name) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone Number</label>
                                <input type="text" name="customer_phone" class="form-control"
                                    placeholder="e.g. 0912345678"
                                    value="{{ old('customer_phone', $order->customer_phone) }}">
                            </div>
                        </div>
                    </div>

                    {{-- Order Items --}}
                    <div class="wg-box mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <h5 class="mb-0">🛒 Order Items</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                + Add Product
                            </button>
                        </div>
                        <div class="divider my-3"></div>

                        <div id="items-container">
                            @foreach($order->items as $index => $item)
                            <div class="product-row d-flex align-items-center gap-3" data-index="{{ $index }}">
                                <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark">{{ $item->product_name }}</div>
                                    <small class="text-muted">${{ number_format($item->price, 2) }} each</small>
                                    <input type="hidden" name="items[{{ $index }}][price]" value="{{ $item->price }}">
                                </div>
                                <div style="width: 110px;">
                                    <label class="form-label small mb-1">Qty</label>
                                    <input type="number" name="items[{{ $index }}][quantity]"
                                        class="form-control form-control-sm item-qty"
                                        value="{{ $item->quantity }}" min="1"
                                        data-price="{{ $item->price }}">
                                </div>
                                <div style="width: 100px;" class="text-end">
                                    <label class="form-label small mb-1">Subtotal</label>
                                    <div class="fw-bold item-subtotal">${{ number_format($item->subtotal, 2) }}</div>
                                </div>
                                <button type="button" class="remove-item-btn" title="Remove item">✕</button>
                            </div>
                            @endforeach
                        </div>

                        <div class="text-muted text-center py-3" id="no-items-msg" style="{{ $order->items->count() ? 'display:none' : '' }}">
                            No items added. Click "Add Product" to add items.
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="wg-box mb-4">
                        <h5>📝 Notes</h5>
                        <div class="divider my-3"></div>
                        <textarea name="note" class="form-control" rows="3"
                            placeholder="Order notes or remarks...">{{ old('note', $order->note) }}</textarea>
                    </div>

                </div>

                {{-- RIGHT: Payment + Summary --}}
                <div class="col-lg-4">

                    {{-- Payment Details --}}
                    <div class="wg-box mb-4">
                        <h5>💳 Payment Details</h5>
                        <div class="divider my-3"></div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Discount ($)</label>
                            <input type="number" name="discount" id="discount-input" class="form-control"
                                step="0.01" min="0" placeholder="0.00"
                                value="{{ old('discount', $order->discount) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                @foreach(['cash','aba','wing','aceleda','amret','card','credit'] as $p)
                                <option value="{{ $p }}" {{ old('payment_method', $order->payment_method) == $p ? 'selected' : '' }}>
                                    {{ strtoupper($p) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Amount Paid ($)</label>
                            <input type="number" name="amount_paid" id="amount-paid-input" class="form-control"
                                step="0.01" min="0" placeholder="0.00"
                                value="{{ old('amount_paid', $order->amount_paid) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" class="form-select">
                                <option value="completed" {{ old('status', $order->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="credit" {{ old('status', $order->status) == 'credit' ? 'selected' : '' }}>Credit</option>
                                <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>

                    {{-- Live Summary --}}
                    <div class="wg-box mb-4">
                        <h5>🧾 Order Summary</h5>
                        <div class="divider my-3"></div>
                        <div id="summary-box">
                            <div class="summary-row">
                                <span class="text-muted">Subtotal</span>
                                <span id="sum-subtotal" class="fw-medium">$0.00</span>
                            </div>
                            <div class="summary-row">
                                <span class="text-muted">Discount</span>
                                <span id="sum-discount" class="text-danger">-$0.00</span>
                            </div>
                            <div class="summary-row">
                                <span class="text-muted">Total</span>
                                <span id="sum-total" class="fw-bold">$0.00</span>
                            </div>
                            <div class="summary-row">
                                <span class="text-muted">Amount Paid</span>
                                <span id="sum-paid" class="text-success fw-medium">$0.00</span>
                            </div>
                            <div class="summary-row summary-total mt-2">
                                <span id="sum-change-label">Change</span>
                                <span id="sum-change" class="text-success">$0.00</span>
                            </div>
                        </div>
                    </div>

                    {{-- Save Button --}}
                    <button type="submit" class="btn btn-primary w-100 btn-lg fw-bold py-3">
                        💾 Save Changes
                    </button>
                    <a href="{{ route('admin.pos.history') }}" class="btn btn-outline-secondary w-100 mt-2">
                        Cancel
                    </a>

                </div>
            </div>
        </form>
    </div>
</div>

{{-- Add Product Modal --}}
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="product-search-input" class="form-control mb-3" placeholder="Search by name or SKU...">
                <div id="product-search-results">
                    <p class="text-muted text-center">Start typing to search products...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let itemIndex = {{ $order -> items -> count()}};

        // ── Live summary calculation ──────────────────────────────
        function recalculate() {
            let subtotal = 0;

            document.querySelectorAll('.product-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
                const price = parseFloat(row.querySelector('.item-qty').dataset.price) || 0;
                const sub = qty * price;
                subtotal += sub;
                row.querySelector('.item-subtotal').textContent = '$' + sub.toFixed(2);
            });

            const discount = parseFloat(document.getElementById('discount-input').value) || 0;
            const total = Math.max(0, subtotal - discount);
            const paid = parseFloat(document.getElementById('amount-paid-input').value) || 0;
            const change = paid - total;

            document.getElementById('sum-subtotal').textContent = '$' + subtotal.toFixed(2);
            document.getElementById('sum-discount').textContent = '-$' + discount.toFixed(2);
            document.getElementById('sum-total').textContent = '$' + total.toFixed(2);
            document.getElementById('sum-paid').textContent = '$' + paid.toFixed(2);

            const changeEl = document.getElementById('sum-change');
            const changeLbl = document.getElementById('sum-change-label');

            if (change >= 0) {
                changeLbl.textContent = 'Change';
                changeEl.textContent = '$' + change.toFixed(2);
                changeEl.className = 'text-success';
            } else {
                changeLbl.textContent = 'Owed';
                changeEl.textContent = '-$' + Math.abs(change).toFixed(2);
                changeEl.className = 'text-danger';
            }
        }

        // ── Listen for input changes ──────────────────────────────
        document.getElementById('edit-order-form').addEventListener('input', recalculate);
        recalculate(); // initial

        // ── Remove item ──────────────────────────────────────────
        document.getElementById('items-container').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item-btn')) {
                e.target.closest('.product-row').remove();
                toggleNoItemsMsg();
                recalculate();
            }
        });

        function toggleNoItemsMsg() {
            const hasItems = document.querySelectorAll('.product-row').length > 0;
            document.getElementById('no-items-msg').style.display = hasItems ? 'none' : '';
        }

        // ── Product search modal ──────────────────────────────────
        let searchTimeout;
        document.getElementById('product-search-input').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const q = this.value.trim();
            if (q.length < 2) {
                document.getElementById('product-search-results').innerHTML = '<p class="text-muted text-center">Type at least 2 characters...</p>';
                return;
            }
            searchTimeout = setTimeout(() => {
                fetch(`{{ route('admin.pos.search') }}?query=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(products => {
                        if (!products.length) {
                            document.getElementById('product-search-results').innerHTML = '<p class="text-muted text-center">No products found.</p>';
                            return;
                        }
                        let html = '<div class="list-group">';
                        products.forEach(p => {
                            const price = p.sale_price || p.regular_price;
                            html += `
                            <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center add-product-btn"
                                data-id="${p.id}" data-name="${p.name}" data-price="${price}" data-stock="${p.quantity}">
                                <div>
                                    <div class="fw-bold">${p.name}</div>
                                    <small class="text-muted">SKU: ${p.SKU} | Stock: ${p.quantity}</small>
                                </div>
                                <span class="badge bg-primary">$${parseFloat(price).toFixed(2)}</span>
                            </button>`;
                        });
                        html += '</div>';
                        document.getElementById('product-search-results').innerHTML = html;
                    });
            }, 300);
        });

        // ── Add product from modal ────────────────────────────────
        document.getElementById('product-search-results').addEventListener('click', function(e) {
            const btn = e.target.closest('.add-product-btn');
            if (!btn) return;

            const id = btn.dataset.id;
            const name = btn.dataset.name;
            const price = parseFloat(btn.dataset.price);
            const idx = itemIndex++;

            const row = document.createElement('div');
            row.className = 'product-row d-flex align-items-center gap-3';
            row.dataset.index = idx;
            row.innerHTML = `
            <input type="hidden" name="items[${idx}][product_id]" value="${id}">
            <div class="flex-grow-1">
                <div class="fw-bold text-dark">${name}</div>
                <small class="text-muted">$${price.toFixed(2)} each</small>
                <input type="hidden" name="items[${idx}][price]" value="${price}">
            </div>
            <div style="width:110px;">
                <label class="form-label small mb-1">Qty</label>
                <input type="number" name="items[${idx}][quantity]"
                    class="form-control form-control-sm item-qty"
                    value="1" min="1" data-price="${price}">
            </div>
            <div style="width:100px;" class="text-end">
                <label class="form-label small mb-1">Subtotal</label>
                <div class="fw-bold item-subtotal">$${price.toFixed(2)}</div>
            </div>
            <button type="button" class="remove-item-btn" title="Remove">✕</button>
        `;

            document.getElementById('items-container').appendChild(row);
            toggleNoItemsMsg();
            recalculate();

            // Close modal & reset search
            bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();
            document.getElementById('product-search-input').value = '';
            document.getElementById('product-search-results').innerHTML = '<p class="text-muted text-center">Start typing to search products...</p>';
        });
    });
</script>
@endpush