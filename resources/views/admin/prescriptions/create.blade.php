@extends('layouts.admin')

@section('title', 'Kê Đơn Điều Trị')

@section('content')
<div class="container-fluid" style="padding-bottom: 3rem;">
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('admin.medical-records.show', $medicalRecord) }}" class="btn btn-secondary btn-lg">
            <i class="fas fa-arrow-left"></i> Quay lại Bệnh án
        </a>
    </div>
    
    <h1 class="h3 mb-4 text-gray-800 font-weight-bold">Kê Đơn Điều Trị - Mã BA: {{ $medicalRecord->record_code }}</h1>

    @if($errors->any())
        <div class="alert alert-danger" style="font-size: 18px;">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-info text-white">
            <h6 class="m-0 font-weight-bold" style="font-size: 20px;"><i class="fas fa-info-circle"></i> Tóm tắt Bệnh Án</h6>
        </div>
        <div class="card-body" style="font-size: 18px;">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Bệnh nhân:</strong> {{ $medicalRecord->patient->full_name }} ({{ $medicalRecord->patient->age }} tuổi)</p>
                    <p><strong>Chẩn đoán:</strong> {{ $medicalRecord->diagnosis }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Định hướng điều trị:</strong> 
                        @if($medicalRecord->treatment_direction === 'oral_only')
                            <span class="badge badge-primary" style="font-size: 16px;">Chỉ Thuốc Uống</span>
                        @elseif($medicalRecord->treatment_direction === 'external_only')
                            <span class="badge badge-danger" style="font-size: 16px;">Chỉ Dùng Ngoài</span>
                        @elseif($medicalRecord->treatment_direction === 'combined')
                            <span class="badge badge-success" style="font-size: 16px;">Kết hợp Uống & Dùng Ngoài</span>
                        @endif
                    </p>
                    <p><strong>Bệnh nền/Dị ứng:</strong> <span class="text-danger">{{ $medicalRecord->allergies }} {{ $medicalRecord->underlying_diseases }}</span></p>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.prescriptions.store') }}" method="POST" id="prescriptionForm">
        @csrf
        <input type="hidden" name="medical_record_id" value="{{ $medicalRecord->id }}">

        {{-- THUỐC UỐNG --}}
        @if(in_array($medicalRecord->treatment_direction, ['oral_only', 'combined']))
        <div class="card shadow mb-4 border-left-primary">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 font-weight-bold text-primary" style="font-size: 20px;"><i class="fas fa-pills"></i> 1. Kê Thuốc Thang (Uống)</h6>
            </div>
            <div class="card-body" style="font-size: 18px;">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="font-weight-bold">Số thang thuốc <span class="text-danger">*</span></label>
                        <input type="number" name="num_of_doses" id="num_of_doses" class="form-control form-control-lg" value="{{ old('num_of_doses', 1) }}" min="1" onchange="calculateTotalHerbs()">
                    </div>
                    <div class="col-md-8">
                        <label class="font-weight-bold">Hướng dẫn sắc thuốc / Cách uống</label>
                        <input type="text" name="usage_instruction" class="form-control form-control-lg" value="{{ old('usage_instruction', 'Sắc 3 bát lấy 1 bát, uống sau ăn 30 phút.') }}">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="herbTable">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Dược liệu (Kho)</th>
                                <th width="150">ĐVT</th>
                                <th width="200">SL 1 Thang</th>
                                <th width="200">Tổng SL (FEFO)</th>
                                <th width="80"></th>
                            </tr>
                        </thead>
                        <tbody id="herbBody">
                            <!-- Items will be appended here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">
                                    <button type="button" class="btn btn-outline-primary font-weight-bold" onclick="addHerbRow()"><i class="fas fa-plus"></i> Thêm vị thuốc</button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- THUỐC DÙNG NGOÀI --}}
        @if(in_array($medicalRecord->treatment_direction, ['external_only', 'combined']) || $medicalRecord->case_type === 'musculoskeletal' || $medicalRecord->case_type === 'combined')
        <div class="card shadow mb-4 border-left-danger">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 font-weight-bold text-danger" style="font-size: 20px;"><i class="fas fa-band-aid"></i> 2. Thuốc / Chế Phẩm Dùng Ngoài</h6>
            </div>
            <div class="card-body" style="font-size: 18px;">
                <div class="table-responsive">
                    <table class="table table-bordered" id="extTable">
                        <thead class="bg-danger text-white">
                            <tr>
                                <th>Chế phẩm (Kho)</th>
                                <th width="150">ĐVT</th>
                                <th width="200">Số lượng (FEFO)</th>
                                <th>Cách dùng / Ghi chú</th>
                                <th width="80"></th>
                            </tr>
                        </thead>
                        <tbody id="extBody">
                            <!-- Items will be appended here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">
                                    <button type="button" class="btn btn-outline-danger font-weight-bold" onclick="addExtRow()"><i class="fas fa-plus"></i> Thêm thuốc dùng ngoài</button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- DỊCH VỤ TRỊ LIỆU --}}
        <div class="card shadow mb-4 border-left-info">
            <div class="card-header py-3 bg-light">
                <h6 class="m-0 font-weight-bold text-info" style="font-size: 20px;"><i class="fas fa-hands-helping"></i> 3. Dịch Vụ Trị Liệu</h6>
            </div>
            <div class="card-body" style="font-size: 18px;">
                <div class="table-responsive">
                    <table class="table table-bordered" id="srvTable">
                        <thead class="bg-info text-white">
                            <tr>
                                <th>Tên dịch vụ trị liệu (Tự nhập)</th>
                                <th width="200">Số buổi/lần</th>
                                <th>Chỉ định / Ghi chú</th>
                                <th width="80"></th>
                            </tr>
                        </thead>
                        <tbody id="srvBody">
                            <!-- Items will be appended here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4">
                                    <button type="button" class="btn btn-outline-info font-weight-bold" onclick="addSrvRow()"><i class="fas fa-plus"></i> Thêm trị liệu</button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body" style="font-size: 18px;">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold text-dark">Lời dặn thêm của bác sĩ</label>
                        <textarea name="note" class="form-control form-control-lg" rows="2" placeholder="Ăn kiêng, sinh hoạt..."></textarea>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold text-dark">Hẹn tái khám (Tùy chọn)</label>
                        <input type="date" name="follow_up_date" class="form-control form-control-lg">
                    </div>
                </div>
            </div>
        </div>

        <div class="text-right">
            <button type="submit" class="btn btn-success btn-lg px-5 font-weight-bold" style="font-size: 22px;">
                <i class="fas fa-save"></i> LƯU ĐƠN & TẠO PHIẾU CẤP THUỐC
            </button>
        </div>
    </form>
</div>

<script>
    let itemIndex = 0;
    const herbs = @json($herbs->values());
    const extProducts = @json($externalProducts->values());

    function addHerbRow() {
        let options = '<option value="">-- Chọn dược liệu --</option>';
        herbs.forEach(h => {
            options += `<option value="${h.id}" data-unit="${h.unit}">${h.name} (Tồn: ${h.stock_quantity})</option>`;
        });

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <input type="hidden" name="items[${itemIndex}][item_type]" value="herb">
            <td>
                <select name="items[${itemIndex}][inventory_item_id]" class="form-control herb-select" required onchange="updateHerbRow(this, ${itemIndex})">
                    ${options}
                </select>
            </td>
            <td><input type="text" name="items[${itemIndex}][unit]" class="form-control herb-unit" readonly></td>
            <td>
                <div class="input-group">
                    <input type="number" step="0.1" name="items[${itemIndex}][quantity_per_dose]" class="form-control herb-qty-per-dose" required oninput="calculateTotalHerbs()" min="0.1">
                </div>
            </td>
            <td>
                <input type="number" step="0.1" class="form-control herb-total bg-light" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove(); calculateTotalHerbs();"><i class="fas fa-trash"></i></button>
            </td>
        `;
        document.getElementById('herbBody').appendChild(tr);
        itemIndex++;
    }

    function updateHerbRow(selectEl, idx) {
        const option = selectEl.options[selectEl.selectedIndex];
        const unit = option.dataset.unit || '';
        const row = selectEl.closest('tr');
        row.querySelector('.herb-unit').value = unit;
    }

    function calculateTotalHerbs() {
        const numDoses = parseFloat(document.getElementById('num_of_doses').value) || 0;
        document.querySelectorAll('#herbBody tr').forEach(tr => {
            const qtyPerDose = parseFloat(tr.querySelector('.herb-qty-per-dose').value) || 0;
            tr.querySelector('.herb-total').value = (qtyPerDose * numDoses).toFixed(1);
        });
    }

    function addExtRow() {
        let options = '<option value="">-- Chọn chế phẩm --</option>';
        extProducts.forEach(p => {
            options += `<option value="${p.id}" data-unit="${p.unit}">${p.name} (Tồn: ${p.stock_quantity})</option>`;
        });

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <input type="hidden" name="items[${itemIndex}][item_type]" value="packaged_product">
            <td>
                <select name="items[${itemIndex}][inventory_item_id]" class="form-control ext-select" required onchange="updateExtRow(this)">
                    ${options}
                </select>
            </td>
            <td><input type="text" name="items[${itemIndex}][unit]" class="form-control ext-unit" readonly></td>
            <td><input type="number" step="0.1" name="items[${itemIndex}][quantity_per_dose]" class="form-control" required min="1"></td>
            <td><input type="text" name="items[${itemIndex}][dosage]" class="form-control" placeholder="VD: Bôi 2 lần/ngày"></td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove();"><i class="fas fa-trash"></i></button>
            </td>
        `;
        document.getElementById('extBody').appendChild(tr);
        itemIndex++;
    }

    function updateExtRow(selectEl) {
        const option = selectEl.options[selectEl.selectedIndex];
        const unit = option.dataset.unit || '';
        const row = selectEl.closest('tr');
        row.querySelector('.ext-unit').value = unit;
    }

    function addSrvRow() {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <input type="hidden" name="items[${itemIndex}][item_type]" value="therapy_service">
            <td><input type="text" name="items[${itemIndex}][custom_name]" class="form-control" placeholder="VD: Xoa bóp bấm huyệt" required></td>
            <td><input type="number" name="items[${itemIndex}][sessions]" class="form-control" placeholder="Số buổi" required min="1"></td>
            <td><input type="text" name="items[${itemIndex}][note]" class="form-control" placeholder="Ghi chú thêm"></td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove();"><i class="fas fa-trash"></i></button>
            </td>
        `;
        document.getElementById('srvBody').appendChild(tr);
        itemIndex++;
    }

    document.getElementById('prescriptionForm').addEventListener('submit', function(e) {
        const herbRows = document.querySelectorAll('#herbBody tr').length;
        const extRows = document.querySelectorAll('#extBody tr').length;
        const srvRows = document.querySelectorAll('#srvBody tr').length;
        
        if (herbRows === 0 && extRows === 0 && srvRows === 0) {
            e.preventDefault();
            alert('Vui lòng thêm ít nhất một hạng mục vào đơn điều trị!');
        }
    });
</script>
@endsection
