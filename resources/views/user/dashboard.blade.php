@extends('layouts.guest')

@section('title', 'Bảng điều khiển — AmaTrung')

@section('content')
@php
    $linkFeatureAvailable = $linkFeatureAvailable ?? false;
    $linkedPatients = $linkedPatients ?? collect();
    $pendingPatientLinks = $pendingPatientLinks ?? collect();
    $matchingPatients = $matchingPatients ?? collect();
    $medicalRecords = $medicalRecords ?? collect();
    $recentPrescriptions = $recentPrescriptions ?? collect();
@endphp
<div class="bg-[var(--color-surface-bg)] min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-[1400px] mx-auto space-y-8">

        {{-- Traditional Blue Mountain Header Banner --}}
        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-blue-600 rounded-[2.5rem] p-8 md:p-12 text-white shadow-xl relative overflow-hidden min-h-[180px]">
            <!-- Mountains and clouds SVG decoration background -->
            <svg class="absolute bottom-0 right-0 h-full w-full pointer-events-none opacity-25" viewBox="0 0 800 300" fill="none" stroke="white" stroke-width="1.5">
                <path d="M 0 280 L 150 150 L 300 260 L 450 120 L 600 240 L 700 160 L 800 250" />
                <path d="M 100 290 L 250 180 L 400 270 L 520 160 L 650 260 L 750 190 L 800 230" opacity="0.6" />
                <path d="M 0 295 Q 50 280 100 295 Q 150 280 200 295 Q 250 280 300 295 Q 350 280 400 295 Q 450 280 500 295 Q 550 280 600 295 Q 650 280 700 295 Q 750 280 800 295" />
            </svg>

            <!-- Mortar Outline SVG decoration background on the right -->
            <div class="absolute right-12 bottom-2 h-44 w-44 hidden md:block pointer-events-none opacity-15">
                <svg class="w-full h-full text-white" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M 20 60 C 20 85, 80 85, 80 60 L 75 40 L 25 40 Z" />
                    <path d="M 65 20 L 48 55" stroke-linecap="round" stroke-width="4" />
                    <path d="M 30 35 Q 35 25 45 35 M 25 38 Q 30 28 35 38" stroke-width="1.5" />
                </svg>
            </div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <!-- Avatar circle with custom Cloud ornament at the bottom -->
                    <div class="relative shrink-0">
                        <div class="w-24 h-24 bg-blue-500/20 border-4 border-white/80 rounded-full flex items-center justify-center text-3xl font-black shadow-lg overflow-hidden">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                {{ mb_substr(auth()->user()->name, 0, 1) }}
                            @endif
                        </div>
                        <!-- Traditional cloud motif at the bottom of the avatar -->
                        <svg class="absolute -bottom-2.5 left-1/2 -translate-x-1/2 w-16 h-7 text-white/90 pointer-events-none" viewBox="0 0 100 40" fill="currentColor">
                            <path d="M 20,30 C 15,30 10,27 10,23 C 10,18 16,15 22,17 C 25,11 35,9 40,14 C 45,10 55,10 60,15 C 65,12 72,14 74,19 C 80,18 85,22 85,27 C 85,32 80,35 75,34 C 72,36 65,36 62,33 C 58,36 48,36 44,33 C 40,36 30,36 26,33 C 24,34 22,34 20,30 Z" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-blue-100 font-medium mb-0.5 text-base md:text-lg">Chào mừng quay lại,</p>
                        <h1 class="text-white text-3xl md:text-4xl font-black tracking-tight flex items-center gap-2">
                            {{ auth()->user()->name }}! <span class="text-2xl md:text-3xl">🍃</span>
                        </h1>
                        <p class="text-blue-50/95 text-xs md:text-sm leading-relaxed max-w-2xl mt-1.5 font-medium">
                            Quản lý thông tin sức khỏe và khám phá tri thức y học cổ truyền để chăm sóc bản thân và gia đình tốt hơn mỗi ngày.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dashboard Content Grid - Align Items Start to prevent stretching --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            {{-- Left Column: "Hồ sơ của bạn" Card --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 relative overflow-hidden flex flex-col justify-between">
                    <!-- Subtle foliage/leaves background at bottom-left -->
                    <svg viewBox="0 0 120 120" class="absolute bottom-0 left-0 w-28 h-28 text-sky-100/30 pointer-events-none" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M 0,120 Q 50,90 90,40" />
                        <path d="M 25,105 Q 20,85 10,90 Q 15,108 25,105" fill="currentColor" opacity="0.4" />
                        <path d="M 45,90 Q 40,70 30,75 Q 35,93 45,90" fill="currentColor" opacity="0.4" />
                        <path d="M 65,70 Q 70,50 60,45 Q 55,63 65,70" fill="currentColor" opacity="0.4" />
                        <path d="M 80,50 Q 90,30 80,25 Q 73,43 80,50" fill="currentColor" opacity="0.4" />
                    </svg>

                    <div>
                        <h3 class="text-lg font-extrabold text-slate-800 mb-8 flex items-center gap-3">
                            <span class="w-9 h-9 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </span>
                            Hồ sơ của bạn
                        </h3>

                        <div class="space-y-6">
                            {{-- Email --}}
                            <div class="flex items-start gap-4">
                                <span class="w-9 h-9 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </span>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Email</p>
                                    <p class="text-sm font-semibold text-slate-700 break-all mt-0.5">{{ auth()->user()->email }}</p>
                                </div>
                            </div>

                            {{-- Phone --}}
                            <div class="flex items-start gap-4">
                                <span class="w-9 h-9 rounded-xl bg-green-50 text-green-500 flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </span>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Số điện thoại</p>
                                    <p class="text-sm font-semibold text-slate-700 mt-0.5">{{ auth()->user()->phone ?? 'Chưa cập nhật' }}</p>
                                </div>
                            </div>

                            {{-- Role --}}
                            <div class="flex items-start gap-4">
                                <span class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                </span>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Vai trò</p>
                                    @php
                                        $roleLabels = [
                                            'admin' => 'Quản trị viên',
                                            'staff' => 'Nhân viên',
                                            'practitioner' => 'Bác sĩ',
                                            'user' => 'Người dùng'
                                        ];
                                        $roleLabel = $roleLabels[auth()->user()->role] ?? 'Người dùng';
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 mt-1 shadow-sm border border-green-100">
                                        {{ $roleLabel }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 relative z-10">
                        {{-- Dashed line --}}
                        <div class="border-t border-dashed border-slate-200 my-6"></div>

                        {{-- Action Buttons --}}
                        <div class="space-y-3.5">
                            <button onclick="openModal('info')" class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-md shadow-blue-500/10 hover:shadow-lg transition-all cursor-pointer text-sm">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Cập nhật thông tin
                            </button>
                            @if(auth()->user()->isAdmin() || auth()->user()->hasRole('admin'))
                                <a href="{{ route('admin.dashboard') }}" class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-slate-900 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-md shadow-slate-900/10 hover:shadow-lg transition-all text-sm">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 10.5L12 4l9 6.5M5 10v9h14v-9M9 19v-5h6v5"></path></svg>
                                    Vào trang quản trị
                                </a>
                            @endif
                            <button onclick="openModal('info')" class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-white border border-slate-200 hover:border-blue-200 text-slate-600 hover:text-blue-600 font-bold rounded-2xl transition-all cursor-pointer shadow-sm text-sm">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                Xem hồ sơ
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: 2x2 Grid & Notice Banner --}}
            <div class="lg:col-span-2 flex flex-col gap-6">
                
                {{-- 2x2 Grid of Actions --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    
                    {{-- Card 1: Kiến thức y khoa --}}
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all group relative overflow-hidden flex flex-col justify-between min-h-[150px]">
                        <div>
                            <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            <h4 class="font-extrabold text-slate-800 mb-1.5 text-base">Kiến thức y khoa</h4>
                            <p class="text-xs text-slate-500 leading-relaxed max-w-[90%]">Đọc các bài viết mới nhất từ chuyên gia y học cổ truyền.</p>
                        </div>
                        <a href="{{ url('/bai-viet') }}" class="text-xs font-bold text-blue-600 flex items-center gap-1 hover:gap-2 transition-all mt-4 relative z-10">
                            Khám phá ngay <span class="text-sm">→</span>
                        </a>
                        
                        <!-- Traditional cloud motif at bottom-right -->
                        <svg class="absolute bottom-0 right-0 w-16 h-8 text-sky-200/20 pointer-events-none" viewBox="0 0 100 50" fill="currentColor">
                            <path d="M 20,35 C 15,35 10,32 10,27 C 10,21 16,18 22,20 C 25,14 35,12 40,17 C 45,13 55,13 60,18 C 65,15 72,17 74,22 C 80,21 85,25 85,30 C 85,35 80,38 75,37 C 72,39 65,39 62,36 C 58,39 48,39 44,36 C 40,39 30,39 26,36 C 24,37 22,37 20,35 Z" />
                        </svg>
                    </div>

                    {{-- Card 2: Từ điển dược liệu --}}
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all group relative overflow-hidden flex flex-col justify-between min-h-[150px]">
                        <div>
                            <div class="w-12 h-12 bg-green-50 text-green-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3a9 9 0 00-9 9c0 5.523 4.477 10 10 10a9 9 0 009-9M12 3a9 9 0 019 9c0 5.523-4.477 10-10 10m0-19v19"></path></svg>
                            </div>
                            <h4 class="font-extrabold text-slate-800 mb-1.5 text-base">Từ điển dược liệu</h4>
                            <p class="text-xs text-slate-500 leading-relaxed max-w-[90%]">Tra cứu hơn 1000+ loại thảo dược và công dụng chi tiết.</p>
                        </div>
                        <a href="{{ url('/tu-dien-thuoc-nam') }}" class="text-xs font-bold text-green-600 flex items-center gap-1 hover:gap-2 transition-all mt-4 relative z-10">
                            Tra cứu ngay <span class="text-sm">→</span>
                        </a>

                        <!-- Traditional cloud motif at bottom-right -->
                        <svg class="absolute bottom-0 right-0 w-16 h-8 text-sky-200/20 pointer-events-none" viewBox="0 0 100 50" fill="currentColor">
                            <path d="M 20,35 C 15,35 10,32 10,27 C 10,21 16,18 22,20 C 25,14 35,12 40,17 C 45,13 55,13 60,18 C 65,15 72,17 74,22 C 80,21 85,25 85,30 C 85,35 80,38 75,37 C 72,39 65,39 62,36 C 58,39 48,39 44,36 C 40,39 30,39 26,36 C 24,37 22,37 20,35 Z" />
                        </svg>
                    </div>

                    {{-- Card 3: Lịch sử khám bệnh --}}
                    <div onclick="scrollToPatientSection('{{ $linkedPatients->isNotEmpty() ? 'medical-history-section' : 'patient-sync-section' }}')" class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all group relative overflow-hidden flex flex-col justify-between min-h-[150px] cursor-pointer">
                        <div>
                            <div class="w-12 h-12 bg-purple-50 text-purple-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            </div>
                            <h4 class="font-extrabold text-slate-800 mb-1.5 text-base">Lịch sử khám bệnh</h4>
                            <p class="text-xs text-slate-500 leading-relaxed max-w-[90%]">
                                @if($linkedPatients->isNotEmpty())
                                    Đang liên kết {{ $linkedPatients->count() }} hồ sơ, có {{ $medicalRecords->count() }} lượt khám gần đây.
                                @else
                                    Đồng bộ bằng số điện thoại để xem lịch sử khám được phép hiển thị.
                                @endif
                            </p>
                        </div>
                        <span class="text-xs font-bold text-purple-600 flex items-center gap-1 hover:gap-2 transition-all mt-4 relative z-10">
                            {{ $linkedPatients->isNotEmpty() ? 'Xem lịch sử' : 'Đồng bộ hồ sơ' }} <span class="text-sm">→</span>
                        </span>

                        <!-- Traditional cloud motif at bottom-right -->
                        <svg class="absolute bottom-0 right-0 w-16 h-8 text-sky-200/20 pointer-events-none" viewBox="0 0 100 50" fill="currentColor">
                            <path d="M 20,35 C 15,35 10,32 10,27 C 10,21 16,18 22,20 C 25,14 35,12 40,17 C 45,13 55,13 60,18 C 65,15 72,17 74,22 C 80,21 85,25 85,30 C 85,35 80,38 75,37 C 72,39 65,39 62,36 C 58,39 48,39 44,36 C 40,39 30,39 26,36 C 24,37 22,37 20,35 Z" />
                        </svg>
                    </div>

                    {{-- Card 4: Toa thuốc gần đây --}}
                    <div onclick="scrollToPatientSection('{{ $linkedPatients->isNotEmpty() ? 'recent-prescriptions-section' : 'patient-sync-section' }}')" class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all group relative overflow-hidden flex flex-col justify-between min-h-[150px] cursor-pointer">
                        <div>
                            <div class="w-12 h-12 bg-orange-50 text-orange-500 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 9.172V5L8 4z"></path></svg>
                            </div>
                            <h4 class="font-extrabold text-slate-800 mb-1.5 text-base">Toa thuốc gần đây</h4>
                            <p class="text-xs text-slate-500 leading-relaxed max-w-[90%]">
                                @if($linkedPatients->isNotEmpty())
                                    Hiển thị {{ $recentPrescriptions->count() }} toa đã xác nhận/cấp thuốc gần đây.
                                @else
                                    Sau khi xác minh hồ sơ, toa thuốc gần đây sẽ hiển thị tại đây.
                                @endif
                            </p>
                        </div>
                        <span class="text-xs font-bold text-orange-600 flex items-center gap-1 hover:gap-2 transition-all mt-4 relative z-10">
                            {{ $linkedPatients->isNotEmpty() ? 'Xem toa thuốc' : 'Đồng bộ hồ sơ' }} <span class="text-sm">→</span>
                        </span>

                        <!-- Traditional cloud motif at bottom-right -->
                        <svg class="absolute bottom-0 right-0 w-16 h-8 text-sky-200/20 pointer-events-none" viewBox="0 0 100 50" fill="currentColor">
                            <path d="M 20,35 C 15,35 10,32 10,27 C 10,21 16,18 22,20 C 25,14 35,12 40,17 C 45,13 55,13 60,18 C 65,15 72,17 74,22 C 80,21 85,25 85,30 C 85,35 80,38 75,37 C 72,39 65,39 62,36 C 58,39 48,39 44,36 C 40,39 30,39 26,36 C 24,37 22,37 20,35 Z" />
                        </svg>
                    </div>
                </div>

                {{-- Patient Medical Data Sync --}}
                @if($linkFeatureAvailable)
                    <section id="patient-sync-section" class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-6 md:p-7">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-6">
                            <div class="flex items-start gap-4">
                                <span class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </span>
                                <div>
                                    <h3 class="text-lg md:text-xl font-black text-slate-800">Đồng bộ hồ sơ khám bệnh</h3>
                                    <p class="text-sm text-slate-500 leading-relaxed mt-1 max-w-2xl">
                                        Hệ thống chỉ hiển thị lịch sử khám và toa thuốc khi hồ sơ bệnh nhân đã được liên kết an toàn với tài khoản của bạn.
                                    </p>
                                </div>
                            </div>
                            @if($linkedPatients->isNotEmpty())
                                <span class="inline-flex items-center justify-center px-4 py-2 rounded-2xl bg-emerald-50 text-emerald-700 text-xs font-extrabold border border-emerald-100">
                                    {{ $linkedPatients->count() }} hồ sơ đã xác minh
                                </span>
                            @endif
                        </div>

                        @if(session('status'))
                            <div class="mb-5 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-5 rounded-2xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if(!auth()->user()->phone)
                            <div class="rounded-3xl bg-slate-50 border border-dashed border-slate-200 p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div>
                                    <p class="font-extrabold text-slate-800">Bạn chưa cập nhật số điện thoại.</p>
                                    <p class="text-sm text-slate-500 mt-1">Vui lòng cập nhật số điện thoại để hệ thống tìm hồ sơ bệnh nhân phù hợp.</p>
                                </div>
                                <button onclick="openModal('info')" class="px-5 py-3 rounded-2xl bg-blue-600 text-white text-sm font-bold shadow-md shadow-blue-500/10 hover:bg-blue-700 transition">
                                    Cập nhật số điện thoại
                                </button>
                            </div>
                        @else
                            <div class="space-y-4">
                                @if($linkedPatients->isNotEmpty())
                                    <div class="rounded-3xl bg-emerald-50/70 border border-emerald-100 p-5">
                                        <p class="text-sm font-extrabold text-emerald-800 mb-3">Hồ sơ đang được hiển thị</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($linkedPatients as $patient)
                                                <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl bg-white border border-emerald-100 text-sm font-bold text-slate-700 shadow-sm">
                                                    <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs">{{ mb_substr($patient->full_name, 0, 1) }}</span>
                                                    {{ $patient->full_name }}
                                                    <span class="text-xs font-semibold text-slate-400">{{ $patient->patient_code }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($pendingPatientLinks->isNotEmpty())
                                    <div class="rounded-3xl bg-amber-50 border border-amber-100 p-5">
                                        <p class="text-sm font-extrabold text-amber-800 mb-3">Yêu cầu đang chờ xác minh</p>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            @foreach($pendingPatientLinks as $link)
                                                @if($link->patient)
                                                    <div class="rounded-2xl bg-white border border-amber-100 px-4 py-3">
                                                        <p class="font-bold text-slate-800">{{ $link->patient->full_name }} <span class="text-xs text-slate-400">({{ $link->patient->patient_code }})</span></p>
                                                        <p class="text-xs text-amber-700 mt-1">Nhà thuốc sẽ xác minh trước khi mở lịch sử khám.</p>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($matchingPatients->isNotEmpty())
                                    <div class="rounded-3xl bg-blue-50/60 border border-blue-100 p-5">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                                            <div>
                                                <p class="text-sm font-extrabold text-blue-900">Tìm thấy hồ sơ có số điện thoại phù hợp</p>
                                                <p class="text-xs text-blue-700/80 mt-1">Chọn đúng hồ sơ của bạn hoặc người thân để gửi yêu cầu liên kết.</p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            @foreach($matchingPatients as $patient)
                                                <form method="POST" action="{{ route('profile.patient-link') }}" class="rounded-2xl bg-white border border-blue-100 p-4 shadow-sm flex items-center justify-between gap-3">
                                                    @csrf
                                                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                                                    <div>
                                                        <p class="font-extrabold text-slate-800">{{ $patient->masked_name }}</p>
                                                        <p class="text-xs text-slate-500 mt-1">{{ $patient->patient_code }} · {{ $patient->matched_relation_label }}</p>
                                                    </div>
                                                    <button type="submit" class="shrink-0 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold shadow-md shadow-blue-500/10 transition">
                                                        Liên kết
                                                    </button>
                                                </form>
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif($linkedPatients->isEmpty() && $pendingPatientLinks->isEmpty())
                                    <div class="rounded-3xl bg-slate-50 border border-dashed border-slate-200 p-5">
                                        <p class="font-extrabold text-slate-800">Chưa tìm thấy hồ sơ phù hợp với số điện thoại hiện tại.</p>
                                        <p class="text-sm text-slate-500 mt-1">Nếu bạn từng khám tại AmaTrung, hãy kiểm tra lại số điện thoại hoặc liên hệ nhà thuốc để được hỗ trợ liên kết hồ sơ.</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </section>
                @endif

                @if($linkedPatients->isNotEmpty())
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                        <section id="medical-history-section" class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-6 md:p-7">
                            <div class="flex items-center justify-between gap-4 mb-5">
                                <div>
                                    <h3 class="text-lg font-black text-slate-800">Lịch sử khám bệnh</h3>
                                    <p class="text-sm text-slate-500 mt-1">Các lượt khám gần đây được phép hiển thị.</p>
                                </div>
                                <span class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center">
                                    <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"></path></svg>
                                </span>
                            </div>

                            <div class="space-y-3">
                                @forelse($medicalRecords as $record)
                                    <div class="rounded-3xl border border-slate-100 bg-slate-50/60 p-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <p class="font-extrabold text-slate-800">{{ $record->patient?->full_name }}</p>
                                                <p class="text-xs text-slate-400 mt-1">{{ $record->record_code }} · {{ optional($record->visit_date)->format('d/m/Y') ?? optional($record->created_at)->format('d/m/Y') }}</p>
                                            </div>
                                            <span class="px-3 py-1 rounded-full bg-white border border-slate-100 text-[11px] font-bold text-slate-500">
                                                {{ $record->staff?->name ?? 'AmaTrung' }}
                                            </span>
                                        </div>
                                        <div class="mt-3 grid grid-cols-1 gap-2 text-sm">
                                            <p class="text-slate-600"><span class="font-bold text-slate-800">Chẩn đoán:</span> {{ \Illuminate\Support\Str::limit($record->diagnosis ?: 'Chưa ghi nhận', 90) }}</p>
                                            <p class="text-slate-600"><span class="font-bold text-slate-800">Triệu chứng:</span> {{ \Illuminate\Support\Str::limit($record->symptoms ?: 'Chưa ghi nhận', 110) }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-3xl border border-dashed border-slate-200 bg-slate-50 p-5 text-center text-sm font-semibold text-slate-500">
                                        Chưa có lượt khám nào được hiển thị.
                                    </div>
                                @endforelse
                            </div>
                        </section>

                        <section id="recent-prescriptions-section" class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-6 md:p-7">
                            <div class="flex items-center justify-between gap-4 mb-5">
                                <div>
                                    <h3 class="text-lg font-black text-slate-800">Toa thuốc gần đây</h3>
                                    <p class="text-sm text-slate-500 mt-1">Chỉ hiển thị toa đã xác nhận hoặc đã cấp thuốc.</p>
                                </div>
                                <span class="w-11 h-11 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center">
                                    <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 9.172V5L8 4z"></path></svg>
                                </span>
                            </div>

                            <div class="space-y-3">
                                @forelse($recentPrescriptions as $prescription)
                                    @php
                                        $statusLabel = $prescription->status === 'dispensed' ? 'Đã cấp thuốc' : 'Đã xác nhận';
                                        $statusClass = $prescription->status === 'dispensed' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-blue-50 text-blue-700 border-blue-100';
                                    @endphp
                                    <div class="rounded-3xl border border-slate-100 bg-slate-50/60 p-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <p class="font-extrabold text-slate-800">Đơn #{{ $prescription->id }}</p>
                                                <p class="text-xs text-slate-400 mt-1">
                                                    {{ $prescription->medicalRecord?->patient?->full_name }} · {{ optional($prescription->created_at)->format('d/m/Y') }}
                                                </p>
                                            </div>
                                            <span class="px-3 py-1 rounded-full border text-[11px] font-extrabold {{ $statusClass }}">{{ $statusLabel }}</span>
                                        </div>

                                        <div class="mt-3 space-y-2 text-sm text-slate-600">
                                            <p><span class="font-bold text-slate-800">Bài thuốc:</span> {{ \Illuminate\Support\Str::limit($prescription->note ?: 'Bài thuốc sắc gia giảm', 90) }}</p>
                                            <p><span class="font-bold text-slate-800">Hướng dẫn:</span> {{ \Illuminate\Support\Str::limit($prescription->public_instruction ?: $prescription->usage_instruction ?: 'Dùng thuốc theo hướng dẫn của thầy thuốc.', 120) }}</p>
                                            @if($prescription->follow_up_date)
                                                <p><span class="font-bold text-slate-800">Tái khám:</span> {{ $prescription->follow_up_date->format('d/m/Y') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-3xl border border-dashed border-slate-200 bg-slate-50 p-5 text-center text-sm font-semibold text-slate-500">
                                        Chưa có toa thuốc nào được hiển thị.
                                    </div>
                                @endforelse
                            </div>
                        </section>
                    </div>
                @endif

                {{-- Bottom Banner: Trải nghiệm sức khỏe thông minh --}}
                <div class="bg-gradient-to-r from-blue-50/70 via-sky-50/50 to-emerald-50/40 border border-slate-100 rounded-[2.5rem] p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden shadow-sm">
                    
                    <!-- Lotus Mascot Water Drop (Left) -->
                    <div class="w-32 h-32 md:w-36 md:h-36 shrink-0 relative z-10 animate-float flex items-center justify-center">
                        <img src="{{ asset('images/user_web/water_drop_mascot.png') }}" alt="Mascot" class="w-full h-full object-contain">
                    </div>

                    <!-- Middle Content -->
                    <div class="flex-grow md:px-4 relative z-10 text-center md:text-left">
                        <h3 class="text-xl md:text-2xl font-black text-slate-800 flex items-center justify-center md:justify-start gap-2 mb-3">
                            Trải nghiệm sức khỏe thông minh
                            <svg class="w-6 h-6 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        </h3>
                        <p class="text-slate-600/90 text-sm leading-relaxed max-w-xl">
                            AmaTrung đồng hành cùng bạn trên hành trình chăm sóc sức khỏe toàn diện bằng tri thức y học cổ truyền. Hãy khám phá, lưu lại thông tin quan trọng và liên hệ với chúng tôi bất cứ khi nào bạn cần hỗ trợ.
                        </p>
                    </div>

                    <!-- Right Stacked Buttons -->
                    <div class="shrink-0 flex flex-col gap-3 w-full md:w-auto relative z-10">
                        <a href="{{ url('/lien-he') }}" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-md shadow-blue-500/10 hover:shadow-lg transition-all text-sm">
                            <!-- Headset Support Icon -->
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Liên hệ hỗ trợ
                        </a>
                        <button onclick="document.getElementById('chatbot-toggle').click();" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-white border border-slate-200 hover:border-blue-200 text-slate-700 hover:text-blue-600 font-bold rounded-2xl transition-all cursor-pointer shadow-sm text-sm">
                            <!-- Chat Bubble Icon -->
                            <svg class="w-4.5 h-4.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            Tư vấn ngay
                        </button>
                    </div>

                    <!-- Mortar and Flowers Decoration (Bottom Right) -->
                    <div class="absolute right-0 bottom-0 w-28 h-28 pointer-events-none opacity-85">
                        <img src="{{ asset('images/user_web/mortar_flowers.png') }}" alt="Decoration" class="w-full h-full object-contain object-bottom object-right">
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

{{-- Profile Edit & Password Change Modal - Wider (max-w-3xl) and shorter setup --}}
<div id="profile-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-fade-in">
    <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-3xl w-full overflow-hidden border border-slate-100 flex flex-col animate-scale-up">
        
        <!-- Modal Tab Header -->
        <div class="px-8 pt-6 pb-2 border-b border-slate-100 flex items-center justify-between">
            <div class="flex gap-6">
                <button id="tab-info-btn" onclick="switchTab('info')" class="pb-3 border-b-2 font-bold text-base transition-colors focus:outline-none cursor-pointer">
                    Thông tin cá nhân
                </button>
                <button id="tab-pwd-btn" onclick="switchTab('pwd')" class="pb-3 border-b-2 font-bold text-base transition-colors focus:outline-none cursor-pointer">
                    Thay đổi mật khẩu
                </button>
            </div>
            <button onclick="closeModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-600 flex items-center justify-center transition-all cursor-pointer focus:outline-none">
                ✕
            </button>
        </div>

        <div class="p-8 max-h-[75vh] overflow-y-auto custom-scrollbar">
            
            {{-- Tab 1: Personal Info Form (Wider 3-column inputs) --}}
            <div id="form-info-section" class="space-y-6">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Avatar Upload Section --}}
                    <div class="flex flex-col items-center gap-2 mb-4">
                        <div class="relative group cursor-pointer" onclick="document.getElementById('avatar-input').click()">
                            <div class="w-20 h-20 rounded-full border-4 border-blue-50 overflow-hidden shadow-sm relative bg-slate-50 flex items-center justify-center text-2xl font-black text-slate-400">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" id="avatar-preview" alt="Avatar" class="w-full h-full object-cover">
                                @else
                                    <span id="avatar-preview-text">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                                    <img src="" id="avatar-preview" class="w-full h-full object-cover hidden">
                                @endif
                            </div>
                            <div class="absolute inset-0 bg-black/40 rounded-full opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                        </div>
                        <input type="file" name="avatar" id="avatar-input" class="hidden" accept="image/*" onchange="previewAvatar(event)">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Tải ảnh đại diện</p>
                        @error('avatar') <p class="text-xs text-red-500 font-semibold">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Địa chỉ Email</label>
                            <input type="email" class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-slate-500 font-semibold cursor-not-allowed text-sm" value="{{ auth()->user()->email }}" disabled>
                            <p class="text-[10px] text-slate-400 mt-1 font-medium">Không thể thay đổi.</p>
                        </div>

                        <div>
                            <label for="name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Họ và tên</label>
                            <input type="text" id="name" name="name" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-semibold text-slate-800 text-sm" value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name') <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Số điện thoại</label>
                            <input type="tel" id="phone" name="phone" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-semibold text-slate-800 text-sm" value="{{ old('phone', auth()->user()->phone) }}">
                            @error('phone') <p class="mt-1 text-xs text-red-500 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                        <button type="button" onclick="closeModal()" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all text-sm cursor-pointer">Hủy</button>
                        <button type="submit" class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-500/10 transition-all text-sm cursor-pointer">Lưu thông tin</button>
                    </div>
                </form>
            </div>

            {{-- Tab 2: Change Password Form (Wider 3-column setup) --}}
            <div id="form-pwd-section" class="space-y-6 hidden">
                <form action="{{ route('profile.password') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="current_password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mật khẩu hiện tại</label>
                            <input type="password" id="current_password" name="current_password" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-semibold text-slate-800 text-sm" required>
                            @error('current_password') <p class="mt-1.5 text-xs text-red-500 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mật khẩu mới</label>
                            <input type="password" id="password" name="password" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-semibold text-slate-800 text-sm" required>
                            @error('password') <p class="mt-1.5 text-xs text-red-500 font-semibold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Xác nhận mật khẩu mới</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-semibold text-slate-800 text-sm" required>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                        <button type="button" onclick="closeModal()" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all text-sm cursor-pointer">Hủy</button>
                        <button type="submit" class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-500/10 transition-all text-sm cursor-pointer">Cập nhật mật khẩu</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

{{-- Upgrade Notice Modal --}}
<div id="upgrade-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-fade-in">
    <div class="bg-white rounded-[2.5rem] shadow-2xl max-w-md w-full p-8 overflow-hidden border border-slate-100 flex flex-col items-center text-center animate-scale-up">
        <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mb-5 animate-pulse">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <h3 id="upgrade-title" class="text-xl font-extrabold text-slate-800 mb-3">Tính năng đang nâng cấp</h3>
        <p class="text-slate-500 text-sm leading-relaxed mb-6">
            Chúng tôi đang liên tục nâng cấp hệ thống để mang lại trải nghiệm tốt nhất. Thông tin chi tiết của <strong class="text-blue-600" id="upgrade-feature-name">tính năng này</strong> sẽ sớm được cập nhật tại đây. Xin cảm ơn sự kiên nhẫn của bạn!
        </p>
        <button onclick="closeUpgradeNotice()" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-md shadow-blue-500/10 hover:shadow-lg transition-all text-sm cursor-pointer">
            Đã hiểu
        </button>
    </div>
</div>

@push('scripts')
<script>
    // Modal Open/Close logic
    function openModal(tab = 'info') {
        const modal = document.getElementById('profile-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        switchTab(tab);
    }

    function closeModal() {
        const modal = document.getElementById('profile-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Switch between Personal Info and Password tabs
    function switchTab(tab) {
        const infoTabBtn = document.getElementById('tab-info-btn');
        const pwdTabBtn = document.getElementById('tab-pwd-btn');
        const infoForm = document.getElementById('form-info-section');
        const pwdForm = document.getElementById('form-pwd-section');

        if (tab === 'info') {
            infoTabBtn.classList.add('border-blue-600', 'text-blue-600');
            infoTabBtn.classList.remove('border-transparent', 'text-slate-400');
            pwdTabBtn.classList.remove('border-blue-600', 'text-blue-600');
            pwdTabBtn.classList.add('border-transparent', 'text-slate-400');
            
            infoForm.classList.remove('hidden');
            pwdForm.classList.add('hidden');
        } else {
            pwdTabBtn.classList.add('border-blue-600', 'text-blue-600');
            pwdTabBtn.classList.remove('border-transparent', 'text-slate-400');
            infoTabBtn.classList.remove('border-blue-600', 'text-blue-600');
            infoTabBtn.classList.add('border-transparent', 'text-slate-400');

            pwdForm.classList.remove('hidden');
            infoForm.classList.add('hidden');
        }
    }

    // Preview uploaded avatar image instantly
    function previewAvatar(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('avatar-preview');
                const previewText = document.getElementById('avatar-preview-text');
                
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
                if (previewText) previewText.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // System Upgrade notice modals
    function showUpgradeNotice(featureName) {
        document.getElementById('upgrade-feature-name').innerText = featureName;
        const modal = document.getElementById('upgrade-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    // Close Upgrade notice
    function closeUpgradeNotice() {
        const modal = document.getElementById('upgrade-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function scrollToPatientSection(sectionId) {
        const section = document.getElementById(sectionId);

        if (section) {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            return;
        }

        showUpgradeNotice('đồng bộ hồ sơ khám bệnh');
    }

    // Automatically reopen modal if validation errors exist on load
    @if($errors->any())
        @if($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation'))
            document.addEventListener("DOMContentLoaded", function() {
                openModal('pwd');
            });
        @else
            document.addEventListener("DOMContentLoaded", function() {
                openModal('info');
            });
        @endif
    @endif

    // Open profile modal if redirected from /profile
    @if(session('open_profile') || request()->query('open_profile'))
        document.addEventListener("DOMContentLoaded", function() {
            openModal('info');
        });
    @endif
</script>
<style>
    /* Premium custom styling */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background-color: #94a3b8;
    }

    /* Animate floats and fades */
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    .animate-float {
        animation: float 5s ease-in-out infinite;
    }

    .animate-fade-in {
        animation: fadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .animate-scale-up {
        animation: scaleUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes scaleUp {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
</style>
@endpush
@endsection
