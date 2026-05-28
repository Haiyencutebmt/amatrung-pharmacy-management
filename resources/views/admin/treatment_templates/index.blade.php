@extends('layouts.admin')

@section('title', 'Dịch vụ trị liệu — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #eff6ff; color: #2563eb; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        <span class="icon">🩺</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Dịch vụ trị liệu</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Quản lý danh sách dịch vụ trị liệu tại phòng khám</p>
    </div>
</div>
@endsection

@section('content')
<div class="treatment-templates-container" style="margin-top: -1rem;">

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 1.5rem; flex-direction: column; align-items: flex-start; display: flex; background: #fef2f2; border: 1px solid #fee2e2; padding: 1rem 1.5rem; border-radius: 1.25rem;">
            <div style="font-weight: 700; margin-bottom: 0.25rem; color: #dc2626;">⚠️ Vui lòng kiểm tra lại dữ liệu dịch vụ trị liệu:</div>
            <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.85rem; color: #b91c1c;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        {{-- LEFT SIDE: List of Services --}}
        <div class="main-content-card" style="border-radius: 1.5rem; padding: 1.5rem; background: #fff; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #1e3a8a;">👐 Danh sách dịch vụ trị liệu</h3>
                    <button type="button" onclick="openAddServiceModal()" style="background: #2563eb; color: #fff; border: none; padding: 0.45rem 0.85rem; border-radius: 0.75rem; font-size: 0.8rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.25rem; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2); transition: all 0.2s;" onmouseover="this.style.background='#1d4ed8'; this.style.transform='translateY(-1px)';" onmouseout="this.style.background='#2563eb'; this.style.transform='none';">
                        ➕ Thêm dịch vụ
                    </button>
                </div>
                
                <form action="{{ route('admin.treatment-templates.index') }}" method="GET" style="display: flex; gap: 0.5rem; flex: 1; max-width: 350px;">
                    <div style="position: relative; width: 100%;">
                        <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem;">🔍</span>
                        <input type="text" name="search_service" placeholder="Tìm tên dịch vụ..." value="{{ request('search_service') }}" style="width: 100%; padding: 0.5rem 0.75rem 0.5rem 2.25rem; border-radius: 0.75rem; border: 1px solid #cbd5e1; font-size: 0.85rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#cbd5e1'">
                    </div>
                    @if(request('search_service'))
                        <a href="{{ route('admin.treatment-templates.index') }}" style="background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 0.5rem 0.75rem; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; text-decoration: none; font-weight: 600;">Reset</a>
                    @endif
                </form>
            </div>

            <div class="table-wrapper" style="border: 1px solid #e2e8f0; border-radius: 1rem; overflow: hidden;">
                <table class="patient-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; text-align: left;">
                            <th style="padding: 1rem; width: 35%; font-size: 0.75rem; font-weight: 850; color: #1e3a5f; letter-spacing: 0.05em;">TÊN DỊCH VỤ</th>
                            <th style="padding: 1rem; width: 15%; text-align: center; font-size: 0.75rem; font-weight: 850; color: #1e3a5f; letter-spacing: 0.05em;">SỐ LẦN MẶC ĐỊNH</th>
                            <th style="padding: 1rem; width: 30%; font-size: 0.75rem; font-weight: 850; color: #1e3a5f; letter-spacing: 0.05em;">HƯỚNG DẪN MẶC ĐỊNH</th>
                            <th style="padding: 1rem; width: 10%; text-align: center; font-size: 0.75rem; font-weight: 850; color: #1e3a5f; letter-spacing: 0.05em;">TRẠNG THÁI</th>
                            <th style="padding: 1rem; width: 10%; text-align: right; font-size: 0.75rem; font-weight: 850; color: #1e3a5f; letter-spacing: 0.05em;">HÀNH ĐỘNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#fcfdfe'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1rem; font-weight: 700; color: #1e3a8a; font-size: 0.92rem;">
                                    {{ $service->name }}
                                </td>
                                <td style="padding: 1rem; text-align: center; font-weight: 700; color: #1e293b;">
                                    {{ $service->default_sessions }} lần
                                </td>
                                <td style="padding: 1rem; color: #475569; font-size: 0.85rem; line-height: 1.45;">
                                    {{ $service->default_instruction ?: '—' }}
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    @if($service->status === 'active')
                                        <span class="badge badge-success" style="font-weight: 700; font-size: 0.72rem; background: #eef9ee; color: #16a34a; padding: 0.2rem 0.6rem; border-radius: 20px; display: inline-block;">Đang dùng</span>
                                    @else
                                        <span class="badge badge-danger" style="font-weight: 700; font-size: 0.72rem; background: #fef2f2; color: #ef4444; padding: 0.2rem 0.6rem; border-radius: 20px; display: inline-block;">Tạm ngưng</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem; text-align: right; white-space: nowrap;">
                                    <div style="display: inline-flex; gap: 0.35rem;">
                                        <button type="button" onclick="editService({{ json_encode($service) }})" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; padding: 0.35rem 0.6rem; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 0.25rem;" onmouseover="this.style.background='#bfdbfe'" onmouseout="this.style.background='#eff6ff'">
                                            ✏️ Sửa
                                        </button>
                                        <form action="{{ route('admin.therapy-services.destroy', $service) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa dịch vụ này không?')" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 0.35rem 0.6rem; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 0.25rem;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fef2f2'">
                                                🗑️ Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: #94a3b8; padding: 3rem 1rem; font-style: italic;">
                                    Không tìm thấy dịch vụ trị liệu nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($services->hasPages())
                <div class="pagination-wrapper" style="margin-top: 1rem; border-top: none; padding: 0.5rem 0;">
                    {{ $services->appends(['search_service' => request('search_service')])->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- Modal Backdrop -->
    <div id="serviceFormModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center; padding: 1rem;">
        <!-- Modal Dialog -->
        <div class="main-content-card" style="width: 100%; max-width: 500px; border-radius: 1.5rem; padding: 2rem; background: #fff; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); position: relative; border: 1.5px solid #bfdbfe;">
            
            <!-- Modal Header with Close Button -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 id="form-card-title" style="margin: 0; font-size: 1.2rem; font-weight: 850; color: #1e3a8a; display: flex; align-items: center; gap: 0.5rem;">
                    <span id="form-icon">✨</span> <span id="form-title-text">Thêm dịch vụ mới</span>
                </h3>
                <button type="button" onclick="resetForm()" style="background: none; border: none; font-size: 1.75rem; color: #64748b; cursor: pointer; padding: 0.25rem; display: flex; align-items: center; justify-content: center; transition: color 0.2s; line-height: 1;" onmouseover="this.style.color='#1e293b'" onmouseout="this.style.color='#64748b'">
                    &times;
                </button>
            </div>

            <form id="service-form" action="{{ route('admin.therapy-services.store') }}" method="POST">
                @csrf
                <div id="method-container"></div>
                
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem; text-align: left;">Tên dịch vụ trị liệu *</label>
                    <input type="text" name="name" id="service-name" required placeholder="VD: Điện châm trị liệu..." value="{{ old('name') }}" style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 0.75rem; border: 1.5px solid #cbd5e1; font-size: 0.9rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#cbd5e1'">
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem; text-align: left;">Số lần mặc định *</label>
                    <input type="number" name="default_sessions" id="service-sessions" required min="1" placeholder="VD: 1" value="{{ old('default_sessions', 1) }}" style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 0.75rem; border: 1.5px solid #cbd5e1; font-size: 0.9rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#cbd5e1'">
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem; text-align: left;">Hướng dẫn mặc định</label>
                    <textarea name="default_instruction" id="service-instruction" placeholder="VD: Nghỉ ngơi 10 phút sau thủ thuật, tránh gió lùa..." style="width: 100%; height: 100px; padding: 0.65rem 0.85rem; border-radius: 0.75rem; border: 1.5px solid #cbd5e1; font-size: 0.9rem; outline: none; transition: border-color 0.2s; resize: vertical;" onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#cbd5e1'">{{ old('default_instruction') }}</textarea>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem; text-align: left;">Trạng thái sử dụng</label>
                    <select name="status" id="service-status" style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 0.75rem; border: 1.5px solid #cbd5e1; font-size: 0.9rem; outline: none; transition: border-color 0.2s; background: #fff;" onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#cbd5e1'">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>🟢 Đang hoạt động (Khả dụng)</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>🔴 Tạm ngưng (Ẩn)</option>
                    </select>
                </div>

                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                    <button type="button" onclick="resetForm()" style="background: #fff; color: #64748b; border: 1px solid #cbd5e1; border-radius: 0.75rem; padding: 0.65rem 1.25rem; font-size: 0.88rem; font-weight: 600; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#fff'">
                        Hủy
                    </button>
                    <button type="submit" id="btn-submit-form" class="btn-save" style="padding: 0.65rem 1.5rem; border-radius: 0.75rem; font-size: 0.88rem; font-weight: 700; border: none; height: auto;">
                        💾 Lưu dịch vụ
                    </button>
                </div>
            </form>
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
    background: #2563eb;
    color: #fff;
    border-color: #2563eb;
}
.pagination {
    display: flex;
    gap: 0.25rem;
    list-style: none;
    padding: 0;
    margin: 0;
}
.badge-success {
    background: #eef9ee;
    color: #16a34a;
}
.badge-danger {
    background: #fef2f2;
    color: #ef4444;
}
.btn-save {
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.65rem 1.5rem;
    font-weight: 700;
    font-size: 0.88rem;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
    transition: all 0.2s;
}
.btn-save:hover {
    background: #1d4ed8;
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
    
    // Auto-open modal if validation fails or old inputs are present
    @if ($errors->any() || old('name'))
        document.getElementById('serviceFormModal').style.display = 'flex';
    @endif
});

// --- QUICK FORM LOGIC (Services) ---
function openAddServiceModal() {
    resetForm();
    document.getElementById('serviceFormModal').style.display = 'flex';
    document.getElementById('service-name').focus();
}

function closeServiceModal() {
    document.getElementById('serviceFormModal').style.display = 'none';
}

function editService(service) {
    // Change Form Action & Method
    const form = document.getElementById('service-form');
    form.action = `/admin/therapy-services/${service.id}`;
    
    const methodContainer = document.getElementById('method-container');
    methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
    
    // Update Title & Icons
    document.getElementById('form-card-title').style.color = '#1e3a8a';
    document.getElementById('form-icon').innerText = '✏️';
    document.getElementById('form-title-text').innerText = 'Cập nhật dịch vụ';
    document.getElementById('btn-submit-form').innerText = '💾 Cập nhật';
    
    // Populate inputs
    document.getElementById('service-name').value = service.name;
    document.getElementById('service-sessions').value = service.default_sessions;
    document.getElementById('service-instruction').value = service.default_instruction || '';
    document.getElementById('service-status').value = service.status;
    
    // Open Modal
    document.getElementById('serviceFormModal').style.display = 'flex';
    
    // Highlight form
    document.getElementById('service-name').focus();
}

function resetForm() {
    const form = document.getElementById('service-form');
    form.action = "{{ route('admin.therapy-services.store') }}";
    
    document.getElementById('method-container').innerHTML = '';
    
    // Restore Title & Icons
    document.getElementById('form-card-title').style.color = '#1e3a8a';
    document.getElementById('form-icon').innerText = '✨';
    document.getElementById('form-title-text').innerText = 'Thêm dịch vụ mới';
    document.getElementById('btn-submit-form').innerText = '💾 Lưu dịch vụ';
    
    // Clear inputs
    document.getElementById('service-name').value = '';
    document.getElementById('service-sessions').value = 1; // Default to 1 instead of 3
    document.getElementById('service-instruction').value = '';
    document.getElementById('service-status').value = 'active';
    
    closeServiceModal();
}

// Close modal when clicking backdrop
window.addEventListener('click', function(event) {
    const modal = document.getElementById('serviceFormModal');
    if (event.target === modal) {
        resetForm();
    }
});
</script>
@endpush
