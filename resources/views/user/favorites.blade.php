@extends('layouts.guest')

@section('title', 'Mục yêu thích — AmaTrung')

@section('content')
<div class="bg-[var(--color-surface-bg)] min-h-screen py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    {{-- Nền loang màu hồng và xanh biển pastel --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute top-[-5%] left-[-5%] w-[400px] h-[400px] bg-pink-200/50 rounded-full blur-[100px] animate-float-slow"></div>
        <div class="absolute top-[30%] right-[-10%] w-[500px] h-[500px] bg-sky-200/50 rounded-full blur-[120px] animate-float-fast"></div>
        <div class="absolute bottom-[-10%] left-[20%] w-[600px] h-[600px] bg-indigo-100/40 rounded-full blur-[100px] animate-float-slow"></div>
    </div>

    <div class="max-w-6xl mx-auto space-y-8 animate-fade-in relative z-10">

        {{-- Simple Cute Header (No Banner container, per request) --}}
        <div class="py-2 text-center md:text-left">
            <h1 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tight leading-tight flex items-center justify-center md:justify-start gap-3">
                Góc yêu thích của "{{ auth()->user()->name }}" <span class="text-rose-500 animate-pulse text-4xl">💖</span>
            </h1>
        </div>

        {{-- Tab Section --}}
        <div class="space-y-8">
            {{-- Cute Tab Buttons - Pastel Blue Theme (Smaller padding and font) --}}
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 border-b border-slate-200/60 pb-2">
                <button id="tab-herbs-btn" onclick="switchTab('herbs')" class="px-6 py-3.5 rounded-2xl font-black text-sm md:text-base transition-all duration-300 flex items-center gap-2 cursor-pointer shadow-sm border focus:outline-none bg-sky-500 text-white border-sky-400">
                    <span class="text-lg">🌿</span> Dược liệu đã lưu ({{ $favHerbs->count() }})
                </button>
                <button id="tab-articles-btn" onclick="switchTab('articles')" class="px-6 py-3.5 rounded-2xl font-black text-sm md:text-base transition-all duration-300 flex items-center gap-2 cursor-pointer shadow-sm border focus:outline-none bg-white/80 backdrop-blur-sm text-slate-600 border-slate-200 hover:bg-sky-50 hover:text-sky-600 hover:border-sky-100">
                    <span class="text-lg">📖</span> Bài viết yêu thích ({{ $favArticles->count() }})
                </button>
            </div>

            {{-- Tab 1: Favorite Herbs Section --}}
            <div id="herbs-section" class="space-y-6">
                @if($favHerbs->count() > 0)
                    {{-- Count Header (Smaller font and padding) --}}
                    <div class="flex items-center gap-3 px-6 py-3.5 rounded-full bg-sky-50/80 backdrop-blur-sm border border-sky-100/70 text-sky-850 text-sm md:text-base font-semibold shadow-sm w-fit">
                        <span class="text-sky-500 text-base">🌱</span>
                        <span>Bạn đang có <strong class="text-sky-600 font-black">{{ $favHerbs->count() }} dược liệu</strong> được lưu</span>
                    </div>

                    {{-- 4 Columns Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 items-stretch">
                        @foreach($favHerbs as $herb)
                            <div class="bg-white/90 backdrop-blur-md rounded-[2rem] border border-slate-100/80 p-3 sm:p-6 shadow-sm hover:shadow-[0_15px_30px_rgba(59,130,246,0.1)] hover:border-sky-200/80 transition-all flex flex-row sm:flex-col justify-between items-center sm:items-stretch min-h-0 sm:min-h-[300px]">
                                {{-- Compact image at the top --}}
                                <a href="{{ route('herb-dictionary.show', $herb->slug) }}" class="block w-20 h-20 sm:w-full sm:h-36 shrink-0 rounded-xl sm:rounded-2xl bg-sky-50/70 p-1.5 sm:p-2 flex items-center justify-center border border-slate-100/50 overflow-hidden shadow-inner mb-0 mr-3 sm:mb-5 sm:mr-0">
                                    @if($herb->primary_image_url)
                                        <img src="{{ $herb->primary_image_url }}" alt="{{ $herb->name }}" class="w-full h-full object-cover rounded-lg sm:rounded-xl">
                                    @else
                                        <span class="text-2xl sm:text-4xl">🌿</span>
                                    @endif
                                </a>

                                {{-- Text details below image --}}
                                <div class="flex-grow flex-1 flex flex-col justify-between h-20 sm:h-auto min-w-0">
                                    <div>
                                        <span class="inline-flex items-center px-2 py-0.5 sm:px-3 sm:py-1 rounded-full text-[8px] sm:text-[10px] font-extrabold uppercase tracking-wide bg-sky-50 text-sky-600 border border-sky-100/50">
                                            dược liệu
                                        </span>
                                        <h4 class="font-black text-slate-800 text-sm sm:text-lg mt-0.5 sm:mt-2 leading-snug truncate">
                                            <a href="{{ route('herb-dictionary.show', $herb->slug) }}" class="hover:text-sky-600 transition">
                                                {{ $herb->name }}
                                            </a>
                                        </h4>
                                        <p class="text-[10px] sm:text-xs font-bold text-slate-400 italic truncate mt-0.5 sm:mt-1">
                                            {{ $herb->scientific_name }}
                                        </p>
                                    </div>

                                    <div>
                                        {{-- Divider --}}
                                        <div class="border-t border-slate-100/60 my-1.5 sm:my-3 hidden sm:block"></div>

                                        {{-- Action Row --}}
                                        <div class="flex items-center justify-between gap-1.5 sm:gap-3 mt-1 sm:mt-0">
                                            <a href="{{ route('herb-dictionary.show', $herb->slug) }}" class="inline-flex items-center justify-center gap-1 flex-grow py-1.5 sm:py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-extrabold rounded-full text-[10px] sm:text-sm shadow-md transition-all cursor-pointer">
                                                Xem chi tiết <span class="text-[8px] sm:text-[10px] hidden sm:inline">▶</span>
                                            </a>

                                            {{-- Unfavorite heart --}}
                                            <form action="{{ route('herb-dictionary.favorite', $herb->slug) }}" method="POST" class="inline shrink-0">
                                                @csrf
                                                <button type="submit" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-200 flex items-center justify-center shadow-sm text-rose-500 hover:scale-105 active:scale-95 transition-all cursor-pointer focus:outline-none" title="Bỏ thích">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 fill-current text-rose-500" viewBox="0 0 24 24">
                                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Placeholder Card (bouncing design) --}}
                        @if($favHerbs->count() < 4)
                            <div class="border-2 border-dashed border-sky-300 bg-sky-50/40 backdrop-blur-sm rounded-[2rem] p-4 sm:p-6 flex flex-col items-center justify-center text-center min-h-[200px] sm:min-h-[300px] w-full shadow-sm hover:bg-sky-50/60 transition-colors">
                                <svg class="w-16 h-12 sm:w-24 sm:h-16 text-sky-400 mb-2 sm:mb-3" viewBox="0 0 100 60" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M 30,35 L 70,35 L 65,55 L 35,55 Z" fill="#f0f9ff" stroke="#38bdf8" stroke-width="2" />
                                    <path d="M 30,35 L 20,25 L 50,25 Z" fill="#e0f2fe" stroke="#38bdf8" />
                                    <path d="M 70,35 L 80,25 L 50,25 Z" fill="#e0f2fe" stroke="#38bdf8" />
                                    <path d="M 45,20 Q 42,12 48,10 Q 52,18 45,20 Z" fill="#0ea5e9" opacity="0.6" stroke="none" />
                                    <path d="M 55,20 Q 58,12 52,10 Q 48,18 55,20 Z" fill="#38bdf8" opacity="0.6" stroke="none" />
                                    <path d="M 38,18 Q 32,15 36,10 Q 42,12 38,18 Z" fill="#7dd3fc" opacity="0.6" stroke="none" />
                                    <path d="M 68,18 C 65,15 60,15 60,20 C 60,25 68,30 68,30 C 68,30 76,25 76,20 C 76,15 71,15 68,18 Z" fill="#f43f5e" stroke="none" />
                                    <path d="M 68,30 Q 64,36 62,42" stroke="#bae6fd" stroke-dasharray="2 2" />
                                </svg>
                                <h4 class="font-extrabold text-slate-800 text-xs sm:text-sm mb-1 sm:mb-1.5">Còn nhiều điều thú vị đang chờ bạn!</h4>
                                <p class="text-slate-500 text-[10px] sm:text-xs leading-relaxed max-w-[220px] mx-auto">
                                    Lưu thêm dược liệu hoặc khám phá các bài viết hữu ích khác để bộ sưu tập của bạn phong phú hơn nhé.
                                </p>
                                <a href="{{ route('herb-dictionary.index') }}" class="mt-3 sm:mt-5 inline-flex items-center gap-1.5 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-extrabold rounded-full text-xs sm:text-sm shadow-md hover:scale-102 transition-all cursor-pointer">
                                    Xem các dược liệu khác <span class="text-[10px]">→</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                    {{-- Cute Empty State --}}
                    <div class="bg-white rounded-[2.5rem] border border-slate-100 p-12 text-center shadow-sm max-w-xl mx-auto space-y-6">
                        <div class="text-6xl animate-bounce">🌱</div>
                        <h3 class="text-xl font-black text-slate-800">Ôi! Bạn chưa lưu thảo dược nào hết trơn...</h3>
                        <p class="text-slate-500 text-sm leading-relaxed max-w-sm mx-auto font-medium">
                            Đừng lo nhé, cẩm nang thuốc Nam phong phú của chúng tôi có hàng ngàn vị thuốc quý. Hãy ghé qua tìm kiếm vị thuốc bạn quan tâm nhé!
                        </p>
                        <a href="{{ route('herb-dictionary.index') }}" class="inline-flex items-center gap-2 px-6 py-3.5 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-2xl shadow-md shadow-sky-500/10 hover:shadow-lg transition-all text-sm">
                            Đến Từ điển dược liệu
                        </a>
                    </div>
                @endif
            </div>

            {{-- Tab 2: Favorite Articles Section --}}
            <div id="articles-section" class="space-y-6 hidden">
                @if($favArticles->count() > 0)
                    {{-- Count Header (Smaller font and padding) --}}
                    <div class="flex items-center gap-3 px-6 py-3.5 rounded-full bg-sky-50/80 backdrop-blur-sm border border-sky-100/70 text-sky-850 text-sm md:text-base font-semibold shadow-sm w-fit">
                        <span class="text-sky-500 text-base">📖</span>
                        <span>Bạn đang có <strong class="text-sky-600 font-black">{{ $favArticles->count() }} bài viết</strong> được lưu</span>
                    </div>

                    {{-- 4 Columns Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 items-stretch">
                        @foreach($favArticles as $article)
                            <div class="bg-white/90 backdrop-blur-md rounded-[2rem] border border-slate-100/80 p-3 sm:p-6 shadow-sm hover:shadow-[0_15px_30px_rgba(59,130,246,0.1)] hover:border-sky-200/80 transition-all flex flex-row sm:flex-col justify-between items-center sm:items-stretch min-h-0 sm:min-h-[300px]">
                                {{-- Compact image at the top --}}
                                <a href="{{ route('articles.show', $article->slug) }}" class="block w-20 h-20 sm:w-full sm:h-36 shrink-0 rounded-xl sm:rounded-2xl bg-sky-50/70 p-1.5 sm:p-2 flex items-center justify-center border border-slate-100/50 overflow-hidden shadow-inner mb-0 mr-3 sm:mb-5 sm:mr-0">
                                    @if($article->featured_image)
                                        <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover rounded-lg sm:rounded-xl">
                                    @else
                                        <span class="text-2xl sm:text-4xl">📖</span>
                                    @endif
                                </a>

                                {{-- Text details below image --}}
                                <div class="flex-grow flex-1 flex flex-col justify-between h-20 sm:h-auto min-w-0">
                                    <div>
                                        <span class="inline-flex items-center px-2 py-0.5 sm:px-3 sm:py-1 rounded-full text-[8px] sm:text-[10px] font-extrabold uppercase tracking-wide bg-sky-50 text-sky-600 border border-sky-100/50">
                                            {{ $article->category_label ?? 'Bài viết' }}
                                        </span>
                                        <h4 class="font-black text-slate-800 text-sm sm:text-lg mt-0.5 sm:mt-2 leading-snug line-clamp-2">
                                            <a href="{{ route('articles.show', $article->slug) }}" class="hover:text-sky-600 transition">
                                                {{ $article->title }}
                                            </a>
                                        </h4>
                                        <p class="text-[10px] sm:text-xs font-bold text-slate-400 mt-0.5 sm:mt-1">
                                            {{ $article->published_at ? $article->published_at->format('d/m/Y') : $article->created_at->format('d/m/Y') }}
                                        </p>
                                    </div>

                                    <div>
                                        {{-- Divider --}}
                                        <div class="border-t border-slate-100/60 my-1.5 sm:my-3 hidden sm:block"></div>

                                        {{-- Action Row --}}
                                        <div class="flex items-center justify-between gap-1.5 sm:gap-3 mt-1 sm:mt-0">
                                            <a href="{{ route('articles.show', $article->slug) }}" class="inline-flex items-center justify-center gap-1.5 flex-grow py-1.5 sm:py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-extrabold rounded-full text-[10px] sm:text-sm shadow-md transition-all cursor-pointer">
                                                Xem chi tiết <span class="text-[8px] sm:text-[10px] hidden sm:inline">▶</span>
                                            </a>

                                            {{-- Unlike heart --}}
                                            <form action="{{ route('articles.like', $article->id) }}" method="POST" class="inline shrink-0">
                                                @csrf
                                                <button type="submit" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-200 flex items-center justify-center shadow-sm text-rose-500 hover:scale-105 active:scale-95 transition-all cursor-pointer focus:outline-none" title="Bỏ thích">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 fill-current text-rose-500" viewBox="0 0 24 24">
                                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Placeholder Card (bouncing design) --}}
                        @if($favArticles->count() < 4)
                            <div class="border-2 border-dashed border-sky-300 bg-sky-50/40 backdrop-blur-sm rounded-[2rem] p-4 sm:p-6 flex flex-col items-center justify-center text-center min-h-[200px] sm:min-h-[300px] w-full shadow-sm hover:bg-sky-50/60 transition-colors">
                                <svg class="w-16 h-12 sm:w-24 sm:h-16 text-sky-400 mb-2 sm:mb-3" viewBox="0 0 100 60" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M 30,35 L 70,35 L 65,55 L 35,55 Z" fill="#f0f9ff" stroke="#38bdf8" stroke-width="2" />
                                    <path d="M 30,35 L 20,25 L 50,25 Z" fill="#e0f2fe" stroke="#38bdf8" />
                                    <path d="M 70,35 L 80,25 L 50,25 Z" fill="#e0f2fe" stroke="#38bdf8" />
                                    <path d="M 45,20 Q 42,12 48,10 Q 52,18 45,20 Z" fill="#0ea5e9" opacity="0.6" stroke="none" />
                                    <path d="M 55,20 Q 58,12 52,10 Q 48,18 55,20 Z" fill="#38bdf8" opacity="0.6" stroke="none" />
                                    <path d="M 38,18 Q 32,15 36,10 Q 42,12 38,18 Z" fill="#7dd3fc" opacity="0.6" stroke="none" />
                                    <path d="M 68,18 C 65,15 60,15 60,20 C 60,25 68,30 68,30 C 68,30 76,25 76,20 C 76,15 71,15 68,18 Z" fill="#f43f5e" stroke="none" />
                                    <path d="M 68,30 Q 64,36 62,42" stroke="#bae6fd" stroke-dasharray="2 2" />
                                </svg>
                                <h4 class="font-extrabold text-slate-800 text-xs sm:text-sm mb-1 sm:mb-1.5">Còn nhiều điều thú vị đang chờ bạn!</h4>
                                <p class="text-slate-500 text-[10px] sm:text-xs leading-relaxed max-w-[220px] mx-auto">
                                    Lưu thêm dược liệu hoặc khám phá các bài viết hữu ích khác để bộ sưu tập của bạn phong phú hơn nhé.
                                </p>
                                <a href="{{ route('articles.index') }}" class="mt-3 sm:mt-5 inline-flex items-center gap-1.5 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-extrabold rounded-full text-xs sm:text-sm shadow-md hover:scale-102 transition-all cursor-pointer">
                                    Xem các bài viết khác <span class="text-[10px]">→</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                    {{-- Cute Empty State --}}
                    <div class="bg-white rounded-[2.5rem] border border-slate-100 p-12 text-center shadow-sm max-w-xl mx-auto space-y-6">
                        <div class="text-6xl animate-bounce">📖</div>
                        <h3 class="text-xl font-black text-slate-800">Hình như bạn chưa lưu bài viết nào...</h3>
                        <p class="text-slate-500 text-sm leading-relaxed max-w-sm mx-auto font-medium">
                            Chúng tôi cập nhật hàng ngày các bài phân tích y khoa chuyên sâu từ bác sĩ. Hãy dạo quanh góc tri thức để khám phá thêm nhé!
                        </p>
                        <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 px-6 py-3.5 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-2xl shadow-md shadow-sky-500/10 hover:shadow-lg transition-all text-sm">
                            Khám phá bài viết
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    function switchTab(tab) {
        const herbsBtn = document.getElementById('tab-herbs-btn');
        const articlesBtn = document.getElementById('tab-articles-btn');
        const herbsSec = document.getElementById('herbs-section');
        const articlesSec = document.getElementById('articles-section');

        if (tab === 'herbs') {
            // Style herbs tab active
            herbsBtn.className = "px-5 py-3 rounded-2xl font-black text-xs md:text-sm transition-all duration-300 flex items-center gap-2 cursor-pointer shadow-sm border focus:outline-none bg-sky-500 text-white border-sky-400";
            
            // Style articles tab inactive
            articlesBtn.className = "px-5 py-3 rounded-2xl font-black text-xs md:text-sm transition-all duration-300 flex items-center gap-2 cursor-pointer shadow-sm border focus:outline-none bg-white text-slate-600 border-slate-200 hover:bg-sky-50 hover:text-sky-600 hover:border-sky-100";

            // Show sections
            herbsSec.classList.remove('hidden');
            articlesSec.classList.add('hidden');
        } else {
            // Style articles tab active
            articlesBtn.className = "px-5 py-3 rounded-2xl font-black text-xs md:text-sm transition-all duration-300 flex items-center gap-2 cursor-pointer shadow-sm border focus:outline-none bg-sky-500 text-white border-sky-400";
            
            // Style herbs tab inactive
            herbsBtn.className = "px-5 py-3 rounded-2xl font-black text-xs md:text-sm transition-all duration-300 flex items-center gap-2 cursor-pointer shadow-sm border focus:outline-none bg-white text-slate-600 border-slate-200 hover:bg-sky-50 hover:text-sky-600 hover:border-sky-100";

            // Show sections
            articlesSec.classList.remove('hidden');
            herbsSec.classList.add('hidden');
        }
    }
</script>
<style>
    /* Premium Animations & Style rules */
    .animate-float-slow {
        animation: floatSlow 8s ease-in-out infinite;
    }
    .animate-float-fast {
        animation: floatFast 6s ease-in-out infinite;
    }
    @keyframes floatSlow {
        0%, 100% { transform: translate(0px, 0px) rotate(0deg); }
        50% { transform: translate(15px, -15px) rotate(8deg); }
    }
    @keyframes floatFast {
        0%, 100% { transform: translate(0px, 0px) rotate(0deg); }
        50% { transform: translate(-10px, -20px) rotate(-12deg); }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush
@endsection
