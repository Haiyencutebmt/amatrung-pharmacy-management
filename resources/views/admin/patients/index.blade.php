@extends('layouts.admin')

@section('title', 'Quản lý Bệnh nhân — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #eef9ee; color: #5eb542; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        <span class="icon">👥</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Quản lý bệnh nhân</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Theo dõi thông tin và hồ sơ bệnh nhân trong hệ thống</p>
    </div>
</div>
@endsection

@section('content')
@if(session('import_warnings') || session('import_errors'))
    <div style="margin-bottom: 1.5rem; padding: 1.5rem; border-radius: 1rem; background: #ffffff; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); font-family: 'Inter', system-ui, sans-serif; animation: slideIn 0.3s ease-out;">
        <h3 style="margin: 0 0 1rem 0; font-size: 1.1rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 0.5rem;">
            📊 Báo cáo Import Bệnh nhân Chi tiết
        </h3>
        
        @if(session('import_warnings'))
            <div style="background: #fffbeb; border: 1px solid #fef3c7; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1rem;">
                <h4 style="margin: 0 0 0.5rem 0; font-size: 0.95rem; font-weight: 700; color: #b45309; display: flex; align-items: center; gap: 0.5rem;">
                    ⚠️ Dòng trùng lặp đã tự động bỏ qua ({{ count(session('import_warnings')) }})
                </h4>
                <div style="max-height: 150px; overflow-y: auto; font-size: 0.85rem; color: #78350f; line-height: 1.6; padding-left: 1.25rem;">
                    <ul style="margin: 0; padding: 0 0 0 1rem; list-style-type: disc;">
                        @foreach(session('import_warnings') as $warning)
                            <li>{{ $warning }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @if(session('import_errors'))
            <div style="background: #fef2f2; border: 1px solid #fee2e2; padding: 1rem; border-radius: 0.75rem;">
                <h4 style="margin: 0 0 0.5rem 0; font-size: 0.95rem; font-weight: 700; color: #b91c1c; display: flex; align-items: center; gap: 0.5rem;">
                    ❌ Dòng lỗi định dạng / thiếu thông tin ({{ count(session('import_errors')) }})
                </h4>
                <div style="max-height: 150px; overflow-y: auto; font-size: 0.85rem; color: #991b1b; line-height: 1.6; padding-left: 1.25rem;">
                    <ul style="margin: 0; padding: 0 0 0 1rem; list-style-type: disc;">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
@endif

<div class="patient-index-container" style="margin-top: -1rem;">

    {{-- Stats Cards --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline">
                        <span class="icon">👥</span>
                    </div>
                    <span class="stat-title">Tổng Bệnh Nhân</span>
                </div>
                <div class="stat-menu-wrapper">
                    <div class="stat-menu" onclick="toggleStatDropdown(this)">⋮</div>
                    <div class="stat-dropdown">
                        <a href="{{ route('admin.patients.index') }}">Xem danh sách</a>
                        <a href="#">Xuất báo cáo</a>
                    </div>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value">{{ number_format($totalPatients) }}</h3>
                <span class="stat-badge bg-green-light">↗ {{ $growthRate }}% tháng trước</span>
            </div>
            <div class="stat-footer">
                Mới <strong>{{ $newPatientsCount }}</strong> &nbsp;&nbsp; Cũ <strong>{{ $legacyPatientsCount }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline">
                        <span class="icon">👤</span>
                    </div>
                    <span class="stat-title">Mới Trong Tháng</span>
                </div>
                <div class="stat-menu-wrapper">
                    <div class="stat-menu" onclick="toggleStatDropdown(this)">⋮</div>
                    <div class="stat-dropdown">
                        <a href="{{ route('admin.patients.index') }}?sort=newest">Xem danh sách</a>
                        <a href="#">Thống kê chi tiết</a>
                    </div>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value">{{ number_format($newPatientsThisMonth) }}</h3>
                <span class="stat-badge bg-green-light">↗ {{ $growthRate }}% tháng trước</span>
            </div>
            <div class="stat-footer">
                Chiếm <strong>{{ $totalPatients > 0 ? round(($newPatientsThisMonth / $totalPatients) * 100, 1) : 0 }}%</strong> tổng số
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title-group">
                    <div class="stat-icon-outline">
                        <span class="icon">📅</span>
                    </div>
                    <span class="stat-title">Tái Khám Sắp Tới</span>
                </div>
                <div class="stat-menu-wrapper">
                    <div class="stat-menu" onclick="toggleStatDropdown(this)">⋮</div>
                    <div class="stat-dropdown">
                        @if(auth()->user()->isStaff())
                            <a href="{{ route('admin.appointments.index') }}">Quản lý lịch hẹn</a>
                        @else
                            <a href="#">Xem lịch hẹn</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="stat-body">
                <h3 class="stat-value">{{ number_format($upcomingAppointmentsCount) }}</h3>
                <span class="stat-badge bg-green-light">Trong 7 ngày tới</span>
            </div>
            <div class="stat-footer">
                Lịch hẹn cần theo dõi
            </div>
        </div>
    </div>

    {{-- Filters & Table --}}
    <div class="main-content-card">
        <form action="{{ route('admin.patients.index') }}" method="GET" class="filter-form">
            <div class="filter-row">
                <div class="search-input-group">
                    <button type="submit" class="icon" title="Tìm kiếm" style="background: none; border: none; cursor: pointer; padding: 0; outline: none;">🔍</button>
                    <input type="text" name="search" placeholder="Tìm mã BN, tên, SĐT... (Nhấn Enter)" value="{{ request('search') }}">
                </div>
                <div class="action-buttons" style="display: flex; gap: 0.5rem; align-items: center;">
                    <!-- Nút In/Xuất nâng cao -->
                    <div class="dropdown-export-wrapper" style="position: relative; display: inline-block;">
                        <button type="button" class="btn-add screen-only" id="btnExportDropdown" style="border: 1px solid #cbd5e1; cursor: pointer; text-decoration: none; background: #ffffff; color: #475569; display: inline-flex; align-items: center; gap: 0.4rem; box-shadow: none; font-weight: 700;" onclick="toggleExportDropdown()">
                            <span class="icon">📊</span> In & Xuất danh sách ▾
                        </button>
                        <div id="exportDropdownMenu" class="export-dropdown-menu" style="display: none; position: absolute; top: 100%; right: 0; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); padding: 0.5rem; min-width: 280px; z-index: 999; margin-top: 5px;">
                            <div style="font-size: 0.72rem; font-weight: 800; color: #64748b; padding: 0.4rem 0.5rem; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; margin-bottom: 5px; letter-spacing: 0.5px;">🖨️ IN DANH SÁCH BỆNH NHÂN</div>
                            <a href="javascript:void(0)" onclick="printPatientList('all')" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; color: #1e293b; text-decoration: none; font-size: 0.85rem; font-weight: 600; border-radius: 4px;" class="dropdown-item-hover">
                                <span>📄</span> In tất cả danh sách
                            </a>
                            <a href="javascript:void(0)" onclick="printPatientList('month')" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; color: #1e293b; text-decoration: none; font-size: 0.85rem; font-weight: 600; border-radius: 4px;" class="dropdown-item-hover">
                                <span>📅</span> Bệnh nhân mới tháng này
                            </a>
                            <a href="javascript:void(0)" onclick="printPatientList('today')" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; color: #1e293b; text-decoration: none; font-size: 0.85rem; font-weight: 600; border-radius: 4px;" class="dropdown-item-hover">
                                <span>☀️</span> Bệnh nhân đăng ký hôm nay
                            </a>
                            
                            <div style="font-size: 0.72rem; font-weight: 800; color: #64748b; padding: 0.4rem 0.5rem; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; margin: 10px 0 5px 0; letter-spacing: 0.5px;">📥 XUẤT FILE EXCEL (CSV)</div>
                            <a href="{{ route('admin.patients.export-excel') }}?range=all" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; color: #166534; text-decoration: none; font-size: 0.85rem; font-weight: 600; border-radius: 4px;" class="dropdown-item-hover">
                                <span>📥</span> Xuất tất cả danh sách
                            </a>
                            <a href="{{ route('admin.patients.export-excel') }}?range=month" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; color: #166534; text-decoration: none; font-size: 0.85rem; font-weight: 600; border-radius: 4px;" class="dropdown-item-hover">
                                <span>📅</span> Xuất bệnh nhân mới tháng này
                            </a>
                            <a href="{{ route('admin.patients.export-excel') }}?range=today" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; color: #166534; text-decoration: none; font-size: 0.85rem; font-weight: 600; border-radius: 4px;" class="dropdown-item-hover">
                                <span>☀️</span> Xuất bệnh nhân đăng ký hôm nay
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('admin.patients.legacy-create') }}" class="btn-add screen-only" style="border: 1px solid #5eb542; cursor: pointer; text-decoration: none; background: #ffffff; color: #5eb542; padding: 0.75rem 1.25rem;">
                        <span class="icon">📋</span> Nhập hồ sơ giấy
                    </a>
                    <button type="button" class="btn-add screen-only" style="border: none; cursor: pointer; padding: 0.75rem 1.25rem;" onclick="document.getElementById('addPatientModal').style.display='flex'">
                        <span class="icon">+</span> Thêm bệnh nhân
                    </button>
                </div>
            </div>
            <div class="filter-bottom-row">
                <div class="filter-item">
                    <label>Sắp xếp:</label>
                    <select name="sort" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên A - Z</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label>Giới tính:</label>
                    <select name="gender" onchange="this.form.submit()">
                        <option value="">Tất cả</option>
                        <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                        <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                    </select>
                </div>

                <a href="{{ route('admin.patients.index') }}" class="btn-reset">
                    <span class="icon">🔄</span> Làm mới
                </a>
            </div>
        </form>

        {{-- Nút Xóa đã chọn — nằm NGOÀI form tìm kiếm --}}
        <div id="bulkDeleteBar" style="display: none; margin-top: -0.5rem; margin-bottom: 1rem; padding: 0.75rem 1.25rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.75rem; display: none; align-items: center; justify-content: space-between;">
            <span style="color: #991b1b; font-weight: 600; font-size: 0.9rem;">🗑️ Đã chọn <strong id="checkedCount">0</strong> bệnh nhân (tối đa 5)</span>
            <button type="button" id="btnBulkDelete" style="padding: 0.5rem 1.25rem; border-radius: 0.6rem; background: #ef4444; color: #fff; border: none; font-weight: 700; cursor: pointer; font-size: 0.85rem; box-shadow: 0 2px 8px rgba(239,68,68,0.2); display: flex; align-items: center; gap: 0.4rem;">
                🗑️ Xóa đã chọn
            </button>
        </div>

        {{-- Hidden Form for Bulk Deletion --}}
        <form action="{{ route('admin.patients.bulk-destroy') }}" method="POST" id="bulkDeleteForm" style="display: none;">
            @csrf
            @method('DELETE')
            <div id="bulkDeleteInputs"></div>
        </form>

        <!-- Print Header (Hidden on screen, shown in print) -->
        <div class="print-header" style="display: none;">
            <h1>DANH SÁCH BỆNH NHÂN</h1>
            <p>Hệ thống Quản lý Phòng khám Y học Cổ truyền AmaTrung</p>
            <p style="font-size: 9pt; margin-top: 0.5rem; font-weight: normal;">Ngày in: {{ now()->format('d/m/Y H:i') }} | Tổng số: {{ $patients->total() }} bệnh nhân</p>
        </div>

        <div class="table-container">
            <table class="patient-table">
                <thead>
                    <tr>
                        <th style="width: 40px;" class="screen-only"></th>
                        <th style="width: 80px;">MÃ BN</th>
                        <th style="width: 280px;">HỌ VÀ TÊN</th>
                        <th style="width: 120px;">SỐ ĐIỆN THOẠI</th>
                        <th>ĐỊA CHỈ</th>
                        <th>GHI CHÚ Y TẾ</th>
                        <th style="width: 90px;" class="screen-only">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $patient)
                    <tr>
                        <td style="text-align: center;" class="screen-only">
                            <input type="checkbox" value="{{ $patient->id }}" class="patient-checkbox" style="width: 1.1rem; height: 1.1rem; cursor: pointer; accent-color: #ef4444;" title="Chọn để xóa">
                        </td>
                        <td>
                            <span class="patient-code">{{ $patient->patient_code }}</span>
                        </td>
                        <td>
                            <div class="patient-name-cell">
                                <span class="name">{{ $patient->full_name }}</span>
                                @if($patient->is_legacy_data)
                                    <span class="legacy-marker" title="Dữ liệu từ hồ sơ giấy cũ">📋 HS giấy</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($patient->phone)
                                {{ $patient->phone }}
                            @elseif($patient->guardian_phone)
                                <span class="guardian-phone-info">
                                    {{ $patient->guardian_phone }}
                                    <br>
                                    <small>({{ $patient->guardian_name }} - {{ $patient->relationship }})</small>
                                </span>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            {{ $patient->address ?? '—' }}
                        </td>
                        <td class="note-cell" style="font-size: 0.85rem; max-width: 250px; white-space: normal; word-break: break-word;">
                            {{ $patient->note ?? '—' }}
                        </td>
                        <td class="screen-only">
                            <div class="action-cell">
                                <a href="{{ route('admin.patients.show', $patient) }}" class="btn-icon view" title="Xem">
                                    <span>👁️</span> Xem
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination-area">
            <p class="summary">Hiển thị {{ $patients->firstItem() }} đến {{ $patients->lastItem() }} của {{ $patients->total() }} bệnh nhân</p>
            <div class="pagination-controls">
                {{ $patients->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm Bệnh Nhân -->
<div id="addPatientModal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-header">
            <h2>Thêm Bệnh Nhân Mới</h2>
            <button type="button" class="btn-close-modal" onclick="document.getElementById('addPatientModal').style.display='none'">✕</button>
        </div>
        <div class="modal-body">
            @if ($errors->any())
                <div style="background: #fef2f2; color: #ef4444; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem; font-size: 0.9rem;">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.patients.store') }}" method="POST">
                @csrf
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                    <span style="background: #eff6ff; color: #2563eb; width: 36px; height: 36px; border-radius: 0.25rem; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">👤</span>
                    <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f172a;">Thông tin cơ bản</h3>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                    <div class="form-group">
                        <label for="full_name" class="form-label">Họ và tên <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="full_name" id="full_name" class="form-input" placeholder="VD: Nguyễn Văn An" value="{{ old('full_name') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label" style="display: flex; justify-content: space-between; align-items: center;">
                            <span>Số điện thoại <span style="color: #ef4444;">*</span></span>
                            <label style="display: flex; align-items: center; gap: 0.25rem; font-weight: normal; cursor: pointer; font-size: 0.8rem; color: #64748b;">
                                <input type="checkbox" id="is_guardian_phone" name="is_guardian_phone" value="1" {{ old('is_guardian_phone') ? 'checked' : '' }}>
                                SĐT người giám hộ
                            </label>
                        </label>
                        <input type="tel" name="{{ old('is_guardian_phone') ? 'guardian_phone' : 'phone' }}" id="phone" class="form-input" placeholder="VD: 0912345678" value="{{ old('phone') ?? old('guardian_phone') }}" required>
                        <div id="phone-duplicate-warning" style="display: none; color: #ef4444; font-size: 0.8rem; margin-top: 0.3rem; font-weight: 500;"></div>
                    </div>

                    <div class="form-group">
                        <label for="date_of_birth" class="form-label">Ngày sinh <span style="color: #ef4444;">*</span></label>
                        <input type="date" name="date_of_birth" id="date_of_birth" class="form-input" value="{{ old('date_of_birth') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="gender" class="form-label">Giới tính <span style="color: #ef4444;">*</span></label>
                        <select name="gender" id="gender" class="form-input" style="appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2364748b%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 0.65rem auto; padding-right: 2.5rem;" required>
                            <option value="">-- Chọn giới tính --</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label for="address" class="form-label">Địa chỉ thường trú <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="address" id="address" class="form-input" placeholder="Số nhà, đường, phường/xã, quận/huyện..." value="{{ old('address') }}" required>
                </div>

                <div id="guardian_block" style="display: {{ old('is_guardian_phone') ? 'block' : 'none' }}; margin-top: 1.25rem; background: #f8fafc; border-radius: 0.25rem; padding: 1.25rem 1.5rem; border: 1px solid #cbd5e1;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                        <span style="background: #fff1f2; color: #e11d48; width: 32px; height: 32px; border-radius: 0.25rem; display: flex; align-items: center; justify-content: center; font-size: 1rem;">🛡️</span>
                        <h3 style="margin: 0; font-size: 1rem; color: #0f172a; font-weight: 700;">Thông tin người giám hộ <span style="font-weight: 400; color: #94a3b8; font-size: 0.85rem; margin-left: 0.5rem;">(Trẻ em/Người già)</span></h3>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="guardian_name" class="form-label">Họ tên <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="guardian_name" id="guardian_name" class="form-input" value="{{ old('guardian_name') }}">
                        </div>
                        <div class="form-group">
                            <label for="relationship" class="form-label">Quan hệ <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="relationship" id="relationship" class="form-input" value="{{ old('relationship') }}" placeholder="VD: Bố, Mẹ...">
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1.25rem;">
                    <label for="note" class="form-label">Ghi chú y tế / Tiền sử</label>
                    <textarea name="note" id="note" class="form-input" rows="2" placeholder="Nhập các lưu ý quan trọng về bệnh nhân (nếu có)...">{{ old('note') }}</textarea>
                </div>

                <div class="modal-footer" style="display: flex; gap: 1rem; margin-top: 1.5rem; justify-content: flex-end; border-top: 1px solid #f1f5f9; padding-top: 1.25rem;">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('addPatientModal').style.display='none'" style="background: #fff; min-width: 100px; padding: 0.6rem 1.5rem; border-radius: 0.25rem; border: 1px solid #cbd5e1; font-weight: 600; cursor: pointer; color: #64748b;">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary" style="min-width: 150px; padding: 0.6rem 1.5rem; border-radius: 0.25rem; background: #2563eb; color: white; border: none; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 2px 6px rgba(37, 99, 235, 0.15);">
                        Lưu Bệnh Nhân
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const addPatientModal = document.getElementById('addPatientModal');
    const shouldOpenCreateModal = new URLSearchParams(window.location.search).get('open_create') === '1';

    // Auto open modal if validation errors exist or dashboard asks to add a patient
    @if($errors->any())
        if (addPatientModal) {
            addPatientModal.style.display = 'flex';
        }
    @endif

    if (shouldOpenCreateModal && addPatientModal) {
        addPatientModal.style.display = 'flex';
    }

    // Handle guardian phone toggle
    const isGuardianPhoneCheckbox = document.getElementById('is_guardian_phone');
    if (isGuardianPhoneCheckbox) {
        isGuardianPhoneCheckbox.addEventListener('change', function() {
            const phoneInput = document.getElementById('phone');
            const guardianBlock = document.getElementById('guardian_block');
            
            if (this.checked) {
                phoneInput.name = 'guardian_phone';
                guardianBlock.style.display = 'block';
            } else {
                phoneInput.name = 'phone';
                guardianBlock.style.display = 'none';
            }
            if (typeof checkDuplicatePhone === 'function') {
                checkDuplicatePhone();
            }
        });
    }

    // AJAX Check Duplicate Phone
    const phoneInput = document.getElementById('phone');
    const duplicateWarning = document.getElementById('phone-duplicate-warning');
    let typingTimer;

    function checkDuplicatePhone() {
        if(!phoneInput) return;
        const phone = phoneInput.value.trim();
        const isGuardian = document.getElementById('is_guardian_phone') ? document.getElementById('is_guardian_phone').checked : false;
        
        if (phone.length < 8) {
            if(duplicateWarning) duplicateWarning.style.display = 'none';
            phoneInput.style.borderColor = '#cbd5e1';
            return;
        }

        fetch('{{ route("admin.patients.check-duplicate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ phone: phone, is_guardian_phone: isGuardian })
        })
        .then(response => response.json())
        .then(data => {
            const warnings = data.warnings || [];
            const phoneWarning = warnings.find(w => w.type === 'phone');
            
            if (phoneWarning) {
                if(duplicateWarning) {
                    const names = phoneWarning.patients.map(p => `${p.code} - ${p.name}`).join(', ');
                    duplicateWarning.innerHTML = `⚠️ ${phoneWarning.message} (${names})`;
                    duplicateWarning.style.display = 'block';
                }
                phoneInput.style.borderColor = '#ef4444';
            } else {
                if(duplicateWarning) duplicateWarning.style.display = 'none';
                phoneInput.style.borderColor = '#cbd5e1';
            }
        })
        .catch(error => console.error('Error:', error));
    }

    if (phoneInput) {
        phoneInput.addEventListener('keyup', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(checkDuplicatePhone, 500);
        });
        phoneInput.addEventListener('blur', checkDuplicatePhone);
    }

    // Dropdown toggle function
    function toggleStatDropdown(element) {
        // Close all other dropdowns
        document.querySelectorAll('.stat-dropdown.show').forEach(dropdown => {
            if (dropdown !== element.nextElementSibling) {
                dropdown.classList.remove('show');
            }
        });
        // Toggle current
        element.nextElementSibling.classList.toggle('show');
    }

    function toggleExportDropdown() {
        const menu = document.getElementById('exportDropdownMenu');
        if (menu) {
            menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        }
    }

    function printPatientList(range) {
        const url = "{{ route('admin.patients.print-list') }}?range=" + range;
        
        // Tạo iframe ẩn
        const iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = 'none';
        iframe.style.opacity = '0';
        iframe.src = url + "&auto_print=1";
        
        document.body.appendChild(iframe);
        
        iframe.onload = function() {
            try {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            } catch (e) {
                console.error("Iframe print error:", e);
            }
            // Xóa iframe sau khi in để giữ DOM sạch
            setTimeout(() => {
                document.body.removeChild(iframe);
            }, 3000);
        };
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.stat-menu-wrapper')) {
            document.querySelectorAll('.stat-dropdown.show').forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
        if (!event.target.closest('.dropdown-export-wrapper')) {
            const menu = document.getElementById('exportDropdownMenu');
            if (menu) {
                menu.style.display = 'none';
            }
        }
    });

    // Bulk Delete Logic
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.patient-checkbox');
        const bulkBar = document.getElementById('bulkDeleteBar');
        const bulkBtn = document.getElementById('btnBulkDelete');
        const bulkForm = document.getElementById('bulkDeleteForm');
        const checkedCountSpan = document.getElementById('checkedCount');
        const deleteModal = document.getElementById('patientDeleteConfirmModal');
        const deleteCountSpan = document.getElementById('deleteModalCount');

        function updateBulkUI() {
            const count = document.querySelectorAll('.patient-checkbox:checked').length;
            if (count > 0) {
                bulkBar.style.display = 'flex';
                checkedCountSpan.textContent = count;
            } else {
                bulkBar.style.display = 'none';
            }
        }

        // Mỗi checkbox thay đổi
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const count = document.querySelectorAll('.patient-checkbox:checked').length;
                if (count > 5) {
                    this.checked = false;
                    alert('Mỗi lần chỉ có thể xóa tối đa 5 bệnh nhân.');
                    return;
                }
                updateBulkUI();
            });
        });

        // Nút "Xóa đã chọn" → mở modal xác nhận
        if (bulkBtn) {
            bulkBtn.addEventListener('click', function() {
                const count = document.querySelectorAll('.patient-checkbox:checked').length;
                if (count === 0) return;
                deleteCountSpan.textContent = count;
                deleteModal.style.display = 'flex';
            });
        }

        // Nút "Xóa dữ liệu" trong modal → thực sự submit
        window.executePatientBulkDelete = function() {
            const checkedBoxes = document.querySelectorAll('.patient-checkbox:checked');
            const inputsContainer = document.getElementById('bulkDeleteInputs');
            inputsContainer.innerHTML = '';
            checkedBoxes.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                inputsContainer.appendChild(input);
            });
            bulkForm.submit();
        };

        // Đóng modal khi click overlay hoặc ESC
        if (deleteModal) {
            deleteModal.addEventListener('click', function(e) {
                if (e.target === this) this.style.display = 'none';
            });
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && deleteModal) deleteModal.style.display = 'none';
        });
    });
</script>

{{-- MODAL: Xác nhận xóa bệnh nhân --}}
<div id="patientDeleteConfirmModal" style="display: none; position: fixed; inset: 0; z-index: 1000; align-items: center; justify-content: center; background: rgba(15,23,42,0.4); backdrop-filter: blur(4px);">
    <div style="background: #fff; padding: 2rem; border-radius: 1rem; width: 420px; max-width: 90%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); text-align: center; animation: modalSlideIn 0.3s ease-out;">
        <div style="width: 60px; height: 60px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
            <span style="font-size: 1.8rem;">🗑️</span>
        </div>
        <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem;">Xác nhận xóa bệnh nhân</h3>
        <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 2rem; line-height: 1.5;">
            Bạn đang chọn xóa <strong id="deleteModalCount" style="color: #ef4444;">0</strong> bệnh nhân.
            Hành động này không thể hoàn tác. Bệnh nhân đã có bệnh án sẽ không bị xóa.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <button type="button" onclick="document.getElementById('patientDeleteConfirmModal').style.display='none'" style="padding: 0.75rem 1.5rem; border-radius: 0.6rem; border: 1px solid #e2e8f0; background: #fff; color: #64748b; font-weight: 600; cursor: pointer;">Hủy bỏ</button>
            <button type="button" onclick="executePatientBulkDelete()" style="padding: 0.75rem 1.5rem; border-radius: 0.6rem; border: none; background: #ef4444; color: #fff; font-weight: 700; cursor: pointer; box-shadow: 0 2px 8px rgba(239,68,68,0.2);">Xóa dữ liệu</button>
        </div>
    </div>
</div>

<style>
.dropdown-item-hover {
    transition: background 0.15s ease, color 0.15s ease;
}
.dropdown-item-hover:hover {
    background: #f1f5f9 !important;
}

@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

/* CSS Reset & Variables - Note: integrated into index.blade.php for demonstration, normally in app.css */
.patient-index-container {
    --primary-green: #2563eb;
    --text-dark: #1e293b;
    --text-muted: #64748b;
    --bg-light: #f8fafc;
    --border-color: #e2e8f0;
}

.page-header {
    margin-bottom: 2rem;
}

.header-title {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-title .icon-bg {
    width: 48px;
    height: 48px;
    background: #eff6ff;
    color: #2563eb;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.header-title h1 {
    font-size: 1.75rem;
    font-weight: 850;
    color: var(--text-dark);
    margin: 0;
}

.header-title p {
    margin: 0.25rem 0 0 0;
    color: var(--text-muted);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: #fff;
    padding: 1.25rem 1.5rem;
    border-radius: 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    border: 1px solid rgba(241, 245, 249, 0.8);
}

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-title-group {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.stat-icon-outline {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1.5px solid var(--primary-green);
    background: #eff6ff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    color: var(--primary-green);
}

.stat-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text-dark);
}

.stat-menu-wrapper {
    position: relative;
}

.stat-menu {
    color: #cbd5e1;
    font-size: 1.25rem;
    font-weight: 800;
    cursor: pointer;
    line-height: 1;
    padding: 0 0.5rem;
    user-select: none;
}
.stat-menu:hover {
    color: var(--text-dark);
}

.stat-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background: #fff;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    border-radius: 0.75rem;
    padding: 0.5rem;
    min-width: 160px;
    z-index: 10;
    border: 1px solid #f1f5f9;
}

.stat-dropdown.show {
    display: flex;
    flex-direction: column;
    animation: fadeIn 0.2s ease;
}

.stat-dropdown a {
    text-decoration: none;
    color: var(--text-dark);
    font-size: 0.85rem;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    transition: background 0.2s;
    font-weight: 600;
    white-space: nowrap;
}

.stat-dropdown a:hover {
    background: #f8fafc;
    color: var(--primary-green);
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

.stat-body {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.stat-value {
    margin: 0;
    font-size: 2rem;
    font-weight: 850;
    color: var(--text-dark);
    line-height: 1;
}

.stat-badge {
    padding: 0.2rem 0.5rem;
    border-radius: 2rem;
    font-size: 0.75rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.2rem;
}

.bg-green-light {
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #dbeafe;
}

.stat-footer {
    font-size: 0.8rem;
    color: var(--text-muted);
    font-weight: 500;
    padding-top: 0.5rem;
}

.stat-footer strong {
    color: var(--text-dark);
    font-weight: 750;
}

.main-content-card {
    background: #fff;
    border-radius: 0.5rem;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.01);
}

.filter-form {
    margin-bottom: 2rem;
}

.filter-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.search-input-group {
    position: relative;
    width: 400px;
}

.search-input-group input {
    width: 100%;
    padding: 0.8rem 1rem 0.8rem 2.75rem;
    border-radius: 0.25rem;
    border: 1px solid var(--border-color);
    background: #fcfdfe;
}

.search-input-group .icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

.action-buttons {
    display: flex;
    gap: 1rem;
}

.btn-add {
    padding: 0.75rem 1.5rem;
    border-radius: 0.25rem;
    background: #2563eb;
    color: #fff;
    text-decoration: none;
    font-weight: 700;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.15);
}

.filter-bottom-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding-top: 1.5rem;
    border-top: 1px dashed #f1f5f9;
    flex-wrap: wrap;
}

.filter-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.filter-item label {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-muted);
}

.filter-item select {
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
    border: 1px solid var(--border-color);
    background: #fff;
    font-size: 0.85rem;
    font-weight: 600;
}

.btn-reset {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-muted);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

/* Excel Spreadsheet Table Styling */
.table-container {
    overflow-x: auto;
    border: 1px solid #cbd5e1;
    border-radius: 0.25rem;
    background: #fff;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
}

.patient-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.82rem;
    color: #334155;
    font-family: 'Inter', system-ui, sans-serif;
}

.patient-table th {
    text-align: left;
    padding: 0.5rem 0.75rem;
    font-size: 0.78rem;
    font-weight: 700;
    color: #475569;
    background-color: #f1f5f9;
    border: 1px solid #cbd5e1;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

.patient-table td {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e2e8f0;
    background-color: #fff;
    vertical-align: middle;
}

.patient-table tr:nth-child(even) td {
    background-color: #f8fafc;
}

.patient-table tr:hover td {
    background-color: #f1f5f9;
}

.patient-code {
    background: #eff6ff;
    color: #2563eb;
    padding: 0.25rem 0.5rem;
    border-radius: 0.125rem;
    font-weight: 700;
    font-size: 0.8rem;
    border: 1px solid #bfdbfe;
    font-family: monospace;
}

.patient-name-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.patient-name-cell .name {
    font-weight: 700;
    color: #0f172a;
}

.patient-name-cell .legacy-marker {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.15rem 0.4rem;
    background: #fef3c7;
    color: #92400e;
    border-radius: 0.125rem;
    font-size: 0.7rem;
    font-weight: 600;
    white-space: nowrap;
    border: 1px solid #fde68a;
}

.gender-text {
    font-weight: 700;
    color: #475569;
}

.age-text {
    color: #64748b;
    font-weight: 500;
}

.guardian-phone-info {
    font-size: 0.8rem;
    color: #64748b;
    line-height: 1.2;
    display: inline-block;
}

.action-cell {
    display: flex;
    gap: 0.5rem;
}

.btn-icon {
    padding: 0.4rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    border: 1px solid #cbd5e1;
    background: #fff;
    color: #475569;
    transition: all 0.2s;
}

.btn-icon:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #0f172a;
}

/* Pagination Overrides for Laravel */
.pagination-area {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.pagination-area .summary {
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 600;
}

.pagination {
    display: flex;
    gap: 0.25rem;
}

.page-item .page-link {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.25rem;
    background: #fff;
    border: 1px solid #cbd5e1;
    color: #475569;
    text-decoration: none;
    font-weight: 700;
}

.page-item.active .page-link {
    background: #2563eb;
    color: #fff;
    border-color: #2563eb;
    border-radius: 0.25rem;
}

/* Printing Specific Media Styles */
@media print {
    @page {
        size: landscape;
        margin: 15mm;
    }
    
    body {
        background: #fff !important;
        color: #000 !important;
        font-family: "Times New Roman", Times, serif !important;
    }
    
    /* Hide layout chrome and admin pieces */
    .admin-sidebar,
    .admin-header,
    .stats-grid,
    .filter-form,
    #bulkDeleteBar,
    .pagination-area,
    #sidebarBackdrop,
    #flash-messages,
    #addPatientModal,
    #patientDeleteConfirmModal,
    .screen-only
    {
        display: none !important;
    }
    
    .admin-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }

    .main-content-card {
        box-shadow: none !important;
        padding: 0 !important;
        border: none !important;
        background: transparent !important;
    }

    /* Printed Document Header */
    .print-header {
        display: block !important;
        text-align: center;
        margin-bottom: 2rem;
        border-bottom: 2px solid #000;
        padding-bottom: 1rem;
    }
    .print-header h1 {
        font-size: 20pt;
        font-weight: bold;
        margin: 0 0 0.5rem 0;
        text-transform: uppercase;
        color: #000 !important;
    }
    .print-header p {
        font-size: 11pt;
        margin: 0;
        color: #333 !important;
    }
    
    /* Table styles for printer */
    .table-container {
        overflow: visible !important;
        border: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }
    .patient-table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin-top: 1rem !important;
    }
    .patient-table th, 
    .patient-table td {
        border: 1px solid #000 !important;
        padding: 6pt 8pt !important;
        font-size: 9.5pt !important;
        color: #000 !important;
        background: transparent !important;
        text-align: left !important;
        vertical-align: middle !important;
    }
    .patient-table th {
        font-weight: bold !important;
        background-color: #e6e6e6 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .patient-code {
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
        color: #000 !important;
        font-weight: bold !important;
        font-family: monospace !important;
        font-size: 9.5pt !important;
    }
    .patient-name-cell .legacy-marker {
        background: transparent !important;
        border: 1px solid #000 !important;
        color: #000 !important;
        font-size: 8pt !important;
        padding: 1pt 2pt !important;
        border-radius: 0 !important;
    }
}

/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(4px);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-container {
    background: #fff;
    width: 100%;
    max-width: 800px;
    border-radius: 0.5rem;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    animation: modal-pop 0.25s ease-out;
}

@keyframes modal-pop {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.modal-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 800;
    color: #1e293b;
}

.btn-close-modal {
    background: #f1f5f9;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    cursor: pointer;
    font-size: 1rem;
    transition: all 0.2s;
}

.btn-close-modal:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.modal-body {
    padding: 2rem;
    overflow-y: auto;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-label {
    font-size: 0.85rem;
    font-weight: 700;
    color: #475569;
}

.form-input {
    width: 100%;
    box-sizing: border-box;
    padding: 0.6rem 0.8rem;
    border-radius: 0.25rem;
    border: 1px solid #cbd5e1;
    font-size: 0.9rem;
    color: #1e293b;
    background: #fff;
    transition: border-color 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #5eb542;
    box-shadow: 0 0 0 3px rgba(94,181,66,0.15);
}

textarea.form-input {
    resize: vertical;
}

@media (max-width: 768px) {
    .search-input-group {
        width: 100%;
    }
    .action-buttons {
        width: 100%;
        justify-content: space-between;
    }
    .btn-add {
        flex: 1;
        text-align: center;
    }
    .filter-item {
        width: 100%;
        justify-content: space-between;
    }
    .filter-item select {
        width: 60%;
    }
    .btn-reset {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection
