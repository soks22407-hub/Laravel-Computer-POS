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
        border-color: #f43f5e;
        box-shadow: 0 0 0 3px rgba(244,63,94,.1);
        outline: none;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    .items-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #999;
        text-align: left;
        padding: 8px 6px;
        border-bottom: 1.5px solid #f0f0f0;
    }

    .items-table td {
        padding: 8px 6px;
        vertical-align: top;
    }

    .items-table tfoot td {
        font-weight: 700;
        padding-top: 12px;
        border-top: 2px solid #f0f0f0;
        color: #dc2626;
    }

    .variant-select {
        margin-top: 6px;
        display: none;
    }

    .variant-select.active { display: block; }

    .btn-add-item {
        font-size: 12px;
        font-weight: 600;
        color: #f43f5e;
        background: none;
        border: none;
        cursor: pointer;
        float: right;
    }

    .btn-remove-row {
        background: none;
        border: none;
        color: #dc2626;
        cursor: pointer;
        font-size: 15px;
    }

    .btn-submit {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: none;
        background: #f43f5e;
        color: #fff;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        margin-top: 18px;
    }

    .btn-submit:hover { background: #e11d48; }

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

    .side-item {
        border-bottom: 1px solid #f0f0f0;
        padding: 10px 0;
        font-size: 12.5px;
    }

    .side-item .name { font-weight: 700; color: #1a1a2e; }
    .side-item .stock { color: #888; margin-top: 2px; }

    .layout-grid {
        display: grid;
        grid-template-columns: 1fr 300px;
    }

    .stock-warning {
        font-size: 11px;
        color: #dc2626;
        margin-top: 4px;
        display: none;
    }

    @media (max-width: 900px) {
        .layout-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-0" style="padding:5px 0;margin-top:0;">
            <h5>Stock Out</h5>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('ceo.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Stock Out</div></li>
            </ul>
        </div>

        <div class="report-card">
            <div class="layout-grid">
                <div class="form-panel">
                    <form action="{{ route('ceo.stock-out.store') }}" method="POST" id="stockOutForm">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="field-label">Transaction Date</label>
                                <input type="date" name="transaction_date" class="form-control"
                                    value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="field-label">Reason / Reference</label>
                                <input type="text" name="reason" class="form-control"
                                    placeholder="e.g. Damaged / Lost / Order #1234" value="{{ old('reason') }}">
                            </div>
                        </div>

                        <div style="margin-top:22px;">
                            <label class="field-label" style="display:inline-block;">Items</label>
                            <button type="button" class="btn-add-item" onclick="addRow()">+ Add Item</button>
                            <table class="items-table" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th style="width:45%;">Product</th>
                                        <th style="width:15%;">Qty</th>
                                        <th style="width:20%;">Unit Cost</th>
                                        <th style="width:15%;">Subtotal</th>
                                        <th style="width:5%;"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody"></tbody>
                                <tfoot>
                                    <tr>
                                        <td id="itemCount">0 items</td>
                                        <td></td>
                                        <td></td>
                                        <td id="grandTotal">$0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <button type="submit" class="btn-submit">↑ Stock Out</button>
                    </form>
                </div>

                <div class="side-panel" id="sidePanel">
                    <div class="side-empty">
                        <div class="icon">📤</div>
                        <p>Select a product<br>to see current stock</p>
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
                    // product's cost_price as the default (user can override per transaction).
                    'cost' => $p->cost_price ?? 0,
                ];
            })->values(),
        ];
    })->values()->toJson() !!};
</script>

<script>
    let rowIndex = 0;

    function buildProductOptions() {
        let html = '<option value="">— Select Product —</option>';
        PRODUCTS.forEach(p => {
            html += `<option value="${p.id}" data-cost="${p.cost}" data-stock="${p.quantity}" data-has-variants="${p.variants.length > 0}">${p.name} (${p.sku}) — Stock: ${p.quantity}</option>`;
        });
        return html;
    }

    function addRow() {
        const tbody = document.getElementById('itemsBody');
        const row = document.createElement('tr');
        row.className = 'item-row';
        row.innerHTML = `
            <td>
                <select name="items[${rowIndex}][product_id]" class="form-select product-select" required>
                    ${buildProductOptions()}
                </select>
                <select name="items[${rowIndex}][variant_id]" class="form-select variant-select">
                    <option value="">— Select Variant —</option>
                </select>
                <div class="stock-warning">⚠ Quantity exceeds available stock</div>
            </td>
            <td><input type="number" name="items[${rowIndex}][quantity]" class="form-control qty-input" value="1" min="1" required></td>
            <td><input type="number" name="items[${rowIndex}][unit_cost]" class="form-control cost-input" value="0" min="0" step="0.01" required></td>
            <td class="row-subtotal">$0.00</td>
            <td><button type="button" class="btn-remove-row" onclick="removeRow(this)">🗑</button></td>
        `;
        tbody.appendChild(row);
        rowIndex++;
        bindRowEvents(row);
        updateTotals();
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length <= 1) return;
        btn.closest('.item-row').remove();
        updateTotals();
        renderSidePanel();
    }

    function getAvailableStock(row) {
        const productSelect = row.querySelector('.product-select');
        const variantSelect = row.querySelector('.variant-select');
        const product = PRODUCTS.find(p => p.id == productSelect.value);
        if (!product) return 0;

        if (product.variants.length > 0) {
            const variant = product.variants.find(v => v.id == variantSelect.value);
            return variant ? variant.quantity : 0;
        }
        return product.quantity;
    }

    function bindRowEvents(row) {
        const productSelect = row.querySelector('.product-select');
        const variantSelect = row.querySelector('.variant-select');
        const qty = row.querySelector('.qty-input');
        const cost = row.querySelector('.cost-input');
        const warning = row.querySelector('.stock-warning');

        function checkStock() {
            const available = getAvailableStock(row);
            const qtyVal = parseInt(qty.value) || 0;
            const productSelected = productSelect.value !== '';
            warning.style.display = (qtyVal > available && productSelected) ? 'block' : 'none';
        }

        productSelect.addEventListener('change', () => {
            const product = PRODUCTS.find(p => p.id == productSelect.value);
            variantSelect.innerHTML = '<option value="">— Select Variant —</option>';

            if (product && product.variants.length > 0) {
                product.variants.forEach(v => {
                    variantSelect.innerHTML += `<option value="${v.id}" data-cost="${v.cost}" data-stock="${v.quantity}">${v.label} — Stock: ${v.quantity}</option>`;
                });
                variantSelect.classList.add('active');
                variantSelect.required = true;
            } else {
                variantSelect.classList.remove('active');
                variantSelect.required = false;
            }

            if (product) cost.value = product.cost;
            checkStock();
            updateRowSubtotal(row);
            renderSidePanel();
        });

        variantSelect.addEventListener('change', () => {
            const opt = variantSelect.selectedOptions[0];
            if (opt && opt.dataset.cost !== undefined) {
                cost.value = opt.dataset.cost;
            }
            checkStock();
            updateRowSubtotal(row);
            renderSidePanel();
        });

        qty.addEventListener('input', () => { checkStock(); updateRowSubtotal(row); });
        cost.addEventListener('input', () => updateRowSubtotal(row));
    }

    function updateRowSubtotal(row) {
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
        row.querySelector('.row-subtotal').textContent = '$' + (qty * cost).toFixed(2);
        updateTotals();
    }

    function updateTotals() {
        const rows = document.querySelectorAll('.item-row');
        let total = 0;
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
            total += qty * cost;
        });
        document.getElementById('itemCount').textContent = rows.length + (rows.length === 1 ? ' item' : ' items');
        document.getElementById('grandTotal').textContent = '$' + total.toFixed(2);
    }

    function renderSidePanel() {
        const panel = document.getElementById('sidePanel');
        const rows = document.querySelectorAll('.item-row');
        let html = '';

        rows.forEach(row => {
            const productSelect = row.querySelector('.product-select');
            const variantSelect = row.querySelector('.variant-select');
            const product = PRODUCTS.find(p => p.id == productSelect.value);
            if (!product) return;

            if (product.variants.length > 0) {
                const variant = product.variants.find(v => v.id == variantSelect.value);
                if (variant) {
                    html += `<div class="side-item">
                        <div class="name">${product.name}</div>
                        <div class="stock">${variant.label} — Current stock: ${variant.quantity}</div>
                    </div>`;
                }
            } else {
                html += `<div class="side-item">
                    <div class="name">${product.name}</div>
                    <div class="stock">Current stock: ${product.quantity}</div>
                </div>`;
            }
        });

        if (html) {
            panel.innerHTML = html;
        } else {
            panel.innerHTML = `<div class="side-empty"><div class="icon">📤</div><p>Select a product<br>to see current stock</p></div>`;
        }
    }

    // Start with one row
    addRow();
</script>

@endsection