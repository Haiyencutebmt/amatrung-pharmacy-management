@extends('layouts.admin')

@section('title', 'Dashboard — AmaTrung')
@section('page-title', 'Hệ Thống Quản Trị')

@section('content')
{{-- Layout Grid --}}
<div class="dashboard-grid" style="display: grid; gap: 2rem; align-items: start;">
    
    {{-- Left Side: Main Analytics --}}
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        
        {{-- Big Overview Card (Simulation) --}}
        <div class="card" style="position: relative; overflow: hidden; min-height: 400px; display: flex; flex-direction: column;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div>
                    <h3 style="margin: 0; font-size: 1.25rem;">Biểu đồ tăng trưởng</h3>
                    <p style="margin: 0.25rem 0 0; color: #94a3b8; font-size: 0.85rem;">Thống kê lượt khám & bệnh nhân mới</p>
                </div>
                <div style="background: var(--color-primary-50); padding: 0.5rem 1rem; border-radius: 0.75rem; font-size: 0.8rem; font-weight: 700; color: var(--color-primary-600);">
                    Tháng {{ now()->format('m/Y') }}
                </div>
            </div>
            
            {{-- Visual Chart Simulation (SVG) --}}
            <div style="flex-grow: 1; display: flex; align-items: flex-end; padding-bottom: 1rem; position: relative;">
                <svg viewBox="0 0 800 200" style="width: 100%; height: 200px;">
                    <defs>
                        <linearGradient id="grad1" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" style="stop-color:rgba(22, 163, 74, 0.2);stop-opacity:1" />
                            <stop offset="100%" style="stop-color:rgba(22, 163, 74, 0);stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <path d="M0,180 Q100,150 200,160 T400,100 T600,120 T800,50" fill="none" stroke="var(--color-primary-600)" stroke-width="4" stroke-linecap="round" />
                    <path d="M0,180 Q100,150 200,160 T400,100 T600,120 T800,50 L800,200 L0,200 Z" fill="url(#grad1)" />
                    
                    <path d="M0,190 Q150,170 300,180 T600,140 T800,110" fill="none" stroke="#06b6d4" stroke-width="3" stroke-dasharray="8,5" stroke-linecap="round" opacity="0.5" />
                </svg>
                
                {{-- Legend --}}
                <div style="position: absolute; bottom: 0; left: 0; display: flex; gap: 1.5rem; font-size: 0.75rem; font-weight: 700; color: #94a3b8;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: var(--color-primary-600);"></span> Lượt khám
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #06b6d4;"></span> Bệnh nhân mới
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="card">
            <h3 style="margin: 0 0 1.5rem; font-size: 1.1rem;">Thao tác nhanh</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                <a href="{{ route('admin.patients.create') }}" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem; background: var(--color-primary-50); border-radius: 1.5rem; transition: all 0.3s;" onmouseover="this.style.background='var(--color-primary-100)'" onmouseout="this.style.background='var(--color-primary-50)'">
                    <span style="font-size: 2rem;">👤</span>
                    <span style="font-size: 0.85rem; font-weight: 700; color: var(--color-primary-600);">Thêm bệnh nhân</span>
                </a>
                <a href="{{ route('admin.medical-records.create') }}" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem; background: #f5f3ff; border-radius: 1.5rem; transition: all 0.3s;" onmouseover="this.style.background='#ede9fe'" onmouseout="this.style.background='#f5f3ff'">
                    <span style="font-size: 2rem;">📋</span>
                    <span style="font-size: 0.85rem; font-weight: 700; color: #7c3aed;">Tạo bệnh án</span>
                </a>
                <a href="{{ route('admin.warehouse.index') }}" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem; background: #ecfdf5; border-radius: 1.5rem; transition: all 0.3s;" onmouseover="this.style.background='#d1fae5'" onmouseout="this.style.background='#ecfdf5'">
                    <span style="font-size: 2rem;">🏪</span>
                    <span style="font-size: 0.85rem; font-weight: 700; color: #059669;">Quản lý kho</span>
                </a>
                <a href="{{ route('admin.articles.create') }}" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem; background: #fff7ed; border-radius: 1.5rem; transition: all 0.3s;" onmouseover="this.style.background='#ffedd5'" onmouseout="this.style.background='#fff7ed'">
                    <span style="font-size: 2rem;">📰</span>
                    <span style="font-size: 0.85rem; font-weight: 700; color: #ea580c;">Viết bài mới</span>
                </a>
            </div>
        </div>

    </div>

    {{-- Right Side: Statistics --}}
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <div class="card" style="padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="width: 48px; height: 48px; background: #fdf2f8; color: #db2777; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">👥</div>
                <div>
                    <div style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Tổng bệnh nhân</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #1e3a5f;">{{ $stats['patients'] }}</div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="width: 48px; height: 48px; background: var(--color-primary-50); color: var(--color-primary-600); border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">📋</div>
                <div>
                    <div style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Hồ sơ bệnh án</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #1e3a5f;">{{ $stats['medical_records'] }}</div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; background: #ecfeff; color: #0891b2; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">🌿</div>
                <div>
                    <div style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Dược liệu trong kho</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #1e3a5f;">{{ $stats['herbs'] }}</div>
                </div>
            </div>
        </div>

        <div class="card" style="padding: 1.5rem; background: linear-gradient(135deg, var(--color-primary-600), var(--color-primary-500)); color: #fff; border: none;">
            <h4 style="color: rgba(255,255,255,0.8); margin: 0 0 0.5rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">Thông báo hệ thống</h4>
            <p style="margin: 0; font-size: 1rem; font-weight: 600; line-height: 1.4;">Bạn có <span style="background: rgba(255,255,255,0.2); padding: 0.1rem 0.5rem; border-radius: 0.5rem;">{{ $stats['comments_pending'] }}</span> bình luận đang chờ phê duyệt.</p>
            <a href="{{ route('admin.comments.index') }}" style="display: inline-block; margin-top: 1.5rem; color: #fff; text-decoration: none; font-size: 0.85rem; font-weight: 700; border-bottom: 1px solid rgba(255,255,255,0.4);">
                Xem danh sách →
            </a>
        </div>

        <div class="card" style="padding: 1.5rem;">
            <h4 style="margin: 0 0 1rem; font-size: 0.9rem; color: #1e3a5f;">Trạng thái kho dược</h4>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.85rem; color: #64748b;">Sắp hết hàng</span>
                    <span class="badge badge-warning">{{ $stats['herbs_low'] }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.85rem; color: #64748b;">Đã hết hàng</span>
                    <span class="badge badge-danger">{{ $stats['herbs_out'] }}</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

