@extends('layouts.ceo')
@section('content')
<style>
    .pos-product-card {
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
    }

    .pos-product-card:hover {
        border-color: #2377FC;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .pos-product-card img {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border-radius: 6px;
    }

    .cart-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 8px;
    }

    #pos-search {
        font-size: 16px;
        padding: 12px;
    }

    .payment-btn {
        border: 2px solid #d8e5f1;
        border-radius: 8px;
        padding: 10px;
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
    }

    .payment-btn img {
        width: 28px;
        height: 28px;
        object-fit: contain;
    }

    /*.payment-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 80px;
    }*/

    .payment-btn.selected {
        border-color: #2377FC;
        background: #e8f0fe;
        color: #2377FC;
        font-weight: bold;
    }

    .pos-numpad button {
        width: 100%;
        padding: 12px;
        font-size: 18px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        background: white;
        cursor: pointer;
        transition: background 0.1s;
    }

    .pos-numpad button:hover {
        background: #f0f0f0;
    }
</style>

<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding: 5px 0; margin-top: 0;">
            <h3>🛒 Point of Sale</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('ceo.pos.history') }}" class="btn btn-outline-secondary">
                    📋 Sale History
                </a>
            </div>
        </div>

        <div class="row g-3">
            {{-- LEFT: Product Search & Grid --}}
            <div class="col-lg-7">
                <div class="wg-box mb-3">
                    {{-- Search --}}
                    <div class="position-relative mb-3">
                        <input type="text" id="pos-search" class="form-control"
                            placeholder="🔍 Search product by name or SKU or Code...">
                        <div id="pos-search-results" class="position-absolute w-100 bg-white shadow rounded"
                            style="top:100%; z-index:999; display:none; max-height:300px; overflow-y:auto;">
                        </div>
                    </div>

                    {{-- Barcode --}}
                    <div class="d-flex gap-2 mb-3">
                        <input type="text" id="barcode-input" class="form-control"
                            placeholder="📷 Scan barcode">
                        <button class="btn btn-outline-primary" onclick="searchBySKU()">
                            Add
                        </button>
                    </div>
                </div>

                {{-- Product Grid --}}
                <div class="wg-box" style="margin-top: 16px;">
                    <div class="row g-2" id="product-grid">
                        @foreach($products as $product)
                        <div class="col-4 col-md-3">
                            @if($product->variants->count() > 0)
                            <div class="pos-product-card wg-box p-2 text-center"
                                onclick="openVariantModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ asset('uploads/products/Thumbnails/'.$product->image) }}', {{ $product->variants->toJson() }})">
                                <img src="{{ asset('uploads/products/Thumbnails/'.$product->image) }}" alt="{{ $product->name }}">
                                <div class="mt-1" style="font-size:12px; font-weight:600;">
                                    {{ Str::limit($product->name, 20) }}
                                </div>
                                <div class="text-primary fw-bold" style="font-size:13px;">
                                    From ${{ number_format($product->variants->min('price'), 2) }}
                                </div>
                                <div class="text-muted" style="font-size:11px;">
                                    Stock: {{ $product->quantity }}
                                </div>
                                <div style="font-size:10px;" class="badge bg-info mt-1">Select Variant</div>
                            </div>
                            @else
                            {{-- No variants — add directly --}}
                            <div class="pos-product-card wg-box p-2 text-center"
                                onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->sale_price ?: $product->regular_price }}, {{ $product->quantity }})">
                                <img src="{{ asset('uploads/products/Thumbnails/'.$product->image) }}" alt="{{ $product->name }}">
                                <div class="mt-1" style="font-size:12px; font-weight:600;">
                                    {{ Str::limit($product->name, 20) }}
                                </div>
                                <div class="text-primary fw-bold" style="font-size:13px;">
                                    ${{ number_format($product->sale_price ?: $product->regular_price, 2) }}
                                </div>
                                <div class="text-muted" style="font-size:11px;">
                                    Stock: {{ $product->quantity }}
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- RIGHT: Cart & Checkout --}}
            <div class="col-lg-5">
                <div class="wg-box">
                    <h5 class="mb-3">🧾 Current Sale</h5>

                    {{-- Customer Info --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <input type="text" id="customer-name" class="form-control form-control-sm"
                                placeholder="Customer name (optional)">
                        </div>
                        <div class="col-6">
                            <input type="text" id="customer-phone" class="form-control form-control-sm"
                                placeholder="Phone (optional)">
                        </div>
                    </div>

                    {{-- Cart Items --}}
                    <div id="cart-items" style="min-height:200px; max-height:300px; overflow-y:auto;">
                        <p class="text-muted text-center mt-4" id="empty-cart-msg">
                            No items added yet
                        </p>
                    </div>

                    <hr>

                    {{-- Discount from Discount Page --}}
                    <div class="mb-2">
                        <label class="form-label fw-medium">Discount</label>
                        <select id="discount-select" class="form-select form-select-sm" onchange="applyDiscount(this)">
                            <option value="">-- No Discount --</option>
                        </select>
                        <small id="discount-info" class="text-muted"></small>
                    </div>

                    {{-- Totals --}}
                    <table class="w-100 mb-3">
                        <tr>
                            <td>Subtotal</td>
                            <td class="text-end fw-bold" id="display-subtotal">$0.00</td>
                        </tr>
                        <tr id="selected-discount-row" style="display:none;">
                            <td><span class="text-success" id="selected-discount-label">Discount</span></td>
                            <td class="text-end text-success" id="display-selected-discount">-$0.00</td>
                        </tr>
                        <tr>
                            <td>
                                Manual Discount ($)
                                <input type="number" id="discount-input" min="0" value="0"
                                    class="form-control form-control-sm d-inline-block ms-2"
                                    style="width:80px;" oninput="recalculate()">
                            </td>
                            <td class="text-end text-danger" id="display-discount">-$0.00</td>
                        </tr>
                        <tr>
                            <td><strong>TOTAL</strong></td>
                            <td class="text-end">
                                <strong class="text-primary fs-5" id="display-total">$0.00</strong>
                            </td>
                        </tr>
                    </table>

                    {{-- Payment Method --}}
                    <label class="form-label fw-medium">Payment Method</label>
                    <div class="row g-2 mb-3" id="payment-methods">

                        @php
                        $payments = [
                        'cash' => [
                        'label' => 'Cash',
                        'icon' => '💵'
                        ],
                        'aba' => [
                        'label' => 'ABA',
                        'icon' => asset('icons/logo bank/aba.png')
                        ],
                        'wing' => [
                        'label' => 'Wing',
                        'icon' => asset('icons/logo bank/wing.png')
                        ],
                        'aceleda' => [
                        'label' => 'ACLEDA',
                        'icon' => asset('icons/logo bank/acleda.png')
                        ],
                        'amret' => [
                        'label' => 'Amret',
                        'icon' => asset('icons/logo bank/amret.png')
                        ],
                        'card' => [
                        'label' => 'Card',
                        'icon' => '💳'
                        ],
                        'credit' => [
                        'label' => 'Credit',
                        'icon' => '📒'
                        ],
                        ];
                        @endphp

                        @foreach($payments as $val => $payment)
                        <div class="col-4">
                            <div class="payment-btn {{ $val == 'cash' ? 'selected' : '' }}"
                                onclick="selectPayment('{{ $val }}', this)">

                                @if(Str::startsWith($payment['icon'], 'http'))
                                <img src="{{ $payment['icon'] }}"
                                    alt="{{ $payment['label'] }}"
                                    style="width:28px;height:28px;object-fit:contain;">
                                @else
                                <span style="font-size:24px;">{{ $payment['icon'] }}</span>
                                @endif

                                <div class="mt-1">{{ $payment['label'] }}</div>
                            </div>
                        </div>
                        @endforeach

                    </div>

                    {{-- Amount Paid --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium">Amount Paid ($)</label>
                        <input type="number" id="amount-paid" class="form-control form-control-lg"
                            placeholder="0.00" min="0" step="0.01" oninput="calcChange()">
                        <div class="mt-2 p-2 rounded text-center"
                            style="background:#e8f5e9; font-size:16px; display:none;" id="change-display">
                            💰 Change: <strong id="change-amount" class="text-success">$0.00</strong>
                        </div>
                    </div>

                    {{-- Numpad --}}
                    <div class="pos-numpad row g-1 mb-3">
                        @foreach(['1','2','3','4','5','6','7','8','9','.',0,'⌫'] as $key)
                        <div class="col-4">
                            <button onclick="numpad('{{ $key }}')">{{ $key }}</button>
                        </div>
                        @endforeach
                    </div>

                    {{-- Note --}}
                    <textarea id="sale-note" class="form-control form-control-sm mb-3"
                        rows="2" placeholder="Note (optional)"></textarea>

                    {{-- Actions --}}
                    <div class="d-flex gap-2">
                        <button class="btn btn-danger w-50" onclick="clearCart()">
                            🗑️ Clear
                        </button>
                        <button class="btn btn-success w-50 py-3" onclick="completeSale()" id="checkout-btn">
                            Complete Sale
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Receipt Modal --}}
<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🧾 Receipt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="receipt-content"></div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="printReceipt()">🖨️ Print</button>
                <button class="btn btn-success" onclick="newSale()">➕ New Sale</button>
            </div>
        </div>
    </div>
</div>
{{-- Variant Picker Modal --}}
<div class="modal fade" id="variantModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="variantModalTitle">Select Variant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img id="variantModalImg" src="" style="width:80px; height:80px; object-fit:cover; border-radius:8px;">
                </div>
                <div id="variant-list" class="row g-2"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let cart = [];
    let selectedPayment = 'cash';
    let selectedDiscountAmount = 0;

    function openVariantModal(productId, productName, productImg, variants) {
        document.getElementById('variantModalTitle').textContent = productName;
        document.getElementById('variantModalImg').src = productImg;

        const list = document.getElementById('variant-list');
        list.innerHTML = '';

        variants.forEach(v => {
            if (v.quantity <= 0) return; // skip out of stock variants

            list.innerHTML += `
            <div class="col-6">
                <div class="border rounded p-2 text-center" 
                     style="cursor:pointer;" 
                     onclick="addVariantToCart(${productId}, '${productName}', ${v.id}, '${v.color}', '${v.storage}', ${v.price}, ${v.quantity})">
                    <img src="/uploads/variants/${v.image}" 
                         style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
                    <div class="mt-1 fw-bold" style="font-size:12px;">
                        ${v.color} ${v.storage}
                    </div>
                    <div class="text-primary fw-bold" style="font-size:13px;">
                        $${parseFloat(v.price).toFixed(2)}
                    </div>
                    <div class="text-muted" style="font-size:11px;">
                        Stock: ${v.quantity}
                    </div>
                </div>
            </div>
        `;
        });

        new bootstrap.Modal(document.getElementById('variantModal')).show();
    }

    function addVariantToCart(productId, productName, variantId, color, storage, price, stock) {
        const name = `${productName} (${color} ${storage})`;
        const uniqueId = `${productId}-${variantId}`; // ✅ unique per variant

        const existing = cart.find(i => i.uniqueId === uniqueId);
        if (existing) {
            if (existing.qty >= stock) {
                alert(`Only ${stock} left in stock!`);
                return;
            }
            existing.qty++;
            existing.subtotal = existing.qty * existing.price;
        } else {
            cart.push({
                uniqueId,
                id: productId,
                variant_id: variantId,
                name,
                price,
                qty: 1,
                subtotal: price,
                stock
            });
        }

        bootstrap.Modal.getInstance(
            document.getElementById('variantModal')
        )?.hide();
        renderCart();
    }

    //  Add to cart
    function addToCart(id, name, price, stock) {
        const existing = cart.find(i => i.id === id);
        if (existing) {
            if (existing.qty >= stock) {
                alert(`Only ${stock} left in stock!`);
                return;
            }
            existing.qty++;
            existing.subtotal = existing.qty * existing.price;
        } else {
            if (stock <= 0) {
                alert('Product is out of stock!');
                return;
            }
            cart.push({
                id,
                name,
                price,
                qty: 1,
                subtotal: price,
                stock
            });
        }
        renderCart();
    }

    //  Render cart
    function renderCart() {
        const container = document.getElementById('cart-items');
        const emptyMsg = document.getElementById('empty-cart-msg');

        if (cart.length === 0) {
            container.innerHTML = '<p class="text-muted text-center mt-4">No items added yet</p>';
            recalculate();
            return;
        }

        container.innerHTML = cart.map((item, idx) => `
            <div class="cart-item d-flex align-items-center gap-2">
                <div class="flex-grow-1">
                    <div style="font-size:13px; font-weight:600;">${item.name}</div>
                    <div style="font-size:12px; color:#666;">$${item.price.toFixed(2)} each</div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button class="btn btn-sm btn-outline-secondary px-2 py-0"
                        onclick="changeQty(${idx}, -1)">-</button>
                    <span class="mx-1 fw-bold">${item.qty}</span>
                    <button class="btn btn-sm btn-outline-secondary px-2 py-0"
                        onclick="changeQty(${idx}, 1)">+</button>
                </div>
                <div class="fw-bold text-primary" style="min-width:60px; text-align:right;">
                    $${item.subtotal.toFixed(2)}
                </div>
                <button class="btn btn-sm btn-outline-danger px-2 py-0"
                    onclick="removeItem(${idx})">×</button>
            </div>
        `).join('');

        recalculate();
    }

    //  Change quantity
    function changeQty(idx, delta) {
        cart[idx].qty += delta;
        if (cart[idx].qty <= 0) {
            cart.splice(idx, 1);
        } else {
            if (cart[idx].qty > cart[idx].stock) {
                cart[idx].qty = cart[idx].stock;
                alert(`Only ${cart[idx].stock} left in stock!`);
            }
            cart[idx].subtotal = cart[idx].qty * cart[idx].price;
        }
        renderCart();
    }

    //  Remove item
    function removeItem(idx) {
        cart.splice(idx, 1);
        renderCart();
    }

    //recalculate
    function recalculate() {
        const subtotal = cart.reduce((sum, i) => sum + i.subtotal, 0);
        const manualDiscount = parseFloat(document.getElementById('discount-input').value) || 0;
        const totalDiscount = manualDiscount + selectedDiscountAmount;
        const total = Math.max(0, subtotal - totalDiscount);

        document.getElementById('display-subtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('display-discount').textContent = '-$' + manualDiscount.toFixed(2);
        document.getElementById('display-selected-discount').textContent = '-$' + selectedDiscountAmount.toFixed(2);
        document.getElementById('display-total').textContent = '$' + total.toFixed(2);

        calcChange();
    }

    //  Calculate change
    function calcChange() {
        const total = parseFloat(document.getElementById('display-total').textContent.replace('$', '')) || 0;
        const paid = parseFloat(document.getElementById('amount-paid').value) || 0;
        const changeDiv = document.getElementById('change-display');

        if (selectedPayment === 'credit') {
            const owed = Math.max(0, total - paid);
            changeDiv.style.display = 'block';
            changeDiv.style.background = '#fff3e0';
            changeDiv.innerHTML = `📒 Amount Owed: <strong class="text-danger">$${owed.toFixed(2)}</strong>`;
            return;
        }

        const change = paid - total;
        if (paid > 0) {
            changeDiv.style.display = 'block';
            changeDiv.style.background = change >= 0 ? '#e8f5e9' : '#ffebee';
            changeDiv.innerHTML = `💰 Change: <strong class="${change >= 0 ? 'text-success' : 'text-danger'}">$${Math.max(0, change).toFixed(2)}</strong>`;
        } else {
            changeDiv.style.display = 'none';
        }
    }

    //  Numpad
    function numpad(key) {
        const input = document.getElementById('amount-paid');
        if (key === '⌫') {
            input.value = input.value.slice(0, -1);
        } else {
            input.value += key;
        }
        calcChange();
    }

    //  Select payment
    function selectPayment(val, el) {
        selectedPayment = val;
        document.querySelectorAll('.payment-btn').forEach(b => b.classList.remove('selected'));
        el.classList.add('selected');
        calcChange();
    }

    // Load discounts into select on page load
    fetch("{{ route('ceo.pos.discounts') }}")
        .then(r => r.json())
        .then(data => {
            const select = document.getElementById('discount-select');
            data.forEach(d => {
                const label = d.type === 'percent' ?
                    `${d.name} (${d.value}%)` :
                    `${d.name} (-$${parseFloat(d.value).toFixed(2)})`;
                select.innerHTML += `<option value="${d.id}"
                data-type="${d.type}"
                data-value="${d.value}"
                data-min="${d.min_amount ?? 0}"
                data-name="${d.name}">${label}</option>`;
            });
        });

    function applyDiscount(select) {
        const option = select.options[select.selectedIndex];
        const subtotal = cart.reduce((sum, i) => sum + i.subtotal, 0);

        if (!select.value) {
            selectedDiscountAmount = 0;
            document.getElementById('selected-discount-row').style.display = 'none';
            document.getElementById('discount-info').textContent = '';
            recalculate();
            return;
        }

        const type = option.dataset.type;
        const value = parseFloat(option.dataset.value);
        const minAmt = parseFloat(option.dataset.min) || 0;
        const name = option.dataset.name;

        if (subtotal < minAmt) {
            document.getElementById('discount-info').innerHTML =
                `<span class="text-danger">❌ Minimum order $${minAmt} required</span>`;
            select.value = '';
            selectedDiscountAmount = 0;
            recalculate();
            return;
        }

        selectedDiscountAmount = type === 'percent' ?
            Math.round(subtotal * value / 100 * 100) / 100 :
            value;

        document.getElementById('selected-discount-label').textContent = `🎟 ${name}`;
        document.getElementById('selected-discount-row').style.display = '';
        document.getElementById('discount-info').innerHTML =
            `<span class="text-success">✅ Applied!</span>`;

        recalculate();
    }

    // ================= SEARCH PRODUCTS =================
    let searchTimeout;

    document.getElementById('pos-search').addEventListener('input', function() {

        clearTimeout(searchTimeout);

        const q = this.value.trim();
        const resultsBox = document.getElementById('pos-search-results');

        if (q.length < 1) {
            resultsBox.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {

            fetch(`{{ route('ceo.pos.search') }}?query=${encodeURIComponent(q)}`)

                .then(response => response.json())

                .then(data => {

                    console.log(data);

                    if (!data.length) {

                        resultsBox.innerHTML = `
                        <div class="p-3 text-muted">
                            No products found
                        </div>
                    `;

                    } else {

                        resultsBox.innerHTML = data.map(p => `

                        <div class="p-2 d-flex align-items-center gap-2 search-item"
                            style="cursor:pointer;border-bottom:1px solid #eee;">

                            <img
                                src="{{ asset('uploads/products/Thumbnails') }}/${p.image}"
                                style="width:40px;height:40px;object-fit:cover;border-radius:4px;"
                            >

                            <div>
                                <div style="font-size:13px;font-weight:600;">
                                    ${p.name}
                                </div>

                                <div style="font-size:12px;color:#666;">
                                    SKU: ${p.SKU}
                                    |
                                    $${parseFloat(p.sale_price || p.regular_price).toFixed(2)}
                                    |
                                    Stock: ${p.quantity}
                                </div>
                            </div>

                        </div>

                    `).join('');

                        // CLICK EVENTS
                        document.querySelectorAll('.search-item').forEach((el, index) => {

                            el.addEventListener('click', function() {
                                const p = data[index];

                                if (p.variants && p.variants.length > 0) {
                                    // Has variants → open modal
                                    openVariantModal(
                                        p.id,
                                        p.name,
                                        `/uploads/products/Thumbnails/${p.image}`,
                                        p.variants
                                    );
                                } else {
                                    //  No variants → add directly
                                    addToCart(
                                        p.id,
                                        p.name,
                                        parseFloat(p.sale_price || p.regular_price),
                                        p.quantity
                                    );
                                }

                                document.getElementById('pos-search').value = '';
                                resultsBox.style.display = 'none';
                            });

                        });

                    }

                    resultsBox.style.display = 'block';

                })

                .catch(error => {

                    console.error(error);
                    alert('Search failed');

                });

        }, 300);

    });


    // ================= BARCODE SEARCH =================
    function searchBySKU() {
        const sku = document.getElementById('barcode-input').value.trim();
        if (!sku) return;

        fetch(`{{ route('ceo.pos.search') }}?query=${encodeURIComponent(sku)}&barcode=${encodeURIComponent(sku)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    const p = data[0];

                    //  If variant_id returned, add directly without modal
                    if (p.variant_id) {
                        addVariantToCart(
                            p.id,
                            p.name,
                            p.variant_id,
                            '', '', // color/storage already in name
                            parseFloat(p.sale_price || p.regular_price),
                            p.quantity
                        );
                    } else if (p.variants && p.variants.length > 0) {
                        //  Has variants but no specific barcode match → show modal
                        openVariantModal(p.id, p.name,
                            `/uploads/products/Thumbnails/${p.image}`,
                            p.variants);
                    } else {
                        //  No variants → add directly
                        addToCart(
                            p.id,
                            p.name,
                            parseFloat(p.sale_price || p.regular_price),
                            p.quantity
                        );
                    }

                    document.getElementById('barcode-input').value = '';
                } else {
                    alert('Product not found!');
                }
            });
    }

    // ================= ENTER KEY =================
    document.getElementById('barcode-input').addEventListener('keydown', function(e) {

        if (e.key === 'Enter') {

            e.preventDefault();

            searchBySKU();

        }

    });

    // Auto-focus barcode input on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('barcode-input').focus();
    });

    // Re-focus after modal closes
    document.getElementById('variantModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('barcode-input').focus();
    });

    document.getElementById('receiptModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('barcode-input').focus();
    });

    //  Complete sale
    function completeSale() {
        if (cart.length === 0) {
            alert('Please add products to the cart first!');
            return;
        }


        const total = parseFloat(document.getElementById('display-total').textContent.replace('$', '')) || 0;
        const paid = parseFloat(document.getElementById('amount-paid').value) || 0;

        if (selectedPayment !== 'credit' && paid < total) {
            alert(`Amount paid ($${paid.toFixed(2)}) is less than total ($${total.toFixed(2)})!`);
            return;
        }

        const btn = document.getElementById('checkout-btn');
        btn.disabled = true;
        btn.textContent = 'Processing...';

        fetch("{{ route('ceo.pos.checkout') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    items: cart.map(i => ({
                        id: i.id,
                        qty: i.qty,
                        variant_id: i.variant_id ?? null
                    })),
                    customer_name: document.getElementById('customer-name').value,
                    customer_phone: document.getElementById('customer-phone').value,
                    discount: (parseFloat(document.getElementById('discount-input').value) || 0) + selectedDiscountAmount,
                    payment_method: selectedPayment,
                    amount_paid: paid,
                    note: document.getElementById('sale-note').value,
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showReceipt(data.order_id, data.change);
                } else {
                    alert('Error: ' + data.message);
                    btn.disabled = false;
                    btn.textContent = '✅ Complete Sale';
                }
            });
    }

    //  Show receipt
    function showReceipt(orderId, change) {
        fetch(`{{ url('ceo/pos/receipt') }}/${orderId}`)
            .then(r => r.text())
            .then(html => {
                document.getElementById('receipt-content').innerHTML = html;
                new bootstrap.Modal(document.getElementById('receiptModal')).show();
            });
    }

    //  Print receipt
    function printReceipt() {
        const content = document.getElementById('receipt-content').innerHTML;
        const win = window.open('', '_blank');
        win.document.write(`
            <html><head><title>Receipt</title>
            <style>
                body { font-family: monospace; font-size: 13px; max-width: 300px; margin: 0 auto; }
                table { width: 100%; border-collapse: collapse; }
                td { padding: 3px; }
                hr { border: 1px dashed #000; }
                .text-center { text-align: center; }
                .text-end { text-align: right; }
                .fw-bold { font-weight: bold; }
            </style>
            </head><body>${content}</body></html>
        `);
        win.document.close();
        win.print();
    }

    //  New sale
    function newSale() {
        cart = [];
        renderCart();
        selectedDiscountAmount = 0;
        document.getElementById('customer-name').value = '';
        document.getElementById('customer-phone').value = '';
        document.getElementById('discount-select').value = '';
        document.getElementById('discount-info').textContent = '';
        document.getElementById('selected-discount-row').style.display = 'none';
        document.getElementById('amount-paid').value = '';
        document.getElementById('sale-note').value = '';
        document.getElementById('change-display').style.display = 'none';
        bootstrap.Modal.getInstance(document.getElementById('receiptModal')).hide();
        document.getElementById('checkout-btn').disabled = false;
        document.getElementById('checkout-btn').textContent = '✅ Complete Sale';
    }

    //  Clear cart
    function clearCart() {
        if (cart.length > 0 && confirm('Clear all items?')) {
            cart = [];
            renderCart();
        }
    }
</script>
@endpush