@extends('layouts.guest')

@section('title', $entry->name . ' — Từ điển thuốc nam')

@section('content')

<div class="bg-[var(--color-surface-bg)] min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        
        {{-- Breadcrumb & Back --}}
        <div class="mb-8 flex items-center justify-between">
            <a href="{{ route('herb-dictionary.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-primary-600 font-semibold transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại từ điển
            </a>
            
            <form action="{{ route('herb-dictionary.favorite', $entry) }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full font-bold transition-all shadow-sm {{ $entry->isFavoritedBy(auth()->user()) ? 'bg-red-50 text-red-600 border border-red-200 hover:bg-red-100' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 {{ $entry->isFavoritedBy(auth()->user()) ? 'fill-current' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    {{ $entry->isFavoritedBy(auth()->user()) ? 'Đã yêu thích' : 'Thêm vào yêu thích' }}
                </button>
            </form>
        </div>

        {{-- Main Content Grid --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col lg:flex-row">
            
            {{-- Left: Gallery --}}
            <div class="w-full lg:w-2/5 xl:w-1/2 bg-slate-50 p-6 lg:p-8 lg:border-r border-slate-100 flex flex-col">
                <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-sm border border-slate-200 bg-white relative">
                    @if($entry->images->count() > 0)
                        <img src="{{ $entry->images->first()->url }}" alt="{{ $entry->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="font-medium text-sm">Chưa có hình ảnh</span>
                        </div>
                    @endif
                </div>

                @if($entry->images->count() > 1)
                    <div class="grid grid-cols-4 md:grid-cols-5 gap-3 mt-4">
                        @foreach($entry->images as $image)
                            <div class="aspect-square rounded-xl overflow-hidden border-2 border-transparent hover:border-primary-500 cursor-pointer transition-colors shadow-sm bg-white">
                                <img src="{{ $image->url }}" alt="{{ $image->caption ?: $entry->name }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Right: Information --}}
            <div class="w-full lg:w-3/5 xl:w-1/2 p-8 lg:p-12">
                <div class="mb-8">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-50 text-primary-700 text-xs font-bold uppercase tracking-wider mb-4 border border-primary-100">
                        Hồ sơ dược liệu
                    </div>
                    <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-800 mb-2 leading-tight">
                        {{ $entry->name }}
                    </h1>
                    @if($entry->scientific_name)
                        <p class="text-slate-500 italic text-lg font-serif">{{ $entry->scientific_name }}</p>
                    @endif
                </div>

                {{-- Key Facts --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
                    @if($entry->other_names)
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Tên gọi khác</span>
                            <span class="text-slate-700 font-medium">{{ $entry->other_names }}</span>
                        </div>
                    @endif
                    @if($entry->family)
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Họ thực vật</span>
                            <span class="text-slate-700 font-medium">{{ $entry->family }}</span>
                        </div>
                    @endif
                    @if($entry->plant_part)
                        <div class="bg-green-50 rounded-2xl p-4 border border-green-100">
                            <span class="block text-xs font-bold text-green-600 uppercase tracking-wider mb-1">Bộ phận dùng</span>
                            <span class="text-green-800 font-bold">{{ $entry->plant_part }}</span>
                        </div>
                    @endif
                    @if($entry->properties)
                        <div class="bg-blue-50 rounded-2xl p-4 border border-blue-100">
                            <span class="block text-xs font-bold text-blue-600 uppercase tracking-wider mb-1">Tính vị / Đặc điểm</span>
                            <span class="text-blue-800 font-bold">{{ $entry->properties }}</span>
                        </div>
                    @endif
                </div>

                {{-- Detailed Content --}}
                <div class="prose prose-slate prose-lg max-w-none">
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Thông tin cơ bản
                        </h2>
                        <div class="text-slate-600 leading-relaxed bg-white rounded-xl">
                            {!! nl2br(e($entry->basic_info)) !!}
                        </div>
                    </div>

                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Tác dụng ghi nhận
                        </h2>
                        <div class="text-slate-600 leading-relaxed">
                            {!! nl2br(e($entry->effects)) !!}
                        </div>
                    </div>

                    @if($entry->usage_notes)
                        <div class="mb-8">
                            <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Lưu ý khi sử dụng
                            </h2>
                            <div class="text-slate-600 leading-relaxed bg-orange-50 border border-orange-100 p-5 rounded-2xl">
                                {!! nl2br(e($entry->usage_notes)) !!}
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Safety Warning --}}
                <div class="mt-10 bg-red-50 border border-red-200 rounded-2xl p-6 shadow-sm relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 text-red-100 opacity-50">
                        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    </div>
                    <div class="relative z-10">
                        <h2 class="text-lg font-bold text-red-800 mb-2 flex items-center gap-2">
                            Khuyến cáo an toàn
                        </h2>
                        <div class="text-red-700 text-sm leading-relaxed font-medium">
                            {!! nl2br(e($entry->safety_warning ?: 'Không nên tự ý sử dụng thuốc nam hoặc phối hợp nhiều vị thuốc khi chưa được thầy thuốc thăm khám. Mỗi cơ địa, bệnh nền và tình trạng hiện tại có thể cần chỉ định khác nhau. Vui lòng khám trực tiếp tại cơ sở y tế để được tư vấn phù hợp.')) !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
