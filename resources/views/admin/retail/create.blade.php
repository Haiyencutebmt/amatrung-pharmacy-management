@extends('layouts.admin')

@section('title', 'Tạo phiếu xuất kho — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #ecfdf5; color: #16a34a; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        <span class="icon">🛒</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Tạo phiếu xuất kho</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Xuất thuốc dùng ngoài/Trà thảo mộc bán cho khách lẻ</p>
    </div>
</div>
@endsection

@section('content')
<style>
    .retail-form { font-family: 'Inter', system-ui, sans-serif; width: 100%; margin-top: -1rem; }
    .form-card {
        background: #fff; border: 1px solid #f1f5f9; border-radius: 1rem; padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 1.5rem;
    }
    .section-title {
        display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;
        border-bottom: 2px solid #f1f5f9; padding-bottom: 1rem;
    }
    .section-title .icon-box {
        width: 36px; height: 36px; border-radius: 0.75rem; display: flex;
        align-items: center; justify-content: center; font-size: 1.2rem;
    }
    .section-title h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: #1e3a5f; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; }
    .form-group { display: flex; flex-direction: column; gap: 0.5rem; }
    .form-label { font-size: 0.85rem; font-weight: 600; color: #475569; }
    .form-input {
        padding: 0.7rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.75rem;
        font-size: 0.95rem; color: #1e293b; background: #fff; width: 100%; box-sizing: border-box;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-input:focus { outline: none; border-color: #5eb542; box-shadow: 0 0 0 3px rgba(94,181,66,0.15); }
    .form-input::placeholder { color: #94a3b8; }
    textarea.form-input { resize: vertical; min-height: 60px; }

    .item-row {
        display: grid; grid-template-columns: 2.5fr 0.9fr 0.8fr auto;
        gap: 0.75rem; align-items: end; padding: 0.85rem 1rem;
        background: #fafbfc; border-radius: 0.75rem;
        border: 1px solid #f1f5f9; margin-bottom: 0.75rem;
        transition: border-color 0.2s;
    }
    .item-row:hover { border-color: #e2e8f0; }
    .item-row .form-input { padding: 0.55rem 0.75rem; font-size: 0.875rem; border-radius: 0.5rem; }
    .item-row .form-label { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em; }

    .stock-info { font-size: 0.75rem; color: #64748b; margin-top: 0.3rem; display: flex; align-items: center; gap: 0.25rem; }
    .stock-info.warning { color: #ef4444; font-weight: 600; }
    .stock-info.ok { color: #16a34a; }

    .btn-remove-row {
        width: 34px; height: 34px; border-radius: 0.5rem; border: 1px solid #fecaca;
        background: #fef2f2; color: #ef4444; font-size: 1rem; cursor: pointer;
        display: flex; align-items: center; justify-content: center; transition: all 0.2s; flex-shrink: 0;
    }
    .btn-remove-row:hover { background: #ef4444; color: #fff; border-color: #ef4444; }
    .btn-add-row {
        display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.1rem;
        background: #f0fdf4; color: #16a34a; border: 1px dashed #86efac; border-radius: 0.75rem;
        font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.2s; margin-top: 0.25rem;
    }
    .btn-add-row:hover { background: #dcfce7; border-color: #4ade80; }

    .form-actions {
        display: flex; gap: 1rem; margin-top: 2rem; justify-content: flex-end;
        border-top: 1px solid #f1f5f9; padding-top: 1.5rem;
    }
    .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; border-radius: 0.75rem; font-size: 0.9rem; font-weight: 600; text-decoration: none; cursor: pointer; border: none; transition: all 0.2s; }
    .btn-cancel { background: #fff; color: #64748b; border: 1px solid #cbd5e1; }
    .btn-cancel:hover { background: #f8fafc; }
    .btn-save { background: #16a34a; color: #fff; box-shadow: 0 4px 12px rgba(22,163,74,0.2); }
    .btn-save:hover { background: #15803d; }

    .error-box { background: #fef2f2; color: #ef4444; padding: 1rem 1.25rem; border-radius: 0.75rem; margin-bottom: 1.5rem; font-size: 0.9rem; border: 1px solid #fecaca; }
    .error-box ul { margin: 0.5rem 0 0; padding-left: 1.5rem; }

    .sku-badge { font-family: monospace; font-size: 0.75rem; background: #f1f5f9; color: #64748b; padding: 0.1rem 0.4rem; border-radius: 0.25rem; font-weight: 700; }
    .empty-notice { text-align: center; padding: 2rem; color: #94a3b8; font-size: 0.9rem; background: #fafbfc; border-radius: 0.75rem; border: 1px dashed #e2e8f0; }
</style>

<div class="retail-form">
    <div class="form-card">
        @if(session('error'))
            <div class="error-box">⚠️ {{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="error-box">
                <strong>Vui lòng kiểm tra lại:</strong>
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        @if($products->isEmpty())
            <div class="empty-notice">
                <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">📦</div>
                <div style="font-weight: 700; color: #475569; margin-bottom: 0.5rem;">Chưa có thuốc dùng ngoài/Trà thảo mộc nào còn hàng</div>
                <div>Hãy nhập hàng vào kho trước khi tạo phiếu xuất.</div>
                <a href="{{ route('admin.warehouse.index', ['tab'=>'products']) }}" style="display:inline-flex; align-items:center; gap:0.5rem; margin-top:1rem; background:#16a34a; color:#fff; padding:0.6rem 1.25rem; border-radius:0.5rem; font-weight:600; font-size:0.875rem; text-decoration:none;">
                    📦 Đến kho sản phẩm
                </a>
            </div>
        @else

        <form action="{{ route('admin.retail-orders.store') }}" method="POST" id="retailForm">
            @csrf

            {{-- Thông tin khách --}}
            <div class="section-title">
                <div class="icon-box" style="background: #eff6ff; color: #3b82f6;">👤</div>
                <h3>Thông tin khách hàng <span style="font-weight: 400; color: #94a3b8; font-size: 0.85rem; margin-left: 0.5rem;">(không bắt buộc)</span></h3>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="customer_name" class="form-label">Tên khách hàng</label>
                    <input type="text" name="customer_name" id="customer_name" class="form-input" placeholder="Mặc định: Khách lẻ" value="{{ old('customer_name') }}">
                </div>
                <div class="form-group">
                    <label for="customer_phone" class="form-label">Số điện thoại</label>
                    <input type="tel" name="customer_phone" id="customer_phone" class="form-input" placeholder="Không bắt buộc" value="{{ old('customer_phone') }}">
                </div>
                <div class="form-group">
                    <label for="customer_address" class="form-label">Địa chỉ</label>
                    <input type="text" name="customer_address" id="customer_address" class="form-input" placeholder="Không bắt buộc" value="{{ old('customer_address') }}">
                </div>
            </div>

            {{-- Sản phẩm xuất kho --}}
            <div style="margin-top: 2rem;">
                <div class="section-title" style="border-color: #dcfce7;">
                    <div class="icon-box" style="background: #f0fdf4; color: #16a34a;">📦</div>
                    <h3>Thuốc dùng ngoài/Trà thảo mộc xuất kho <span style="color: #ef4444; font-weight: 400; font-size: 0.85rem;">*</span></h3>
                </div>

                <div id="itemRows">
                    <div class="item-row" data-index="0">
                        <div class="form-group">
                            <label class="form-label">Sản phẩm</label>
                            <select name="items[0][product_id]" class="form-input product-select" data-index="0" required>
                                <option value="">— Chọn sản phẩm —</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}"
                                        data-unit="{{ $p->unit }}"
                                        data-stock="{{ floatval($p->stock_quantity) }}"
                                        data-sku="{{ $p->sku }}"
                                        data-price="{{ floatval($p->price) }}"
                                        {{ old('items.0.product_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }}{{ $p->category ? ' — '.$p->category : '' }} (Tồn: {{ number_format($p->stock_quantity, 0) }} {{ $p->unit }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="stock-info" id="stock-0"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Số lượng</label>
                            <input type="number" name="items[0][quantity]" class="form-input qty-input"
                                   step="1" min="1" data-index="0" required placeholder="0"
                                   value="{{ old('items.0.quantity') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Đơn vị</label>
                            <input type="text" class="form-input" id="unit-0" disabled value="" style="background: #f8fafc; color: #64748b; text-align:center; font-weight:700;">
                        </div>
                        <button type="button" class="btn-remove-row" onclick="removeRow(this)" title="Xóa dòng">✕</button>
                    </div>
                </div>

                <button type="button" class="btn-add-row" onclick="addItemRow()">
                    + Thêm sản phẩm
                </button>
            </div>

            {{-- Ghi chú --}}
            <div style="margin-top: 1.5rem;">
                <div class="form-group">
                    <label for="note" class="form-label">Ghi chú phiếu xuất</label>
                    <textarea name="note" id="note" class="form-input" rows="2" placeholder="Ghi chú cho phiếu xuất kho (không bắt buộc)...">{{ old('note') }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.retail-orders.index') }}" class="btn btn-cancel">Hủy bỏ</a>
                <button type="submit" class="btn btn-save">
                    <span>🛒</span> Tạo phiếu xuất kho
                </button>
            </div>
        </form>

        @endif
    </div>
</div>

<script>
    let itemIndex = 1;

    // Build options string once
    const productOptions = `<option value="">— Chọn sản phẩm —</option>` +
        @foreach($products as $p)
        `<option value="{{ $p->id }}" data-unit="{{ $p->unit }}" data-stock="{{ floatval($p->stock_quantity) }}" data-sku="{{ $p->sku }}" data-price="{{ floatval($p->price) }}">{{ addslashes($p->name) }}{{ $p->category ? ' — '.addslashes($p->category) : '' }} (Tồn: {{ number_format($p->stock_quantity,0) }} {{ $p->unit }})</option>` +
        @endforeach
        ``;

    function addItemRow() {
        const container = document.getElementById('itemRows');
        const row = document.createElement('div');
        row.className = 'item-row';
        row.dataset.index = itemIndex;
        row.innerHTML = `
            <div class="form-group">
                <label class="form-label">Sản phẩm</label>
                <select name="items[${itemIndex}][product_id]" class="form-input product-select" data-index="${itemIndex}" required>
                    ${productOptions}
                </select>
                <div class="stock-info" id="stock-${itemIndex}"></div>
            </div>
            <div class="form-group">
                <label class="form-label">Số lượng</label>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-input qty-input"
                       step="1" min="1" data-index="${itemIndex}" required placeholder="0">
            </div>
            <div class="form-group">
                <label class="form-label">Đơn vị</label>
                <input type="text" class="form-input" id="unit-${itemIndex}" disabled value="" style="background:#f8fafc; color:#64748b; text-align:center; font-weight:700;">
            </div>
            <button type="button" class="btn-remove-row" onclick="removeRow(this)" title="Xóa dòng">✕</button>
        `;
        container.appendChild(row);
        bindEvents(row);
        itemIndex++;
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) btn.closest('.item-row').remove();
    }

    function bindEvents(row) {
        const sel = row.querySelector('.product-select');
        const qty = row.querySelector('.qty-input');
        if (sel) sel.addEventListener('change', onProductChange);
        if (qty) qty.addEventListener('input', onQtyChange);
    }

    function onProductChange(e) {
        const idx = e.target.dataset.index;
        const opt = e.target.selectedOptions[0];
        const unit  = opt?.dataset?.unit  || '';
        const stock = parseFloat(opt?.dataset?.stock || 0);
        const sku   = opt?.dataset?.sku   || '';

        document.getElementById('unit-' + idx).value = unit;

        const stockEl = document.getElementById('stock-' + idx);
        if (opt && opt.value) {
            stockEl.innerHTML = `<span class="sku-badge">${sku}</span> Tồn kho: <strong>${stock}</strong> ${unit}`;
            stockEl.className = 'stock-info ok';
        } else {
            stockEl.textContent = '';
            stockEl.className = 'stock-info';
        }
    }

    function onQtyChange(e) {
        const idx = e.target.dataset.index;
        const row = e.target.closest('.item-row');
        const sel  = row.querySelector('.product-select');
        const opt  = sel?.selectedOptions[0];
        const stock = parseFloat(opt?.dataset?.stock || 0);
        const qty   = parseFloat(e.target.value || 0);
        const unit  = opt?.dataset?.unit || '';
        const sku   = opt?.dataset?.sku  || '';
        const stockEl = document.getElementById('stock-' + idx);

        if (!opt?.value) return;
        if (qty > stock) {
            stockEl.innerHTML = `⚠️ Vượt tồn kho! Chỉ còn <strong>${stock}</strong> ${unit}`;
            stockEl.className = 'stock-info warning';
        } else {
            stockEl.innerHTML = `<span class="sku-badge">${sku}</span> Tồn kho: <strong>${stock}</strong> ${unit}`;
            stockEl.className = 'stock-info ok';
        }
    }

    // Bind to initial rows
    document.querySelectorAll('.item-row').forEach(bindEvents);
</script>
@endsection
