@extends('layouts.admin')

@section('title', 'Bài Thuốc Mẫu — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #eef9ee; color: #16a34a; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        <span class="icon">🍵</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Danh sách Bài thuốc mẫu</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Quản lý và thiết lập các bài thuốc mẫu gia truyền phục vụ kê đơn nhanh bằng AI</p>
    </div>
</div>
@endsection

@section('content')
@php
    $oldCreateHerbs = [];
    if (old('form_type') === 'create' && is_array(old('items'))) {
        foreach (old('items') as $item) {
            $herb = $herbs->firstWhere('id', $item['medicinal_herb_id']);
            if ($herb) {
                $oldCreateHerbs[] = [
                    'id' => (string)$herb->id,
                    'name' => $herb->name,
                    'category' => $herb->category ?? 'Dược liệu thô',
                    'quantity' => floatval($item['quantity']),
                    'unit' => $herb->unit ?? 'g'
                ];
            }
        }
    }

    $oldEditHerbs = [];
    if (old('form_type') === 'edit' && is_array(old('items'))) {
        foreach (old('items') as $item) {
            $herb = $herbs->firstWhere('id', $item['medicinal_herb_id']);
            if ($herb) {
                $oldEditHerbs[] = [
                    'id' => (string)$herb->id,
                    'name' => $herb->name,
                    'category' => $herb->category ?? 'Dược liệu thô',
                    'quantity' => floatval($item['quantity']),
                    'unit' => $herb->unit ?? 'g'
                ];
            }
        }
    }
@endphp

<div class="record-index-container" style="margin-top: -1rem;">

    <div class="main-content-card">
        <form action="{{ route('admin.sample-prescriptions.index') }}" method="GET" class="filter-form">
            <div class="filter-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div style="display: flex; gap: 1.5rem; align-items: flex-end; flex: 1;">
                    <div class="filter-item" style="display: flex; flex-direction: column; align-items: flex-start; gap: 0.5rem;">
                        <label style="font-size: 0.85rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Tìm bài thuốc mẫu</label>
                        <div class="search-input-group" style="position: relative; width: 350px;">
                            <span class="icon" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8;">🔍</span>
                            <input type="text" name="search" placeholder="Nhập tên bài thuốc, triệu chứng..." value="{{ request('search') }}" style="width: 100%; padding: 0.8rem 1rem 0.8rem 2.75rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; background: #fcfdfe; font-size: 0.88rem;">
                        </div>
                    </div>
                    <div class="action-buttons" style="display: flex; gap: 1rem; margin-bottom: 2px;">
                        <button type="submit" class="btn-filter" style="padding: 0.75rem 1.5rem; border-radius: 0.75rem; border: 1px solid #eef2ff; background: #f8fbff; color: #3b82f6; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                            Lọc
                        </button>
                        <a href="{{ route('admin.sample-prescriptions.index') }}" class="btn-reset-box" style="padding: 0.75rem 1.5rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; background: #fff; color: #64748b; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                            🔄 Reset
                        </a>
                    </div>
                </div>
                
                <div class="action-buttons" style="display: flex; gap: 0.5rem; align-items: center;">
                    <button type="button" id="btn-bulk-delete" style="display: none; padding: 0.75rem 1.5rem; border-radius: 0.75rem; background: #ef4444; color: #fff; border: none; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2); align-items: center; justify-content: center; gap: 0.35rem; font-size: 0.85rem; white-space: nowrap; transition: all 0.2s;">
                        🗑️ Xóa đã chọn (<span id="selected-count">0</span>)
                    </button>
                    <a href="#" onclick="openAddSampleModal(); return false;" class="btn-add" style="padding: 0.75rem 1.5rem; border-radius: 0.75rem; background: #16a34a; color: #fff; text-decoration: none; font-weight: 700; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2); display: flex; align-items: center; gap: 0.5rem;">
                        + Thêm Bài Thuốc Mẫu
                    </a>
                </div>
            </div>
        </form>

        <form id="bulk-delete-form" action="{{ route('admin.sample-prescriptions.bulk-destroy') }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="table-container" style="overflow-x: auto;">
                <table class="patient-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1.5px solid #f1f5f9; text-align: left;">
                            <th style="width: 45px; text-align: center; padding: 1.25rem 1rem;">
                                <input type="checkbox" id="select-all" style="width: 18px; height: 18px; cursor: pointer; accent-color: #16a34a; margin-top: 3px;">
                            </th>
                            <th style="padding: 1.25rem 1rem; font-size: 0.75rem; font-weight: 850; color: #1e3a5f; letter-spacing: 0.05em;">TÊN BÀI THUỐC MẪU</th>
                            <th style="padding: 1.25rem 1rem; font-size: 0.75rem; font-weight: 850; color: #1e3a5f; letter-spacing: 0.05em; width: 25%;">CHỈ ĐỊNH / TRIỆU CHỨNG</th>
                            <th style="padding: 1.25rem 1rem; font-size: 0.75rem; font-weight: 850; color: #1e3a5f; letter-spacing: 0.05em; width: 35%;">THÀNH PHẦN (1 THANG)</th>
                            <th style="padding: 1.25rem 1rem; font-size: 0.75rem; font-weight: 850; color: #1e3a5f; letter-spacing: 0.05em; width: 15%; text-align: right;">HÀNH ĐỘNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($samples as $sample)
                        <tr style="border-bottom: 1px solid #f8fafc;">
                            <td style="text-align: center; vertical-align: top; padding: 1.25rem 1rem;">
                                <input type="checkbox" name="ids[]" value="{{ $sample->id }}" class="sample-checkbox" style="width: 18px; height: 18px; cursor: pointer; accent-color: #16a34a;">
                            </td>
                            <td style="padding: 1.25rem 1rem; vertical-align: top;">
                                <div style="font-weight: 800; color: #0f766e; font-size: 1.05rem;">
                                    {{ $sample->name }}
                                </div>
                                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem; font-style: italic;">
                                    <strong>Cách dùng:</strong> {{ Str::limit($sample->usage_instruction ?: 'Sắc uống hàng ngày.', 50) }}
                                </div>
                            </td>
                            <td style="padding: 1.25rem 1rem; vertical-align: top;">
                                <span style="background: #eff6ff; color: #2563eb; padding: 0.25rem 0.5rem; border-radius: 6px; font-size: 0.82rem; font-weight: 600; line-height: 1.4; display: inline-block;">
                                    🎯 {{ Str::limit($sample->suggested_condition ?: 'Nhiều thể bệnh', 40) }}
                                </span>
                            </td>
                            <td style="padding: 1.25rem 1rem; vertical-align: top;">
                                <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                                    @forelse($sample->items as $item)
                                        <span style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; font-size: 0.78rem; font-weight: 700; padding: 0.2rem 0.45rem; border-radius: 4px; display: inline-flex; align-items: center;">
                                            🍃 {{ $item->medicinalHerb->name ?? 'N/A' }} ({{ floatval($item->quantity) }}g)
                                        </span>
                                    @empty
                                        <span style="color: #94a3b8; font-size: 0.85rem; font-style: italic;">Chưa cấu hình dược liệu</span>
                                    @endforelse
                                </div>
                            </td>
                            <td style="padding: 1.25rem 1rem; text-align: right; vertical-align: top;">
                                <div class="action-cell" style="display: inline-flex; gap: 0.5rem;">
                                    <a href="#" onclick="openViewSampleModal({{ $sample->id }}); return false;" class="btn-icon view" style="padding: 0.4rem 0.75rem; border-radius: 0.6rem; font-size: 0.75rem; font-weight: 750; text-decoration: none; display: flex; align-items: center; gap: 0.4rem; border: 1px solid #e2e8f0; background: #fff; color: #8b5cf6; cursor: pointer;">
                                        👁️ Xem
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #64748b; padding: 3rem 1rem; font-weight: 500;">
                                Không tìm thấy bài thuốc mẫu nào.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @if($samples->hasPages())
        <div class="pagination-area" style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem;">
            <p class="summary" style="font-size: 0.85rem; color: #64748b; font-weight: 600;">Hiển thị {{ $samples->firstItem() }} đến {{ $samples->lastItem() }} của {{ $samples->total() }} bài thuốc</p>
            <div class="pagination-controls">
                {{ $samples->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Thêm Bài Thuốc Mẫu mới -->
<div id="addSampleModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; opacity: 1;">
    <div style="background: #fff; border-radius: 6px; width: 1200px; max-width: 95%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border: 1px solid #e2e8f0; overflow: hidden; display: flex; flex-direction: column; max-height: 90vh; animation: modalSlideIn 0.3s ease-out;">
        <div style="padding: 1.25rem 2rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                <span>✨</span> Thêm Bài Thuốc Mẫu Mới
            </h2>
            <button type="button" onclick="closeAddSampleModal()" style="background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer; padding: 0.25rem;">✕</button>
        </div>
        
        <div style="padding: 1.5rem; overflow-y: auto; flex: 1; background: #f1f5f9;">
            @if ($errors->any() && old('form_type') === 'create')
                <div style="background: #fef2f2; color: #ef4444; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; font-size: 0.9rem;">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="display: grid; grid-template-columns: 1.55fr 1fr; gap: 1.5rem;">
                {{-- Cột 1: Form chính --}}
                <div class="modal-form-card" style="background: #fff; padding: 1.5rem; border-radius: 6px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <form action="{{ route('admin.sample-prescriptions.store') }}" method="POST" id="addSamplePrescriptionForm">
                        @csrf
                        <input type="hidden" name="form_type" value="create">
                        
                        <h3 style="margin: 0 0 1.25rem 0; font-size: 1.05rem; font-weight: 800; color: #1e3a5f; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">✨ Thông Tin Bài Thuốc</h3>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                            <div class="form-group">
                                <label class="form-label">Tên bài thuốc mẫu *</label>
                                <input type="text" name="name" id="add_name" class="form-input" value="{{ old('name') }}" placeholder="VD: Bát Trân Thang..." required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Chỉ định / Triệu chứng phù hợp</label>
                                <input type="text" name="suggested_condition" id="add_suggested_condition" class="form-input" value="{{ old('suggested_condition') }}" placeholder="VD: Khí huyết lưỡng hư...">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label class="form-label">Cách dùng tổng quát mặc định</label>
                            <input type="text" name="usage_instruction" id="add_usage_instruction" class="form-input" value="{{ old('usage_instruction') }}" placeholder="VD: Sắc ngày 1 thang...">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                            <div class="form-group">
                                <label class="form-label">Dạng dùng</label>
                                <input type="text" name="preparation_type" id="add_preparation_type" class="form-input" value="{{ old('preparation_type') }}" placeholder="VD: Thuốc sắc, thuốc bột, thuốc hoàn...">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Số thang thường dùng</label>
                                <input type="number" name="default_packages" id="add_default_packages" class="form-input" value="{{ old('default_packages') }}" min="1" placeholder="VD: 10, 15...">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label class="form-label">Lưu ý cho bài thuốc</label>
                            <textarea name="notes" id="add_notes" class="form-input" style="height: 60px; resize: vertical;" placeholder="VD: Kiêng đồ cay nóng, không dùng cho phụ nữ có thai...">{{ old('notes') }}</textarea>
                        </div>

                        <h3 style="margin: 2rem 0 1.25rem 0; font-size: 1.05rem; font-weight: 800; color: #1e3a5f; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                            <span>🍃 Danh Sách Các Vị Thuốc Sắc (1 Thang)</span>
                            <span id="add-herb-count-badge" class="badge">0 vị thuốc</span>
                        </h3>

                        <div class="table-container" style="border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; background: #fff; margin-bottom: 2rem;">
                            <table class="herb-table" style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                                <thead>
                                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; text-align: left;">
                                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: #475569;">VỊ THUỐC</th>
                                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: #475569; width: 30%;">PHÂN NHÓM</th>
                                        <th style="padding: 0.75rem 0.5rem; font-weight: 700; color: #475569; text-align: right; width: 22%; white-space: nowrap;">LIỀU LƯỢNG (G)</th>
                                        <th style="padding: 0.75rem 0.5rem; text-align: center; width: 18%; white-space: nowrap;">HÀNH ĐỘNG</th>
                                    </tr>
                                </thead>
                                <tbody id="addHerbsTableBody">
                                    <tr class="empty-state-row">
                                        <td colspan="4" style="text-align: center; color: #94a3b8; padding: 3rem 1rem; font-style: italic;">
                                            Chưa có vị thuốc nào. Hãy chọn dược liệu ở cột bên phải.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Hidden inputs --}}
                        <div id="addHiddenInputsContainer"></div>

                        {{-- Form actions moved to fixed modal footer --}}
                    </form>
                </div>

                {{-- Cột 2: Sidebar thêm dược liệu --}}
                <div class="modal-sidebar-card" style="background: #fff; padding: 1.5rem; border-radius: 6px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: flex-start;">
                    <h3 style="margin: 0 0 1.25rem 0; font-size: 1.05rem; font-weight: 800; color: #1e3a5f; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;">➕ Thêm Vị Thuốc</h3>
                    
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label">Chọn Dược Liệu từ Kho *</label>
                        <div class="searchable-select-wrapper" id="addHerbSelectWrapper" style="position: relative;">
                            <div class="searchable-select-trigger" onclick="toggleAddHerbDropdown()" style="display: flex; justify-content: space-between; align-items: center; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.6rem 0.8rem; font-size: 0.9rem; background: #fff; cursor: pointer; min-height: 38px; box-sizing: border-box;">
                                <span id="addHerbSelectLabel" style="color: #64748b; font-size: 0.92rem;">-- Click hoặc gõ để tìm dược liệu --</span>
                                <span style="font-size: 0.8rem; color: #94a3b8;">▼</span>
                            </div>
                            <div id="addHerbDropdownContainer" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #cbd5e1; border-radius: 4px; z-index: 10000; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); margin-top: 4px; flex-direction: column; overflow: hidden;">
                                <input type="text" id="addHerbSearchInput" placeholder="Gõ từ khóa tìm kiếm..." oninput="filterAddHerbs()" style="border: none; border-bottom: 1px solid #e2e8f0; padding: 0.6rem 0.8rem; outline: none; font-size: 0.9rem; width: 100%; box-sizing: border-box;">
                                <div id="addHerbOptionsList" style="max-height: 200px; overflow-y: auto;">
                                    <div class="searchable-select-option empty-option" data-value="" onclick="selectAddHerb('', '-- Chọn dược liệu --', '', 'g')" style="padding: 0.6rem 0.8rem; font-size: 0.9rem; cursor: pointer; color: #64748b; font-style: italic;">-- Click để chọn dược liệu --</div>
                                    @foreach($herbs as $herb)
                                        <div class="searchable-select-option herb-opt" data-value="{{ $herb->id }}" data-name="{{ $herb->name }}" data-category="{{ $herb->category ?? 'Dược liệu thô' }}" data-unit="{{ $herb->unit }}" onclick="selectAddHerb('{{ $herb->id }}', '{{ $herb->name }}', '{{ $herb->category ?? 'Dược liệu thô' }}', '{{ $herb->unit }}')" style="padding: 0.6rem 0.8rem; font-size: 0.9rem; cursor: pointer; border-bottom: 1px solid #f8fafc; color: #1e293b;">
                                            <strong>{{ $herb->name }}</strong> <span style="color: #64748b; font-size: 0.8rem; margin-left: 0.5rem;">({{ $herb->category ?? 'Dược liệu thô' }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <input type="hidden" id="addHerbSelect" value="">
                            <input type="hidden" id="addHerbSelectedName" value="">
                            <input type="hidden" id="addHerbSelectedCategory" value="">
                            <input type="hidden" id="addHerbSelectedUnit" value="">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label">Liều lượng (g / thang) *</label>
                        <div style="display: flex; align-items: stretch;">
                            <input type="number" id="addHerbQty" class="form-input" style="flex: 1; border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: none;" step="0.1" min="0.1" placeholder="Ví dụ: 12">
                            <span style="font-weight: 700; color: #475569; font-size: 0.9rem; background: #f1f5f9; border: 1px solid #cbd5e1; border-left: none; padding: 0 0.85rem; border-top-right-radius: 4px; border-bottom-right-radius: 4px; display: flex; align-items: center;" id="addHerbUnitLabel">g</span>
                        </div>
                    </div>

                    <button type="button" onclick="addHerbToAddForm()" class="btn-add-herb" style="width: 100%;">
                        Thêm Vị Thuốc ⚡
                    </button>

                    <div style="margin-top: 2rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 1rem; color: #166534; font-size: 0.82rem; line-height: 1.5;">
                        <strong>💡 Hướng dẫn:</strong>
                        <ul style="margin: 0.4rem 0 0 0; padding-left: 1.2rem;">
                            <li>Chọn dược liệu trong kho và nhập liều lượng cho 01 thang thuốc.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Footer -->
        <div style="padding: 1rem 2rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 1rem; background: #f8fafc; z-index: 10;">
            <button type="button" onclick="closeAddSampleModal()" class="btn-cancel">Hủy</button>
            <button type="submit" form="addSamplePrescriptionForm" class="btn-save">💾 Lưu Bài Thuốc Mẫu</button>
        </div>
    </div>
</div>

<!-- Modal Chỉnh Sửa Bài Thuốc Mẫu -->
<div id="editSampleModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; opacity: 1;">
    <div style="background: #fff; border-radius: 6px; width: 1200px; max-width: 95%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border: 1px solid #e2e8f0; overflow: hidden; display: flex; flex-direction: column; max-height: 90vh; animation: modalSlideIn 0.3s ease-out;">
        <div style="padding: 1.25rem 2rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                <span>✏️</span> Chỉnh Sửa Bài Thuốc Mẫu
            </h2>
            <button type="button" onclick="closeEditSampleModal()" style="background: none; border: none; font-size: 1.5rem; color: #94a3b8; cursor: pointer; padding: 0.25rem;">✕</button>
        </div>
        
        <div style="padding: 1.5rem; overflow-y: auto; flex: 1; background: #f1f5f9;">
            @if ($errors->any() && old('form_type') === 'edit')
                <div style="background: #fef2f2; color: #ef4444; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; font-size: 0.9rem;">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="display: grid; grid-template-columns: 1.55fr 1fr; gap: 1.5rem;">
                {{-- Cột 1: Form chính --}}
                <div class="modal-form-card" style="background: #fff; padding: 1.5rem; border-radius: 6px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <form method="POST" id="editSamplePrescriptionForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_type" value="edit">
                        <input type="hidden" name="id" id="edit_sample_id" value="">
                        
                        <h3 style="margin: 0 0 1.25rem 0; font-size: 1.05rem; font-weight: 800; color: #1e3a5f; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem;">✨ Thông Tin Bài Thuốc</h3>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                            <div class="form-group">
                                <label class="form-label">Tên bài thuốc mẫu *</label>
                                <input type="text" name="name" id="edit_name" class="form-input" placeholder="VD: Bát Trân Thang..." required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Chỉ định / Triệu chứng phù hợp</label>
                                <input type="text" name="suggested_condition" id="edit_suggested_condition" class="form-input" placeholder="VD: Khí huyết lưỡng hư...">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label class="form-label">Cách dùng tổng quát mặc định</label>
                            <input type="text" name="usage_instruction" id="edit_usage_instruction" class="form-input" placeholder="VD: Sắc ngày 1 thang...">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                            <div class="form-group">
                                <label class="form-label">Dạng dùng</label>
                                <input type="text" name="preparation_type" id="edit_preparation_type" class="form-input" placeholder="VD: Thuốc sắc, thuốc bột, thuốc hoàn...">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Số thang thường dùng</label>
                                <input type="number" name="default_packages" id="edit_default_packages" class="form-input" min="1" placeholder="VD: 10, 15...">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label class="form-label">Lưu ý cho bài thuốc</label>
                            <textarea name="notes" id="edit_notes" class="form-input" style="height: 60px; resize: vertical;" placeholder="VD: Kiêng đồ cay nóng, không dùng cho phụ nữ có thai..."></textarea>
                        </div>

                        <h3 style="margin: 2rem 0 1.25rem 0; font-size: 1.05rem; font-weight: 800; color: #1e3a5f; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                            <span>🍃 Danh Sách Các Vị Thuốc Sắc (1 Thang)</span>
                            <span id="edit-herb-count-badge" class="badge">0 vị thuốc</span>
                        </h3>

                        <div class="table-container" style="border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; background: #fff; margin-bottom: 2rem;">
                            <table class="herb-table" style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                                <thead>
                                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; text-align: left;">
                                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: #475569;">VỊ THUỐC</th>
                                        <th style="padding: 0.75rem 1rem; font-weight: 700; color: #475569; width: 30%;">PHÂN NHÓM</th>
                                        <th style="padding: 0.75rem 0.5rem; font-weight: 700; color: #475569; text-align: right; width: 22%; white-space: nowrap;">LIỀU LƯỢNG (G)</th>
                                        <th style="padding: 0.75rem 0.5rem; text-align: center; width: 18%; white-space: nowrap;">HÀNH ĐỘNG</th>
                                    </tr>
                                </thead>
                                <tbody id="editHerbsTableBody">
                                    <tr class="empty-state-row">
                                        <td colspan="4" style="text-align: center; color: #94a3b8; padding: 3rem 1rem; font-style: italic;">
                                            Chưa có vị thuốc nào. Hãy chọn dược liệu ở cột bên phải.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Hidden inputs --}}
                        <div id="editHiddenInputsContainer"></div>

                        {{-- Form actions moved to fixed modal footer --}}
                    </form>
                </div>

                {{-- Cột 2: Sidebar thêm dược liệu --}}
                <div class="modal-sidebar-card" style="background: #fff; padding: 1.5rem; border-radius: 6px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h3 style="margin: 0 0 1.25rem 0; font-size: 1.05rem; font-weight: 800; color: #1e3a5f; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;">➕ Thêm Vị Thuốc</h3>
                    
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label">Chọn Dược Liệu từ Kho *</label>
                        <div class="searchable-select-wrapper" id="editHerbSelectWrapper" style="position: relative;">
                            <div class="searchable-select-trigger" onclick="toggleEditHerbDropdown()" style="display: flex; justify-content: space-between; align-items: center; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.6rem 0.8rem; font-size: 0.9rem; background: #fff; cursor: pointer; min-height: 38px; box-sizing: border-box;">
                                <span id="editHerbSelectLabel" style="color: #64748b; font-size: 0.92rem;">-- Click hoặc gõ để tìm dược liệu --</span>
                                <span style="font-size: 0.8rem; color: #94a3b8;">▼</span>
                            </div>
                            <div id="editHerbDropdownContainer" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #cbd5e1; border-radius: 4px; z-index: 10000; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); margin-top: 4px; flex-direction: column; overflow: hidden;">
                                <input type="text" id="editHerbSearchInput" placeholder="Gõ từ khóa tìm kiếm..." oninput="filterEditHerbs()" style="border: none; border-bottom: 1px solid #e2e8f0; padding: 0.6rem 0.8rem; outline: none; font-size: 0.9rem; width: 100%; box-sizing: border-box;">
                                <div id="editHerbOptionsList" style="max-height: 200px; overflow-y: auto;">
                                    <div class="searchable-select-option empty-option" data-value="" onclick="selectEditHerb('', '-- Chọn dược liệu --', '', 'g')" style="padding: 0.6rem 0.8rem; font-size: 0.9rem; cursor: pointer; color: #64748b; font-style: italic;">-- Click để chọn dược liệu --</div>
                                    @foreach($herbs as $herb)
                                        <div class="searchable-select-option herb-opt" data-value="{{ $herb->id }}" data-name="{{ $herb->name }}" data-category="{{ $herb->category ?? 'Dược liệu thô' }}" data-unit="{{ $herb->unit }}" onclick="selectEditHerb('{{ $herb->id }}', '{{ $herb->name }}', '{{ $herb->category ?? 'Dược liệu thô' }}', '{{ $herb->unit }}')" style="padding: 0.6rem 0.8rem; font-size: 0.9rem; cursor: pointer; border-bottom: 1px solid #f8fafc; color: #1e293b;">
                                            <strong>{{ $herb->name }}</strong> <span style="color: #64748b; font-size: 0.8rem; margin-left: 0.5rem;">({{ $herb->category ?? 'Dược liệu thô' }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <input type="hidden" id="editHerbSelect" value="">
                            <input type="hidden" id="editHerbSelectedName" value="">
                            <input type="hidden" id="editHerbSelectedCategory" value="">
                            <input type="hidden" id="editHerbSelectedUnit" value="">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label">Liều lượng (g / thang) *</label>
                        <div style="display: flex; align-items: stretch;">
                            <input type="number" id="editHerbQty" class="form-input" style="flex: 1; border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: none;" step="0.1" min="0.1" placeholder="Ví dụ: 12">
                            <span style="font-weight: 700; color: #475569; font-size: 0.9rem; background: #f1f5f9; border: 1px solid #cbd5e1; border-left: none; padding: 0 0.85rem; border-top-right-radius: 4px; border-bottom-right-radius: 4px; display: flex; align-items: center;" id="editHerbUnitLabel">g</span>
                        </div>
                    </div>

                    <button type="button" onclick="addHerbToEditForm()" class="btn-add-herb" style="width: 100%;">
                        Thêm Vị Thuốc ⚡
                    </button>

                    <div style="margin-top: 2rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 1rem; color: #166534; font-size: 0.82rem; line-height: 1.5;">
                        <strong>💡 Hướng dẫn:</strong>
                        <ul style="margin: 0.4rem 0 0 0; padding-left: 1.2rem;">
                            <li>Chọn dược liệu trong kho và nhập liều lượng cho 01 thang thuốc.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Footer -->
        <div style="padding: 1rem 2rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 1rem; background: #f8fafc; z-index: 10;">
            <button type="button" onclick="closeEditSampleModal()" class="btn-cancel">Hủy</button>
            <button type="submit" form="editSamplePrescriptionForm" class="btn-save">💾 Cập Nhật Bài Thuốc</button>
        </div>
    </div>
</div>

{{-- Modal Xem Chi Tiết Bài Thuốc Mẫu --}}
<div id="viewSampleModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; opacity: 1;">
    <div style="background: #fff; border-radius: 6px; width: 1100px; max-width: 95%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); border: 1px solid #e2e8f0; overflow: hidden; display: flex; flex-direction: column; max-height: 85vh; animation: modalSlideIn 0.3s ease-out;">
        <div style="padding: 0.85rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <h2 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 0.5rem;">
                <span>📋</span> <span id="view_modal_title">Chi Tiết Bài Thuốc Mẫu</span>
            </h2>
            <button type="button" onclick="closeViewSampleModal()" style="background: none; border: none; font-size: 1.3rem; color: #94a3b8; cursor: pointer; padding: 0.25rem;">✕</button>
        </div>

        <div style="padding: 1rem 1.5rem; overflow-y: auto; flex: 1; background: #f1f5f9;">
            {{-- Loading spinner --}}
            <div id="viewSampleLoading" style="text-align: center; padding: 2rem; display: none;">
                <div style="font-size: 1.5rem; margin-bottom: 0.35rem;">⏳</div>
                <div style="color: #64748b; font-size: 0.85rem;">Đang tải dữ liệu...</div>
            </div>

            <div id="viewSampleContent" style="display: none;">
                {{-- Layout ngang 2 cột --}}
                <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 1rem;">
                    {{-- Cột trái: Thông tin chung + Lưu ý --}}
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div style="background: #fff; padding: 1rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                            <h3 style="margin: 0 0 0.75rem 0; font-size: 0.85rem; font-weight: 800; color: #1e3a5f; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.4rem;">📝 Thông Tin Chung</h3>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem;">
                                <div style="grid-column: 1 / -1;">
                                    <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.15rem;">Tên bài thuốc</div>
                                    <div id="view_name" style="font-weight: 800; color: #0f766e; font-size: 0.95rem;"></div>
                                </div>
                                <div style="grid-column: 1 / -1;">
                                    <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.15rem;">Chỉ định / Triệu chứng</div>
                                    <div id="view_condition" style="font-weight: 600; color: #2563eb; font-size: 0.85rem;"></div>
                                </div>
                                <div style="grid-column: 1 / -1;">
                                    <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.15rem;">Cách dùng tổng quát</div>
                                    <div id="view_usage" style="color: #334155; font-size: 0.83rem; line-height: 1.45;"></div>
                                </div>
                                <div>
                                    <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.15rem;">Dạng dùng</div>
                                    <div id="view_preparation_type" style="color: #334155; font-size: 0.83rem;"></div>
                                </div>
                                <div>
                                    <div style="font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.15rem;">Số thang thường dùng</div>
                                    <div id="view_default_packages" style="color: #334155; font-size: 0.83rem;"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Lưu ý --}}
                        <div id="view_notes_section" style="background: #fffbeb; padding: 0.75rem 1rem; border-radius: 6px; border: 1px solid #fde68a; display: none;">
                            <div style="font-size: 0.72rem; font-weight: 800; color: #b45309; margin-bottom: 0.25rem;">⚠️ LƯU Ý</div>
                            <div id="view_notes" style="color: #92400e; font-size: 0.82rem; line-height: 1.45;"></div>
                        </div>
                    </div>

                    {{-- Cột phải: Danh sách vị thuốc --}}
                    <div style="background: #fff; padding: 1rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <h3 style="margin: 0 0 0.6rem 0; font-size: 0.85rem; font-weight: 800; color: #1e3a5f; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.4rem; display: flex; justify-content: space-between; align-items: center;">
                            <span>🍃 Thành Phần (1 Thang)</span>
                            <span id="view_herb_count" class="badge" style="background: #f0fdf4; color: #166534; padding: 0.15rem 0.5rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700;"></span>
                        </h3>
                        <div style="max-height: 350px; overflow-y: auto;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.82rem;">
                                <thead>
                                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; text-align: left;">
                                        <th style="padding: 0.45rem 0.6rem; font-weight: 700; color: #475569; font-size: 0.72rem;">#</th>
                                        <th style="padding: 0.45rem 0.6rem; font-weight: 700; color: #475569; font-size: 0.72rem;">VỊ THUỐC</th>
                                        <th style="padding: 0.45rem 0.6rem; font-weight: 700; color: #475569; font-size: 0.72rem;">PHÂN NHÓM</th>
                                        <th style="padding: 0.45rem 0.6rem; font-weight: 700; color: #475569; font-size: 0.72rem; text-align: right;">LIỀU LƯỢNG</th>
                                    </tr>
                                </thead>
                                <tbody id="viewHerbsTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div style="padding: 0.75rem 1.5rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 0.75rem; background: #f8fafc; z-index: 10;">
            <button type="button" onclick="closeViewSampleModal()" class="btn-cancel" style="padding: 0.5rem 1.25rem; font-size: 0.85rem;">Đóng</button>
            <button type="button" id="viewToEditBtn" class="btn-save" style="background: #3b82f6; padding: 0.5rem 1.25rem; font-size: 0.85rem;">✏️ Chỉnh sửa bài thuốc</button>
        </div>
    </div>
</div>

{{-- Custom Confirmation Modal --}}
<div id="confirmModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.25s ease;">
    <div style="background: #fff; border-radius: 0.5rem; width: 480px; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); transform: scale(0.9); transition: transform 0.25s ease; border: 1px solid #f1f5f9;" id="confirmModalCard">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="width: 64px; height: 64px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 2.2rem; margin-bottom: 1rem;">
                ⚠️
            </div>
            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.35rem; font-weight: 800; color: #1e293b;">Xác nhận xóa bài thuốc mẫu?</h3>
            <p style="margin: 0; color: #64748b; font-size: 0.95rem; line-height: 1.5;" id="confirmModalText">Bạn có chắc chắn muốn xóa bài thuốc mẫu này không? Hành động này không thể hoàn tác.</p>
        </div>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <button type="button" id="confirmCancelBtn" style="flex: 1; padding: 0.8rem; border-radius: 0.375rem; border: 1px solid #cbd5e1; background: #fff; color: #475569; font-weight: 700; cursor: pointer; transition: all 0.2s;">
                Hủy bỏ
            </button>
            <button type="button" id="confirmOkBtn" style="flex: 1; padding: 0.8rem; border-radius: 0.375rem; border: none; background: #ef4444; color: #fff; font-weight: 700; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.15);">
                Đồng ý xóa
            </button>
        </div>
    </div>
</div>
<style>
.main-content-card {
    background: #fff;
    border-radius: 1.5rem;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.02);
}
.btn-icon:hover {
    background: #f8fafc !important;
}
.page-item .page-link {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.5rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    color: #1e293b;
    text-decoration: none;
    font-weight: 700;
}
.page-item.active .page-link {
    background: #16a34a;
    color: #fff;
    border-color: #16a34a;
}
.pagination {
    display: flex;
    gap: 0.25rem;
    list-style: none;
    padding: 0;
    margin: 0;
}

/* Modal styles */
.searchable-select-trigger:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}
.searchable-select-option {
    padding: 0.6rem 0.8rem;
    font-size: 0.9rem;
    cursor: pointer;
    border-bottom: 1px solid #f8fafc;
    color: #1e293b;
    transition: all 0.15s;
    text-align: left;
}
.searchable-select-option:hover {
    background: #f0fdf4;
    color: #166534;
}
.searchable-select-option.selected {
    background: #eef9ee;
    color: #16a34a;
    font-weight: 700;
}
.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}
.form-label {
    font-size: 0.85rem;
    font-weight: 700;
    color: #475569;
    text-align: left;
}
.form-input {
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 0.6rem 0.8rem;
    font-size: 0.9rem;
    outline: none;
    transition: all 0.2s;
    background: #fff;
}
.form-input:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15);
}
.badge {
    background: #eef9ee;
    color: #16a34a;
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 800;
}
.btn-save {
    background: #16a34a;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 0.6rem 1.5rem;
    font-weight: 700;
    font-size: 0.9rem;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(22, 163, 74, 0.2);
}
.btn-save:hover {
    background: #15803d;
}
.btn-cancel {
    background: #fff;
    color: #475569;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 0.6rem 1.5rem;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
}
.btn-cancel:hover {
    background: #f8fafc;
}
.btn-add-herb {
    background: #1e293b;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 0.7rem 1.2rem;
    font-weight: 700;
    font-size: 0.9rem;
    cursor: pointer;
}
.btn-add-herb:hover {
    background: #0f172a;
}
.herb-table td {
    padding: 0.85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
}
.btn-remove-herb {
    background: #fef2f2;
    color: #ef4444;
    border: 1px solid #fee2e2;
    border-radius: 4px;
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
    font-weight: 700;
    cursor: pointer;
}
.btn-remove-herb:hover {
    background: #fee2e2;
}
@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
</style>
@endsection

@push('scripts')
<script>
// Hide default flash messages
document.addEventListener('DOMContentLoaded', function() {
    const flashMessages = document.getElementById('flash-messages');
    if (flashMessages) {
        flashMessages.style.display = 'none';
    }
});

// --- ADD FORM LOGIC ---
let addAddedHerbs = @json($oldCreateHerbs);

function openAddSampleModal() {
    selectAddHerb('', '-- Click hoặc gõ để tìm dược liệu --', '', 'g');
    document.getElementById('addSampleModal').style.display = 'flex';
    renderAddHerbsTable();
}

function closeAddSampleModal() {
    document.getElementById('addSampleModal').style.display = 'none';
}

function addHerbToAddForm() {
    const select = document.getElementById('addHerbSelect');
    const qtyInput = document.getElementById('addHerbQty');
    
    if (!select.value) {
        alert('Vui lòng chọn một dược liệu từ kho!');
        return;
    }

    const herbId = select.value;
    const qty = parseFloat(qtyInput.value);

    if (isNaN(qty) || qty <= 0) {
        alert('Vui lòng nhập liều lượng lớn hơn 0!');
        return;
    }

    // Check if exists
    const exists = addAddedHerbs.some(item => item.id === herbId);
    if (exists) {
        alert('Dược liệu này đã có trong danh sách! Hãy xóa đi và thêm lại với liều lượng mới.');
        return;
    }

    const name = document.getElementById('addHerbSelectedName').value;
    const category = document.getElementById('addHerbSelectedCategory').value || 'Dược liệu thô';
    const unit = document.getElementById('addHerbSelectedUnit').value || 'g';

    addAddedHerbs.push({
        id: herbId,
        name: name,
        category: category,
        quantity: qty,
        unit: unit
    });

    qtyInput.value = '';
    selectAddHerb('', '-- Click hoặc gõ để tìm dược liệu --', '', 'g');

    renderAddHerbsTable();
}

function removeHerbFromAddForm(herbId) {
    addAddedHerbs = addAddedHerbs.filter(item => item.id !== herbId.toString());
    renderAddHerbsTable();
}

function renderAddHerbsTable() {
    const tableBody = document.getElementById('addHerbsTableBody');
    const countBadge = document.getElementById('add-herb-count-badge');
    const hiddenContainer = document.getElementById('addHiddenInputsContainer');

    tableBody.innerHTML = '';
    hiddenContainer.innerHTML = '';

    if (addAddedHerbs.length === 0) {
        tableBody.innerHTML = `
            <tr class="empty-state-row">
                <td colspan="4" style="text-align: center; color: #94a3b8; padding: 3rem 1rem; font-style: italic;">
                    Chưa có vị thuốc nào. Hãy chọn dược liệu ở cột bên phải.
                </td>
            </tr>
        `;
        countBadge.textContent = '0 vị thuốc';
        return;
    }

    countBadge.textContent = `${addAddedHerbs.length} vị thuốc`;

    addAddedHerbs.forEach((item, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="padding: 0.85rem 1rem; font-weight: 700; color: #0f766e; text-align: left;">🍃 ${item.name}</td>
            <td style="padding: 0.85rem 1rem; color: #475569; text-align: left;">${item.category}</td>
            <td style="padding: 0.85rem 1rem; text-align: right; font-weight: 800; color: #1e3a5f;">${item.quantity} ${item.unit}</td>
            <td style="padding: 0.85rem 1rem; text-align: center;">
                <button type="button" class="btn-remove-herb" onclick="removeHerbFromAddForm('${item.id}')">Xóa</button>
            </td>
        `;
        tableBody.appendChild(tr);

        // Inputs hidden
        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = `items[${index}][medicinal_herb_id]`;
        inputId.value = item.id;
        hiddenContainer.appendChild(inputId);

        const inputQty = document.createElement('input');
        inputQty.type = 'hidden';
        inputQty.name = `items[${index}][quantity]`;
        inputQty.value = item.quantity;
        hiddenContainer.appendChild(inputQty);
    });
}

// --- EDIT FORM LOGIC ---
let editAddedHerbs = @json($oldEditHerbs);

function openEditSampleModal(sampleId) {
    // Show spinner or fetch info immediately
    fetch(`/admin/sample-prescriptions/${sampleId}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('edit_sample_id').value = data.id;
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_suggested_condition').value = data.suggested_condition || '';
            document.getElementById('edit_usage_instruction').value = data.usage_instruction || '';
            document.getElementById('edit_preparation_type').value = data.preparation_type || '';
            document.getElementById('edit_default_packages').value = data.default_packages || '';
            document.getElementById('edit_notes').value = data.notes || '';

            // Reset searchable select
            selectEditHerb('', '-- Click hoặc gõ để tìm dược liệu --', '', 'g');

            // Populate editAddedHerbs array
            editAddedHerbs = data.items.map(item => ({
                id: String(item.medicinal_herb_id),
                name: item.medicinal_herb ? item.medicinal_herb.name : 'N/A',
                category: item.medicinal_herb ? item.medicinal_herb.category : 'Dược liệu thô',
                quantity: parseFloat(item.quantity),
                unit: item.medicinal_herb ? item.medicinal_herb.unit : 'g'
            }));

            // Set Form action action route
            document.getElementById('editSamplePrescriptionForm').action = `/admin/sample-prescriptions/${sampleId}`;

            renderEditHerbsTable();
            document.getElementById('editSampleModal').style.display = 'flex';
        })
        .catch(err => {
            alert('Có lỗi xảy ra khi tải dữ liệu bài thuốc.');
            console.error(err);
        });
}

function closeEditSampleModal() {
    document.getElementById('editSampleModal').style.display = 'none';
}

function addHerbToEditForm() {
    const select = document.getElementById('editHerbSelect');
    const qtyInput = document.getElementById('editHerbQty');
    
    if (!select.value) {
        alert('Vui lòng chọn một dược liệu từ kho!');
        return;
    }

    const herbId = select.value;
    const qty = parseFloat(qtyInput.value);

    if (isNaN(qty) || qty <= 0) {
        alert('Vui lòng nhập liều lượng lớn hơn 0!');
        return;
    }

    // Check if exists
    const exists = editAddedHerbs.some(item => item.id === herbId);
    if (exists) {
        alert('Dược liệu này đã có trong danh sách! Hãy xóa đi và thêm lại với liều lượng mới.');
        return;
    }

    const name = document.getElementById('editHerbSelectedName').value;
    const category = document.getElementById('editHerbSelectedCategory').value || 'Dược liệu thô';
    const unit = document.getElementById('editHerbSelectedUnit').value || 'g';

    editAddedHerbs.push({
        id: herbId,
        name: name,
        category: category,
        quantity: qty,
        unit: unit
    });

    qtyInput.value = '';
    selectEditHerb('', '-- Click hoặc gõ để tìm dược liệu --', '', 'g');

    renderEditHerbsTable();
}

function removeHerbFromEditForm(herbId) {
    editAddedHerbs = editAddedHerbs.filter(item => item.id !== herbId.toString());
    renderEditHerbsTable();
}

function renderEditHerbsTable() {
    const tableBody = document.getElementById('editHerbsTableBody');
    const countBadge = document.getElementById('edit-herb-count-badge');
    const hiddenContainer = document.getElementById('editHiddenInputsContainer');

    tableBody.innerHTML = '';
    hiddenContainer.innerHTML = '';

    if (editAddedHerbs.length === 0) {
        tableBody.innerHTML = `
            <tr class="empty-state-row">
                <td colspan="4" style="text-align: center; color: #94a3b8; padding: 3rem 1rem; font-style: italic;">
                    Chưa có vị thuốc nào. Hãy chọn dược liệu ở cột bên phải.
                </td>
            </tr>
        `;
        countBadge.textContent = '0 vị thuốc';
        return;
    }

    countBadge.textContent = `${editAddedHerbs.length} vị thuốc`;

    editAddedHerbs.forEach((item, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="padding: 0.85rem 1rem; font-weight: 700; color: #0f766e; text-align: left;">🍃 ${item.name}</td>
            <td style="padding: 0.85rem 1rem; color: #475569; text-align: left;">${item.category}</td>
            <td style="padding: 0.85rem 1rem; text-align: right; font-weight: 800; color: #1e3a5f;">${item.quantity} ${item.unit}</td>
            <td style="padding: 0.85rem 1rem; text-align: center;">
                <button type="button" class="btn-remove-herb" onclick="removeHerbFromEditForm('${item.id}')">Xóa</button>
            </td>
        `;
        tableBody.appendChild(tr);

        // Inputs hidden
        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = `items[${index}][medicinal_herb_id]`;
        inputId.value = item.id;
        hiddenContainer.appendChild(inputId);

        const inputQty = document.createElement('input');
        inputQty.type = 'hidden';
        inputQty.name = `items[${index}][quantity]`;
        inputQty.value = item.quantity;
        hiddenContainer.appendChild(inputQty);
    });
}

// Validate submit
document.getElementById('addSamplePrescriptionForm').addEventListener('submit', function(e) {
    if (addAddedHerbs.length === 0) {
        e.preventDefault();
        alert('Bài thuốc mẫu phải có ít nhất 1 vị thuốc! Hãy thêm các vị thuốc từ cột bên phải.');
    }
});

document.getElementById('editSamplePrescriptionForm').addEventListener('submit', function(e) {
    if (editAddedHerbs.length === 0) {
        e.preventDefault();
        alert('Bài thuốc mẫu phải có ít nhất 1 vị thuốc! Hãy thêm các vị thuốc từ cột bên phải.');
    }
});

// Reopen modal on validation errors
@if ($errors->any())
    @if (old('form_type') === 'edit')
        document.getElementById('edit_sample_id').value = "{{ old('id') }}";
        document.getElementById('edit_name').value = "{{ old('name') }}";
        document.getElementById('edit_suggested_condition').value = "{{ old('suggested_condition') }}";
        document.getElementById('edit_usage_instruction').value = "{{ old('usage_instruction') }}";
        document.getElementById('editSamplePrescriptionForm').action = `/admin/sample-prescriptions/{{ old('id') }}`;
        renderEditHerbsTable();
        document.getElementById('editSampleModal').style.display = 'flex';
    @elseif (old('form_type') === 'create')
        renderAddHerbsTable();
        document.getElementById('addSampleModal').style.display = 'flex';
    @endif
@endif

// --- BULK DELETE / SELECTION LOGIC ---
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const sampleCheckboxes = document.querySelectorAll('.sample-checkbox');
    const bulkDeleteBtn = document.getElementById('btn-bulk-delete');
    const selectedCountSpan = document.getElementById('selected-count');
    const bulkDeleteForm = document.getElementById('bulk-delete-form');

    function toggleBulkDeleteButton() {
        const checkedCount = document.querySelectorAll('.sample-checkbox:checked').length;
        if (selectedCountSpan) {
            selectedCountSpan.textContent = checkedCount;
        }
        if (bulkDeleteBtn) {
            if (checkedCount > 0) {
                bulkDeleteBtn.style.display = 'inline-flex';
            } else {
                bulkDeleteBtn.style.display = 'none';
            }
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            sampleCheckboxes.forEach(cb => {
                cb.checked = selectAllCheckbox.checked;
            });
            toggleBulkDeleteButton();
        });
    }

    sampleCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = document.querySelectorAll('.sample-checkbox:checked').length === sampleCheckboxes.length;
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
            }
            toggleBulkDeleteButton();
        });
    });

    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const checkedCount = document.querySelectorAll('.sample-checkbox:checked').length;
            
            const confirmModal = document.getElementById('confirmModal');
            if (confirmModal) {
                const confirmModalCard = document.getElementById('confirmModalCard');
                const confirmModalText = document.getElementById('confirmModalText');
                const confirmCancelBtn = document.getElementById('confirmCancelBtn');
                const confirmOkBtn = document.getElementById('confirmOkBtn');

                confirmModalText.innerHTML = `Bạn có chắc chắn muốn xóa <strong>${checkedCount} bài thuốc mẫu</strong> đã chọn không? Hành động này không thể hoàn tác.`;
                confirmModal.style.display = 'flex';
                setTimeout(() => {
                    confirmModal.style.opacity = '1';
                    if (confirmModalCard) confirmModalCard.style.transform = 'scale(1)';
                }, 10);

                const closeHandler = () => {
                    confirmModal.style.opacity = '0';
                    if (confirmModalCard) confirmModalCard.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        confirmModal.style.display = 'none';
                    }, 250);
                    confirmOkBtn.removeEventListener('click', confirmHandler);
                    confirmCancelBtn.removeEventListener('click', closeHandler);
                };

                const confirmHandler = () => {
                    bulkDeleteForm.submit();
                    closeHandler();
                };

                confirmCancelBtn.addEventListener('click', closeHandler);
                confirmOkBtn.addEventListener('click', confirmHandler);
            } else {
                if (confirm(`Bạn có chắc chắn muốn xóa ${checkedCount} bài thuốc mẫu đã chọn không?`)) {
                    bulkDeleteForm.submit();
                }
            }
        });
    }
});
// --- SEARCHABLE SELECT LOGIC ---
function toggleAddHerbDropdown() {
    const container = document.getElementById('addHerbDropdownContainer');
    const isHidden = container.style.display === 'none';
    closeAllHerbDropdowns();
    if (isHidden) {
        container.style.display = 'flex';
        const searchInput = document.getElementById('addHerbSearchInput');
        searchInput.value = '';
        filterAddHerbs();
        searchInput.focus();
    }
}

function filterAddHerbs() {
    const input = document.getElementById('addHerbSearchInput');
    const filter = input.value.toLowerCase().trim();
    const options = document.querySelectorAll('#addHerbOptionsList .herb-opt');
    options.forEach(opt => {
        const name = opt.getAttribute('data-name').toLowerCase();
        const category = opt.getAttribute('data-category').toLowerCase();
        if (name.includes(filter) || category.includes(filter)) {
            opt.style.display = 'block';
        } else {
            opt.style.display = 'none';
        }
    });
}

function selectAddHerb(id, name, category, unit) {
    document.getElementById('addHerbSelect').value = id;
    const label = document.getElementById('addHerbSelectLabel');
    if (id) {
        label.textContent = `${name} (${category})`;
        label.style.color = '#1e293b';
        label.style.fontWeight = '700';
        document.getElementById('addHerbSelectedName').value = name;
        document.getElementById('addHerbSelectedCategory').value = category;
        document.getElementById('addHerbSelectedUnit').value = unit;
        document.getElementById('addHerbUnitLabel').textContent = unit || 'g';
    } else {
        label.textContent = '-- Click hoặc gõ để tìm dược liệu --';
        label.style.color = '#64748b';
        label.style.fontWeight = 'normal';
        document.getElementById('addHerbSelectedName').value = '';
        document.getElementById('addHerbSelectedCategory').value = '';
        document.getElementById('addHerbSelectedUnit').value = '';
        document.getElementById('addHerbUnitLabel').textContent = 'g';
    }
    
    const options = document.querySelectorAll('#addHerbOptionsList .herb-opt');
    options.forEach(opt => {
        if (opt.getAttribute('data-value') === id.toString()) {
            opt.classList.add('selected');
        } else {
            opt.classList.remove('selected');
        }
    });
    document.getElementById('addHerbDropdownContainer').style.display = 'none';
}

function toggleEditHerbDropdown() {
    const container = document.getElementById('editHerbDropdownContainer');
    const isHidden = container.style.display === 'none';
    closeAllHerbDropdowns();
    if (isHidden) {
        container.style.display = 'flex';
        const searchInput = document.getElementById('editHerbSearchInput');
        searchInput.value = '';
        filterEditHerbs();
        searchInput.focus();
    }
}

function filterEditHerbs() {
    const input = document.getElementById('editHerbSearchInput');
    const filter = input.value.toLowerCase().trim();
    const options = document.querySelectorAll('#editHerbOptionsList .herb-opt');
    options.forEach(opt => {
        const name = opt.getAttribute('data-name').toLowerCase();
        const category = opt.getAttribute('data-category').toLowerCase();
        if (name.includes(filter) || category.includes(filter)) {
            opt.style.display = 'block';
        } else {
            opt.style.display = 'none';
        }
    });
}

function selectEditHerb(id, name, category, unit) {
    document.getElementById('editHerbSelect').value = id;
    const label = document.getElementById('editHerbSelectLabel');
    if (id) {
        label.textContent = `${name} (${category})`;
        label.style.color = '#1e293b';
        label.style.fontWeight = '700';
        document.getElementById('editHerbSelectedName').value = name;
        document.getElementById('editHerbSelectedCategory').value = category;
        document.getElementById('editHerbSelectedUnit').value = unit;
        document.getElementById('editHerbUnitLabel').textContent = unit || 'g';
    } else {
        label.textContent = '-- Click hoặc gõ để tìm dược liệu --';
        label.style.color = '#64748b';
        label.style.fontWeight = 'normal';
        document.getElementById('editHerbSelectedName').value = '';
        document.getElementById('editHerbSelectedCategory').value = '';
        document.getElementById('editHerbSelectedUnit').value = '';
        document.getElementById('editHerbUnitLabel').textContent = 'g';
    }
    
    const options = document.querySelectorAll('#editHerbOptionsList .herb-opt');
    options.forEach(opt => {
        if (opt.getAttribute('data-value') === id.toString()) {
            opt.classList.add('selected');
        } else {
            opt.classList.remove('selected');
        }
    });
    document.getElementById('editHerbDropdownContainer').style.display = 'none';
}

function closeAllHerbDropdowns() {
    const addCont = document.getElementById('addHerbDropdownContainer');
    if (addCont) addCont.style.display = 'none';
    const editCont = document.getElementById('editHerbDropdownContainer');
    if (editCont) editCont.style.display = 'none';
}

document.addEventListener('click', function(e) {
    const addWrapper = document.getElementById('addHerbSelectWrapper');
    if (addWrapper && !addWrapper.contains(e.target)) {
        const addContainer = document.getElementById('addHerbDropdownContainer');
        if (addContainer) addContainer.style.display = 'none';
    }
    const editWrapper = document.getElementById('editHerbSelectWrapper');
    if (editWrapper && !editWrapper.contains(e.target)) {
        const editContainer = document.getElementById('editHerbDropdownContainer');
        if (editContainer) editContainer.style.display = 'none';
    }
});
// --- VIEW DETAIL MODAL ---
function openViewSampleModal(sampleId) {
    const modal = document.getElementById('viewSampleModal');
    const loading = document.getElementById('viewSampleLoading');
    const content = document.getElementById('viewSampleContent');

    modal.style.display = 'flex';
    loading.style.display = 'block';
    content.style.display = 'none';

    fetch(`/admin/sample-prescriptions/${sampleId}`)
        .then(res => res.json())
        .then(data => {
            // Fill basic info
            document.getElementById('view_modal_title').textContent = data.name;
            document.getElementById('view_name').textContent = data.name;
            document.getElementById('view_condition').textContent = data.suggested_condition || 'Nhiều thể bệnh';
            document.getElementById('view_usage').textContent = data.usage_instruction || 'Sắc uống hàng ngày.';
            document.getElementById('view_preparation_type').textContent = data.preparation_type || '—';
            document.getElementById('view_default_packages').textContent = data.default_packages ? `${data.default_packages} thang` : '—';

            // Notes
            const notesSection = document.getElementById('view_notes_section');
            if (data.notes) {
                document.getElementById('view_notes').textContent = data.notes;
                notesSection.style.display = 'block';
            } else {
                notesSection.style.display = 'none';
            }

            // Herbs table
            const tbody = document.getElementById('viewHerbsTableBody');
            tbody.innerHTML = '';
            if (data.items && data.items.length > 0) {
                document.getElementById('view_herb_count').textContent = `${data.items.length} vị thuốc`;
                data.items.forEach((item, index) => {
                    const herbName = item.medicinal_herb ? item.medicinal_herb.name : 'N/A';
                    const category = item.medicinal_herb ? item.medicinal_herb.category : 'Dược liệu thô';
                    const unit = item.medicinal_herb ? item.medicinal_herb.unit : 'g';
                    const qty = parseFloat(item.quantity);
                    const tr = document.createElement('tr');
                    tr.style.borderBottom = '1px solid #f1f5f9';
                    tr.innerHTML = `
                        <td style="padding: 0.4rem 0.6rem; color: #94a3b8; font-size: 0.78rem;">${index + 1}</td>
                        <td style="padding: 0.4rem 0.6rem; font-weight: 700; color: #0f766e; font-size: 0.82rem;">🍃 ${herbName}</td>
                        <td style="padding: 0.4rem 0.6rem; color: #475569; font-size: 0.82rem;">${category}</td>
                        <td style="padding: 0.4rem 0.6rem; text-align: right; font-weight: 800; color: #1e3a5f; font-size: 0.82rem;">${qty} ${unit}</td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                document.getElementById('view_herb_count').textContent = '0 vị thuốc';
                tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #94a3b8; padding: 1.5rem; font-style: italic; font-size: 0.82rem;">Chưa cấu hình dược liệu.</td></tr>';
            }

            // Edit button
            document.getElementById('viewToEditBtn').onclick = function() {
                closeViewSampleModal();
                openEditSampleModal(sampleId);
            };

            loading.style.display = 'none';
            content.style.display = 'block';
        })
        .catch(err => {
            console.error(err);
            loading.innerHTML = '<div style="color: #ef4444;">❌ Có lỗi xảy ra khi tải dữ liệu.</div>';
        });
}

function closeViewSampleModal() {
    document.getElementById('viewSampleModal').style.display = 'none';
}

// Close view modal on overlay click
document.getElementById('viewSampleModal').addEventListener('click', function(e) {
    if (e.target === this) closeViewSampleModal();
});

</script>
@endpush
