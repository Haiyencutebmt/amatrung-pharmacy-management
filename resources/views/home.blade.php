@extends('layouts.guest')
@section('title', 'Trang chủ - AmaTrung')

@section('content')
<div class="relative bg-gradient-to-b from-sky-50 via-white to-sky-100 min-h-screen pb-10 sm:pb-20 overflow-hidden font-sans">
    
    <!-- Hero Section -->
    <div class="max-w-[1400px] mx-auto px-3 sm:px-4 mt-6 md:mt-16">
        <div class="relative bg-cover bg-center bg-no-repeat rounded-[1.5rem] sm:rounded-[2.5rem] pt-8 sm:pt-12 md:pt-16 pb-14 sm:pb-28 md:pb-36 text-center shadow-lg border border-sky-100" style="background-image: url('{{ asset('images/home-imagecute.png') }}');">
            
            <!-- Logo -->
            <div class="absolute -top-4 sm:-top-10 md:-top-14 left-1/2 -translate-x-1/2 z-20">
                <div class="bg-white/95 p-1 sm:p-2 rounded-full shadow-md border-2 sm:border-4 border-sky-100/80">
                    <img src="{{ asset('images/amatrung_logo.png') }}" class="w-8 h-8 sm:w-16 sm:h-16 md:w-24 md:h-24 object-contain rounded-full" alt="Logo">
                </div>
            </div>

            <!-- Golden Badge -->
            <div class="flex justify-center mb-2 sm:mb-4 md:mb-5">
                <div class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1 rounded-full border border-[#d4af37]/45 bg-[#fffdf9]/90 text-[#b58e3d] font-bold text-[8px] sm:text-[10px] md:text-xs tracking-wider shadow-sm transition-transform hover:scale-105 duration-300">
                    <span class="text-[10px] sm:text-xs opacity-80">⚜️</span>
                    <span>Tinh hoa thảo dược – Chăm sóc từ tâm</span>
                    <span class="text-[10px] sm:text-xs opacity-80">⚜️</span>
                </div>
            </div>
            
            <!-- Title -->
            <h1 class="text-sm sm:text-xl md:text-4xl font-extrabold text-[#1a5b8f] tracking-wide mb-1 sm:mb-1.5 md:mb-2 drop-shadow-sm leading-tight">NHÀ THUỐC Y HỌC CỔ TRUYỀN</h1>
            <h2 class="text-xl sm:text-2.5xl md:text-5.5xl lg:text-6.5xl font-black text-[#1a5b8f] uppercase tracking-widest drop-shadow-md mb-2 sm:mb-4 md:mb-6">AMATRUNG</h2>
            
            <!-- Quote -->
            <div class="hidden sm:block max-w-3xl mx-auto mb-6 md:mb-8 relative px-2 md:px-4">
                <p class="text-sm md:text-lg text-[#1a5b8f] font-semibold italic leading-relaxed text-center drop-shadow-sm">
                    "Thuốc đắng dã tật, sự sống đơm hoa.<br>
                    Mỗi vị thuốc đều mang trong mình linh khí của đất trời,<br class="hidden md:block">
                    kết nối hệ tự nhiên với sức khỏe con người."
                </p>
            </div>

            <!-- 5 Elements Characters (Overlapping bottom edge) -->
            {{-- Mobile: Compact 5-icon grid below hero --}}
            <div class="flex sm:hidden justify-center items-center gap-2 mt-4 px-2 relative z-20">
                <a href="{{ route('articles.index') }}" class="flex flex-col items-center gap-1 w-1/5">
                    <img src="{{ asset('images/Kim.png') }}" alt="Kim" class="w-11 h-11 object-contain drop-shadow-md">
                    <span class="text-[7px] font-bold text-[#6a5b48] bg-[#f0e6d2]/80 px-2 py-0.5 rounded-full whitespace-nowrap">Bài viết</span>
                </a>
                <a href="{{ route('herb-dictionary.index') }}" class="flex flex-col items-center gap-1 w-1/5">
                    <img src="{{ asset('images/Mộc.png') }}" alt="Mộc" class="w-12 h-12 object-contain drop-shadow-md">
                    <span class="text-[7px] font-bold text-[#3a6121] bg-[#d1eeb2]/80 px-1.5 py-0.5 rounded-full whitespace-nowrap">Dược liệu</span>
                </a>
                <a href="{{ url('/') }}" class="flex flex-col items-center gap-1 w-1/5">
                    <img src="{{ asset('images/Thủy.png') }}" alt="Thủy" class="w-14 h-14 object-contain drop-shadow-md">
                    <span class="text-[7px] font-bold text-[#155096] bg-[#badbff]/80 px-2 py-0.5 rounded-full whitespace-nowrap">Trang chủ</span>
                </a>
                <a href="{{ route('profile.favorites') }}" class="flex flex-col items-center gap-1 w-1/5">
                    <img src="{{ asset('images/Hỏa.png') }}" alt="Hỏa" class="w-12 h-12 object-contain drop-shadow-md">
                    <span class="text-[7px] font-bold text-[#a34110] bg-[#ffd0a8]/80 px-1.5 py-0.5 rounded-full whitespace-nowrap">Yêu thích</span>
                </a>
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 w-1/5">
                    <img src="{{ asset('images/Thổ.png') }}" alt="Thổ" class="w-11 h-11 object-contain drop-shadow-md">
                    <span class="text-[7px] font-bold text-[#6b4721] bg-[#e8cdb0]/80 px-1.5 py-0.5 rounded-full whitespace-nowrap">Hồ sơ</span>
                </a>
            </div>

            {{-- Desktop: Original absolute positioned characters --}}
            <div class="hidden sm:flex absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2 w-full max-w-5xl px-2 md:px-4 justify-center items-end gap-4 md:gap-6 z-50 h-[140px] md:h-[200px]">
                <!-- KIM -->
                <a href="{{ route('articles.index') }}" class="group w-[14%] max-w-[150px] transform hover:-translate-y-4 transition-all duration-300 relative z-20 hover:z-50 mb-4 md:mb-12 flex flex-col items-center">
                    <img src="{{ asset('images/Kim.png') }}" alt="Kim" class="w-full h-auto drop-shadow-xl hover:drop-shadow-2xl">
                    <div class="mt-2 flex items-center justify-center gap-1 md:gap-1.5 px-2 py-1 md:px-4 md:py-2 bg-gradient-to-br from-gray-50 via-[#f0e6d2] to-gray-200 border-2 border-white rounded-full shadow-[0_4px_10px_rgba(0,0,0,0.08)] text-[#6a5b48] font-bold text-[11px] md:text-sm whitespace-nowrap group-hover:shadow-[0_6px_15px_rgba(0,0,0,0.12)] group-hover:scale-105 transition-all">
                        <svg class="w-3 h-3 md:w-4 md:h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Bài viết
                    </div>
                </a>
                
                <!-- MỘC -->
                <a href="{{ route('herb-dictionary.index') }}" class="group w-[17%] max-w-[190px] transform hover:-translate-y-4 transition-all duration-300 relative z-30 hover:z-50 mb-2 md:mb-6 flex flex-col items-center">
                    <img src="{{ asset('images/Mộc.png') }}" alt="Mộc" class="w-full h-auto drop-shadow-xl hover:drop-shadow-2xl">
                    <div class="mt-2 flex items-center justify-center gap-1 md:gap-1.5 px-2 py-1 md:px-4 md:py-2 bg-gradient-to-br from-[#eefade] via-[#d1eeb2] to-[#a4d673] border-2 border-white rounded-full shadow-[0_4px_10px_rgba(0,0,0,0.08)] text-[#3a6121] font-bold text-[11px] md:text-sm whitespace-nowrap group-hover:shadow-[0_6px_15px_rgba(0,0,0,0.12)] group-hover:scale-105 transition-all">
                        <svg class="w-3 h-3 md:w-4 md:h-4 opacity-80" fill="currentColor" viewBox="0 0 24 24"><path d="M17.75 2a8.75 8.75 0 0 0-7.39 13.43l-4.07 4.07a1 1 0 0 0 1.42 1.42l4.07-4.07A8.75 8.75 0 0 0 22 10.75V4a2 2 0 0 0-2-2h-2.25Zm-3 10.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Z"></path></svg>
                        Từ điển dược liệu
                    </div>
                </a>
                
                <!-- THỦY (Center, Biggest) -->
                <a href="{{ url('/') }}" class="group w-[24%] max-w-[240px] transform hover:-translate-y-4 transition-all duration-300 relative z-40 hover:z-50 mb-0 flex flex-col items-center">
                    <img src="{{ asset('images/Thủy.png') }}" alt="Thủy" class="w-full h-auto drop-shadow-[0_15px_20px_rgba(59,130,246,0.3)] hover:drop-shadow-[0_20px_25px_rgba(59,130,246,0.5)]">
                    <div class="mt-2 flex items-center justify-center gap-1 md:gap-1.5 px-3 py-1 md:px-5 md:py-2 bg-gradient-to-br from-[#e6f2ff] via-[#badbff] to-[#7dbbfa] border-2 border-white rounded-full shadow-[0_4px_10px_rgba(0,0,0,0.08)] text-[#155096] font-bold text-xs md:text-[15px] whitespace-nowrap group-hover:shadow-[0_6px_15px_rgba(0,0,0,0.12)] group-hover:scale-105 transition-all">
                        <svg class="w-3 h-3 md:w-5 md:h-5 opacity-80" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Trang chủ
                    </div>
                </a>
                
                <!-- HỎA -->
                <a href="{{ route('profile.favorites') }}" class="group w-[17%] max-w-[190px] transform hover:-translate-y-4 transition-all duration-300 relative z-30 hover:z-50 mb-2 md:mb-6 flex flex-col items-center">
                    <img src="{{ asset('images/Hỏa.png') }}" alt="Hỏa" class="w-full h-auto drop-shadow-xl hover:drop-shadow-2xl">
                    <div class="mt-2 flex items-center justify-center gap-1 md:gap-1.5 px-2 py-1 md:px-4 md:py-2 bg-gradient-to-br from-[#fff1e6] via-[#ffd0a8] to-[#ff9e66] border-2 border-white rounded-full shadow-[0_4px_10px_rgba(0,0,0,0.08)] text-[#a34110] font-bold text-[11px] md:text-sm whitespace-nowrap group-hover:shadow-[0_6px_15px_rgba(0,0,0,0.12)] group-hover:scale-105 transition-all">
                        <svg class="w-3 h-3 md:w-4 md:h-4 opacity-80" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>
                        Góc yêu thích
                    </div>
                </a>
                
                <!-- THỔ -->
                <a href="{{ route('dashboard') }}" class="group w-[14%] max-w-[150px] transform hover:-translate-y-4 transition-all duration-300 relative z-20 hover:z-50 mb-4 md:mb-12 flex flex-col items-center">
                    <img src="{{ asset('images/Thổ.png') }}" alt="Thổ" class="w-full h-auto drop-shadow-xl hover:drop-shadow-2xl">
                    <div class="mt-2 flex items-center justify-center gap-1 md:gap-1.5 px-2 py-1 md:px-4 md:py-2 bg-gradient-to-br from-[#fcf4ea] via-[#e8cdb0] to-[#c9a073] border-2 border-white rounded-full shadow-[0_4px_10px_rgba(0,0,0,0.08)] text-[#6b4721] font-bold text-[11px] md:text-sm whitespace-nowrap group-hover:shadow-[0_6px_15px_rgba(0,0,0,0.12)] group-hover:scale-105 transition-all">
                        <svg class="w-3 h-3 md:w-4 md:h-4 opacity-80" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                        Bảng điều khiển
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Section 2: Core Values (Image based) -->
    <div class="hidden sm:block max-w-[1400px] mx-auto px-3 sm:px-4 relative z-10 mt-10 sm:mt-32 md:mt-48 lg:mt-56 mb-10 sm:mb-16">
        <div class="bg-[#fbfcfa] border-2 border-[#e7efe9] rounded-[2rem] sm:rounded-[3rem] p-4 sm:p-6 md:p-10 relative shadow-[0_8px_30px_rgb(0,0,0,0.04)] mt-8 sm:mt-12 md:mt-16">
            <!-- Title Badge -->
            <div class="absolute -top-4 sm:-top-6 md:-top-10 left-1/2 -translate-x-1/2 z-10 w-full flex justify-center">
                <img src="{{ asset('images/suckhoe-tunhien-connguoi.png') }}" class="w-[200px] sm:w-[380px] md:w-[500px] lg:w-[600px] h-auto object-contain drop-shadow-sm hover:scale-105 transition-transform" alt="Vì sức khỏe - Vì tự nhiên - Vì con người">
            </div>
            
            <!-- 5 Items: Grid on mobile, flex on desktop -->
            <div class="grid grid-cols-5 gap-1 md:gap-4 sm:flex sm:flex-row sm:items-center sm:justify-between sm:gap-4 mt-6 sm:mt-6 px-0 sm:px-2 md:px-4">
                <!-- Item 1 -->
                <div class="flex flex-col items-center text-center sm:w-1/5 group">
                    <img src="{{ asset('images/Thao-duoc-chat-luong.png') }}" class="w-full h-auto max-w-[52px] min-[375px]:max-w-[64px] min-[410px]:max-w-[76px] sm:max-w-none object-contain rounded-2xl group-hover:-translate-y-2 transition-transform duration-300 drop-shadow-sm" alt="Thảo dược chất lượng">
                </div>
                
                <!-- Arrow -->
                <div class="hidden md:flex flex-col items-center text-green-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                </div>

                <!-- Item 2 -->
                <div class="flex flex-col items-center text-center sm:w-1/5 group">
                    <img src="{{ asset('images/An-toan-lanh-tinh.png') }}" class="w-full h-auto max-w-[52px] min-[375px]:max-w-[64px] min-[410px]:max-w-[76px] sm:max-w-none object-contain rounded-2xl group-hover:-translate-y-2 transition-transform duration-300 drop-shadow-sm" alt="An toàn lành tính">
                </div>

                <!-- Arrow -->
                <div class="hidden md:flex flex-col items-center text-green-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                </div>

                <!-- Item 3 -->
                <div class="flex flex-col items-center text-center sm:w-1/5 group">
                    <img src="{{ asset('images/y-hoc-co-truyen.png') }}" class="w-full h-auto max-w-[52px] min-[375px]:max-w-[64px] min-[410px]:max-w-[76px] sm:max-w-none object-contain rounded-2xl group-hover:-translate-y-2 transition-transform duration-300 drop-shadow-sm" alt="Y học cổ truyền bài bản">
                </div>

                <!-- Arrow -->
                <div class="hidden md:flex flex-col items-center text-green-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                </div>

                <!-- Item 4 -->
                <div class="flex flex-col items-center text-center sm:w-1/5 group">
                    <img src="{{ asset('images/Tan-tam-trach-nhiem.png') }}" class="w-full h-auto max-w-[52px] min-[375px]:max-w-[64px] min-[410px]:max-w-[76px] sm:max-w-none object-contain rounded-2xl group-hover:-translate-y-2 transition-transform duration-300 drop-shadow-sm" alt="Tận tâm trách nhiệm">
                </div>

                <!-- Arrow -->
                <div class="hidden md:flex flex-col items-center text-green-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                </div>

                <!-- Item 5 -->
                <div class="flex flex-col items-center text-center sm:w-1/5 group">
                    <img src="{{ asset('images/Ket-noi-tu-nhien.png') }}" class="w-full h-auto max-w-[52px] min-[375px]:max-w-[64px] min-[410px]:max-w-[76px] sm:max-w-none object-contain rounded-2xl group-hover:-translate-y-2 transition-transform duration-300 drop-shadow-sm" alt="Kết nối tự nhiên">
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2.5: Latest Articles -->
    <div class="max-w-[1400px] mx-auto px-4 relative z-10 mt-16 md:mt-24 mb-16">
        <div class="bg-[#5ca8e6] rounded-[2.5rem] p-6 md:p-10 pt-10 md:pt-14 pb-8 md:pb-12 relative shadow-[0_8px_30px_rgb(0,0,0,0.15)] mt-12 md:mt-16 text-white">
            <!-- Background Decorative Wrapper (strictly clips inner shapes) -->
            <div class="absolute inset-0 rounded-[2.5rem] overflow-hidden pointer-events-none z-0">
                <!-- Decorative Lotus Image (Bottom Left) -->
                <img src="{{ asset('images/hoa-sen-image.png') }}" alt="Lotus" class="absolute -bottom-10 -left-10 w-64 md:w-80 opacity-50 pointer-events-none z-0">
            </div>
            
            <!-- Pill Header -->
            <div class="absolute -top-7 left-1/2 -translate-x-1/2 flex items-center bg-white rounded-full pr-8 pl-1.5 py-1.5 shadow-md border-[3px] border-[#91c5f2] z-10 whitespace-nowrap">
                <div class="bg-[#2978c4] w-11 h-11 rounded-full flex items-center justify-center mr-4 text-white">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v2H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"></path><path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"></path></svg>
                </div>
                <span class="font-extrabold text-[#2978c4] text-base md:text-lg uppercase tracking-wide border-l border-gray-200 pl-4">BÀI VIẾT MỚI NHẤT</span>
            </div>

            <div class="mt-8 md:mt-6 relative z-10">
                @if(isset($latestArticles) && $latestArticles->count() > 0)
                @php
                    $latestArticle = $latestArticles->first();
                @endphp
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 md:gap-12 max-w-[1200px] mx-auto">
                    
                    <!-- Left: Doctor Image -->
                    <div class="lg:col-span-4 flex flex-col items-center justify-center text-center">
                        <img src="{{ asset('images/doctor.JPG') }}" alt="Bác sĩ AmaTrung" class="w-full max-w-[310px] md:max-w-[330px] aspect-[4/5] object-cover rounded-[2rem] shadow-[0_10px_30px_rgba(0,0,0,0.2)] border-[4px] border-white/90 transition-transform duration-500 hover:scale-[1.02]">
                        <h3 class="mt-5 text-xl font-bold text-white drop-shadow-md">Thầy thuốc Y Hiếu Niê <br><span class="text-lg opacity-90 font-medium">(AmaTrung)</span></h3>
                        <p class="mt-3 text-[15px] md:text-base text-white/95 leading-relaxed max-w-[340px] drop-shadow-sm font-medium">
                            Tên Amatrung bắt nguồn từ cách gọi của người dân tộc Ê-Đê, .... 
                            <a href="{{ route('about.doctor') }}" class="font-extrabold underline hover:text-[#1a3754] transition-colors">xem tiếp.</a>
                        </p>
                    </div>

                    <!-- Right: Latest Article directly on blue bg -->
                    <div class="lg:col-span-8 flex flex-col justify-between text-left">
                        <div>
                            <h2 class="text-lg md:text-xl font-bold text-white/90 mb-4 border-b border-white/20 pb-3 uppercase tracking-wide">BÀI VIẾT ĐÁNG CHÚ Ý</h2>
                            
                            <a href="{{ route('articles.show', $latestArticle->slug) }}" class="group flex flex-row sm:flex-col gap-3 sm:gap-5 mt-2 hover:-translate-y-1 transition-transform duration-300">
                                <!-- Article Thumbnail -->
                                <div class="w-28 h-24 sm:w-full sm:h-48 md:h-64 shrink-0 rounded-xl overflow-hidden shadow-lg border border-white/10 relative">
                                    @if($latestArticle->featured_image)
                                        <img src="{{ Storage::url($latestArticle->featured_image) }}" alt="{{ $latestArticle->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                    @else
                                        <div class="w-full h-full bg-[#2978c4] flex items-center justify-center text-white/50 group-hover:scale-105 transition-transform duration-700">
                                            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>
                                        </div>
                                    @endif
                                    @if($latestArticle->category)
                                    <div class="absolute top-3 left-3 bg-[#1a5b8f]/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-white shadow-sm border border-white/20 hidden sm:block">
                                        {{ $latestArticle->category_label }}
                                    </div>
                                    @endif
                                </div>
                                
                                <!-- Article Content -->
                                <div class="flex flex-col flex-1 min-w-0">
                                    <span class="text-[9px] font-bold text-blue-200 sm:hidden block mb-0.5">
                                        {{ $latestArticle->category_label }}
                                    </span>
                                    <h3 class="font-bold text-sm sm:text-xl md:text-2xl text-white leading-snug mb-1.5 sm:mb-3 group-hover:text-blue-200 transition-colors uppercase line-clamp-2">
                                        {{ $latestArticle->title }}
                                    </h3>
                                    <div class="text-blue-100/90 text-xs sm:text-sm md:text-base mb-2 sm:mb-4 line-clamp-2 sm:line-clamp-3 leading-relaxed hidden sm:block">
                                        {!! $latestArticle->excerpt !!}
                                    </div>
                                    
                                    <div class="flex items-center gap-3 sm:gap-6 mt-1 sm:mt-2 text-[10px] sm:text-sm text-blue-200 font-medium">
                                        <div class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            {{ $latestArticle->author->name ?? 'AmaTrung' }}
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            {{ $latestArticle->published_at ? $latestArticle->published_at->format('d/m/Y') : $latestArticle->created_at->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        
                        <!-- Bottom "Xem thêm" button inside normal flow -->
                        <div class="mt-8 flex justify-end">
                            <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 text-xs md:text-sm font-bold text-[#1a5b8f] bg-white hover:bg-blue-50 px-4 md:px-5 py-2 md:py-2.5 rounded-full transition-all duration-300 shadow-md hover:shadow-lg">
                                Xem thêm nhiều bài viết khác
                                <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
                
                @else
                <div class="text-center text-blue-200 py-12 bg-white/10 rounded-3xl max-w-3xl mx-auto border border-white/20 shadow-sm backdrop-blur-sm">
                    Chưa có bài viết nào được đăng tải.
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Section 3: Dịch vụ (Full Width) -->
    <div class="max-w-[1400px] mx-auto px-4 relative z-10 mt-16 md:mt-24 mb-16">
        <!-- Pill Header -->
        <div class="flex justify-center mb-10">
            <div class="flex items-center bg-white rounded-full pr-8 pl-1.5 py-1.5 shadow-md border border-gray-100 z-10 whitespace-nowrap">
                <div class="bg-[#5a90d4] w-11 h-11 rounded-full flex items-center justify-center mr-4 text-white">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"></path></svg>
                </div>
                <span class="font-extrabold text-[#1a5b8f] text-base md:text-lg uppercase tracking-wide border-l border-gray-200 pl-4">DỊCH VỤ CỦA CHÚNG TÔI</span>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-6 gap-6 md:gap-10 max-w-5xl mx-auto">
            <!-- Service 1 -->
            <div class="col-span-1 md:col-span-2 flex justify-center">
                <img src="{{ asset('images/dich-vu/bo-thuoc-truyen.png') }}" class="w-full max-w-[220px] h-auto object-contain rounded-3xl shadow-[0_4px_15px_rgba(0,0,0,0.05)] hover:-translate-y-2 hover:shadow-lg transition-all duration-300 bg-white" alt="Bó thuốc nam">
            </div>
            
            <!-- Service 2 -->
            <div class="col-span-1 md:col-span-2 flex justify-center">
                <img src="{{ asset('images/dich-vu/duoc-lieu-tu-nhien.png') }}" class="w-full max-w-[220px] h-auto object-contain rounded-3xl shadow-[0_4px_15px_rgba(0,0,0,0.05)] hover:-translate-y-2 hover:shadow-lg transition-all duration-300 bg-white" alt="Dược liệu tự nhiên">
            </div>

            <!-- Service 3 -->
            <div class="col-span-1 md:col-span-2 flex justify-center">
                <img src="{{ asset('images/dich-vu/Tu-van-suc-khoe.png') }}" class="w-full max-w-[220px] h-auto object-contain rounded-3xl shadow-[0_4px_15px_rgba(0,0,0,0.05)] hover:-translate-y-2 hover:shadow-lg transition-all duration-300 bg-white" alt="Tư vấn sức khỏe YHCT">
            </div>

            <!-- Service 4 -->
            <div class="col-span-1 md:col-span-2 md:col-start-2 flex justify-center">
                <img src="{{ asset('images/dich-vu/thuoc-gia-truyen.png') }}" class="w-full max-w-[220px] h-auto object-contain rounded-3xl shadow-[0_4px_15px_rgba(0,0,0,0.05)] hover:-translate-y-2 hover:shadow-lg transition-all duration-300 bg-white" alt="Thuốc thang gia truyền">
            </div>

            <!-- Service 5 -->
            <div class="col-span-2 md:col-span-2 flex justify-center">
                <img src="{{ asset('images/dich-vu/Tra-thao-moc.png') }}" class="w-1/2 md:w-full max-w-[220px] h-auto object-contain rounded-3xl shadow-[0_4px_15px_rgba(0,0,0,0.05)] hover:-translate-y-2 hover:shadow-lg transition-all duration-300 bg-white" alt="Trà thảo mộc bồi dưỡng">
            </div>
        </div>
    </div>

    <!-- Section 3.5: Từ điển hôm nay (Random herbs preview) -->
    <div class="max-w-[1400px] mx-auto px-4 relative z-10 mt-16 md:mt-24 mb-16">
        <div class="bg-white border border-sky-100 rounded-[2.5rem] p-6 md:p-10 pt-10 md:pt-14 pb-8 md:pb-12 relative shadow-[0_20px_50px_rgba(59,130,246,0.14)]">
            <!-- Background Ambient Sea-Blue & White Smoke Canvas -->
            <canvas id="ambient-smoke-canvas" class="absolute inset-0 rounded-[2.5rem] pointer-events-none z-0 w-full h-full filter blur-[15px]"></canvas>
            
            <!-- Background Decorative Wrapper (strictly clips inner shapes) -->
            <div class="absolute inset-0 rounded-[2.5rem] overflow-hidden pointer-events-none z-0">
                <div class="absolute -top-20 -left-20 w-80 h-80 bg-gradient-to-br from-blue-100/30 to-sky-200/20 rounded-full blur-[80px] opacity-60"></div>
                <div class="absolute -bottom-20 -right-20 w-96 h-96 bg-gradient-to-br from-sky-200/20 to-blue-100/30 rounded-full blur-[100px] opacity-60"></div>
            </div>

            <!-- Pill Header -->
            <div class="absolute -top-7 left-1/2 -translate-x-1/2 flex items-center bg-white rounded-full pr-8 pl-1.5 py-1.5 shadow-md border border-sky-100 z-10 whitespace-nowrap">
                <div class="bg-[#5a90d4] w-11 h-11 rounded-full flex items-center justify-center mr-4 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <span class="font-extrabold text-[#1a5b8f] text-base md:text-lg uppercase tracking-wide border-l border-gray-200 pl-4">Từ điển hôm nay</span>
            </div>

            @if(isset($randomHerbs) && $randomHerbs->count() > 0)
                <div class="relative z-10">
                    <!-- Layout for Desktop & Tablet (>= sm) -->
                    <div class="hidden sm:grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 max-w-6xl mx-auto">
                        @foreach($randomHerbs as $entry)
                            <div class="bg-white/90 backdrop-blur-sm rounded-3xl overflow-hidden shadow-sm border border-sky-100/50 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col group relative">
                                {{-- Media --}}
                                <div class="aspect-[4/3] bg-slate-50 relative overflow-hidden shrink-0">
                                    @if($entry->primary_image_url)
                                        <img src="{{ $entry->primary_image_url }}" alt="{{ $entry->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-primary-50 text-primary-200 group-hover:scale-105 transition-transform duration-500">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Content --}}
                                <div class="p-5 flex flex-col flex-grow">
                                    <h3 class="text-lg font-bold text-slate-800 leading-snug mb-1">
                                        {{ $entry->name }}
                                    </h3>
                                    @if($entry->scientific_name)
                                        <p class="text-slate-400 italic text-xs mb-3 font-serif line-clamp-1">{{ $entry->scientific_name }}</p>
                                    @endif
                                    <p class="text-slate-500 text-sm mb-4 line-clamp-2 leading-relaxed flex-grow">
                                        {{ $entry->short_info ?: 'Đang cập nhật thông tin cơ bản.' }}
                                    </p>
                                    
                                    @auth
                                        <a href="{{ route('herb-dictionary.show', $entry) }}" class="block w-full py-2 text-center bg-slate-50 hover:bg-primary-50 text-[#1a5b8f] font-bold border border-slate-200 hover:border-primary-200 rounded-xl transition-colors text-sm">
                                            Xem chi tiết
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}" class="block w-full py-2 text-center bg-slate-50 text-slate-400 font-semibold border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors text-xs">
                                            Đăng nhập để xem
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Layout for Mobile (< sm) -->
                    <div class="flex sm:hidden flex-col gap-3.5 max-w-md mx-auto">
                        @foreach($randomHerbs as $entry)
                            @php
                                $detailUrl = auth()->check() ? route('herb-dictionary.show', $entry) : route('login');
                            @endphp
                            <a href="{{ $detailUrl }}" class="relative flex flex-row items-center bg-white/95 backdrop-blur-sm rounded-2xl p-2.5 border border-sky-100/60 shadow-[0_2px_8px_rgba(59,130,246,0.04)] hover:shadow-md hover:border-sky-200/80 transition-all duration-300 group">
                                {{-- Image --}}
                                <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0 bg-slate-50 border border-slate-100 relative">
                                    @if($entry->primary_image_url)
                                        <img src="{{ $entry->primary_image_url }}" alt="{{ $entry->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-sky-50/50 text-sky-300">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Content --}}
                                <div class="ml-3 flex-grow min-w-0 pr-2">
                                    <h3 class="text-sm font-bold text-slate-800 truncate">
                                        {{ $entry->name }}
                                    </h3>
                                    @if($entry->scientific_name)
                                        <p class="text-slate-400 italic text-[10px] font-serif truncate">
                                            {{ $entry->scientific_name }}
                                        </p>
                                    @endif
                                    <p class="text-slate-500 text-xs line-clamp-1 mt-0.5 leading-relaxed">
                                        {{ $entry->short_info ?: 'Đang cập nhật thông tin cơ bản.' }}
                                    </p>
                                </div>

                                {{-- Arrow Indicator --}}
                                <div class="w-8 h-8 rounded-full bg-slate-50 group-hover:bg-[#5a90d4]/10 flex items-center justify-center text-slate-300 group-hover:text-[#1a5b8f] shrink-0 transition-colors">
                                    <svg class="w-4 h-4 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-8 flex justify-center">
                        <a href="{{ route('herb-dictionary.index') }}" class="inline-flex items-center gap-2 text-xs md:text-sm font-bold text-[#1a5b8f] bg-white hover:bg-blue-50 px-4 md:px-5 py-2 md:py-2.5 rounded-full transition-all duration-300 shadow-sm border border-slate-100">
                            Khám phá thêm dược liệu khác
                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                </div>
            @else
                <div class="text-center text-slate-400 py-12 bg-white/80 rounded-3xl max-w-3xl mx-auto border border-slate-100 shadow-sm">
                    Đang cập nhật danh mục từ điển dược liệu.
                </div>
            @endif
            </div>
        </div>
    </div>

    <!-- Section 4: Why Choose Us & Map -->
    <div class="max-w-[1400px] mx-auto px-4 relative z-10 mt-16 md:mt-24 mb-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-16 xl:gap-24">
            
            <!-- Left: Why Choose Us -->
            <div class="bg-[#f0f7ff] rounded-[2.5rem] p-6 md:p-8 relative mt-10 lg:mt-0 shadow-sm border border-blue-50">
                <!-- Pill Header -->
                <div class="absolute -top-7 left-1/2 -translate-x-1/2 flex items-center bg-[#2978c4] rounded-full pr-8 pl-1.5 py-1.5 shadow-md border-[3px] border-[#a1c6ed] z-10 whitespace-nowrap">
                    <div class="bg-white w-10 h-10 rounded-full flex items-center justify-center mr-4 text-[#2978c4]">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    </div>
                    <span class="font-extrabold text-white text-base md:text-lg uppercase tracking-wide">VÌ SAO CHỌN AMATRUNG?</span>
                </div>

                <div class="mt-8 space-y-4">
                    <!-- Point 1 -->
                    <div class="bg-white rounded-2xl p-4 md:p-5 flex items-center gap-4 shadow-[0_4px_10px_rgba(0,0,0,0.03)] border border-gray-50 hover:shadow-md transition-shadow">
                        <div class="bg-[#3881ca] text-white rounded-full p-1 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="font-bold text-[#1a3754] text-[15px] md:text-[16px] leading-snug">Nguồn gốc thảo dược rõ ràng,<br>được chọn lọc kỹ lưỡng</p>
                    </div>
                    
                    <!-- Point 2 -->
                    <div class="bg-white rounded-2xl p-4 md:p-5 flex items-center gap-4 shadow-[0_4px_10px_rgba(0,0,0,0.03)] border border-gray-50 hover:shadow-md transition-shadow">
                        <div class="bg-[#3881ca] text-white rounded-full p-1 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="font-bold text-[#1a3754] text-[15px] md:text-[16px] leading-snug">Kết hợp tinh hoa YHCT<br>với kiến thức hiện đại</p>
                    </div>

                    <!-- Point 3 -->
                    <div class="bg-white rounded-2xl p-4 md:p-5 flex items-center gap-4 shadow-[0_4px_10px_rgba(0,0,0,0.03)] border border-gray-50 hover:shadow-md transition-shadow">
                        <div class="bg-[#3881ca] text-white rounded-full p-1 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="font-bold text-[#1a3754] text-[15px] md:text-[16px] leading-snug">Luôn đặt sức khỏe và sự hài lòng<br>của khách hàng lên hàng đầu</p>
                    </div>
                </div>
            </div>

            <!-- Right: Google Map -->
            <div class="bg-white rounded-[2.5rem] p-2 relative shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden mt-10 lg:mt-0 min-h-[350px]">
                <iframe src="https://maps.google.com/maps?q=Nh%C3%A0%20s%C3%A0n%20th%C3%A2n%20y%C3%AAu,%2054/36%20Ama%20Jhao,%20Ph%C6%B0%E1%BB%9Dng%20T%C3%A2n%20L%E1%BA%ADp,%20Bu%C3%B4n%20Ma%20Thu%E1%BB%99t,%20%C4%90%E1%BA%AFk%20L%E1%BA%AFk&t=&z=15&ie=UTF8&iwloc=&output=embed" width="100%" height="100%" style="border:0; border-radius: 2.2rem;" allowfullscreen="" loading="lazy" class="w-full h-full object-cover"></iframe>
            </div>
            
        </div>
    </div>

    <!-- Section 4.5: Bạn cần hỗ trợ? -->
    <div class="max-w-4xl mx-auto px-4 relative z-10 mt-16 md:mt-24 mb-16">
        <div class="bg-gradient-to-br from-white to-[#eff6ff]/75 border border-sky-100/80 rounded-[2.5rem] p-8 md:p-12 shadow-[0_20px_50px_rgba(59,130,246,0.04)]">
            <h2 class="text-3xl font-black text-[#1a5b8f] tracking-wide mb-3 uppercase text-center">Bạn cần hỗ trợ?</h2>
            <p class="text-slate-600 text-sm md:text-base mb-8 max-w-2xl mx-auto text-center leading-relaxed">
                AmaTrung rất hân hạnh được hỗ trợ bạn, hãy để lại thông tin cho chúng tôi nhé. Yêu cầu của bạn sẽ được xử lý và phản hồi trong thời gian sớm nhất.
            </p>
            
            <form id="support-contact-form" class="space-y-6 max-w-3xl mx-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-[#1a5b8f] uppercase tracking-wider mb-2">Họ tên *</label>
                        <input type="text" name="name" required placeholder="Tên đầy đủ" class="w-full px-5 py-3.5 bg-white border border-slate-200 focus:border-blue-500 rounded-2xl focus:outline-none text-slate-800 text-sm placeholder-slate-400 shadow-sm focus:ring-2 focus:ring-blue-100 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#1a5b8f] uppercase tracking-wider mb-2">Số điện thoại *</label>
                        <input type="tel" name="phone" required placeholder="Số điện thoại của bạn" class="w-full px-5 py-3.5 bg-white border border-slate-200 focus:border-blue-500 rounded-2xl focus:outline-none text-slate-800 text-sm placeholder-slate-400 shadow-sm focus:ring-2 focus:ring-blue-100 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#1a5b8f] uppercase tracking-wider mb-2">Tin nhắn</label>
                    <textarea name="message" rows="4" placeholder="Đừng ngại hỏi về đơn hàng của bạn" class="w-full px-5 py-3.5 bg-white border border-slate-200 focus:border-blue-500 rounded-2xl focus:outline-none text-slate-800 text-sm placeholder-slate-400 shadow-sm focus:ring-2 focus:ring-blue-100 transition-all resize-none"></textarea>
                </div>
                <div class="flex justify-center">
                    <button type="submit" class="px-12 py-3.5 bg-[#1a5b8f] hover:bg-blue-700 text-white font-extrabold rounded-full transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5 text-sm uppercase tracking-wider">
                        Gửi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="success-submit-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[99999] flex items-center justify-center hidden px-4">
        <div class="bg-white rounded-[2rem] p-8 max-w-md w-full text-center shadow-2xl border border-sky-100 transform scale-95 transition-transform duration-300">
            <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-5 border-4 border-emerald-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-2xl font-black text-slate-800 mb-3 uppercase">Gửi thành công!</h3>
            <p class="text-slate-600 text-sm mb-6 leading-relaxed">
                Cảm ơn bạn đã liên hệ. Đội ngũ y tế AmaTrung sẽ sớm liên lạc lại với bạn qua số điện thoại để tư vấn và hỗ trợ.
            </p>
            <button type="button" onclick="document.getElementById('success-submit-modal').classList.add('hidden')" class="w-full py-3 bg-[#1a5b8f] hover:bg-blue-700 text-white font-extrabold rounded-full transition-colors text-sm uppercase tracking-wider shadow-md">
                Đóng
            </button>
        </div>
    </div>

    <!-- Survey / Feedback Section -->
    <div class="max-w-4xl mx-auto px-4 mt-16 sm:mt-24 relative z-10 text-center pb-8">
        <div class="bg-gradient-to-br from-[#1a5b8f] via-[#2978c4] to-[#5ca8e6] rounded-[2.5rem] p-8 md:p-12 shadow-[0_20px_50px_rgba(59,130,246,0.18)] border border-white/20 text-white relative overflow-hidden group">
            <!-- Decorative Background Shapes -->
            <div class="absolute -top-12 -right-12 w-40 h-40 bg-white/10 rounded-full blur-2xl pointer-events-none group-hover:scale-125 transition-transform duration-700"></div>
            <div class="absolute -bottom-16 -left-16 w-52 h-52 bg-sky-200/10 rounded-full blur-2xl pointer-events-none group-hover:scale-110 transition-transform duration-700"></div>
            
            <div class="relative z-10 max-w-2xl mx-auto">
                <span class="inline-flex items-center gap-1.5 px-4.5 py-1.5 rounded-full border border-white/20 bg-white/10 text-white font-bold text-xs md:text-sm tracking-wider shadow-sm mb-4">
                    <span>📋</span> Khảo sát & Đánh giá
                </span>
                
                <h3 class="text-xl md:text-3xl font-black mb-3 leading-snug uppercase">
                    Đóng góp ý kiến của bạn
                </h3>
                
                <p class="text-blue-100/90 text-sm md:text-base mb-8 leading-relaxed font-medium">
                    Sự hài lòng của bạn là động lực phát triển lớn nhất của AmaTrung. Hãy dành ít phút để làm bài khảo sát giúp chúng tôi cải thiện chất lượng phục vụ ngày một tốt hơn!
                </p>
                
                <a href="https://forms.gle/g2MLGBGG5uSMoMoj7" target="_blank" rel="noopener noreferrer" 
                   class="inline-flex items-center gap-2.5 px-8 py-3.5 bg-white hover:bg-amber-500 text-[#1a5b8f] hover:text-white font-black rounded-full transition-all duration-300 shadow-md hover:shadow-xl hover:-translate-y-1 transform hover:scale-[1.03] text-sm md:text-base uppercase tracking-wider">
                    <span>Tham gia khảo sát</span>
                    <svg class="w-4.5 h-4.5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('ambient-smoke-canvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        
        let width = canvas.width = canvas.offsetWidth || canvas.clientWidth || 1000;
        let height = canvas.height = canvas.offsetHeight || canvas.clientHeight || 500;
        
        const resizeObserver = new ResizeObserver(entries => {
            for (let entry of entries) {
                width = canvas.width = entry.contentRect.width;
                height = canvas.height = entry.contentRect.height;
            }
        });
        resizeObserver.observe(canvas.parentElement);

        class SmokeParticle {
            constructor() {
                this.reset(true);
            }
            
            reset(initial = false) {
                this.x = Math.random() * width;
                this.y = initial ? Math.random() * height : height + Math.random() * 100;
                this.radius = 140 + Math.random() * 160;
                this.vx = (Math.random() - 0.5) * 0.25;
                this.vy = -0.15 - Math.random() * 0.25; // drift upward slowly
                this.maxLife = 600 + Math.random() * 800;
                this.life = initial ? Math.random() * this.maxLife : this.maxLife;
                
                // Color choices: White (255,255,255), sky-100 (224,242,254), sky-200 (186,230,253), blue-100 (219,234,254)
                const colors = [
                    '255, 255, 255',
                    '224, 242, 254',
                    '186, 230, 253',
                    '219, 234, 254'
                ];
                this.color = colors[Math.floor(Math.random() * colors.length)];
                this.targetAlpha = 0.08 + Math.random() * 0.12;
                this.alpha = 0;
            }
            
            update() {
                this.x += this.vx;
                this.y += this.vy;
                this.life--;
                
                // Fade in initially
                if (this.maxLife - this.life < 150) {
                    this.alpha = (this.maxLife - this.life) / 150 * this.targetAlpha;
                } 
                // Fade out at end
                else if (this.life < 150) {
                    this.alpha = this.life / 150 * this.targetAlpha;
                } else {
                    this.alpha = this.targetAlpha;
                }
                
                // Ripple/pulse size slightly
                this.radius += Math.sin(this.life * 0.005) * 0.05;
                
                if (this.life <= 0 || this.y < -this.radius || this.x < -this.radius || this.x > width + this.radius) {
                    this.reset();
                }
            }
            
            draw() {
                if (this.alpha <= 0) return;
                const grad = ctx.createRadialGradient(this.x, this.y, 0, this.x, this.y, this.radius);
                grad.addColorStop(0, `rgba(${this.color}, ${this.alpha})`);
                grad.addColorStop(0.5, `rgba(${this.color}, ${this.alpha * 0.4})`);
                grad.addColorStop(1, 'rgba(0, 0, 0, 0)');
                
                ctx.fillStyle = grad;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                ctx.fill();
            }
        }
        
        const particles = Array.from({ length: 15 }, () => new SmokeParticle());
        
        function render() {
            ctx.clearRect(0, 0, width, height);
            
            particles.forEach(p => {
                p.update();
                p.draw();
            });
            
            requestAnimationFrame(render);
        }
        
        // Handle support contact form submission
        const supportForm = document.getElementById('support-contact-form');
        const successModal = document.getElementById('success-submit-modal');
        if (supportForm && successModal) {
            supportForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(supportForm);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch('{{ route("contact.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: formData.get('name'),
                        phone: formData.get('phone'),
                        message: formData.get('message')
                    })
                })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    throw new Error('Đã có lỗi xảy ra. Vui lòng thử lại sau.');
                })
                .then(data => {
                    supportForm.reset();
                    successModal.classList.remove('hidden');
                })
                .catch(err => {
                    alert(err.message);
                });
            });
        }
        
        render();
    });
</script>
@endpush
