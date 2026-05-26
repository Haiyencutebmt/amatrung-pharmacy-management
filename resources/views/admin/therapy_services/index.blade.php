@extends('layouts.admin')

@section('title', 'Dịch vụ trị liệu — AmaTrung')

@section('header-left')
<div class="header-title" style="display: flex; align-items: center; gap: 1rem;">
    <div class="icon-bg" style="width: 44px; height: 44px; background: #eef9ee; color: #16a34a; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
        <span class="icon">👐</span>
    </div>
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0; line-height: 1.2;">Dịch vụ trị liệu</h1>
        <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">Quản lý danh mục các dịch vụ trị liệu và nắn bóp tại phòng khám</p>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid" style="margin-top: -1rem;">
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
            <span>⚠️</span> {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 1.5rem; flex-direction: column; align-items: flex-start;">
            <div style="font-weight: 700; margin-bottom: 0.25rem;">⚠️ Vui lòng kiểm tra lại dữ liệu:</div>
            <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.85rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1.8fr 1.2fr; gap: 1.5rem; align-items: start;">
        {{-- LEFT SIDE: List of Services --}}
        <div class="card" style="border-radius: 1.5rem; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap;">
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #0f766e;">👐 Danh sách dịch vụ</h3>
                
                <form action="{{ route('admin.therapy-services.index') }}" method="GET" style="display: flex; gap: 0.5rem; flex: 1; max-width: 350px;">
                    <div style="position: relative; width: 100%;">
                        <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.85rem;">🔍</span>
                        <input type="text" name="search" placeholder="Tìm tên dịch vụ..." value="{{ request('search') }}" style="width: 100%; padding: 0.5rem 0.75rem 0.5rem 2.25rem; border-radius: 0.75rem; border: 1px solid #cbd5e1; font-size: 0.85rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--color-primary-500)'" onblur="this.style.borderColor='#cbd5e1'">
                    </div>
                    @if(request('search'))
                        <a href="{{ route('admin.therapy-services.index') }}" style="background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 0.5rem 0.75rem; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; text-decoration: none; font-weight: 600;">Reset</a>
                    @endif
                </form>
            </div>

            <div class="table-wrapper" style="border: 1px solid #e2e8f0; border-radius: 1rem; overflow: hidden;">
                <table class="data-table">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <th style="padding: 1rem; width: 35%;">TÊN DỊCH VỤ</th>
                            <th style="padding: 1rem; width: 15%; text-align: center;">SỐ BUỔI</th>
                            <th style="padding: 1rem; width: 30%;">HƯỚNG DẪN MẶC ĐỊNH</th>
                            <th style="padding: 1rem; width: 10%; text-align: center;">TRẠNG THÁI</th>
                            <th style="padding: 1rem; width: 10%; text-align: right;">HÀNH ĐỘNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#fcfdfe'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1rem; font-weight: 700; color: #0f766e; font-size: 0.92rem;">
                                    {{ $service->name }}
                                </td>
                                <td style="padding: 1rem; text-align: center; font-weight: 700; color: #1e293b;">
                                    {{ $service->default_sessions }} buổi
                                </td>
                                <td style="padding: 1rem; color: #475569; font-size: 0.85rem; line-height: 1.45;">
                                    {{ $service->default_instruction ?: '—' }}
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    @if($service->status === 'active')
                                        <span class="badge badge-success" style="font-weight: 700; font-size: 0.72rem;">Đang dùng</span>
                                    @else
                                        <span class="badge badge-danger" style="font-weight: 700; font-size: 0.72rem;">Tạm ngưng</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem; text-align: right; white-space: nowrap;">
                                    <div style="display: inline-flex; gap: 0.35rem;">
                                        <button type="button" onclick="editService({{ json_encode($service) }})" style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 0.35rem 0.6rem; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 0.25rem;" onmouseover="this.style.background='#bbf7d0'" onmouseout="this.style.background='#f0fdf4'">
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
                    {{ $services->withQueryString()->links() }}
                </div>
            @endif
        </div>

        {{-- RIGHT SIDE: Quick Form Card --}}
        <div class="card" style="border-radius: 1.5rem; padding: 1.5rem; border: 1px solid var(--color-primary-100); background: #fdfefe;">
            <h3 id="form-card-title" style="margin: 0 0 1.25rem 0; font-size: 1.1rem; font-weight: 850; color: #166534; display: flex; align-items: center; gap: 0.5rem;">
                <span id="form-icon">✨</span> <span id="form-title-text">Thêm dịch vụ mới</span>
            </h3>

            <form id="service-form" action="{{ route('admin.therapy-services.store') }}" method="POST">
                @csrf
                <div id="method-container"></div> {{-- Will hold @method('PUT') when editing --}}
                
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem;">Tên dịch vụ trị liệu *</label>
                    <input type="text" name="name" id="service-name" required placeholder="VD: Điện châm trị liệu..." value="{{ old('name') }}" style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 0.75rem; border: 1.5px solid #cbd5e1; font-size: 0.9rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--color-primary-500)'" onblur="this.style.borderColor='#cbd5e1'">
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem;">Số buổi mặc định *</label>
                    <input type="number" name="default_sessions" id="service-sessions" required min="1" placeholder="VD: 3" value="{{ old('default_sessions', 3) }}" style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 0.75rem; border: 1.5px solid #cbd5e1; font-size: 0.9rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--color-primary-500)'" onblur="this.style.borderColor='#cbd5e1'">
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem;">Hướng dẫn mặc định</label>
                    <textarea name="default_instruction" id="service-instruction" placeholder="VD: Nghỉ ngơi 10 phút sau thủ thuật, tránh gió lùa..." style="width: 100%; height: 100px; padding: 0.65rem 0.85rem; border-radius: 0.75rem; border: 1.5px solid #cbd5e1; font-size: 0.9rem; outline: none; transition: border-color 0.2s; resize: vertical;" onfocus="this.style.borderColor='var(--color-primary-500)'" onblur="this.style.borderColor='#cbd5e1'">{{ old('default_instruction') }}</textarea>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem;">Trạng thái sử dụng</label>
                    <select name="status" id="service-status" style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 0.75rem; border: 1.5px solid #cbd5e1; font-size: 0.9rem; outline: none; transition: border-color 0.2s; background: #fff;" onfocus="this.style.borderColor='var(--color-primary-500)'" onblur="this.style.borderColor='#cbd5e1'">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>🟢 Đang hoạt động (Khả dụng)</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>🔴 Tạm ngưng (Ẩn)</option>
                    </select>
                </div>

                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                    <button type="button" id="btn-cancel-edit" onclick="resetForm()" style="display: none; background: #fff; color: #64748b; border: 1px solid #cbd5e1; border-radius: 0.75rem; padding: 0.65rem 1.25rem; font-size: 0.88rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        Hủy
                    </button>
                    <button type="submit" id="btn-submit-form" class="btn btn-primary" style="padding: 0.65rem 1.5rem; border-radius: 0.75rem; font-size: 0.88rem; font-weight: 700; border: none;">
                        💾 Lưu dịch vụ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editService(service) {
        // Change Form Action & Method
        const form = document.getElementById('service-form');
        form.action = `/admin/therapy-services/${service.id}`;
        
        const methodContainer = document.getElementById('method-container');
        methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        // Update Title & Icons
        document.getElementById('form-card-title').style.color = '#1e3a5f';
        document.getElementById('form-icon').innerText = '✏️';
        document.getElementById('form-title-text').innerText = 'Cập nhật dịch vụ';
        document.getElementById('btn-submit-form').innerText = '💾 Cập nhật';
        
        // Populate inputs
        document.getElementById('service-name').value = service.name;
        document.getElementById('service-sessions').value = service.default_sessions;
        document.getElementById('service-instruction').value = service.default_instruction || '';
        document.getElementById('service-status').value = service.status;
        
        // Show Cancel Button
        document.getElementById('btn-cancel-edit').style.display = 'inline-block';
        
        // Highlight form
        document.getElementById('service-name').focus();
    }

    function resetForm() {
        const form = document.getElementById('service-form');
        form.action = "{{ route('admin.therapy-services.store') }}";
        
        document.getElementById('method-container').innerHTML = '';
        
        // Restore Title & Icons
        document.getElementById('form-card-title').style.color = '#166534';
        document.getElementById('form-icon').innerText = '✨';
        document.getElementById('form-title-text').innerText = 'Thêm dịch vụ mới';
        document.getElementById('btn-submit-form').innerText = '💾 Lưu dịch vụ';
        
        // Clear inputs
        document.getElementById('service-name').value = '';
        document.getElementById('service-sessions').value = 3;
        document.getElementById('service-instruction').value = '';
        document.getElementById('service-status').value = 'active';
        
        // Hide Cancel Button
        document.getElementById('btn-cancel-edit').style.display = 'none';
    }
</script>
@endsection
