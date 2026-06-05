@extends('layouts.guest')

@section('title', $entry->name . ' — Từ điển dược liệu')

@section('content')

<div class="bg-[var(--color-surface-bg)] min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        
        {{-- Breadcrumb & Actions --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <nav class="flex items-center text-sm font-medium text-slate-500">
                <a href="{{ route('herb-dictionary.index') }}" class="hover:text-primary-600 transition-colors flex items-center gap-1 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm hover:shadow mr-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Quay lại
                </a>
                <span class="mx-3 text-slate-300 hidden sm:inline">|</span>
                <div class="hidden sm:flex items-center">
                    <a href="/" class="hover:text-primary-600 transition-colors">Trang chủ</a>
                    <svg class="w-4 h-4 mx-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <a href="{{ route('herb-dictionary.index') }}" class="hover:text-primary-600 transition-colors">Từ điển dược liệu</a>
                    <svg class="w-4 h-4 mx-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <span class="text-slate-700 truncate max-w-[200px]">{{ $entry->name }}</span>
                </div>
            </nav>
            
            <form action="{{ route('herb-dictionary.favorite', $entry) }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold transition-all shadow-sm {{ $entry->isFavoritedBy(auth()->user()) ? 'bg-red-50 text-red-600 border border-red-200 hover:bg-red-100' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 {{ $entry->isFavoritedBy(auth()->user()) ? 'fill-current text-red-500' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    {{ $entry->isFavoritedBy(auth()->user()) ? 'Đã lưu' : 'Lưu dược liệu' }}
                </button>
            </form>
        </div>

        {{-- Top Section: Image & Profile --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col lg:flex-row mb-8">
            {{-- Left: Gallery --}}
            <div class="w-full lg:w-4/12 bg-slate-50/50 p-4 lg:p-6 lg:border-r border-slate-100 flex flex-col justify-center">
                <div id="main-image-container" @if($entry->images->count() > 0) onclick="openLightbox('{{ $entry->images->first()->url }}')" @endif class="aspect-[4/3] max-h-56 md:max-h-64 w-full mx-auto rounded-2xl overflow-hidden shadow-sm border border-slate-200 bg-white relative group {{ $entry->images->count() > 0 ? 'cursor-pointer' : '' }}">
                    @if($entry->images->count() > 0)
                        <img id="main-herb-image" src="{{ $entry->images->first()->url }}" alt="{{ $entry->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-10 h-10 text-white drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                        </div>
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                            <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="font-medium text-sm">Chưa có hình ảnh</span>
                        </div>
                    @endif
                </div>

                @if($entry->images->count() > 1)
                    <div class="grid grid-cols-4 sm:grid-cols-5 lg:grid-cols-4 gap-3 mt-4">
                        @foreach($entry->images as $image)
                            <div onclick="changeMainImage('{{ $image->url }}')" class="aspect-square rounded-xl overflow-hidden border-2 border-transparent hover:border-primary-500 cursor-pointer transition-colors shadow-sm bg-white">
                                <img src="{{ $image->url }}" alt="{{ $image->caption ?: $entry->name }}" class="w-full h-full object-cover hover:opacity-80 transition-opacity">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Right: Information --}}
            <div class="w-full lg:w-8/12 p-5 sm:p-6 lg:p-8 flex flex-col justify-center">
                <div class="mb-6">
                    <div class="inline-block px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-bold uppercase tracking-wider mb-4 border border-blue-100">
                        Hồ sơ dược liệu
                    </div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-800 mb-2 tracking-tight">
                        {{ $entry->name }}
                    </h1>
                    @if($entry->scientific_name)
                        <p class="text-slate-500 italic text-lg sm:text-xl font-serif mt-2">{{ $entry->scientific_name }}</p>
                    @endif
                </div>

                {{-- Key Facts Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if($entry->other_names)
                        <div class="bg-slate-50 rounded-2xl p-5">
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Tên gọi khác</span>
                            <span class="text-slate-800 text-lg font-medium leading-snug">{{ $entry->other_names }}</span>
                        </div>
                    @endif
                    @if($entry->family)
                        <div class="bg-slate-50 rounded-2xl p-5">
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Họ thực vật</span>
                            <span class="text-slate-800 text-lg font-medium leading-snug">{{ $entry->family }}</span>
                        </div>
                    @endif
                    @if($entry->plant_part)
                        <div class="bg-green-50 rounded-2xl p-5">
                            <span class="block text-xs font-bold text-green-700 uppercase tracking-wider mb-1">Bộ phận dùng</span>
                            <span class="text-green-800 text-lg font-bold leading-snug">{{ $entry->plant_part }}</span>
                        </div>
                    @endif
                    @if($entry->properties)
                        <div class="bg-blue-50 rounded-2xl p-5">
                            <span class="block text-xs font-bold text-blue-700 uppercase tracking-wider mb-1">Tính vị / Đặc điểm</span>
                            <span class="text-blue-800 text-lg font-bold leading-snug">{{ $entry->properties }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Detailed Content - Article Layout --}}
        <div id="herb-details-section" class="mt-8">

            {{-- Content Sections --}}
            <div class="flex flex-col gap-6">
                
                {{-- Content: Tổng quan --}}
                <div id="section-basic" class="bg-white rounded-3xl p-6 lg:p-8 shadow-sm border border-slate-100">
                    <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3 border-b border-slate-100 pb-4">
                        <div class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center shadow-sm shrink-0">
                            <span class="font-bold italic text-base">i</span>
                        </div>
                        Thông tin cơ bản
                    </h2>
                    <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed text-base">
                        {!! nl2br(e($entry->basic_info ?: 'Đang cập nhật...')) !!}
                    </div>
                </div>

                {{-- Content: Công dụng --}}
                <div id="section-effects" class="bg-white rounded-3xl p-6 lg:p-8 shadow-sm border border-slate-100">
                    <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3 border-b border-slate-100 pb-4">
                        <div class="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center shadow-sm shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </div>
                        Tác dụng ghi nhận
                    </h2>
                    <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed text-base">
                        {!! nl2br(e($entry->effects ?: 'Đang cập nhật...')) !!}
                    </div>
                </div>

                {{-- Content: Cách dùng --}}
                <div id="section-usage" class="bg-white rounded-3xl p-6 lg:p-8 shadow-sm border border-slate-100">
                    <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3 border-b border-slate-100 pb-4">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shadow-sm shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M4 11a1 1 0 112 0v1h6v-1a1 1 0 112 0v1a2 2 0 01-2 2H6a2 2 0 01-2-2v-1zM3 7h14a1 1 0 011 1v1a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1z"></path></svg>
                        </div>
                        Cách dùng tham khảo
                    </h2>
                    <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed text-base">
                        {!! nl2br(e($entry->usage_notes ?: 'Đang cập nhật...')) !!}
                    </div>
                </div>

                {{-- Content: Lưu ý an toàn --}}
                <div id="section-safety" class="bg-white rounded-3xl p-6 lg:p-8 shadow-sm border border-slate-100">
                    <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3 border-b border-slate-100 pb-4">
                        <div class="w-10 h-10 rounded-full bg-red-100 text-red-500 flex items-center justify-center shadow-sm shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        Lưu ý an toàn
                    </h2>
                    <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed text-base">
                        {!! nl2br(e($entry->safety_warning ?: 'Không nên tự ý sử dụng thuốc nam hoặc phối hợp nhiều vị thuốc khi chưa được thầy thuốc thăm khám. Vui lòng khám trực tiếp tại cơ sở y tế để được tư vấn phù hợp.')) !!}
                    </div>
                </div>

            </div>
            
            {{-- Fixed Blue Warning Banner --}}
            <div class="mt-6 bg-blue-50/80 rounded-2xl p-5 border border-blue-100 flex items-start gap-4 shadow-sm relative overflow-hidden">
                <div class="w-8 h-8 rounded-full border-2 border-blue-400 text-blue-500 flex items-center justify-center shrink-0 bg-white z-10 relative mt-1">
                    <span class="font-bold italic text-sm">i</span>
                </div>
                <div class="z-10 relative">
                    <p class="text-blue-800 font-bold mb-1">Lưu ý:</p>
                    <p class="text-blue-700 text-sm leading-relaxed">Thông tin trên chỉ mang tính tham khảo, không thay thế chẩn đoán hoặc tư vấn chuyên môn.<br>Vui lòng tham khảo ý kiến thầy thuốc hoặc chuyên gia y tế trước khi sử dụng.</p>
                </div>

            </div>

        </div>

    </div>
</div>

<style>
    html {
        scroll-behavior: smooth;
    }
</style>

    </div>
</div>

{{-- Lightbox --}}
<div id="image-lightbox" class="fixed inset-0 z-[99999] bg-black/90 hidden items-center justify-center opacity-0 transition-opacity duration-300" onclick="closeLightbox()">
    <button onclick="closeLightbox()" class="absolute top-6 right-6 text-white/70 hover:text-white bg-black/50 hover:bg-black p-3 rounded-full transition-colors z-50">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
    <img id="lightbox-img" src="" class="max-w-[95vw] max-h-[95vh] object-contain rounded-lg shadow-2xl scale-95 transition-transform duration-300" onclick="event.stopPropagation()">
</div>

<script>
    function changeMainImage(url) {
        const mainImg = document.getElementById('main-herb-image');
        if(mainImg) {
            mainImg.src = url;
            const container = document.getElementById('main-image-container');
            container.onclick = function() { openLightbox(url); };
        }
    }

    function openLightbox(url) {
        if(!url) return;
        const lightbox = document.getElementById('image-lightbox');
        const img = document.getElementById('lightbox-img');
        img.src = url;
        lightbox.classList.remove('hidden');
        lightbox.classList.add('flex');
        
        setTimeout(() => {
            lightbox.classList.remove('opacity-0');
            img.classList.remove('scale-95');
            img.classList.add('scale-100');
        }, 10);
    }

    function closeLightbox() {
        const lightbox = document.getElementById('image-lightbox');
        const img = document.getElementById('lightbox-img');
        lightbox.classList.add('opacity-0');
        img.classList.remove('scale-100');
        img.classList.add('scale-95');
        
        setTimeout(() => {
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
        }, 300);
    }
</script>

@endsection
