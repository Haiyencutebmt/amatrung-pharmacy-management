@extends('layouts.guest')

@section('title', 'Từ điển yêu thích — AmaTrung')

@section('content')
<div class="bg-[var(--color-surface-bg)] min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-10">
            <div>
                <a href="{{ route('herb-dictionary.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-primary-600 font-semibold mb-4 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Quay lại từ điển
                </a>
                <h1 class="text-3xl font-extrabold text-slate-800 mb-2">Mục từ điển yêu thích</h1>
                <p class="text-slate-500">Các cây thuốc bạn đã lưu để xem lại nhanh.</p>
            </div>
            <div class="bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm font-semibold text-slate-700">
                Tổng cộng: <span class="text-primary-600">{{ $entries->total() }}</span> mục
            </div>
        </div>

        {{-- Grid --}}
        @if($entries->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @foreach($entries as $entry)
                    <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col group relative">
                        
                        {{-- Unfavorite Button (Absolute) --}}
                        <div class="absolute top-3 right-3 z-20">
                            <form action="{{ route('herb-dictionary.favorite', $entry) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-10 h-10 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center text-red-500 hover:text-white hover:bg-red-500 hover:scale-110 transition-all shadow-sm group/btn" title="Bỏ yêu thích">
                                    <svg class="w-5 h-5 fill-current" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                </button>
                            </form>
                        </div>

                        {{-- Media --}}
                        <div class="aspect-[4/3] bg-slate-100 relative overflow-hidden">
                            @if($entry->primary_image_url)
                                <img src="{{ $entry->primary_image_url }}" alt="{{ $entry->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center bg-primary-50 text-primary-200 group-hover:scale-105 transition-transform duration-500">
                                    <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="p-6 flex flex-col flex-grow">
                            <h3 class="text-xl font-bold text-slate-800 leading-tight mb-2">
                                {{ $entry->name }}
                            </h3>
                            
                            @if($entry->scientific_name)
                                <p class="text-slate-500 italic text-sm mb-3 font-serif">{{ $entry->scientific_name }}</p>
                            @endif
                            
                            <p class="text-slate-600 text-sm mb-6 line-clamp-2 leading-relaxed flex-grow">
                                {{ $entry->short_info ?: 'Đang cập nhật thông tin cơ bản.' }}
                            </p>
                            
                            <a href="{{ route('herb-dictionary.show', $entry) }}" class="block w-full py-2.5 text-center bg-primary-50 text-primary-600 font-bold border border-primary-100 hover:border-primary-300 hover:bg-primary-100 rounded-xl transition-colors">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-16 pagination-wrapper rounded-3xl shadow-sm">
                {{ $entries->links() }}
            </div>
        @else
            <div class="bg-white border border-slate-200 rounded-3xl p-16 text-center max-w-2xl mx-auto shadow-sm mt-8">
                <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 text-red-300">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 mb-3">Chưa có mục yêu thích</h3>
                <p class="text-slate-500 text-lg mb-8">Bạn chưa lưu cây thuốc nào. Hãy quay lại từ điển để khám phá và lưu lại những cây thuốc hữu ích nhé.</p>
                <a href="{{ route('herb-dictionary.index') }}" class="inline-block px-8 py-3.5 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 transition-colors shadow-md shadow-primary-500/20">
                    Khám phá từ điển
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
