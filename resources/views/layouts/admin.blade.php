<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Quản trị — AmaTrung">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản trị — AmaTrung')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="margin: 0; padding: 0;">

    {{-- Sidebar --}}
    <aside class="admin-sidebar" id="adminSidebar">
        {{-- Logo --}}
        <div style="padding: 2rem 1.5rem; margin-bottom: 1rem;">
            <a href="{{ url('/admin/dashboard') }}" style="text-decoration: none; display: flex; align-items: center; gap: 1rem;">
                <img src="{{ asset('images/amatrung_logo.png') }}" alt="AmaTrung Logo" style="width: 42px; height: 42px; object-fit: contain; border-radius: 50%; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                <div>
                    <span style="font-size: 1.25rem; font-weight: 800; color: var(--color-primary-700); letter-spacing: -0.02em; display: block; line-height: 1.1;">AmaTrung</span>
                    <span style="font-size: 0.7rem; color: var(--color-primary-500); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Y Học Cổ Truyền</span>
                </div>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="admin-sidebar-nav">
            <a href="{{ url('/admin/dashboard') }}"
               class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <span class="nav-icon" style="color: var(--color-primary-600);">📊</span> Dashboard
            </a>

            <div class="sidebar-section-header" onclick="toggleSidebarSection('clinical-care')">
                <span class="section-title" style="font-size: 0.75rem; text-transform: uppercase; color: #cbd5e1; letter-spacing: 0.1em; font-weight: 800; transition: color 0.15s;">Clinical Care</span>
                <span class="section-arrow" id="arrow-clinical-care" style="font-size: 0.7rem; color: #cbd5e1; transition: transform 0.2s, color 0.15s; transform: rotate(0deg); line-height: 1;">▲</span>
            </div>

            <div id="section-clinical-care" style="display: block;">
                @if(auth()->user()->hasPermission('patients.view'))
                <a href="{{ url('/admin/patients') }}"
                   class="nav-link {{ request()->is('admin/patients*') ? 'active' : '' }}">
                    <span class="nav-icon" style="color: var(--color-primary-600);">👤</span> Bệnh nhân
                </a>
                @endif
                @if(auth()->user()->hasPermission('medical_records.view'))
                <a href="{{ url('/admin/medical-records') }}"
                   class="nav-link {{ request()->is('admin/medical-records*') ? 'active' : '' }}">
                    <span class="nav-icon" style="color: var(--color-primary-600);">📋</span> Bệnh án
                </a>
                @endif
                @if(auth()->user()->hasPermission('prescriptions.view'))
                <a href="{{ url('/admin/prescriptions') }}"
                   class="nav-link {{ request()->is('admin/prescriptions*') ? 'active' : '' }}">
                    <span class="nav-icon" style="color: var(--color-primary-600);">💊</span> Đơn điều trị
                </a>
                <a href="{{ route('admin.treatment-templates.index') }}"
                   class="nav-link {{ request()->is('admin/treatment-templates*') || request()->is('admin/sample-prescriptions*') || request()->is('admin/therapy-services*') ? 'active' : '' }}">
                    <span class="nav-icon" style="color: var(--color-primary-600);">🩺</span> Dịch vụ trị liệu
                </a>
                @endif

                @if(auth()->user()->isStaff())
                <a href="{{ route('admin.appointments.index') }}"
                   class="nav-link {{ request()->is('admin/appointments*') ? 'active' : '' }}">
                    <span class="nav-icon" style="color: var(--color-primary-600);">📅</span> Lịch hẹn
                </a>
                @endif
                @if(auth()->user()->hasPermission('manage_inventory') || auth()->user()->hasPermission('medicinal_herbs.view'))
                <a href="{{ route('admin.inventory.index') }}"
                   class="nav-link {{ request()->is('admin/inventory*') || request()->is('admin/warehouse*') || request()->is('admin/medicinal-herbs*') || request()->is('admin/packaged-products*') ? 'active' : '' }}">
                    <span class="nav-icon" style="color: var(--color-primary-600);">🏪</span> Quản lý kho
                </a>
                @endif
            </div>

            <div class="sidebar-section-header" onclick="toggleSidebarSection('systems')">
                <span class="section-title" style="font-size: 0.75rem; text-transform: uppercase; color: #cbd5e1; letter-spacing: 0.1em; font-weight: 800; transition: color 0.15s;">Systems</span>
                <span class="section-arrow" id="arrow-systems" style="font-size: 0.7rem; color: #cbd5e1; transition: transform 0.2s, color 0.15s; transform: rotate(0deg); line-height: 1;">▲</span>
            </div>

            <div id="section-systems" style="display: block;">
                @if(auth()->user()->hasPermission('articles.manage'))
                <a href="{{ url('/admin/articles') }}"
                   class="nav-link {{ request()->is('admin/articles*') ? 'active' : '' }}">
                    <span class="nav-icon" style="color: var(--color-primary-600);">📰</span> Bài viết
                </a>
                @endif
                @if(auth()->user()->hasPermission('herb_dictionary.manage'))
                <a href="{{ route('admin.herb-dictionary.index') }}"
                   class="nav-link {{ request()->is('admin/herb-dictionary*') ? 'active' : '' }}">
                    <span class="nav-icon" style="color: var(--color-primary-600);">🌱</span> Từ điển thuốc nam
                </a>
                @endif
                @if(auth()->user()->hasPermission('comments.manage'))
                <a href="{{ url('/admin/comments') }}"
                   class="nav-link {{ request()->is('admin/comments*') ? 'active' : '' }}">
                    <span class="nav-icon" style="color: var(--color-primary-600);">💬</span> Bình luận
                </a>
                @endif
                @if(auth()->user()->hasPermission('contact_messages.manage'))
                <a href="{{ url('/admin/contact-messages') }}"
                   class="nav-link {{ request()->is('admin/contact-messages*') ? 'active' : '' }}">
                    <span class="nav-icon" style="color: var(--color-primary-600);">📧</span> Yêu cầu hỗ trợ
                </a>
                @endif

                @if(auth()->user()->isAdmin())
                <a href="{{ url('/admin/users') }}"
                   class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                    <span class="nav-icon" style="color: var(--color-primary-600);">👥</span> Tài khoản
                </a>
                @endif
            </div>
        </nav>

        {{-- User info --}}
        <div style="padding: 1.5rem; margin: 1rem 1.25rem 1.5rem; background: var(--color-surface-bg); border-radius: 1.5rem; border: 1px solid var(--color-surface-border);">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, var(--color-primary-500), var(--color-primary-600)); display: flex; align-items: center; justify-content: center; font-size: 1rem; font-weight: 700; color: #fff; box-shadow: 0 4px 10px rgba(22, 163, 74, 0.2);">
                    {{ mb_substr(auth()->user()->name ?? 'A', 0, 1) }}
                </div>
                <div>
                    <div style="font-size: 0.9rem; font-weight: 700; color: #1e3a5f;">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div style="font-size: 0.65rem; color: var(--color-primary-600); text-transform: uppercase; font-weight: 600;">{{ auth()->user()->role ?? 'admin' }}</div>
                </div>
            </div>
            <form action="{{ url('/logout') }}" method="POST">
                @csrf
                <button type="submit" style="width: 100%; padding: 0.6rem; background: #fff; border: 1px solid #eef2ff; border-radius: 10px; color: #ef4444; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: all 0.2s;">
                    🚪 Đăng xuất
                </button>
            </form>
        </div>

        {{-- Mobile Close Button --}}
        <button onclick="document.getElementById('adminSidebar').classList.remove('open'); document.getElementById('sidebarBackdrop').classList.remove('active')" class="sidebar-close-btn" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; color: #64748b; cursor: pointer; display: none;">&times;</button>
    </aside>

    {{-- Mobile Backdrop --}}
    <div id="sidebarBackdrop" onclick="document.getElementById('adminSidebar').classList.remove('open'); this.classList.remove('active')" class="sidebar-backdrop"></div>

    {{-- Main Content Area --}}
    <div class="admin-content">
        {{-- Top Header --}}
        <div class="admin-header">
            <div style="display: flex; align-items: center; gap: 1.5rem; flex-grow: 1;">
                <button onclick="document.getElementById('adminSidebar').classList.add('open'); document.getElementById('sidebarBackdrop').classList.add('active')"
                        style="display: none; background: #fff; border: 1px solid #f0f3ff; border-radius: 12px; width: 44px; height: 44px; align-items: center; justify-content: center; font-size: 1.25rem; cursor: pointer; color: #1e3a5f; box-shadow: 0 4px 10px rgba(0,0,0,0.02);"
                        id="sidebarToggle">
                    ☰
                </button>
                @yield('header-left')
            </div>

            <div style="display: flex; align-items: center; gap: 1.5rem;">
                @hasSection('header-right')
                    @yield('header-right')
                @else
                    <a href="{{ url('/') }}" style="color: #64748b; text-decoration: none; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; background: #fff; padding: 0.6rem 1.2rem; border-radius: 12px; border: 1px solid #f0f3ff;">
                        🏠 <span>Về trang chủ</span>
                    </a>
                @endif
            </div>
        </div>

        {{-- Flash messages --}}
        <div id="flash-messages">
            @if(session('status'))
                <div class="card" style="margin-bottom: 1.5rem; background: #ecfdf5; border-color: #d1fae5; padding: 1rem 1.5rem; border-radius: 1.25rem;">
                    <span style="color: #059669; font-weight: 600;">✅ {{ session('status') }}</span>
                </div>
            @endif
            @if(session('success'))
                <div class="card" style="margin-bottom: 1.5rem; background: #ecfdf5; border-color: #d1fae5; padding: 1rem 1.5rem; border-radius: 1.25rem;">
                    <span style="color: #059669; font-weight: 600;">✅ {{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="card" style="margin-bottom: 1.5rem; background: #fef2f2; border-color: #fee2e2; padding: 1rem 1.5rem; border-radius: 1.25rem;">
                    <span style="color: #dc2626; font-weight: 600;">❌ {{ session('error') }}</span>
                </div>
            @endif
        </div>

        @if(trim($__env->yieldContent('page-title')))
        <div style="margin-bottom: 2rem;">
            <h1 style="margin: 0; font-size: 1.75rem; font-weight: 800; color: #1e3a5f;">@yield('page-title')</h1>
        </div>
        @endif

        @yield('content')
    </div>

    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const adminSidebar = document.getElementById('adminSidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        if (window.innerWidth <= 1024) {
            sidebarToggle.style.display = 'flex';
        }
        window.addEventListener('resize', function() {
            if (window.innerWidth > 1024) {
                sidebarToggle.style.display = 'none';
                adminSidebar.classList.remove('open');
                sidebarBackdrop.classList.remove('active');
            } else {
                sidebarToggle.style.display = 'flex';
            }
        });
    </script>

    @stack('scripts')

    {{-- Global Toast Box --}}
    <div id="globalToast" style="display: none; position: fixed; bottom: 2rem; right: 2rem; background: #fff; border-radius: 16px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15); border: 1px solid #e2e8f0; z-index: 99999; padding: 1.25rem 1.5rem; width: 420px; max-width: calc(100vw - 4rem); transform: translateY(100px); opacity: 0; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); align-items: center; justify-content: space-between; gap: 1rem; font-family: 'Inter', system-ui, sans-serif;">
        <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1;">
            <div id="toastIcon" style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; font-weight: 700;">
                ✓
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.15rem; flex: 1; text-align: left;">
                <span id="toastMessage" style="font-size: 0.92rem; font-weight: 700; color: #1e293b; line-height: 1.4;">Thông báo</span>
            </div>
        </div>
        

        
        <button onclick="closeToast()" style="background: none; border: none; font-size: 1.2rem; color: #94a3b8; cursor: pointer; padding: 0.25rem; flex-shrink: 0;">✕</button>
    </div>

    <script>
    function closeToast() {
        const toast = document.getElementById('globalToast');
        if (toast) {
            toast.style.transform = 'translateY(100px)';
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 400);
        }
    }

    window.showToastMessage = function(message, type = 'success') {
        const toast = document.getElementById('globalToast');
        const toastIcon = document.getElementById('toastIcon');
        const toastMessage = document.getElementById('toastMessage');

        if (!toast) return;

        toastMessage.textContent = message;
        if (type === 'success') {
            toastIcon.style.background = '#d1fae5';
            toastIcon.style.color = '#10b981';
            toastIcon.innerHTML = '✓';
        } else if (type === 'error') {
            toastIcon.style.background = '#fee2e2';
            toastIcon.style.color = '#ef4444';
            toastIcon.innerHTML = '✕';
        } else if (type === 'warning') {
            toastIcon.style.background = '#fef3c7';
            toastIcon.style.color = '#f59e0b';
            toastIcon.innerHTML = '⚠';
        }
        
        toast.style.display = 'flex';
        setTimeout(() => {
            toast.style.transform = 'translateY(0)';
            toast.style.opacity = '1';
        }, 100);

        setTimeout(() => {
            closeToast();
        }, 5000);
    };

    document.addEventListener('DOMContentLoaded', function() {
        const flashMessages = document.getElementById('flash-messages');
        if (flashMessages) {
            flashMessages.style.display = 'none'; // Hide default flash messages
        }

        @if(session('success'))
            window.showToastMessage({!! json_encode(session('success')) !!}, 'success');
        @elseif(session('status'))
            window.showToastMessage({!! json_encode(session('status')) !!}, 'success');
        @elseif(session('error'))
            window.showToastMessage({!! json_encode(session('error')) !!}, 'error');
        @endif
    });
    </script>

    {{-- Prescription Detail Modal --}}
    <div id="prescriptionDetailModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); z-index: 999999; justify-content: center; align-items: center; padding: 1.5rem;">
        <div style="background: #fff; width: 920px; max-width: 95vw; height: 90vh; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); display: flex; flex-direction: column; overflow: hidden; border: 1px solid #e2e8f0; animation: modalEnter 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
            {{-- Modal Header --}}
            <div style="background: #f8fafc; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0; font-family: 'Inter', system-ui, sans-serif;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span style="font-size: 1.5rem;">💊</span>
                    <div style="text-align: left;">
                        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; color: #1e293b;" id="prescriptionModalTitle">Chi Tiết Đơn Điều Trị</h3>
                        <p style="margin: 0; font-size: 0.78rem; color: #64748b; font-weight: 600;">Xem phác đồ & In phiếu trực tiếp</p>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button onclick="printModalPrescription()" style="background: #2563eb; color: white; border: none; border-radius: 6px; padding: 0.45rem 1.1rem; font-size: 0.85rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 0.3rem; transition: all 0.2s;">
                        🖨️ In Phiếu
                    </button>
                    
                    <button onclick="closePrescriptionModal()" style="background: none; border: none; font-size: 1.25rem; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">✕</button>
                </div>
            </div>
            
            {{-- Modal Body --}}
            <div style="flex: 1; overflow-y: auto; background: #f1f5f9; padding: 2rem 1rem; display: flex; justify-content: center; position: relative;">
                <div id="prescriptionModalSpinner" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.85); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.75rem; z-index: 10;">
                    <svg style="animation: spin 1s linear infinite; width: 2rem; height: 2rem; color: #0d9488;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span style="font-weight: 700; color: #0d9488; font-size: 0.95rem; font-family: sans-serif;">Đang tải thông tin đơn thuốc...</span>
                </div>
                
                <div id="modalPrintView" style="display: block; width: 100%;">
                    <div style="display: flex; justify-content: center;">
                        <div class="paper a4" id="modalPaperDoc" style="background: #fff; border: 1px solid #cbd5e1; box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08); border-radius: 4px; padding: 12mm; font-family: 'Times New Roman', Times, serif; color: #000; transition: width 0.3s, min-height 0.3s; min-height: 297mm; width: 210mm;">
                            {{-- AJAX Content --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes modalEnter {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        
        /* CSS specifically for print-area visibility when printing from modal */
        @media print {
            body.printing-modal #app, body.printing-modal header, body.printing-modal nav, body.printing-modal aside, body.printing-modal .admin-content, body.printing-modal .no-print-area, body.printing-modal .no-print {
                display: none !important;
                visibility: hidden !important;
            }
            body.printing-modal #prescriptionDetailModal {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                height: auto !important;
                background: #fff !important;
                backdrop-filter: none !important;
                display: block !important;
                padding: 0 !important;
            }
            body.printing-modal #prescriptionDetailModal > div {
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }
            body.printing-modal #prescriptionDetailModal > div > div:first-child {
                display: none !important; /* hide modal header */
            }
            body.printing-modal #prescriptionDetailModal > div > div:last-child {
                padding: 0 !important;
                background: #fff !important;
            }
            body.printing-modal #modalPaperDoc {
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            @page {
                size: A4 portrait;
                margin: 5mm;
            }
        }
        
        .sidebar-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.5rem 1.5rem 0.5rem;
            cursor: pointer;
            user-select: none;
        }
        .sidebar-section-header:hover span.section-title {
            color: #94a3b8 !important;
        }
        .sidebar-section-header:hover span.section-arrow {
            color: #64748b !important;
        }
    </style>

    <script>
        let activePrescriptionPrintId = null;
        let activePrescriptionPrintType = 'patient';

        function openPrescriptionModal(prescriptionId, printType = 'patient', autoPrint = false, titleLabel = null) {
            const modal = document.getElementById('prescriptionDetailModal');
            const spinner = document.getElementById('prescriptionModalSpinner');
            const contentArea = document.getElementById('modalPaperDoc');
            const titleText = document.getElementById('prescriptionModalTitle');
            const normalizedType = printType === 'internal' ? 'internal' : 'patient';

            activePrescriptionPrintId = prescriptionId;
            activePrescriptionPrintType = normalizedType;
            
            titleText.textContent = normalizedType === 'internal'
                ? 'Phiếu ' + (titleLabel || 'nội bộ') + ' #' + prescriptionId
                : 'Chi Tiết Đơn Điều Trị #' + prescriptionId;
            contentArea.innerHTML = '';
            spinner.style.display = 'flex';
            modal.style.display = 'flex';
            setModalPaperDefaults();
            
            // Fetch view via ajax
            const url = '/admin/prescriptions/' + prescriptionId + (normalizedType === 'internal' ? '?type=internal' : '');
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Không thể tải dữ liệu đơn.');
                return response.text();
            })
            .then(html => {
                contentArea.innerHTML = html;
                spinner.style.display = 'none';
                if (autoPrint) {
                    setTimeout(() => {
                        printModalPrescription();
                    }, 350);
                }
            })
            .catch(err => {
                contentArea.innerHTML = `<div style="padding: 2rem; text-align: center; color: #b91c1c; font-weight: bold; font-family: sans-serif;">
                    ❌ Lỗi: ${err.message}
                </div>`;
                spinner.style.display = 'none';
            });
        }
        
        function closePrescriptionModal() {
            document.getElementById('prescriptionDetailModal').style.display = 'none';
        }
        
        // Allow close when clicking outside the modal box
        document.getElementById('prescriptionDetailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePrescriptionModal();
            }
        });
        
        function setModalPaperDefaults() {
            const paper = document.getElementById('modalPaperDoc');
            if (paper) {
                paper.style.width = '210mm';
                paper.style.minHeight = '297mm';
                paper.style.padding = '12mm';
            }
            
            let styleTag = document.getElementById('modal-print-page-style');
            if (!styleTag) {
                styleTag = document.createElement('style');
                styleTag.id = 'modal-print-page-style';
                document.head.appendChild(styleTag);
            }
            styleTag.innerHTML = '@media print { @page { size: A4 portrait; margin: 5mm; } }';
        }
        
        function printModalPrescription() {
            if (!activePrescriptionPrintId) {
                return;
            }

            const params = new URLSearchParams({
                type: activePrescriptionPrintType === 'internal' ? 'internal' : 'patient',
                auto_print: '1'
            });

            window.open(`/admin/prescriptions/${activePrescriptionPrintId}/print?${params.toString()}`, '_blank', 'noopener');
        }
        
        // Global Event Listener to capture clicking on prescription show links
        document.addEventListener('click', function(e) {
            // Find if target or any parent is a prescription link
            let element = e.target;
            while (element && element !== document.body) {
                if (element.tagName === 'A' && element.href) {
                    // Match URLs like: /admin/prescriptions/{id} or route('admin.prescriptions.show', {id})
                    // But NOT edit, delete, or print pages
                    const match = element.href.match(/\/admin\/prescriptions\/(\d+)(?:\?type=(patient|internal))?$/);
                    if (match) {
                        e.preventDefault();
                        const prescriptionId = match[1];
                        const printType = match[2] || 'patient';
                        openPrescriptionModal(prescriptionId, printType);
                        return;
                    }
                }
                element = element.parentElement;
            }
        });

        function toggleSidebarSection(sectionId) {
            const section = document.getElementById('section-' + sectionId);
            const arrow = document.getElementById('arrow-' + sectionId);
            if (!section || !arrow) return;

            const isCollapsed = section.style.display === 'none';
            if (isCollapsed) {
                section.style.display = 'block';
                arrow.style.transform = 'rotate(0deg)';
                localStorage.setItem('sidebar_section_' + sectionId, 'expanded');
            } else {
                section.style.display = 'none';
                arrow.style.transform = 'rotate(180deg)';
                localStorage.setItem('sidebar_section_' + sectionId, 'collapsed');
            }
        }

        // Initialize collapse state on DOMContentLoaded
        document.addEventListener('DOMContentLoaded', function() {
            ['clinical-care', 'systems'].forEach(function(sectionId) {
                const section = document.getElementById('section-' + sectionId);
                const arrow = document.getElementById('arrow-' + sectionId);
                if (!section || !arrow) return;

                const savedState = localStorage.getItem('sidebar_section_' + sectionId);
                if (savedState === 'collapsed') {
                    section.style.display = 'none';
                    arrow.style.transform = 'rotate(180deg)';
                } else {
                    section.style.display = 'block';
                    arrow.style.transform = 'rotate(0deg)';
                }
            });
        });
    </script>
</body>
</html>
