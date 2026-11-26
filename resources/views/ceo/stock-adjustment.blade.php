@extends('layouts.ceo')
@section('content')

<style>
    body { background: #f0f2f5; }

    .report-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
        overflow: hidden;
    }

    .form-panel { padding: 24px; }

    .field-label {
        font-size: 12px;
        font-weight: 600;
        color: #888;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 6px;
        display: block;
    }

    .form-control, .form-select {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13px;
        padding: 9px 12px;
        width: 100%;
        background: #fafafa;
    }

    .form-control:focus, .form-select:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79,70,229,.1);
        outline: none;
    }

    .variant-select {
        margin-top: 10px;
        display: none;
    }

    .variant-select.active { display: block; }

    .stat-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-top: 18px;
    }

    .stat-box {
        background: #fafbfc;
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 14px;
        text-align: center;
    }

    .stat-label { font-size: 11px; color: #999; text-transform: uppercase; letter-spacing: .04em; }
    .stat-value { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-top: 4px; }
    .stat-value.diff-pos { color: #16a34a; }
    .stat-value.diff-neg { color: #dc2626; }

    .warning-box {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 12.5px;
        color: #92400e;
        margin-top: 16px;
    }

    .btn-submit {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: none;
        background: #4f46e5;
        color: #fff;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        margin-top: 18px;
    }

    .btn-submit:hover { background: #4338ca; }

    .side-panel {
        background: #fafbfc;
        border-left: 1px solid #f0f0f0;
        padding: 24px;
        min-height: 100%;
    }

    .side-empty {
        text-align: center;
        color: #bbb;
        padding: 60px 10px;
        font-size: 13px;
    }

    .side-empty .icon { font-size: 34px; margin-bottom: 10px; }

    .product-card { display: none; }
    .product-card.active { display: block; }

    .product-title { font-weight: 700; font-size: 14px; color: #1a1a2e; }
    .product-sub { font-size: 11px; color: #aaa; margin-bottom: 16px; }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
        font-size: 13px;
    }

    .info-row .val { font-weight: 700; }

    .layout-grid {
        display: grid;
        grid-template-columns: 1fr 300px;
    }

    .pagination-wrap {
        padding: 16px 24px;
    }

    @media (max-width: 900px) {
        .layout-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding:9px 0;margin-top:0;">
            <h6>Stock Adjustment</h6>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('ceo.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Stock Adjustment</div></li>
            </ul>
        </div>

        <div class="report-card">
            <div class="layout-grid">
                <div class="form-panel">
                    <form action="{{ route('ceo.stock-adjustment.store') }}" method="POST" id="adjustForm">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="field-label">Transaction Date</label>
                                <input type="date" name="transaction_date" class="form-control"
                                    value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="field-label">Reason <span style="color:#dc2626;">*</span></label>
                                <input type="text" name="reason" class="form-control"
                                    placeholder="e.g. Physical count correction" value="{{ old('reason') }}" required>
                            </div>
                        </div>

                        <div style="margin-top:20px;">
                            <label class="field-label">Product</label>
                            <select name="product_id" id="productSelect" class="form-select" required>
                                <option value="">— Select Product —</option>
                            </select>
                            <select name="variant_id" id="variantSelect" class="form-select variant-select">
                                <option value="">— Select Variant —</option>
                            </select>
                        </div>

                        <div class="stat-row">
                            <div class="stat-box">
                                <div class="stat-label">Current</div>
                                <div class="stat-value" id="statCurrent">0</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-label">Difference</div>
                                <div class="stat-value" id="statDiff">0</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-label">New Stock</div>
                                <div class="stat-value" id="statNew">0</div>
                            </div>
                        </div>

                        <div style="margin-top:20px;">
                            <label class="field-label">New Quantity</label>
                            <input type="number" name="new_quantity" id="newQuantityInput"
                                class="form-control" min="0" placeholder="Enter the correct quantity" required disabled>
                        </div>

                        <div class="warning-box">
                            ⚠ Note: adjustments update stock immediately and are logged in the inventory history. Make sure the new quantity reflects an actual physical count.
                        </div>

                        <button type="submit" class="btn-submit">↻ Update Adjustment</button>
                    </form>
                </div>

                <div class="side-panel">
                    <div class="side-empty" id="sideEmpty">
                        <div class="icon">📦</div>
                        <p>Select a product<br>to see current stock</p>
                    </div>
                    <div class="product-card" id="productCard">
                        <div class="product-title" id="cardName">—</div>
                        <div class="product-sub" id="cardSku">—</div>
                        <div class="info-row"><span>Current Stock</span><span class="val" id="cardStock">0</span></div>
                        <div class="info-row"><span>Updated Stock</span><span class="val" id="cardNewStock">0</span></div>
                        <div class="info-row"><span>Difference</span><span class="val" id="cardDiff">0</span></div>
                        <div class="info-row" style="border-bottom:none;"><span>Unit Cost</span><span class="val" id="cardPrice">$0.00</span></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Product + variant data available to JS --}}
<script>
    const PRODUCTS = {!! $products->map(function($p) {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'sku' => $p->SKU,
            'cost' => $p->cost_price ?? 0,
            'quantity' => $p->quantity ?? 0,
            'variants' => $p->variants->map(function($v) use ($p) {
                return [
                    'id' => $v->id,
                    'label' => trim(($v->color ?? '') . ' / ' . ($v->storage ?? ''), ' /'),
                    'quantity' => $v->quantity ?? 0,
                    // Variants don't have their own cost_price column — use the parent
                    // product's cost_price for the Unit Cost display.
                    'cost' => $p->cost_price ?? 0,
                ];
            })->values(),
        ];
    })->values()->toJson() !!};
</script>

<script>
    const productSelect  = document.getElementById('productSelect');
    const variantSelect  = document.getElementById('variantSelect');
    const newQtyInput    = document.getElementById('newQuantityInput');
    const sideEmpty      = document.getElementById('sideEmpty');
    const productCard    = document.getElementById('productCard');

    function buildProductOptions() {
        let html = '<option value="">— Select Product —</option>';
        PRODUCTS.forEach(p => {
            html += `<option value="${p.id}">${p.name} (${p.sku})${p.variants.length === 0 ? ' — Current: ' + p.quantity : ''}</option>`;
        });
        return html;
    }
    productSelect.innerHTML = buildProductOptions();

    function getSelectedProduct() {
        return PRODUCTS.find(p => p.id == productSelect.value);
    }

    function getSelectedVariant(product) {
        if (!product || product.variants.length === 0) return null;
        return product.variants.find(v => v.id == variantSelect.value) || null;
    }

    function resetEmptyState() {
        sideEmpty.style.display = 'block';
        productCard.classList.remove('active');
        newQtyInput.value = '';
        newQtyInput.disabled = true;
        document.getElementById('statCurrent').textContent = '0';
        document.getElementById('statDiff').textContent = '0';
        document.getElementById('statNew').textContent = '0';
    }

    function refresh() {
        const product = getSelectedProduct();

        if (!product) { resetEmptyState(); return; }

        // Product has variants but none chosen yet -> wait for variant selection
        if (product.variants.length > 0 && !variantSelect.value) {
            resetEmptyState();
            return;
        }

        const variant = getSelectedVariant(product);
        const current  = variant ? variant.quantity : product.quantity;
        const price    = variant ? variant.cost : product.cost;
        const label    = variant ? `${product.name} — ${variant.label}` : product.name;

        newQtyInput.disabled = false;
        if (newQtyInput.value === '') newQtyInput.value = current;

        sideEmpty.style.display = 'none';
        productCard.classList.add('active');

        const newQty = parseInt(newQtyInput.value) || 0;
        const diff = newQty - current;

        document.getElementById('statCurrent').textContent = current;
        document.getElementById('statNew').textContent = newQty;
        const diffEl = document.getElementById('statDiff');
        diffEl.textContent = (diff > 0 ? '+' : '') + diff;
        diffEl.className = 'stat-value ' + (diff > 0 ? 'diff-pos' : diff < 0 ? 'diff-neg' : '');

        document.getElementById('cardName').textContent = label;
        document.getElementById('cardSku').textContent = product.sku;
        document.getElementById('cardStock').textContent = current;
        document.getElementById('cardNewStock').textContent = newQty;
        document.getElementById('cardDiff').textContent = (diff > 0 ? '+' : '') + diff;
        document.getElementById('cardPrice').textContent = '$' + parseFloat(price).toFixed(2);
    }

    productSelect.addEventListener('change', () => {
        const product = getSelectedProduct();
        newQtyInput.value = '';
        variantSelect.innerHTML = '<option value="">— Select Variant —</option>';

        if (product && product.variants.length > 0) {
            product.variants.forEach(v => {
                variantSelect.innerHTML += `<option value="${v.id}">${v.label} — Current: ${v.quantity}</option>`;
            });
            variantSelect.classList.add('active');
            variantSelect.required = true;
        } else {
            variantSelect.classList.remove('active');
            variantSelect.required = false;
        }

        refresh();
    });

    variantSelect.addEventListener('change', () => {
        newQtyInput.value = '';
        refresh();
    });

    newQtyInput.addEventListener('input', refresh);

    resetEmptyState();
</script>

@endsection