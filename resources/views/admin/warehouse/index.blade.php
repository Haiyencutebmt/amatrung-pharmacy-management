@extends('layouts.admin')

@section('title', 'Quản lý kho — AmaTrung')
@section('page-title', '')

@section('header-left')
<div style="display: flex; align-items: center; gap: 1rem;">
    <div style="width: 44px; height: 44px; background: linear-gradient(135deg, #eef9ee, #d1fae5); color: #5eb542; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; border: 1px solid #bbf7d0;">
        🏪
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Quản lý kho</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Kho dược liệu & thuốc dùng ngoài/trà thảo mộc</p>
    </div>
</div>
@endsection

@section('content')
<div class="warehouse-container">

    {{-- ── Tab Switcher ─────────────────────────────────────────── --}}
    <div class="wh-tabs">
        <a href="{{ route('admin.warehouse.index', array_merge(request()->except('tab', 'herb_page', 'product_page'), ['tab' => 'herbs'])) }}"
           class="wh-tab {{ $tab === 'herbs' ? 'active' : '' }}" id="tab-herbs">
            <span>🌿</span> Kho dược liệu
            <span class="wh-tab-badge" style="{{ $tab === 'herbs' ? 'background:#5eb542;color:#fff;' : 'background:#e2e8f0;color:#64748b;' }}">{{ number_format($totalHerbs) }}</span>
        </a>
        <a href="{{ route('admin.warehouse.index', array_merge(request()->except('tab', 'herb_page', 'product_page'), ['tab' => 'products'])) }}"
           class="wh-tab {{ $tab === 'products' ? 'active' : '' }}" id="tab-products">
            <span>📦</span> Thuốc dùng ngoài/Trà thảo mộc
            <span class="wh-tab-badge" style="{{ $tab === 'products' ? 'background:#5eb542;color:#fff;' : 'background:#e2e8f0;color:#64748b;' }}">{{ number_format($totalProducts) }}</span>
        </a>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- TAB 1: KHO DƯỢC LIỆU                                       --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if($tab === 'herbs')
    <div id="panel-herbs">

        {{-- Stats Cards --}}
        <div class="wh-stats-grid">
            <a href="{{ route('admin.warehouse.index', array_merge(request()->query(), ['tab'=>'herbs','filter'=>null,'herb_page'=>null])) }}"
               class="wh-stat {{ !request('filter') ? 'wh-stat--active' : '' }}">
                <div class="wh-stat__icon">🌿</div>
                <div class="wh-stat__body">
                    <div class="wh-stat__value">{{ number_format($totalHerbs) }}</div>
                    <div class="wh-stat__label">Tổng dược liệu</div>
                </div>
            </a>
            <a href="{{ route('admin.warehouse.index', array_merge(request()->query(), ['tab'=>'herbs','filter'=>'warning','herb_page'=>null])) }}"
               class="wh-stat {{ request('filter')==='warning' ? 'wh-stat--warning' : '' }}">
                <div class="wh-stat__icon" style="background:#fffbeb;color:#d97706;">⚠️</div>
                <div class="wh-stat__body">
                    <div class="wh-stat__value" style="{{ request('filter')==='warning' ? '' : 'color:#d97706;' }}">{{ number_format($outOfStockCount + $warningStockCount) }}</div>
                    <div class="wh-stat__label">Sắp hết / Hết hàng</div>
                </div>
            </a>
            <a href="{{ route('admin.warehouse.index', array_merge(request()->query(), ['tab'=>'herbs','filter'=>'expired','herb_page'=>null])) }}"
               class="wh-stat {{ request('filter')==='expired' ? 'wh-stat--expired' : '' }}">
                <div class="wh-stat__icon" style="background:#fef2f2;color:#dc2626;">❌</div>
                <div class="wh-stat__body">
                    <div class="wh-stat__value" style="{{ request('filter')==='expired' ? '' : 'color:#dc2626;' }}">{{ number_format($expiredCount) }}</div>
                    <div class="wh-stat__label">Hết hạn sử dụng</div>
                </div>
            </a>
        </div>

        {{-- Filters & Table --}}
        <div class="wh-card">
            <form action="{{ route('admin.warehouse.index') }}" method="GET" class="wh-filter-form">
                <input type="hidden" name="tab" value="herbs">
                @if(request('filter'))
                    <input type="hidden" name="filter" value="{{ request('filter') }}">
                @endif
                <div class="wh-filter-row">
                    <div style="display:flex; gap:0.75rem; align-items:flex-end; flex-wrap:wrap; flex:1;">
                        <div class="wh-filter-item">
                            <label class="wh-label">Tìm dược liệu</label>
                            <div class="wh-search-wrap">
                                <span class="wh-search-icon">🔍</span>
                                <input type="text" name="search" placeholder="Tên dược liệu, phân loại..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="wh-filter-item">
                            <label class="wh-label">Cách dùng</label>
                            <select name="usage_type" onchange="this.form.submit()" class="wh-select">
                                <option value="">-- Tất cả --</option>
                                <option value="Uống" {{ request('usage_type')=='Uống' ? 'selected' : '' }}>Uống</option>
                                <option value="Sắc" {{ request('usage_type')=='Sắc' ? 'selected' : '' }}>Sắc</option>
                                <option value="Dùng ngoài" {{ request('usage_type')=='Dùng ngoài' ? 'selected' : '' }}>Dùng ngoài</option>
                            </select>
                        </div>
                        <a href="{{ route('admin.warehouse.index', ['tab'=>'herbs']) }}" class="wh-btn-reset">🔄 Reset</a>
                    </div>
                    <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
                        <button type="button" id="btn-bulk-delete" style="display:none; height:38px; padding:0 1rem; border-radius:0.375rem; background:#ef4444; color:#fff; border:none; font-weight:700; cursor:pointer; align-items:center; gap:0.35rem; font-size:0.85rem;">
                            🗑️ Xóa đã chọn (<span id="selected-count">0</span>)
                        </button>
                        <div style="position:relative; display:inline-block;">
                            <button type="button" id="btnExportDropdown" class="wh-btn-outline" onclick="toggleExportDropdown()">
                                📊 In & Nhập/Xuất Excel ▾
                            </button>
                            <div id="exportDropdownMenu" style="display:none; position:absolute; top:100%; right:0; background:#fff; border:1px solid #cbd5e1; border-radius:4px; box-shadow:0 10px 25px rgba(0,0,0,0.1); padding:0.5rem; min-width:240px; z-index:999; margin-top:5px;">
                                <div style="font-size:0.72rem; font-weight:800; color:#64748b; padding:0.4rem 0.5rem; text-transform:uppercase; border-bottom:1px solid #e2e8f0; margin-bottom:5px; letter-spacing:0.5px;">🖨️ IN KHO DƯỢC LIỆU</div>
                                <a href="javascript:void(0)" onclick="printHerbList()" style="display:flex; align-items:center; gap:0.5rem; padding:0.5rem; color:#1e293b; text-decoration:none; font-size:0.85rem; font-weight:600; border-radius:4px;" class="wh-dropdown-item">
                                    📄 In danh sách hiện tại
                                </a>
                                <div style="font-size:0.72rem; font-weight:800; color:#64748b; padding:0.4rem 0.5rem; text-transform:uppercase; border-bottom:1px solid #e2e8f0; margin:10px 0 5px; letter-spacing:0.5px;">📥 XUẤT FILE EXCEL (CSV)</div>
                                <a href="{{ route('admin.medicinal-herbs.export-excel') }}?search={{ request('search') }}&usage_type={{ request('usage_type') }}" style="display:flex; align-items:center; gap:0.5rem; padding:0.5rem; color:#166534; text-decoration:none; font-size:0.85rem; font-weight:600; border-radius:4px;" class="wh-dropdown-item">
                                    📥 Xuất danh sách Excel
                                </a>
                                <div style="font-size:0.72rem; font-weight:800; color:#64748b; padding:0.4rem 0.5rem; text-transform:uppercase; border-bottom:1px solid #e2e8f0; margin:10px 0 5px; letter-spacing:0.5px;">📤 NHẬP FILE EXCEL (IMPORT)</div>
                                <a href="{{ route('admin.medicinal-herbs.download-template') }}" style="display:flex; align-items:center; gap:0.5rem; padding:0.5rem; color:#475569; text-decoration:none; font-size:0.85rem; font-weight:600; border-radius:4px;" class="wh-dropdown-item">
                                    📥 Tải file mẫu nhập liệu
                                </a>
                                <a href="javascript:void(0)" onclick="document.getElementById('importExcelModal').style.display='flex'" style="display:flex; align-items:center; gap:0.5rem; padding:0.5rem; color:#2563eb; text-decoration:none; font-size:0.85rem; font-weight:600; border-radius:4px;" class="wh-dropdown-item">
                                    📤 Nhập danh sách từ Excel
                                </a>
                            </div>
                        </div>
                        <button type="button" class="wh-btn-primary" onclick="document.getElementById('addHerbModal').style.display='flex'">
                            + Thêm Dược Liệu
                        </button>
                    </div>
                </div>
            </form>

            <form id="bulk-delete-form" action="{{ route('admin.medicinal-herbs.bulk-destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="wh-table-wrap">
                    <table class="wh-table">
                        <thead>
                            <tr>
                                <th style="width:45px; text-align:center;">
                                    <input type="checkbox" id="select-all" style="width:18px; height:18px; cursor:pointer; accent-color:#16a34a; margin-top:3px;">
                                </th>
                                <th>TÊN DƯỢC LIỆU</th>
                                <th>PHÂN LOẠI</th>
                                <th>TỒN KHO</th>
                                <th>TRẠNG THÁI</th>
                                <th>CẢNH BÁO</th>
                                <th>HÀNH ĐỘNG</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($herbs as $herb)
                            <tr>
                                <td style="text-align:center; vertical-align:middle;">
                                    <input type="checkbox" name="ids[]" value="{{ $herb->id }}" class="herb-checkbox" style="width:18px; height:18px; cursor:pointer; accent-color:#16a34a;">
                                </td>
                                <td>
                                    <div style="font-weight:750; color:#1e293b; font-size:1rem;">{{ $herb->name }}</div>
                                    @if($herb->usage_type == 'Dùng ngoài' || str_contains(strtolower($herb->usage_type), 'xoa bóp'))
                                        <div style="font-size:0.75rem; color:#ef4444; font-weight:bold; margin-top:0.25rem; display:flex; align-items:center; gap:0.25rem;">
                                            <span>⚠️</span> Chỉ dùng ngoài da, không uống!
                                        </div>
                                    @endif
                                </td>
                                <td><span style="font-weight:600; color:#475569;">{{ $herb->category ?? 'N/A' }}</span></td>
                                <td>
                                    @if($herb->stock_quantity <= 0)
                                        <span style="color:#ef4444; font-weight:800; font-size:1.1rem;">0</span> <span style="color:#64748b; font-weight:600;">{{ $herb->unit }}</span>
                                    @elseif($herb->isWarningStock())
                                        <span style="color:#f59e0b; font-weight:800; font-size:1.1rem;">{{ floatval($herb->stock_quantity) }}</span> <span style="color:#64748b; font-weight:600;">{{ $herb->unit }}</span>
                                    @else
                                        <span style="font-weight:750; color:#1e3a5f; font-size:1.05rem;">{{ floatval($herb->stock_quantity) }}</span> <span style="color:#64748b; font-weight:600;">{{ $herb->unit }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($herb->status == 'active')
                                        <span style="background:#eef9ee; color:#16a34a; padding:0.3rem 0.75rem; border-radius:0.25rem; font-weight:800; font-size:0.75rem;">Đang dùng</span>
                                    @elseif($herb->status == 'out_of_stock')
                                        <span style="background:#f1f5f9; color:#64748b; padding:0.3rem 0.75rem; border-radius:0.25rem; font-weight:800; font-size:0.75rem;">Hết hàng</span>
                                    @else
                                        <span style="background:#fef2f2; color:#ef4444; padding:0.3rem 0.75rem; border-radius:0.25rem; font-weight:800; font-size:0.75rem;">Ngừng dùng</span>
                                    @endif
                                </td>
                                <td>
                                    @if($herb->stock_quantity <= 0)
                                        <div style="color:#ef4444; font-size:0.8rem; font-weight:700; display:flex; align-items:center; gap:0.25rem;"><span>⚠️</span> Hết hàng</div>
                                    @elseif($herb->isWarningStock())
                                        <div style="color:#f59e0b; font-size:0.8rem; font-weight:700; display:flex; align-items:center; gap:0.25rem;"><span>⚠️</span> Sắp hết</div>
                                    @endif
                                    @if($herb->isExpired())
                                        <div style="color:#ef4444; font-size:0.8rem; font-weight:700; display:flex; align-items:center; gap:0.25rem;"><span>❌</span> Hết hạn ({{ $herb->expiry_date->format('d/m/Y') }})</div>
                                    @elseif($herb->expiry_date)
                                        <div style="color:#64748b; font-size:0.8rem; font-weight:600;">HSD: {{ $herb->expiry_date->format('d/m/Y') }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="wh-action-cell">
                                        <a href="javascript:void(0)"
                                           class="wh-btn-edit btn-edit-trigger"
                                           data-id="{{ $herb->id }}"
                                           data-name="{{ e($herb->name) }}"
                                           data-category="{{ e($herb->category) }}"
                                           data-usage_type="{{ e($herb->usage_type) }}"
                                           data-unit="{{ e($herb->unit) }}"
                                           data-stock_quantity="{{ floatval($herb->stock_quantity) }}"
                                           data-expiry_date="{{ $herb->expiry_date?->format('Y-m-d') }}"
                                           data-status="{{ e($herb->status) }}"
                                           data-warning_note="{{ e($herb->warning_note) }}"
                                           data-description="{{ e($herb->description) }}">
                                            ✏️ Sửa
                                        </a>
                                        <a href="javascript:void(0)"
                                           class="wh-btn-history btn-history-trigger"
                                           data-id="{{ $herb->id }}"
                                           data-name="{{ e($herb->name) }}">
                                            📜 Lịch sử
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" style="text-align:center; color:#64748b; padding:3rem 1rem; font-weight:500;">
                                    Không tìm thấy dược liệu nào.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            @if($herbs->hasPages())
            <div class="wh-pagination">
                <p>Hiển thị {{ $herbs->firstItem() }} đến {{ $herbs->lastItem() }} của {{ $herbs->total() }} dược liệu</p>
                <div>{{ $herbs->withQueryString()->links() }}</div>
            </div>
            @endif
        </div>

    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- TAB 2: THUỐC DÙNG NGOÀI / TRÀ THẢO MỘC                      --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if($tab === 'products')
    <div id="panel-products">

        {{-- Stats --}}
        <div class="wh-stats-grid">
            <div class="wh-stat">
                <div class="wh-stat__icon">📦</div>
                <div class="wh-stat__body">
                    <div class="wh-stat__value">{{ $totalProducts }}</div>
                    <div class="wh-stat__label">Tổng sản phẩm</div>
                </div>
            </div>
            <div class="wh-stat">
                <div class="wh-stat__icon" style="background:#eff6ff; color:#3b82f6;">✅</div>
                <div class="wh-stat__body">
                    <div class="wh-stat__value">{{ $activeProducts }}</div>
                    <div class="wh-stat__label">Còn hàng</div>
                </div>
            </div>
            <div class="wh-stat">
                <div class="wh-stat__icon" style="background:#fef2f2; color:#ef4444;">⚠️</div>
                <div class="wh-stat__body">
                    <div class="wh-stat__value">{{ $lowStock }}</div>
                    <div class="wh-stat__label">Sắp hết hàng (≤ 10)</div>
                </div>
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="wh-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; gap:1rem; flex-wrap:wrap;">
                <form method="GET" style="display:flex; gap:0.75rem; flex:1; flex-wrap:wrap;">
                    <input type="hidden" name="tab" value="products">
                    <div style="display:flex; align-items:center; gap:0.5rem; background:#fff; border:1px solid #e2e8f0; border-radius:0.375rem; padding:0.5rem 0.85rem; flex:1; max-width:360px;">
                        <span style="color:#94a3b8;">🔍</span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên, mã SKU..."
                               style="border:none; outline:none; font-size:0.875rem; width:100%; color:#1e293b;">
                    </div>
                    <select name="status" onchange="this.form.submit()" style="border:1px solid #e2e8f0; border-radius:0.375rem; padding:0.5rem 0.85rem; font-size:0.875rem; background:#fff; color:#475569; cursor:pointer;">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('status')==='active' ? 'selected' : '' }}>Còn hàng</option>
                        <option value="inactive" {{ request('status')==='inactive' ? 'selected' : '' }}>Hết hàng</option>
                    </select>
                    <button type="submit" style="background:#f1f5f9; border:1px solid #e2e8f0; border-radius:0.375rem; padding:0.5rem 1rem; font-size:0.875rem; font-weight:600; color:#475569; cursor:pointer;">Lọc</button>
                </form>
                <button type="button" onclick="openAddProductModal()" style="display:inline-flex; align-items:center; gap:0.5rem; background:#16a34a; color:#fff; border:none; padding:0.65rem 1.25rem; border-radius:0.375rem; font-weight:700; font-size:0.875rem; cursor:pointer; white-space:nowrap;">
                    + Thêm sản phẩm
                </button>
            </div>

            {{-- Table --}}
            <div style="background:#fff; border:1px solid #e2e8f0; border-radius:0.375rem; overflow:hidden;">
                @if($products->isEmpty())
                    <div style="padding:4rem 2rem; text-align:center;">
                        <div style="font-size:3rem; margin-bottom:1rem; color:#cbd5e1;">📦</div>
                        <h3 style="font-size:1.1rem; font-weight:700; color:#1e3a5f; margin:0 0 0.5rem;">Chưa có thuốc dùng ngoài/trà thảo mộc</h3>
                        <p style="color:#64748b; font-size:0.9rem; margin:0 0 1.5rem;">Hãy tạo thuốc dùng ngoài/trà thảo mộc đầu tiên từ kho dược liệu.</p>
                        <button type="button" onclick="openAddProductModal()" style="display:inline-flex; align-items:center; gap:0.5rem; background:#16a34a; color:#fff; padding:0.65rem 1.25rem; border-radius:0.375rem; font-weight:700; font-size:0.875rem; border:none; cursor:pointer;">
                            + Thêm sản phẩm đầu tiên
                        </button>
                    </div>
                @else
                    <table style="width:100%; border-collapse:collapse; font-size:0.875rem;">
                        <thead>
                            <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0;">
                                <th style="padding:0.85rem 1rem; text-align:left; font-weight:700; color:#475569; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;">SKU</th>
                                <th style="padding:0.85rem 1rem; text-align:left; font-weight:700; color:#475569; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;">Tên sản phẩm</th>
                                <th style="padding:0.85rem 1rem; text-align:left; font-weight:700; color:#475569; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;">Phân loại</th>
                                <th style="padding:0.85rem 1rem; text-align:center; font-weight:700; color:#475569; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;">Tồn kho</th>
                                <th style="padding:0.85rem 1rem; text-align:center; font-weight:700; color:#475569; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;">Đơn vị</th>
                                <th style="padding:0.85rem 1rem; text-align:center; font-weight:700; color:#475569; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;">Hạn sử dụng</th>
                                <th style="padding:0.85rem 1rem; text-align:center; font-weight:700; color:#475569; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;">Trạng thái</th>
                                <th style="padding:0.85rem 1rem; text-align:center; font-weight:700; color:#475569; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr style="border-bottom:1px solid #f1f5f9; transition:background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                                <td style="padding:0.9rem 1rem;">
                                    <span style="font-family:monospace; font-size:0.8rem; background:#f1f5f9; color:#475569; padding:0.2rem 0.5rem; border-radius:0.25rem; font-weight:700;">{{ $product->sku }}</span>
                                </td>
                                <td style="padding:0.9rem 1rem;">
                                    <div style="font-weight:700; color:#1e3a5f;">{{ $product->name }}</div>
                                    @if($product->description)
                                        <div style="font-size:0.78rem; color:#94a3b8; margin-top:0.15rem;">{{ Str::limit($product->description, 55) }}</div>
                                    @endif
                                </td>
                                <td style="padding:0.9rem 1rem;">
                                    @if($product->category)
                                        <span style="background:#f0f9ff; color:#0369a1; border:1px solid #bae6fd; padding:0.2rem 0.6rem; border-radius:999px; font-size:0.75rem; font-weight:700; white-space:nowrap;">{{ $product->category }}</span>
                                    @else
                                        <span style="color:#cbd5e1; font-size:0.8rem;">Chưa phân loại</span>
                                    @endif
                                </td>
                                <td style="padding:0.9rem 1rem; text-align:center;">
                                    @if($product->stock_quantity <= 0)
                                        <span style="background:#fef2f2; color:#dc2626; padding:0.2rem 0.6rem; border-radius:0.25rem; font-size:0.78rem; font-weight:700; border:1px solid #fecaca;">Hết hàng</span>
                                    @elseif($product->stock_quantity <= 10)
                                        <span style="background:#fef3c7; color:#d97706; padding:0.2rem 0.6rem; border-radius:0.25rem; font-size:0.78rem; font-weight:700; border:1px solid #fde68a;">{{ number_format($product->stock_quantity, 0) }}</span>
                                    @else
                                        <span style="font-weight:700; color:#1e3a5f; font-size:0.95rem;">{{ number_format($product->stock_quantity, 0) }}</span>
                                    @endif
                                </td>
                                <td style="padding:0.9rem 1rem; text-align:center; color:#64748b; font-weight:600;">{{ $product->unit }}</td>
                                <td style="padding:0.9rem 1rem; text-align:center;">
                                    @if($product->expiry_date && $product->expiry_date->isPast())
                                        <span style="background:#fef2f2; color:#dc2626; padding:0.2rem 0.55rem; border-radius:0.25rem; font-size:0.76rem; font-weight:800; border:1px solid #fecaca;">Hết hạn {{ $product->expiry_date->format('d/m/Y') }}</span>
                                    @elseif($product->expiry_date)
                                        <span style="color:#475569; font-size:0.82rem; font-weight:700;">{{ $product->expiry_date->format('d/m/Y') }}</span>
                                    @else
                                        <span style="color:#cbd5e1; font-size:0.82rem;">—</span>
                                    @endif
                                </td>
                                <td style="padding:0.9rem 1rem; text-align:center;">
                                    @if($product->status === 'active')
                                        <span style="background:#f0fdf4; color:#16a34a; padding:0.25rem 0.65rem; border-radius:0.25rem; font-size:0.75rem; font-weight:700; border:1px solid #dcfce7;">✅ Còn hàng</span>
                                    @else
                                        <span style="background:#fef2f2; color:#dc2626; padding:0.25rem 0.65rem; border-radius:0.25rem; font-size:0.75rem; font-weight:700; border:1px solid #fecaca;">⛔ Hết hàng</span>
                                    @endif
                                </td>
                                <td style="padding:0.9rem 1rem; text-align:center;">
                                    <div style="display:flex; align-items:center; justify-content:center; gap:0.5rem;">
                                        <button type="button"
                                            class="btn-product-edit"
                                            data-action="{{ route('admin.packaged-products.update', $product) }}"
                                            data-sku="{{ e($product->sku) }}"
                                            data-name="{{ e($product->name) }}"
                                            data-description="{{ e($product->description ?? '') }}"
                                            data-category="{{ e($product->category ?? '') }}"
                                            data-unit="{{ e($product->unit) }}"
                                            data-stock="{{ e($product->stock_quantity) }}"
                                            data-expiry="{{ $product->expiry_date?->format('Y-m-d') }}"
                                            data-status="{{ e($product->status) }}"
                                            style="display:inline-flex; align-items:center; gap:0.3rem; background:#eff6ff; color:#3b82f6; border:1px solid #dbeafe; padding:0.35rem 0.75rem; border-radius:0.25rem; font-size:0.78rem; font-weight:700; cursor:pointer; transition:all 0.15s;"
                                            onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                                            ✏️ Sửa
                                        </button>
                                        <form method="POST" action="{{ route('admin.packaged-products.destroy', $product) }}" onsubmit="return confirm('Xóa sản phẩm \'{{ addslashes($product->name) }}\'?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="display:inline-flex; align-items:center; gap:0.3rem; background:#fef2f2; color:#ef4444; border:1px solid #fecaca; padding:0.35rem 0.75rem; border-radius:0.25rem; font-size:0.78rem; font-weight:700; cursor:pointer; transition:all 0.15s;"
                                                    onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fef2f2'">
                                                🗑 Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($products->hasPages())
                    <div style="padding:1rem 1.5rem; border-top:1px solid #f1f5f9;">
                        {{ $products->withQueryString()->links() }}
                    </div>
                    @endif
                @endif
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- MODALS: Thuốc dùng ngoài / Trà thảo mộc      --}}
    {{-- ═══════════════════════════════════════════ --}}

    {{-- Modal Thêm thuốc dùng ngoài/trà thảo mộc --}}
    <div id="addProductModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.65); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:0.75rem; width:680px; max-width:96%; box-shadow:0 25px 50px -12px rgba(0,0,0,0.15); border:1px solid #f1f5f9; overflow:hidden; display:flex; flex-direction:column; max-height:92vh; animation:ppModalIn 0.25s ease-out;">
            <div style="padding:1.25rem 1.75rem; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; background:#fafbfc;">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:36px; height:36px; background:#f0fdf4; border:1px solid #dcfce7; border-radius:0.5rem; display:flex; align-items:center; justify-content:center; font-size:1.1rem;">📦</div>
                    <h2 style="margin:0; font-size:1.15rem; font-weight:800; color:#1e293b;">Thêm Thuốc Dùng Ngoài/Trà Thảo Mộc</h2>
                </div>
                <button type="button" onclick="closeAddProductModal()" style="background:none; border:none; font-size:1.4rem; color:#94a3b8; cursor:pointer;">✕</button>
            </div>
            <div style="padding:1.5rem 1.75rem; overflow-y:auto; flex:1;">
                <form id="addProductForm" action="{{ route('admin.packaged-products.store') }}" method="POST">
                    @csrf
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">

                        {{-- Tên sản phẩm --}}
                        <div style="grid-column:span 2; display:flex; flex-direction:column; gap:0.4rem;">
                            <label class="pp-label">Tên sản phẩm <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="name" class="pp-input" placeholder="Vd: Trà Cam thảo, Cao dán núi..." required>
                        </div>

                        {{-- Phân loại --}}
                        <div style="display:flex; flex-direction:column; gap:0.4rem;">
                            <label class="pp-label">Phân loại</label>
                            <select name="category" class="pp-input">
                                <option value="">— Chưa phân loại —</option>
                                @foreach(\App\Models\PackagedProduct::categories() as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Đơn vị --}}
                        <div style="display:flex; flex-direction:column; gap:0.4rem;">
                            <label class="pp-label">Đơn vị <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="unit" class="pp-input" value="gói" placeholder="gói, hộp, lọ, chai..." required>
                        </div>

                        {{-- Mô tả --}}
                        <div style="grid-column:span 2; display:flex; flex-direction:column; gap:0.4rem;">
                            <label class="pp-label">Mô tả / Thành phần</label>
                            <textarea name="description" class="pp-input" rows="2" placeholder="Thành phần, công dụng, hướng dẫn sử dụng..."></textarea>
                        </div>

                        {{-- Tồn kho --}}
                        <div style="display:flex; flex-direction:column; gap:0.4rem;">
                            <label class="pp-label">Số lượng tồn kho <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="stock_quantity" value="0" min="0" step="1" class="pp-input" required>
                        </div>

                        {{-- Hạn sử dụng --}}
                        <div style="display:flex; flex-direction:column; gap:0.4rem;">
                            <label class="pp-label">Hạn sử dụng</label>
                            <input type="date" name="expiry_date" class="pp-input">
                        </div>

                        {{-- Trạng thái --}}
                        <div style="display:flex; flex-direction:column; gap:0.4rem;">
                            <label class="pp-label">Trạng thái</label>
                            <select name="status" class="pp-input">
                                <option value="active">✅ Còn hàng</option>
                                <option value="inactive">⛔ Hết hàng</option>
                            </select>
                        </div>

                    </div>
                    <div style="display:flex; gap:0.75rem; margin-top:1.5rem; justify-content:flex-end; border-top:1px solid #f1f5f9; padding-top:1.25rem;">
                        <button type="button" onclick="closeAddProductModal()" style="padding:0.65rem 1.5rem; background:#fff; border:1px solid #e2e8f0; border-radius:0.375rem; font-weight:700; cursor:pointer; color:#475569;">Hủy bỏ</button>
                        <button type="submit" style="padding:0.65rem 1.75rem; background:#16a34a; color:#fff; border:none; border-radius:0.375rem; font-weight:700; cursor:pointer;">💾 Lưu sản phẩm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Sửa thuốc dùng ngoài/trà thảo mộc --}}
    <div id="editProductModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.65); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:0.75rem; width:680px; max-width:96%; box-shadow:0 25px 50px -12px rgba(0,0,0,0.15); border:1px solid #f1f5f9; overflow:hidden; display:flex; flex-direction:column; max-height:92vh; animation:ppModalIn 0.25s ease-out;">
            <div style="padding:1.25rem 1.75rem; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; background:#fafbfc;">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="width:36px; height:36px; background:#eff6ff; border:1px solid #dbeafe; border-radius:0.5rem; display:flex; align-items:center; justify-content:center; font-size:1.1rem;">✏️</div>
                    <div>
                        <h2 style="margin:0; font-size:1.15rem; font-weight:800; color:#1e293b;">Chỉnh Sửa Thuốc Dùng Ngoài/Trà Thảo Mộc</h2>
                        <p id="editProductSku" style="margin:0; font-size:0.78rem; color:#64748b; font-weight:600;"></p>
                    </div>
                </div>
                <button type="button" onclick="closeEditProductModal()" style="background:none; border:none; font-size:1.4rem; color:#94a3b8; cursor:pointer;">✕</button>
            </div>
            <div style="padding:1.5rem 1.75rem; overflow-y:auto; flex:1;">
                <form id="editProductForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">

                        {{-- Tên sản phẩm --}}
                        <div style="grid-column:span 2; display:flex; flex-direction:column; gap:0.4rem;">
                            <label class="pp-label">Tên sản phẩm <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="name" id="ep_name" class="pp-input" required>
                        </div>

                        {{-- Phân loại --}}
                        <div style="display:flex; flex-direction:column; gap:0.4rem;">
                            <label class="pp-label">Phân loại</label>
                            <select name="category" id="ep_category" class="pp-input">
                                <option value="">— Chưa phân loại —</option>
                                @foreach(\App\Models\PackagedProduct::categories() as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Đơn vị --}}
                        <div style="display:flex; flex-direction:column; gap:0.4rem;">
                            <label class="pp-label">Đơn vị <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="unit" id="ep_unit" class="pp-input" required>
                        </div>

                        {{-- Mô tả --}}
                        <div style="grid-column:span 2; display:flex; flex-direction:column; gap:0.4rem;">
                            <label class="pp-label">Mô tả / Thành phần</label>
                            <textarea name="description" id="ep_description" class="pp-input" rows="2"></textarea>
                        </div>

                        {{-- Tồn kho --}}
                        <div style="display:flex; flex-direction:column; gap:0.4rem;">
                            <label class="pp-label">Số lượng tồn kho <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="stock_quantity" id="ep_stock" min="0" step="1" class="pp-input" required>
                        </div>

                        {{-- Hạn sử dụng --}}
                        <div style="display:flex; flex-direction:column; gap:0.4rem;">
                            <label class="pp-label">Hạn sử dụng</label>
                            <input type="date" name="expiry_date" id="ep_expiry_date" class="pp-input">
                        </div>

                        {{-- Trạng thái --}}
                        <div style="display:flex; flex-direction:column; gap:0.4rem;">
                            <label class="pp-label">Trạng thái</label>
                            <select name="status" id="ep_status" class="pp-input">
                                <option value="active">✅ Còn hàng</option>
                                <option value="inactive">⛔ Hết hàng</option>
                            </select>
                        </div>

                    </div>
                    <div style="display:flex; gap:0.75rem; margin-top:1.5rem; justify-content:flex-end; border-top:1px solid #f1f5f9; padding-top:1.25rem;">
                        <button type="button" onclick="closeEditProductModal()" style="padding:0.65rem 1.5rem; background:#fff; border:1px solid #e2e8f0; border-radius:0.375rem; font-weight:700; cursor:pointer; color:#475569;">Hủy bỏ</button>
                        <button type="submit" style="padding:0.65rem 1.75rem; background:#3b82f6; color:#fff; border:none; border-radius:0.375rem; font-weight:700; cursor:pointer;">💾 Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @endif

</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- MODALS (chỉ cần khi tab Herbs)                                      --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
@if($tab === 'herbs')

{{-- Modal Thêm Dược Liệu --}}
<div id="addHerbModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:0.5rem; width:950px; max-width:95%; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1); border:1px solid #f1f5f9; overflow:hidden; display:flex; flex-direction:column; max-height:90vh;">
        <div style="padding:1.25rem 2rem; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; background:#fafbfc;">
            <h2 style="margin:0; font-size:1.25rem; font-weight:850; color:#1e293b;">🌿 Thêm Dược Liệu Mới</h2>
            <button type="button" onclick="document.getElementById('addHerbModal').style.display='none'" style="background:none; border:none; font-size:1.5rem; color:#94a3b8; cursor:pointer;">✕</button>
        </div>
        <div style="padding:1.5rem 2rem; overflow-y:auto; flex:1;">
            @if ($errors->any())
                <div style="background:#fef2f2; color:#ef4444; padding:1rem; border-radius:0.375rem; margin-bottom:1.5rem; font-size:0.9rem;">
                    <ul style="margin:0; padding-left:1.5rem;">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('admin.medicinal-herbs.store') }}" method="POST">
                @csrf
                <input type="hidden" name="form_type" value="create">
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1.25rem;">
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <label style="font-weight:700; color:#475569; font-size:0.9rem;">Tên dược liệu <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" class="wh-form-input" placeholder="VD: Nhân sâm..." value="{{ old('name') }}" required>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <label style="font-weight:700; color:#475569; font-size:0.9rem;">Phân loại</label>
                        <select name="category" class="wh-form-input">
                            <option value="Dược liệu bốc thuốc" {{ old('category')=='Dược liệu bốc thuốc'?'selected':'' }}>Dược liệu bốc thuốc</option>
                            <option value="Dược liệu rời" {{ old('category')=='Dược liệu rời'?'selected':'' }}>Dược liệu rời</option>
                            <option value="Thuốc ngâm rượu" {{ old('category')=='Thuốc ngâm rượu'?'selected':'' }}>Thuốc ngâm rượu</option>
                            <option value="Chế phẩm dùng ngoài" {{ old('category')=='Chế phẩm dùng ngoài'?'selected':'' }}>Chế phẩm dùng ngoài</option>
                        </select>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <label style="font-weight:700; color:#475569; font-size:0.9rem;">Cách dùng</label>
                        <select name="usage_type" class="wh-form-input">
                            <option value="Sắc" {{ old('usage_type')=='Sắc'?'selected':'' }}>Sắc</option>
                            <option value="Uống" {{ old('usage_type')=='Uống'?'selected':'' }}>Uống</option>
                            <option value="Dùng ngoài" {{ old('usage_type')=='Dùng ngoài'?'selected':'' }}>Dùng ngoài</option>
                        </select>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <label style="font-weight:700; color:#475569; font-size:0.9rem;">Đơn vị tính <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="unit" class="wh-form-input" placeholder="VD: g, kg, gói..." value="{{ old('unit') }}" required>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <label style="font-weight:700; color:#475569; font-size:0.9rem;">Số lượng tồn kho <span style="color:#ef4444;">*</span></label>
                        <input type="number" step="any" name="stock_quantity" class="wh-form-input" placeholder="0" value="{{ old('stock_quantity','0') }}" required>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <label style="font-weight:700; color:#475569; font-size:0.9rem;">Hạn sử dụng</label>
                        <input type="date" name="expiry_date" class="wh-form-input" value="{{ old('expiry_date') }}">
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <label style="font-weight:700; color:#475569; font-size:0.9rem;">Trạng thái <span style="color:#ef4444;">*</span></label>
                        <select name="status" class="wh-form-input" required>
                            <option value="active" {{ old('status')=='active'?'selected':'' }}>Đang sử dụng</option>
                            <option value="out_of_stock" {{ old('status')=='out_of_stock'?'selected':'' }}>Hết hàng</option>
                            <option value="expired" {{ old('status')=='expired'?'selected':'' }}>Ngừng sử dụng</option>
                        </select>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <label style="font-weight:700; color:#475569; font-size:0.9rem;">Cảnh báo đặc biệt</label>
                        <input type="text" name="warning_note" class="wh-form-input" placeholder="VD: Phụ nữ có thai không dùng..." value="{{ old('warning_note') }}">
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem; grid-column:span 3;">
                        <label style="font-weight:700; color:#475569; font-size:0.9rem;">Mô tả / Tác dụng</label>
                        <textarea name="description" class="wh-form-input" rows="2" placeholder="Mô tả công dụng, tính vị...">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div style="display:flex; gap:1rem; margin-top:1.5rem; justify-content:flex-end; border-top:1px solid #f1f5f9; padding-top:1.25rem;">
                    <button type="button" onclick="document.getElementById('addHerbModal').style.display='none'" style="background:#fff; min-width:100px; padding:0.75rem 1.5rem; border-radius:0.375rem; border:1px solid #cbd5e1; font-weight:700; cursor:pointer; color:#64748b;">Hủy bỏ</button>
                    <button type="submit" style="min-width:160px; padding:0.75rem 1.5rem; border-radius:0.375rem; background:#5eb542; color:white; border:none; font-weight:700; cursor:pointer;">Lưu dược liệu</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Sửa Dược Liệu --}}
<div id="editHerbModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:0.5rem; width:950px; max-width:95%; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1); border:1px solid #f1f5f9; overflow:hidden; display:flex; flex-direction:column; max-height:90vh;">
        <div style="padding:1.25rem 2rem; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; background:#fafbfc;">
            <h2 style="margin:0; font-size:1.25rem; font-weight:850; color:#1e293b;">✏️ Chỉnh Sửa Dược Liệu</h2>
            <button type="button" onclick="document.getElementById('editHerbModal').style.display='none'" style="background:none; border:none; font-size:1.5rem; color:#94a3b8; cursor:pointer;">✕</button>
        </div>
        <div style="padding:1.5rem 2rem; overflow-y:auto; flex:1;">
            <form id="editHerbForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_type" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1.25rem;">
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <label style="font-weight:700; color:#475569; font-size:0.9rem;">Tên dược liệu <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="name" id="edit_name" class="wh-form-input" placeholder="VD: Nhân sâm..." required>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <label style="font-weight:700; color:#475569; font-size:0.9rem;">Phân loại</label>
                        <select name="category" id="edit_category" class="wh-form-input">
                            <option value="Dược liệu bốc thuốc">Dược liệu bốc thuốc</option>
                            <option value="Dược liệu rời">Dược liệu rời</option>
                            <option value="Thuốc ngâm rượu">Thuốc ngâm rượu</option>
                            <option value="Chế phẩm dùng ngoài">Chế phẩm dùng ngoài</option>
                        </select>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <label style="font-weight:700; color:#475569; font-size:0.9rem;">Cách dùng</label>
                        <select name="usage_type" id="edit_usage_type" class="wh-form-input">
                            <option value="Sắc">Sắc</option>
                            <option value="Uống">Uống</option>
                            <option value="Dùng ngoài">Dùng ngoài</option>
                        </select>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <label style="font-weight:700; color:#475569; font-size:0.9rem;">Đơn vị tính <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="unit" id="edit_unit" class="wh-form-input" required>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <label style="font-weight:700; color:#475569; font-size:0.9rem;">Số lượng tồn kho <span style="color:#ef4444;">*</span></label>
                        <input type="number" step="any" name="stock_quantity" id="edit_stock_quantity" class="wh-form-input" required>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <label style="font-weight:700; color:#475569; font-size:0.9rem;">Hạn sử dụng</label>
                        <input type="date" name="expiry_date" id="edit_expiry_date" class="wh-form-input">
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <label style="font-weight:700; color:#475569; font-size:0.9rem;">Trạng thái <span style="color:#ef4444;">*</span></label>
                        <select name="status" id="edit_status" class="wh-form-input" required>
                            <option value="active">Đang sử dụng</option>
                            <option value="out_of_stock">Hết hàng</option>
                            <option value="expired">Ngừng sử dụng</option>
                        </select>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <label style="font-weight:700; color:#475569; font-size:0.9rem;">Cảnh báo đặc biệt</label>
                        <input type="text" name="warning_note" id="edit_warning_note" class="wh-form-input">
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem; grid-column:span 3;">
                        <label style="font-weight:700; color:#475569; font-size:0.9rem;">Mô tả / Tác dụng</label>
                        <textarea name="description" id="edit_description" class="wh-form-input" rows="2"></textarea>
                    </div>
                </div>
                <div style="display:flex; gap:1rem; margin-top:1.5rem; justify-content:flex-end; border-top:1px solid #f1f5f9; padding-top:1.25rem;">
                    <button type="button" onclick="document.getElementById('editHerbModal').style.display='none'" style="background:#fff; min-width:100px; padding:0.75rem 1.5rem; border-radius:0.375rem; border:1px solid #cbd5e1; font-weight:700; cursor:pointer; color:#64748b;">Hủy bỏ</button>
                    <button type="submit" style="min-width:160px; padding:0.75rem 1.5rem; border-radius:0.375rem; background:#5eb542; color:white; border:none; font-weight:700; cursor:pointer;">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Nhập Excel --}}
<div id="importExcelModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center;">
    <div style="width:550px; background:#fff; border-radius:0.5rem; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1); padding:1.5rem; border:1px solid #f1f5f9;">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #e2e8f0; padding-bottom:0.75rem; margin-bottom:1.25rem;">
            <h3 style="font-size:1.15rem; font-weight:800; color:#1e293b; margin:0;">📤 Nhập Dược Liệu từ Excel</h3>
            <button type="button" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#94a3b8;" onclick="document.getElementById('importExcelModal').style.display='none'">×</button>
        </div>
        <form action="{{ route('admin.medicinal-herbs.import-excel') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; font-weight:700; color:#475569; margin-bottom:0.5rem; font-size:0.9rem;">Chọn file Excel hoặc CSV (.xlsx, .xls, .csv)</label>
                <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required style="width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:0.375rem; font-size:0.9rem; background:#f8fafc;">
                <p style="font-size:0.78rem; color:#64748b; margin-top:0.65rem; background:#f8fafc; padding:0.75rem; border-left:3px solid #5eb542; border-radius:2px;">
                    * <strong>Cấu trúc tệp:</strong> Sử dụng file mẫu để đảm bảo đúng định dạng.
                    <a href="{{ route('admin.medicinal-herbs.download-template') }}" style="color:#2563eb; font-weight:700;">📥 Tải file mẫu</a><br>
                    * <strong>Trùng tên:</strong> Hệ thống sẽ tự động cộng dồn số lượng tồn kho.
                </p>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.75rem; border-top:1px solid #e2e8f0; padding-top:1rem;">
                <button type="button" onclick="document.getElementById('importExcelModal').style.display='none'" style="padding:0.6rem 1.25rem; border-radius:0.375rem; background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; font-weight:700; cursor:pointer;">Hủy</button>
                <button type="submit" style="padding:0.6rem 1.25rem; border-radius:0.375rem; background:#5eb542; color:#fff; border:none; font-weight:700; cursor:pointer;">Tải lên & Nhập</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Xác nhận xóa --}}
<div id="confirmModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center; opacity:0; transition:opacity 0.25s ease;">
    <div id="confirmModalCard" style="background:#fff; border-radius:0.5rem; width:480px; padding:2rem; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1); transform:scale(0.9); transition:transform 0.25s ease; border:1px solid #f1f5f9;">
        <div style="text-align:center; margin-bottom:1.5rem;">
            <div style="width:64px; height:64px; background:#fee2e2; color:#ef4444; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:2.2rem; margin-bottom:1rem;">⚠️</div>
            <h3 style="margin:0 0 0.5rem; font-size:1.35rem; font-weight:800; color:#1e293b;">Xác nhận xóa dược liệu?</h3>
            <p style="margin:0; color:#64748b; font-size:0.95rem;" id="confirmModalText">Bạn có chắc muốn xóa các dược liệu đã chọn không?</p>
        </div>
        <div style="display:flex; gap:1rem; justify-content:center;">
            <button type="button" id="confirmCancelBtn" style="flex:1; padding:0.8rem; border-radius:0.375rem; border:1px solid #cbd5e1; background:#fff; color:#475569; font-weight:700; cursor:pointer;">Hủy bỏ</button>
            <button type="button" id="confirmOkBtn" style="flex:1; padding:0.8rem; border-radius:0.375rem; border:none; background:#ef4444; color:#fff; font-weight:700; cursor:pointer;">Đồng ý xóa</button>
        </div>
    </div>
</div>

{{-- Modal Lịch sử thao tác --}}
<div id="historyStockModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:0.5rem; width:700px; max-width:95%; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1); border:1px solid #f1f5f9; overflow:hidden; display:flex; flex-direction:column; max-height:80vh;">
        <div style="padding:1.25rem 2rem; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; background:#fafbfc;">
            <h2 style="margin:0; font-size:1.25rem; font-weight:850; color:#1e293b;">📜 Lịch Sử: <span id="history_herb_name" style="color:#5eb542;">...</span></h2>
            <button type="button" onclick="document.getElementById('historyStockModal').style.display='none'" style="background:none; border:none; font-size:1.5rem; color:#94a3b8; cursor:pointer;">✕</button>
        </div>
        <div style="padding:1.5rem 2rem; overflow-y:auto; flex:1;">
            <div id="history_loading" style="text-align:center; padding:2rem; color:#64748b; font-weight:600;">⏳ Đang tải...</div>
            <div id="history_empty" style="display:none; text-align:center; padding:3rem; color:#64748b;">❌ Chưa có lịch sử thay đổi.</div>
            <div id="history_content" style="display:none;">
                <div id="history_list" style="display:flex; flex-direction:column; gap:0.75rem;"></div>
            </div>
        </div>
        <div style="display:flex; gap:1rem; padding:1rem 2rem; justify-content:flex-end; border-top:1px solid #f1f5f9;">
            <button type="button" onclick="document.getElementById('historyStockModal').style.display='none'" style="background:#fff; padding:0.6rem 1.25rem; border-radius:0.375rem; border:1px solid #cbd5e1; font-weight:700; cursor:pointer; color:#64748b;">Đóng</button>
        </div>
    </div>
</div>

@endif

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- STYLES                                                              --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<style>
.warehouse-container { --primary: #5eb542; --primary-dark: #4fa235; --text-dark: #1e293b; --text-muted: #64748b; --border: #e2e8f0; --bg-light: #f8fafc; }

/* Tab switcher */
.wh-tabs {
    display: flex;
    gap: 0.25rem;
    background: #f1f5f9;
    padding: 0.35rem;
    border-radius: 0.75rem;
    margin-bottom: 1.5rem;
    width: fit-content;
}
.wh-tab {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.6rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none;
    color: #64748b;
    transition: all 0.2s;
}
.wh-tab:hover { background: #fff; color: #1e293b; }
.wh-tab.active { background: #fff; color: var(--primary); box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.wh-tab-badge { padding: 0.15rem 0.5rem; border-radius: 999px; font-size: 0.75rem; font-weight: 800; }

/* Stats */
.wh-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
.wh-stat { background: #fff; padding: 1.25rem 1.5rem; border-radius: 0.5rem; display: flex; align-items: center; gap: 1rem; border: 1px solid #f1f5f9; box-shadow: 0 4px 15px rgba(0,0,0,0.02); text-decoration: none; transition: all 0.2s; cursor: pointer; }
.wh-stat:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-color: #cbd5e1; }
.wh-stat--active { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(94,181,66,0.15); }
.wh-stat--warning { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.15); }
.wh-stat--expired { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.15); }
.wh-stat__icon { width: 48px; height: 48px; min-width: 48px; border-radius: 0.5rem; background: #eef9ee; color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
.wh-stat__value { font-size: 1.75rem; font-weight: 850; color: var(--text-dark); line-height: 1; }
.wh-stat__label { font-size: 0.8rem; color: var(--text-muted); font-weight: 600; margin-top: 0.2rem; }

/* Card */
.wh-card { background: #fff; border-radius: 0.5rem; padding: 1.75rem; box-shadow: 0 10px 30px rgba(0,0,0,0.02); border: 1px solid #f1f5f9; }

/* Filter row */
.wh-filter-form { margin-bottom: 1.5rem; }
.wh-filter-row { display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem; }
.wh-filter-item { display: flex; flex-direction: column; gap: 0.35rem; }
.wh-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
.wh-search-wrap { position: relative; width: 280px; }
.wh-search-wrap input { width: 100%; height: 38px; padding: 0 1rem 0 2.25rem; border-radius: 0.375rem; border: 1px solid var(--border); background: #fcfdfe; font-size: 0.88rem; color: var(--text-dark); transition: all 0.2s; box-sizing: border-box; }
.wh-search-wrap input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(94,181,66,0.15); outline: none; }
.wh-search-icon { position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.9rem; }
.wh-select { height: 38px; padding: 0 1.75rem 0 0.75rem; border-radius: 0.375rem; border: 1px solid var(--border); background: #fcfdfe; color: var(--text-dark); font-weight: 600; font-size: 0.88rem; outline: none; cursor: pointer; }
.wh-btn-reset { height: 38px; padding: 0 1.25rem; border-radius: 0.375rem; border: 1px solid var(--border); background: #fff; color: var(--text-muted); font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; transition: all 0.2s; }
.wh-btn-reset:hover { background: #f8fafc; border-color: #cbd5e1; color: var(--text-dark); }
.wh-btn-outline { height: 38px; padding: 0 1rem; border-radius: 0.375rem; border: 1px solid var(--border); background: #fff; color: #475569; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.85rem; transition: all 0.2s; }
.wh-btn-primary { height: 38px; padding: 0 1.25rem; border-radius: 0.375rem; background: var(--primary); color: #fff; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; transition: all 0.2s; text-decoration: none; }
.wh-btn-primary:hover { background: var(--primary-dark); }
.wh-dropdown-item:hover { background: #f1f5f9 !important; }

/* Table */
.wh-table-wrap { overflow-x: auto; border: 1px solid #cbd5e1; border-radius: 0.375rem; }
.wh-table { width: 100%; border-collapse: collapse; }
.wh-table thead tr { background: #f8fafc; }
.wh-table th { text-align: left; padding: 0.85rem 1rem; font-size: 0.8rem; font-weight: 800; color: #1e3a5f; letter-spacing: 0.02em; border: 1px solid #cbd5e1; }
.wh-table td { padding: 0.85rem 1rem; border: 1px solid #e2e8f0; vertical-align: middle; }
.wh-table tbody tr:hover { background: #fafbfc; }
.wh-action-cell { display: flex; gap: 0.5rem; }
.wh-btn-edit { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.9rem; border-radius: 0.375rem; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-weight: 700; text-decoration: none; font-size: 0.82rem; cursor: pointer; transition: all 0.2s; }
.wh-btn-edit:hover { background: #16a34a; color: #fff; border-color: #16a34a; }
.wh-btn-history { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.9rem; border-radius: 0.375rem; background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; font-weight: 700; text-decoration: none; font-size: 0.82rem; cursor: pointer; transition: all 0.2s; }
.wh-btn-history:hover { background: #e2e8f0; color: #1e293b; }

/* Form inputs in modals */
.wh-form-input { padding: 0.75rem 1rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; outline: none; font-size: 0.95rem; width: 100%; box-sizing: border-box; transition: all 0.2s; }
.wh-form-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(94,181,66,0.15); }

/* Pagination */
.wh-pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; }
.wh-pagination p { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; }
.pagination { display: flex; gap: 0.25rem; }
.page-item .page-link { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 0.5rem; background: #fff; border: 1px solid var(--border); color: var(--text-dark); text-decoration: none; font-weight: 700; }
.page-item.active .page-link { background: var(--primary); color: #fff; border-color: var(--primary); }
/* Product modal inputs */
.pp-label { font-size: 0.8rem; font-weight: 700; color: #475569; }
.pp-input { padding: 0.65rem 0.9rem; border-radius: 0.375rem; border: 1px solid #e2e8f0; font-size: 0.9rem; color: #1e293b; outline: none; width: 100%; box-sizing: border-box; transition: border-color 0.2s; }
.pp-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }

@keyframes ppModalIn {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to   { opacity: 1; transform: scale(1)   translateY(0); }
}
</style>

@push('scripts')
<script>
// ── Product Modal Functions ───────────────────────────────────────
window.openAddProductModal = function() {
    const modal = document.getElementById('addProductModal');
    if (modal) { modal.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
};
window.closeAddProductModal = function() {
    const modal = document.getElementById('addProductModal');
    if (modal) { modal.style.display = 'none'; document.body.style.overflow = ''; }
};
window.openEditProductModal = function(product) {
    const modal = document.getElementById('editProductModal');
    if (!modal) return;

    const data = product || {};
    document.getElementById('editProductForm').action = data.action || `/admin/packaged-products/${data.id || ''}`;
    document.getElementById('editProductSku').textContent = data.sku ? `SKU: ${data.sku}` : '';
    document.getElementById('ep_name').value        = data.name        || '';
    document.getElementById('ep_description').value = data.description || '';
    document.getElementById('ep_category').value    = data.category    || '';
    document.getElementById('ep_unit').value        = data.unit        || '';
    document.getElementById('ep_stock').value       = data.stock       || 0;
    document.getElementById('ep_expiry_date').value = data.expiry      || '';
    document.getElementById('ep_status').value      = data.status      || 'active';
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
};
window.closeEditProductModal = function() {
    const modal = document.getElementById('editProductModal');
    if (modal) { modal.style.display = 'none'; document.body.style.overflow = ''; }
};
// Close modals when clicking backdrop
document.addEventListener('click', function(e) {
    if (e.target.id === 'addProductModal')  closeAddProductModal();
    if (e.target.id === 'editProductModal') closeEditProductModal();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeAddProductModal(); closeEditProductModal(); }
});

document.addEventListener('click', function(e) {
    const trigger = e.target.closest('.btn-product-edit');
    if (!trigger) return;

    e.preventDefault();
    openEditProductModal({
        action: trigger.dataset.action,
        sku: trigger.dataset.sku,
        name: trigger.dataset.name,
        description: trigger.dataset.description,
        category: trigger.dataset.category,
        unit: trigger.dataset.unit,
        stock: trigger.dataset.stock,
        expiry: trigger.dataset.expiry,
        status: trigger.dataset.status,
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // ── Herb tab JS ───────────────────────────────────────────────
    const editTriggers = document.querySelectorAll('.btn-edit-trigger');
    const editModal = document.getElementById('editHerbModal');
    const editForm = document.getElementById('editHerbForm');

    if (editTriggers.length && editModal) {
        editTriggers.forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.dataset.id;
                editForm.action = `/admin/medicinal-herbs/${id}`;
                document.getElementById('edit_id').value = id || '';
                document.getElementById('edit_name').value = this.dataset.name || '';
                document.getElementById('edit_category').value = this.dataset.category || 'Dược liệu bốc thuốc';
                document.getElementById('edit_usage_type').value = this.dataset.usage_type || 'Sắc';
                document.getElementById('edit_unit').value = this.dataset.unit || '';
                document.getElementById('edit_stock_quantity').value = this.dataset.stock_quantity || '0';
                document.getElementById('edit_expiry_date').value = this.dataset.expiry_date || '';
                document.getElementById('edit_status').value = this.dataset.status || 'active';
                document.getElementById('edit_warning_note').value = this.dataset.warning_note || '';
                document.getElementById('edit_description').value = this.dataset.description || '';
                editModal.style.display = 'flex';
            });
        });
    }

    // Bulk delete
    const selectAllCheckbox = document.getElementById('select-all');
    const herbCheckboxes = document.querySelectorAll('.herb-checkbox');
    const bulkDeleteBtn = document.getElementById('btn-bulk-delete');
    const selectedCountSpan = document.getElementById('selected-count');
    const bulkDeleteForm = document.getElementById('bulk-delete-form');
    const confirmModal = document.getElementById('confirmModal');
    const confirmModalCard = document.getElementById('confirmModalCard');
    const confirmModalText = document.getElementById('confirmModalText');
    const confirmCancelBtn = document.getElementById('confirmCancelBtn');
    const confirmOkBtn = document.getElementById('confirmOkBtn');

    function toggleBulkBtn() {
        const cnt = document.querySelectorAll('.herb-checkbox:checked').length;
        if (selectedCountSpan) selectedCountSpan.textContent = cnt;
        if (bulkDeleteBtn) bulkDeleteBtn.style.display = cnt > 0 ? 'inline-flex' : 'none';
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            herbCheckboxes.forEach(cb => cb.checked = this.checked);
            toggleBulkBtn();
        });
    }
    herbCheckboxes.forEach(cb => cb.addEventListener('change', toggleBulkBtn));

    function showConfirmModal(text, onConfirm) {
        confirmModalText.innerHTML = text;
        confirmModal.style.display = 'flex';
        setTimeout(() => {
            confirmModal.style.opacity = '1';
            if (confirmModalCard) confirmModalCard.style.transform = 'scale(1)';
        }, 10);
        const close = () => {
            confirmModal.style.opacity = '0';
            if (confirmModalCard) confirmModalCard.style.transform = 'scale(0.9)';
            setTimeout(() => confirmModal.style.display = 'none', 250);
            confirmOkBtn.removeEventListener('click', confirm);
            confirmCancelBtn.removeEventListener('click', close);
        };
        const confirm = () => { onConfirm(); close(); };
        confirmCancelBtn && confirmCancelBtn.addEventListener('click', close);
        confirmOkBtn && confirmOkBtn.addEventListener('click', confirm);
    }

    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const cnt = document.querySelectorAll('.herb-checkbox:checked').length;
            showConfirmModal(`Bạn có chắc chắn muốn xóa <strong>${cnt} dược liệu</strong> đã chọn?`, () => bulkDeleteForm.submit());
        });
    }

    // Export dropdown
    window.toggleExportDropdown = function() {
        const menu = document.getElementById('exportDropdownMenu');
        if (menu) menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    };
    document.addEventListener('click', function(e) {
        const wrapper = document.querySelector('[id="btnExportDropdown"]')?.closest('div');
        const menu = document.getElementById('exportDropdownMenu');
        if (menu && wrapper && !wrapper.parentElement.contains(e.target)) menu.style.display = 'none';
    });

    // Print herbs
    window.printHerbList = function() {
        const search = "{{ request('search') }}";
        const usageType = "{{ request('usage_type') }}";
        const url = "{{ route('admin.medicinal-herbs.print-list') }}?search=" + encodeURIComponent(search) + "&usage_type=" + encodeURIComponent(usageType) + "&auto_print=1";
        const iframe = document.createElement('iframe');
        iframe.style.cssText = 'position:fixed;width:0;height:0;border:none;opacity:0;';
        iframe.src = url;
        document.body.appendChild(iframe);
        iframe.onload = function() {
            try { iframe.contentWindow.focus(); iframe.contentWindow.print(); } catch(e) {}
            setTimeout(() => document.body.removeChild(iframe), 3000);
        };
    };

    // History modal
    const historyTriggers = document.querySelectorAll('.btn-history-trigger');
    const historyModal = document.getElementById('historyStockModal');
    if (historyTriggers.length && historyModal) {
        historyTriggers.forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.dataset.id;
                const name = this.dataset.name;
                document.getElementById('history_herb_name').textContent = name;
                document.getElementById('history_loading').style.display = 'block';
                document.getElementById('history_empty').style.display = 'none';
                document.getElementById('history_content').style.display = 'none';
                document.getElementById('history_list').innerHTML = '';
                historyModal.style.display = 'flex';

                fetch(`/admin/medicinal-herbs/${id}/stock-logs`)
                    .then(r => r.json())
                    .then(data => {
                        document.getElementById('history_loading').style.display = 'none';
                        if (!data.logs.length) {
                            document.getElementById('history_empty').style.display = 'block';
                        } else {
                            const list = document.getElementById('history_list');
                            data.logs.forEach(log => {
                                let badge = '';
                                if (log.change_quantity > 0) badge = `<span style="color:#16a34a;font-weight:800;background:#eef9ee;padding:0.25rem 0.5rem;border-radius:0.25rem;font-size:0.8rem;border:1px solid #bbf7d0;">+${log.change_quantity} ${data.unit}</span>`;
                                else if (log.change_quantity < 0) badge = `<span style="color:#dc2626;font-weight:800;background:#fef2f2;padding:0.25rem 0.5rem;border-radius:0.25rem;font-size:0.8rem;border:1px solid #fecaca;">${log.change_quantity} ${data.unit}</span>`;
                                else badge = `<span style="color:#64748b;font-weight:800;background:#f1f5f9;padding:0.25rem 0.5rem;border-radius:0.25rem;font-size:0.8rem;">0 ${data.unit}</span>`;
                                const card = document.createElement('div');
                                card.style.cssText = 'background:#fff;padding:0.85rem 1.15rem;border-radius:6px;border:1px solid #cbd5e1;display:flex;flex-direction:column;gap:0.4rem;';
                                card.innerHTML = `
                                    <div style="display:flex;justify-content:space-between;align-items:center;">
                                        <div style="font-weight:800;color:#1e3a5f;font-size:0.92rem;">📝 ${log.action_type_label}</div>
                                        <div>${badge}</div>
                                    </div>
                                    ${log.note ? `<div style="font-size:0.8rem;color:#475569;background:#f8fafc;padding:0.4rem 0.6rem;border-radius:4px;border-left:3px solid #cbd5e1;font-style:italic;">${log.note}</div>` : ''}
                                    <div style="margin-top:0.4rem;padding-top:0.4rem;border-top:1px dashed #e2e8f0;display:flex;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;font-size:0.78rem;color:#64748b;">
                                        <span>👤 <strong>${log.operator}</strong> &nbsp; 📅 ${log.time}</span>
                                        <span>Trước: <strong>${log.old_quantity} ${data.unit}</strong> ➔ Sau: <strong style="color:#0f766e;">${log.new_quantity} ${data.unit}</strong></span>
                                    </div>`;
                                list.appendChild(card);
                            });
                            document.getElementById('history_content').style.display = 'block';
                        }
                    })
                    .catch(() => { document.getElementById('history_loading').innerHTML = '<span style="color:#ef4444;">❌ Lỗi khi tải lịch sử.</span>'; });
            });
        });
    }
});
</script>
@endpush

@endsection
