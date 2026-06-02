@extends('layouts.guest')

@section('title', 'Từ điển dược liệu — AmaTrung')

@section('content')
<div class="bg-[var(--color-surface-bg)] min-h-screen py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-6 animate-fade-in">

        {{-- Hero Section (Shorter padding and smaller fonts) --}}
        <div class="relative bg-cover bg-center bg-no-repeat rounded-[2.5rem] py-8 md:py-10 text-center shadow-md border border-sky-100" style="background-image: url('{{ asset('images/home-imagecute.png') }}');">
            <!-- Golden Badge -->
            <div class="flex justify-center mb-3">
                <div class="inline-flex items-center gap-2 px-4 py-1 rounded-full border border-[#d4af37]/45 bg-[#fffdf9]/90 text-[#b58e3d] font-bold text-[11px] tracking-wider shadow-sm transition-transform hover:scale-105 duration-300">
                    <span class="text-xs opacity-80">⚜️</span>
                    <span>Cơ sở dữ liệu y khoa</span>
                    <span class="text-xs opacity-80">⚜️</span>
                </div>
            </div>
            
            <h1 class="text-2xl md:text-4xl font-black text-[#1a5b8f] tracking-wide mb-2 uppercase">Từ điển dược liệu</h1>
            
            <div class="max-w-2xl mx-auto px-4 flex flex-col items-center">
                <p class="text-xs md:text-sm text-[#1a5b8f] font-semibold italic leading-relaxed text-center drop-shadow-sm mb-4">
                    "Tra cứu công dụng, đặc tính và cách dùng của các loại thảo dược. Thông tin được biên soạn để tham khảo, không thay thế thăm khám trực tiếp."
                </p>

                <div class="shrink-0">
                    @auth
                        <a href="{{ route('herb-dictionary.favorites') }}" class="inline-flex items-center gap-2 px-5 py-2 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-full transition-all shadow-sm text-xs">
                            <svg class="w-4 h-4 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>
                            Mục yêu thích của bạn
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-full transition-all shadow-sm text-xs">
                            Đăng nhập để lưu thuốc
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Search & Toolbar --}}
        @if($searchEnabled)
            <div class="space-y-4">
                <form action="{{ route('herb-dictionary.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 bg-white p-2 rounded-2xl shadow-sm border border-slate-100 max-w-3xl mx-auto w-full">
                    <!-- Preserve letter in search -->
                    @if(request('letter'))
                        <input type="hidden" name="letter" value="{{ request('letter') }}">
                    @endif
                    <div class="flex-1 relative">
                        <svg class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên thuốc, tên khoa học, tác dụng..." class="w-full pl-11 pr-4 py-2.5 bg-transparent border-none focus:ring-0 text-slate-800 text-sm placeholder-slate-400 focus:outline-none">
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <button type="submit" class="px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-extrabold rounded-xl transition-all shadow-sm text-xs cursor-pointer">
                            Tra cứu
                        </button>
                    </div>
                </form>

                <!-- A-Z Alphabet Filter (Compact style to fit on one row on desktop) -->
                <div class="flex flex-col gap-3">
                    <div class="text-[11px] md:text-xs font-extrabold text-slate-400 uppercase tracking-widest text-center">Lọc nhanh theo chữ cái đầu</div>
                    <div class="flex flex-wrap justify-center gap-1.5 max-w-full mx-auto">
                        @php
                            $alphabet = ['Tất cả', 'A', 'B', 'C', 'D', 'Đ', 'E', 'G', 'H', 'I', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'X', 'Y'];
                            $currentLetter = request('letter');
                        @endphp
                        @foreach($alphabet as $letter)
                            @php
                                $isActive = ($letter === 'Tất cả' && !$currentLetter) || ($currentLetter === $letter);
                                if ($letter === 'Tất cả') {
                                    $url = route('herb-dictionary.index', request()->except(['letter', 'page']));
                                } else {
                                    $url = route('herb-dictionary.index', array_merge(request()->all(), ['letter' => $letter, 'page' => 1]));
                                }
                            @endphp
                            <a href="{{ $url }}" class="inline-flex items-center justify-center px-3 py-1 md:px-3.5 md:py-1.5 rounded-lg md:rounded-xl text-xs md:text-sm font-extrabold transition-all duration-200 shadow-sm border {{ $isActive ? 'bg-sky-500 border-sky-500 text-white shadow-sm' : 'bg-white border-slate-200 text-slate-600 hover:text-sky-600 hover:border-sky-300 hover:bg-sky-50' }}">
                                {{ $letter }}
                            </a>
                        @endforeach
                    </div>
                </div>

                @if(request('q') || request('letter'))
                    <div class="flex justify-center mt-1">
                        <a href="{{ route('herb-dictionary.index') }}" class="inline-flex items-center gap-1 px-4 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-full transition-all shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Xóa lọc
                        </a>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-6 text-center max-w-3xl mx-auto shadow-sm">
                <div class="inline-flex items-center justify-center w-11 h-11 rounded-full bg-blue-100 text-blue-600 mb-2">
                    <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-1">Cần đăng nhập</h3>
                <p class="text-slate-500 text-xs leading-relaxed max-w-lg mx-auto">Khách chỉ có thể xem danh sách. Vui lòng đăng nhập để tra cứu, xem chi tiết và lưu các cây thuốc yêu thích.</p>
                <a href="{{ route('login') }}" class="inline-block mt-3 text-sky-600 text-xs font-bold hover:underline">Đăng nhập ngay →</a>
            </div>
        @endif

        {{-- Grid (4 Columns of Portrait Cards, matching Yeu Thich page style) --}}
        <div class="pt-2">
            @if($entries->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 items-stretch">
                    @foreach($entries as $entry)
                        <div class="bg-white rounded-[1.75rem] border border-slate-100/80 p-3 sm:p-5 shadow-sm hover:shadow-[0_10px_25px_rgba(59,130,246,0.05)] hover:border-sky-100/70 transition-all flex flex-row sm:flex-col justify-between items-center sm:items-stretch min-h-0 sm:min-h-[280px]">
                            
                            {{-- Compact image at the top --}}
                            @auth
                                <a href="{{ route('herb-dictionary.show', $entry->slug) }}" class="block w-20 h-20 sm:w-full sm:h-32 shrink-0 rounded-xl sm:rounded-2xl bg-sky-50/70 p-1.5 sm:p-2 flex items-center justify-center border border-slate-100/50 overflow-hidden shadow-inner mb-0 mr-3 sm:mb-4 sm:mr-0">
                            @else
                                <a href="{{ route('login') }}" class="block w-20 h-20 sm:w-full sm:h-32 shrink-0 rounded-xl sm:rounded-2xl bg-sky-50/70 p-1.5 sm:p-2 flex items-center justify-center border border-slate-100/50 overflow-hidden shadow-inner mb-0 mr-3 sm:mb-4 sm:mr-0">
                            @endauth
                                @if($entry->primary_image_url)
                                    <img src="{{ $entry->primary_image_url }}" alt="{{ $entry->name }}" class="w-full h-full object-cover rounded-lg sm:rounded-xl">
                                @else
                                    <span class="text-2xl sm:text-3xl">🌿</span>
                                @endif
                            </a>

                            {{-- Text details below image --}}
                            <div class="flex-grow flex-1 flex flex-col justify-between h-20 sm:h-auto min-w-0">
                                <div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[8px] sm:text-[9px] font-extrabold uppercase tracking-wide bg-sky-50 text-sky-600 border border-sky-100/50">
                                        dược liệu
                                    </span>
                                    <h4 class="font-black text-slate-800 text-sm sm:text-base mt-0.5 sm:mt-1 leading-snug truncate">
                                        @auth
                                            <a href="{{ route('herb-dictionary.show', $entry->slug) }}" class="hover:text-sky-600 transition">
                                                {{ $entry->name }}
                                            </a>
                                        @else
                                            <a href="{{ route('login') }}" class="hover:text-sky-600 transition">
                                                {{ $entry->name }}
                                            </a>
                                        @endauth
                                    </h4>
                                    <p class="text-[10px] sm:text-[11px] font-bold text-slate-400 italic truncate mt-0.5">
                                        {{ $entry->scientific_name }}
                                    </p>
                                </div>

                                <div>
                                    {{-- Divider --}}
                                    <div class="border-t border-slate-100/60 my-1.5 sm:my-2 hidden sm:block"></div>

                                    {{-- Action Row --}}
                                    <div class="flex items-center justify-between gap-1.5 sm:gap-2 mt-1 sm:mt-0">
                                        @auth
                                            <a href="{{ route('herb-dictionary.show', $entry->slug) }}" class="inline-flex items-center justify-center gap-1 flex-grow py-1.5 sm:py-2 bg-sky-500 hover:bg-sky-600 text-white font-extrabold rounded-full text-[10px] sm:text-xs shadow-sm transition-all cursor-pointer">
                                                Xem chi tiết <span class="text-[8px] sm:text-[9px] hidden sm:inline">▶</span>
                                            </a>
                                        @else
                                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-1 flex-grow py-1.5 sm:py-2 bg-slate-105 text-slate-500 font-extrabold rounded-full text-[10px] sm:text-xs transition-all cursor-pointer">
                                                Đăng nhập để xem
                                            </a>
                                        @endauth

                                        @auth
                                            {{-- Favorite heart button toggle --}}
                                            <form action="{{ route('herb-dictionary.favorite', $entry->slug) }}" method="POST" class="inline shrink-0">
                                                @csrf
                                                <button type="submit" class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white hover:bg-rose-50 border border-slate-100 hover:border-rose-100 flex items-center justify-center shadow-sm text-rose-500 hover:scale-105 active:scale-95 transition-all cursor-pointer focus:outline-none" title="{{ $entry->isFavoritedBy(auth()->user()) ? 'Bỏ thích' : 'Yêu thích' }}">
                                                    @if($entry->isFavoritedBy(auth()->user()))
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-4.5 sm:w-4.5 fill-current text-rose-500" viewBox="0 0 24 24">
                                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-4.5 sm:w-4.5 fill-none stroke-current text-slate-400 hover:text-rose-500" stroke-width="2" viewBox="0 0 24 24">
                                                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                        </svg>
                                                    @endif
                                                </button>
                                            </form>
                                        @endauth
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
                <div class="bg-white border border-slate-200/80 rounded-3xl p-12 text-center max-w-2xl mx-auto shadow-sm">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-350">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1.5">Không tìm thấy kết quả</h3>
                    <p class="text-slate-500 text-sm">Chưa có cây thuốc nào phù hợp với tìm kiếm của bạn. Hãy thử một từ khóa khác hoặc xóa lọc.</p>
                    @if(request('q') || request('letter'))
                        <a href="{{ route('herb-dictionary.index') }}" class="inline-block mt-4 px-5 py-2 bg-sky-50 text-sky-600 font-bold rounded-xl hover:bg-sky-100 transition-colors text-xs">
                            Xóa lọc tìm kiếm
                        </a>
                    @endif
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
