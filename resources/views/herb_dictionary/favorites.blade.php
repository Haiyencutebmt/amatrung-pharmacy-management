@extends('layouts.guest')

@section('title', 'Từ điển yêu thích — AmaTrung')

@section('content')
<div class="bg-[var(--color-surface-bg)] min-h-screen py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-6 animate-fade-in">
        
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 pb-2 border-b border-slate-200/60">
            <div>
                <a href="{{ route('herb-dictionary.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-sky-650 transition-colors mb-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Quay lại từ điển
                </a>
                <h1 class="text-2xl md:text-3xl font-black text-slate-800 tracking-tight leading-tight">Mục từ điển yêu thích</h1>
                <p class="text-slate-400 text-xs font-bold mt-1">Các cây thuốc bạn đã lưu để xem lại nhanh.</p>
            </div>
            <div class="bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm text-xs font-bold text-slate-600">
                Tổng cộng: <span class="text-sky-500 font-extrabold">{{ $entries->total() }}</span> mục
            </div>
        </div>

        {{-- Grid --}}
        @if($entries->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 items-stretch">
                @foreach($entries as $entry)
                    <div class="bg-white rounded-[1.75rem] border border-slate-100/80 p-5 shadow-sm hover:shadow-[0_10px_25px_rgba(59,130,246,0.05)] hover:border-sky-100/70 transition-all flex flex-col justify-between relative overflow-hidden min-h-[280px]">
                        
                        {{-- Compact image at the top --}}
                        <div class="w-full h-32 rounded-2xl bg-sky-50/70 p-2 flex items-center justify-center border border-slate-100/50 overflow-hidden shadow-inner mb-4 shrink-0">
                            @if($entry->primary_image_url)
                                <img src="{{ $entry->primary_image_url }}" alt="{{ $entry->name }}" class="w-full h-full object-cover rounded-xl">
                            @else
                                <span class="text-3xl">🌿</span>
                            @endif
                        </div>

                        {{-- Text details below image --}}
                        <div class="flex-grow flex flex-col justify-between">
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-wide bg-sky-50 text-sky-600 border border-sky-100/50">
                                    dược liệu
                                </span>
                                <h4 class="font-black text-slate-800 text-base mt-1 leading-snug truncate">
                                    {{ $entry->name }}
                                </h4>
                                <p class="text-[11px] font-bold text-slate-400 italic truncate mt-0.5">
                                    {{ $entry->scientific_name }}
                                </p>
                            </div>

                            <div>
                                {{-- Divider --}}
                                <div class="border-t border-slate-100/60 my-2"></div>

                                {{-- Action Row --}}
                                <div class="flex items-center justify-between gap-2">
                                    <a href="{{ route('herb-dictionary.show', $entry->slug) }}" class="inline-flex items-center justify-center gap-1 flex-grow py-2 bg-sky-500 hover:bg-sky-600 text-white font-extrabold rounded-full text-xs shadow-sm transition-all cursor-pointer">
                                        Xem chi tiết <span class="text-[9px]">▶</span>
                                    </a>

                                    {{-- Unfavorite heart --}}
                                    <form action="{{ route('herb-dictionary.favorite', $entry->slug) }}" method="POST" class="inline shrink-0">
                                        @csrf
                                        <button type="submit" class="w-9 h-9 rounded-full bg-white hover:bg-rose-50 border border-slate-100 hover:border-rose-100 flex items-center justify-center shadow-sm text-rose-500 hover:scale-105 active:scale-95 transition-all cursor-pointer focus:outline-none" title="Bỏ thích">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5 fill-current text-rose-500" viewBox="0 0 24 24">
                                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-12 pagination-wrapper rounded-3xl shadow-sm">
                {{ $entries->links() }}
            </div>
        @else
            <div class="bg-white border border-slate-200/80 rounded-3xl p-12 text-center max-w-2xl mx-auto shadow-sm mt-4">
                <div class="w-16 h-16 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-4 text-rose-350">
                    <svg class="w-8 h-8 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-1.5">Chưa có mục yêu thích</h3>
                <p class="text-slate-500 text-sm mb-6">Bạn chưa lưu cây thuốc nào. Hãy quay lại từ điển để khám phá và lưu lại những cây thuốc hữu ích nhé.</p>
                <a href="{{ route('herb-dictionary.index') }}" class="inline-flex items-center gap-1.5 px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-extrabold rounded-full transition-all text-xs shadow-sm">
                    Khám phá từ điển
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
