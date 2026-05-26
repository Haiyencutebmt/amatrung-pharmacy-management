@extends('layouts.admin')

@section('title', 'Sửa Bài Thuốc Mẫu — AmaTrung')
@section('page-title', 'Chỉnh Sửa Bài Thuốc Mẫu')

@section('content')
<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 2rem; margin-top: -1rem;">
    
    {{-- Cột 1: Thông tin chung & Danh sách các vị thuốc đã chọn --}}
    <div class="main-card">
        <form action="{{ route('admin.sample-prescriptions.update', $samplePrescription) }}" method="POST" id="samplePrescriptionForm">
            @csrf
            @method('PUT')
            
            <h3 class="section-title">✨ Thông Tin Bài Thuốc</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="name" class="form-label">Tên bài thuốc mẫu *</label>
                    <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $samplePrescription->name) }}" placeholder="VD: Bát Trân Thang, Độc Hoạt Tang Ký Sinh..." required>
                </div>
                <div class="form-group">
                    <label for="suggested_condition" class="form-label">Chỉ định / Triệu chứng phù hợp</label>
                    <input type="text" name="suggested_condition" id="suggested_condition" class="form-input" value="{{ old('suggested_condition', $samplePrescription->suggested_condition) }}" placeholder="VD: Khí huyết lưỡng hư, đau nhức xương khớp...">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="usage_instruction" class="form-label">Cách dùng tổng quát mặc định</label>
                <input type="text" name="usage_instruction" id="usage_instruction" class="form-input" value="{{ old('usage_instruction', $samplePrescription->usage_instruction) }}" placeholder="VD: Sắc ngày 1 thang, chia 2 lần sáng/chiều sau ăn ấm...">
            </div>

            <h3 class="section-title" style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: center;">
                <span>🍃 Danh Sách Các Vị Thuốc Sắc (1 Thang)</span>
                <span id="herb-count-badge" class="badge">0 vị thuốc</span>
            </h3>

            <div class="table-container" style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background: #fff; margin-bottom: 2rem;">
                <table class="herb-table" style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; text-align: left;">
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: #475569;">VỊ THUỐC</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: #475569; width: 30%;">PHÂN NHÓM</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: #475569; text-align: right; width: 25%;">LIỀU LƯỢNG (G)</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; width: 15%;">HÀNH ĐỘNG</th>
                        </tr>
                    </thead>
                    <tbody id="addedHerbsTableBody">
                        <tr id="empty-state-row">
                            <td colspan="4" style="text-align: center; color: #94a3b8; padding: 3rem 1rem; font-style: italic;">
                                Chưa có vị thuốc nào được thêm vào bài thuốc này. Hãy chọn dược liệu ở cột bên phải.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Inputs hidden chứa mảng dược liệu gửi lên Server --}}
            <div id="hiddenInputsContainer"></div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid #f1f5f9; padding-top: 1.5rem;">
                <a href="{{ route('admin.sample-prescriptions.index') }}" class="btn-cancel">Hủy</a>
                <button type="submit" class="btn-save">💾 Cập Nhật Bài Thuốc</button>
            </div>
        </form>
    </div>

    {{-- Cột 2: Form chọn dược liệu để thêm nhanh --}}
    <div class="sidebar-card">
        <h3 class="section-title">➕ Thêm Vị Thuốc Vào Bài</h3>
        
        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label class="form-label">Chọn Dược Liệu từ Kho *</label>
            <select id="herbSelect" class="form-input" style="width: 100%; font-size: 0.92rem;" onchange="updateSelectedHerbUnit()">
                <option value="">-- Click để chọn dược liệu --</option>
                @foreach($herbs as $herb)
                    <option value="{{ $herb->id }}" data-name="{{ $herb->name }}" data-category="{{ $herb->category ?? 'Dược liệu thô' }}" data-unit="{{ $herb->unit }}">
                        {{ $herb->name }} ({{ $herb->category ?? 'Không phân nhóm' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label class="form-label">Liều lượng (g / thang) *</label>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <input type="number" id="herbQty" class="form-input" style="flex: 1;" step="0.1" min="0.1" placeholder="Nhập số gam, ví dụ: 12">
                <span style="font-weight: 700; color: #475569; font-size: 0.95rem; background: #e2e8f0; padding: 0.5rem 0.85rem; border-radius: 8px;" id="herbUnitLabel">g</span>
            </div>
        </div>

        <button type="button" onclick="addHerbToList()" class="btn-add-herb" style="width: 100%;">
            Thêm Vị Thuốc ⚡
        </button>

        <div style="margin-top: 2rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 1rem; color: #166534; font-size: 0.82rem; line-height: 1.5;">
            <strong>💡 Mẹo xây dựng bài thuốc:</strong>
            <ul style="margin: 0.4rem 0 0 0; padding-left: 1.2rem;">
                <li>Chọn đúng tên dược liệu có sẵn trong kho để hệ thống tự động nhận diện và tính toán trừ tồn kho khi kê đơn.</li>
                <li>Liều lượng được tính theo chuẩn Đông Y Việt Nam (đơn vị gam) trên <strong>01 thang thuốc</strong>.</li>
            </ul>
        </div>
    </div>
</div>

<style>
.main-card, .sidebar-card {
    background: #fff;
    border-radius: 1.25rem;
    padding: 1.75rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.015);
    border: 1px solid #eef2f6;
}
.section-title {
    margin: 0 0 1.25rem 0;
    font-size: 1.1rem;
    font-weight: 800;
    color: #1e3a5f;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 0.5rem;
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
}
.form-input {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 0.6rem 0.8rem;
    font-size: 0.9rem;
    outline: none;
    transition: all 0.2s;
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
    border-radius: 8px;
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
    border-radius: 8px;
    padding: 0.6rem 1.5rem;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
}
.btn-cancel:hover {
    background: #f8fafc;
}
.btn-add-herb {
    background: #1e293b;
    color: white;
    border: none;
    border-radius: 8px;
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
    border-radius: 6px;
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
    font-weight: 700;
    cursor: pointer;
}
.btn-remove-herb:hover {
    background: #fee2e2;
}
</style>

@push('scripts')
<script>
    // Nạp danh sách dược liệu cũ đã lưu từ PHP sang Javascript
    let addedHerbs = @json($samplePrescription->items->map(function($item) {
        return [
            'id' => (string)$item->medicinal_herb_id,
            'name' => $item->medicinalHerb->name ?? 'N/A',
            'category' => $item->medicinalHerb->category ?? 'Dược liệu thô',
            'quantity' => floatval($item->quantity),
            'unit' => $item->medicinalHerb->unit ?? 'g'
        ];
    }));

    // Khởi tạo bảng ngay sau khi load trang
    document.addEventListener('DOMContentLoaded', function() {
        renderHerbsTable();
    });

    function updateSelectedHerbUnit() {
        const select = document.getElementById('herbSelect');
        const unitLabel = document.getElementById('herbUnitLabel');
        if (select.selectedIndex > 0) {
            const opt = select.options[select.selectedIndex];
            unitLabel.textContent = opt.getAttribute('data-unit') || 'g';
        } else {
            unitLabel.textContent = 'g';
        }
    }

    function addHerbToList() {
        const select = document.getElementById('herbSelect');
        const qtyInput = document.getElementById('herbQty');
        
        if (select.selectedIndex === 0) {
            alert('Vui lòng chọn một dược liệu từ kho!');
            return;
        }

        const herbId = select.value;
        const qty = parseFloat(qtyInput.value);

        if (isNaN(qty) || qty <= 0) {
            alert('Vui lòng nhập liều lượng lớn hơn 0!');
            return;
        }

        // Kiểm tra xem dược liệu đã được thêm chưa
        const exists = addedHerbs.some(item => item.id === herbId);
        if (exists) {
            alert('Dược liệu này đã có trong danh sách! Bạn có thể xóa đi và thêm lại với liều lượng mới.');
            return;
        }

        const opt = select.options[select.selectedIndex];
        const herbName = opt.getAttribute('data-name');
        const herbCategory = opt.getAttribute('data-category');
        const herbUnit = opt.getAttribute('data-unit');

        addedHerbs.push({
            id: herbId,
            name: herbName,
            category: herbCategory,
            quantity: qty,
            unit: herbUnit
        });

        qtyInput.value = '';
        select.selectedIndex = 0;
        document.getElementById('herbUnitLabel').textContent = 'g';

        renderHerbsTable();
    }

    function removeHerbFromList(herbId) {
        addedHerbs = addedHerbs.filter(item => item.id !== herbId.toString());
        renderHerbsTable();
    }

    function renderHerbsTable() {
        const tableBody = document.getElementById('addedHerbsTableBody');
        const countBadge = document.getElementById('herb-count-badge');
        const hiddenContainer = document.getElementById('hiddenInputsContainer');

        // Reset HTML
        tableBody.innerHTML = '';
        hiddenContainer.innerHTML = '';

        if (addedHerbs.length === 0) {
            tableBody.innerHTML = `
                <tr id="empty-state-row">
                    <td colspan="4" style="text-align: center; color: #94a3b8; padding: 3rem 1rem; font-style: italic;">
                        Chưa có vị thuốc nào được thêm vào bài thuốc này. Hãy chọn dược liệu ở cột bên phải.
                    </td>
                </tr>
            `;
            countBadge.textContent = '0 vị thuốc';
            return;
        }

        countBadge.textContent = `${addedHerbs.length} vị thuốc`;

        addedHerbs.forEach((item, index) => {
            // Render hàng trong bảng
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="padding: 0.85rem 1rem; font-weight: 700; color: #0f766e;">🍃 ${item.name}</td>
                <td style="padding: 0.85rem 1rem; color: #475569;">${item.category}</td>
                <td style="padding: 0.85rem 1rem; text-align: right; font-weight: 800; color: #1e3a5f;">${item.quantity} ${item.unit}</td>
                <td style="padding: 0.85rem 1rem; text-align: center;">
                    <button type="button" class="btn-remove-herb" onclick="removeHerbFromList('${item.id}')">Xóa</button>
                </td>
            `;
            tableBody.appendChild(tr);

            // Render hidden input gửi lên controller
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

    // Kiểm tra trước khi submit form
    document.getElementById('samplePrescriptionForm').addEventListener('submit', function(e) {
        if (addedHerbs.length === 0) {
            e.preventDefault();
            alert('Bài thuốc mẫu phải có ít nhất 1 vị thuốc! Hãy thêm các vị thuốc từ cột bên phải.');
        }
    });
</script>
@endpush
@endsection
