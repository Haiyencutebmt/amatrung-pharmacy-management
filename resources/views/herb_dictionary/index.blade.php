@extends('layouts.guest')

@section('title', 'Từ điển dược liệu — AmaTrung')

@section('content')

{{-- Hero Section --}}
<section class="max-w-7xl mx-auto px-4 mt-8 md:mt-12">
    <div class="relative bg-cover bg-center bg-no-repeat rounded-[2.5rem] py-12 md:py-16 text-center shadow-lg border border-sky-100" style="background-image: url('{{ asset('images/home-imagecute.png') }}');">
        
        <!-- Golden Badge -->
        <div class="flex justify-center mb-4">
            <div class="inline-flex items-center gap-2.5 px-5 py-1.5 rounded-full border border-[#d4af37]/45 bg-[#fffdf9]/90 text-[#b58e3d] font-bold text-xs md:text-sm tracking-wider shadow-sm transition-transform hover:scale-105 duration-300">
                <span class="text-sm opacity-80">⚜️</span>
                <span>Cơ sở dữ liệu y khoa</span>
                <span class="text-sm opacity-80">⚜️</span>
            </div>
        </div>
        
        <h1 class="text-3xl md:text-5xl font-black text-[#1a5b8f] tracking-wide mb-3 uppercase">Từ điển dược liệu</h1>
        
        <div class="max-w-3xl mx-auto px-4 flex flex-col items-center">
            <p class="text-base md:text-lg text-[#1a5b8f] font-semibold italic leading-relaxed text-center drop-shadow-sm mb-6">
                "Tra cứu công dụng, đặc tính và cách dùng của các loại thảo dược. Thông tin được biên soạn để tham khảo, không thay thế thăm khám trực tiếp."
            </p>

            <div class="shrink-0">
                @auth
                    <a href="{{ route('herb-dictionary.favorites') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#1a5b8f] hover:bg-blue-700 text-white font-bold rounded-full transition-colors shadow-md shadow-blue-500/10">
                        <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>
                        Mục yêu thích của bạn
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#1a5b8f] hover:bg-blue-700 text-white font-bold rounded-full transition-colors shadow-md shadow-blue-500/10">
                        Đăng nhập để lưu thuốc
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>

{{-- Toolbar & Search --}}
<section class="py-8 bg-[var(--color-surface-bg)] px-4 sm:px-6 lg:px-8 border-b border-slate-200">
    <div class="max-w-7xl mx-auto">
        @if($searchEnabled)
            <div class="flex flex-col gap-5">
                <form action="{{ route('herb-dictionary.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 bg-white p-3 rounded-2xl shadow-sm border border-slate-100">
                    <!-- Preserve letter in search -->
                    @if(request('letter'))
                        <input type="hidden" name="letter" value="{{ request('letter') }}">
                    @endif
                    <div class="flex-1 relative">
                        <svg class="w-6 h-6 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên thuốc, tên khoa học, tác dụng..." class="w-full pl-12 pr-4 py-3 bg-transparent border-none focus:ring-0 text-slate-800 text-lg placeholder-slate-400 focus:outline-none">
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <button type="submit" class="px-8 py-3 bg-[#1a5b8f] hover:bg-blue-700 text-white font-bold rounded-xl transition-colors shadow-md shadow-blue-500/20">
                            Tra cứu
                        </button>
                    </div>
                </form>

                <!-- A-Z Alphabet Filter -->
                <div class="flex flex-col gap-2.5">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider text-center">Lọc nhanh theo chữ cái đầu</div>
                    <div class="flex flex-wrap justify-center gap-1.5 sm:gap-2">
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
                            <a href="{{ $url }}" class="inline-flex items-center justify-center px-3.5 py-1.5 rounded-lg text-sm font-bold transition-all duration-200 shadow-sm border {{ $isActive ? 'bg-[#1a5b8f] border-[#1a5b8f] text-white shadow-md shadow-blue-500/10' : 'bg-white border-slate-200 text-slate-600 hover:text-[#1a5b8f] hover:border-blue-300 hover:bg-blue-50' }}">
                                {{ $letter }}
                            </a>
                        @endforeach
                    </div>
                </div>

                @if(request('q') || request('letter'))
                    <div class="flex justify-center mt-2">
                        <a href="{{ route('herb-dictionary.index') }}" class="inline-flex items-center gap-1.5 px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-bold rounded-full transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Xóa lọc
                        </a>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 text-center shadow-sm">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 text-blue-600 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-1">Cần đăng nhập</h3>
                <p class="text-slate-600 text-sm">Khách chỉ có thể xem danh sách. Vui lòng đăng nhập để tra cứu, xem chi tiết và lưu các cây thuốc yêu thích.</p>
                <a href="{{ route('login') }}" class="inline-block mt-4 text-blue-600 font-bold hover:underline">Đăng nhập ngay →</a>
            </div>
        @endif
    </div>
</section>

{{-- Grid --}}
<section class="py-16 bg-[var(--color-surface-bg)] px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        @if($entries->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @foreach($entries as $entry)
                    <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col group">
                        
                        {{-- Media --}}
                        <div class="aspect-[4/3] bg-slate-100 relative overflow-hidden">
                            @if($entry->primary_image_url)
                                <img src="{{ $entry->primary_image_url }}" alt="{{ $entry->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-primary-50 text-primary-200 group-hover:scale-105 transition-transform duration-500">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif

                            <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur-md text-white text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>
                                {{ $entry->favorites_count }}
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="p-6 flex flex-col flex-grow">
                            <div class="flex justify-between items-start gap-4 mb-2">
                                <h3 class="text-xl font-bold text-slate-800 leading-tight">
                                    {{ $entry->name }}
                                </h3>
                                @auth
                                    @if($entry->isFavoritedBy(auth()->user()))
                                        <div class="bg-red-50 text-red-600 border border-red-100 text-xs font-bold px-2 py-1 rounded-md shrink-0">Đã lưu</div>
                                    @endif
                                @endauth
                            </div>
                            
                            @if($entry->scientific_name)
                                <p class="text-slate-500 italic text-sm mb-3 font-serif">{{ $entry->scientific_name }}</p>
                            @endif
                            
                            <p class="text-slate-600 text-sm mb-6 line-clamp-3 leading-relaxed flex-grow">
                                {{ $entry->short_info ?: 'Đang cập nhật thông tin cơ bản.' }}
                            </p>
                            
                            <div class="flex flex-wrap gap-2 mb-6">
                                @if($entry->plant_part)
                                    <span class="bg-green-50 text-primary-700 border border-green-100 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $entry->plant_part }}</span>
                                @endif
                                @if($entry->properties)
                                    <span class="bg-blue-50 text-blue-700 border border-blue-100 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $entry->properties }}</span>
                                @endif
                            </div>

                            @auth
                                <a href="{{ route('herb-dictionary.show', $entry) }}" class="block w-full py-2.5 text-center bg-slate-50 hover:bg-primary-50 text-primary-600 font-bold border border-slate-200 hover:border-primary-200 rounded-xl transition-colors">
                                    Xem chi tiết
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="block w-full py-2.5 text-center bg-slate-50 text-slate-500 font-semibold border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors text-sm">
                                    Đăng nhập để xem
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-16 pagination-wrapper rounded-3xl shadow-sm">
                {{ $entries->links() }}
            </div>
        @else
            <div class="bg-white border border-slate-200 rounded-3xl p-12 text-center max-w-2xl mx-auto shadow-sm">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Không tìm thấy kết quả</h3>
                <p class="text-slate-500">Chưa có cây thuốc nào phù hợp với tìm kiếm của bạn. Hãy thử một từ khóa khác.</p>
                @if(request('q'))
                    <a href="{{ route('herb-dictionary.index') }}" class="inline-block mt-6 px-6 py-2.5 bg-primary-50 text-primary-600 font-bold rounded-xl hover:bg-primary-100 transition-colors">
                        Xóa tìm kiếm
                    </a>
                @endif
            </div>
        @endif
    </div>
</section>

@endsection
