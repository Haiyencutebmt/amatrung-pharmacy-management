@extends('layouts.guest')

@section('title', 'Tài khoản — AmaTrung')

@section('content')
<div class="bg-[var(--color-surface-bg)] min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        
        {{-- Header Section --}}
        <div class="bg-gradient-to-br from-primary-900 to-primary-800 rounded-[2.5rem] p-8 md:p-12 text-white shadow-xl mb-8 relative overflow-hidden">
            <!-- Decorative bg -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary-600/30 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-green-500/20 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 bg-white/10 border border-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-3xl font-black shadow-inner">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-primary-200 font-semibold mb-1">Chào mừng quay lại,</p>
                        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">{{ auth()->user()->name }}!</h1>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    @if(auth()->user()->isStaff())
                        <a href="{{ url('/admin/dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-primary-700 font-bold rounded-xl shadow-sm hover:bg-primary-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Trang quản trị
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Main Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            {{-- Info Card --}}
            <div class="md:col-span-1">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 h-full">
                    <h3 class="text-lg font-extrabold text-slate-800 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                        Hồ sơ của bạn
                    </h3>
                    
                    <div class="space-y-5">
                        <div>
                            <p class="text-sm font-semibold text-slate-500 mb-1">Email</p>
                            <p class="font-bold text-slate-800">{{ auth()->user()->email ?? 'Chưa cập nhật' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-500 mb-1">Số điện thoại</p>
                            <p class="font-bold text-slate-800">{{ auth()->user()->phone ?? 'Chưa cập nhật' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-500 mb-2">Vai trò</p>
                            <div>
                                @switch(auth()->user()->role)
                                    @case('admin')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">
                                            Quản trị viên
                                        </span>
                                        @break
                                    @case('staff')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                            Nhân viên
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                            Người dùng
                                        </span>
                                @endswitch
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100">
                        <a href="{{ route('profile.edit') }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-50 text-slate-700 hover:text-primary-700 font-bold border border-slate-200 hover:border-primary-200 hover:bg-primary-50 rounded-xl transition-all shadow-sm">
                            Cập nhật thông tin
                        </a>
                    </div>
                </div>
            </div>

            {{-- Quick Actions & Content --}}
            <div class="md:col-span-2 space-y-8">
                
                {{-- Quick Actions --}}
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ url('/kien-thuc') }}" class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all group flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <h4 class="font-bold text-slate-800 mb-1">Kiến thức y khoa</h4>
                        <p class="text-xs text-slate-500">Đọc các bài viết mới nhất từ chuyên gia.</p>
                    </a>

                    <a href="{{ url('/tu-dien-thuoc-nam') }}" class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all group flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-green-50 text-green-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <h4 class="font-bold text-slate-800 mb-1">Từ điển Dược liệu</h4>
                        <p class="text-xs text-slate-500">Tra cứu nhanh hơn 1000+ loại thảo dược.</p>
                    </a>
                </div>

                {{-- Dashboard Notice --}}
                <div class="bg-gradient-to-r from-primary-50 to-emerald-50 rounded-[2rem] p-8 border border-primary-100/50">
                    <h3 class="text-xl font-bold text-primary-800 mb-3 flex items-center gap-2">
                        <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Trải nghiệm sức khỏe thông minh
                    </h3>
                    <p class="text-primary-700/80 leading-relaxed mb-6">
                        Chúng tôi đang liên tục nâng cấp hệ thống để mang lại trải nghiệm y tế trực tuyến tốt nhất cho bạn. Lịch sử khám bệnh và toa thuốc điện tử sẽ sớm được cập nhật tại đây.
                    </p>
                    <a href="{{ url('/lien-he') }}" class="inline-block px-5 py-2.5 bg-white text-primary-600 font-bold border border-primary-200 rounded-xl hover:bg-primary-50 transition-colors shadow-sm">
                        Liên hệ hỗ trợ
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
