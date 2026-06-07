@extends('layouts.guest')

@section('title', 'Bài Viết — AmaTrung')

@section('content')
@php
    $categoryIcons = [
        'duoc-lieu-bai-thuoc' => [
            'label' => 'Dược liệu & bài thuốc',
            'icon' => 'M12 3.75c2.9 0 5.25 2.35 5.25 5.25 0 4.08-3.28 7.25-5.25 8.94C10.03 16.25 6.75 13.08 6.75 9A5.25 5.25 0 0 1 12 3.75Zm0 14.19V21m0-3.06c-2.9-.83-6.75-3.6-6.75-8.94A6.75 6.75 0 0 1 12 2.25 6.75 6.75 0 0 1 18.75 9c0 5.34-3.85 8.11-6.75 8.94Z',
            'tone' => 'text-emerald-600 bg-emerald-50',
        ],
        'benh-hoc-phuong-phap-dieu-tri' => [
            'label' => 'Bệnh học & điều trị',
            'icon' => 'M12 3.75 19.5 7.5v4.88c0 4.66-3.18 7.82-7.5 8.87-4.32-1.05-7.5-4.21-7.5-8.87V7.5L12 3.75Zm0 4.5v7.5m-3.75-3.75h7.5',
            'tone' => 'text-blue-600 bg-blue-50',
        ],
        'cham-soc-suc-khoe-duong-sinh' => [
            'label' => 'Chăm sóc & dưỡng sinh',
            'icon' => 'M12 21c-3.86-3.16-7.5-6.5-7.5-10.88A4.12 4.12 0 0 1 8.62 6c1.32 0 2.55.63 3.38 1.62A4.42 4.42 0 0 1 15.38 6a4.12 4.12 0 0 1 4.12 4.12C19.5 14.5 15.86 17.84 12 21Z',
            'tone' => 'text-cyan-600 bg-cyan-50',
        ],
        'tin-tuc-phong-kham' => [
            'label' => 'Tin tức phòng khám',
            'icon' => 'M5.25 4.5h13.5v15H5.25v-15Zm3 3h7.5m-7.5 3h7.5m-7.5 3h4.5',
            'tone' => 'text-indigo-600 bg-indigo-50',
        ],
        'y-hoc-hien-dai-ket-hop' => [
            'label' => 'Y học hiện đại kết hợp',
            'icon' => 'M12 3.75a8.25 8.25 0 1 0 0 16.5 8.25 8.25 0 0 0 0-16.5Zm0 3v10.5m-4.5-5.25h9',
            'tone' => 'text-violet-600 bg-violet-50',
        ],
    ];

    $latestArticles = $articles->getCollection()->take(3);
@endphp

<div class="bg-gradient-to-b from-sky-50 via-white to-sky-100/50 min-h-screen py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-6 animate-fade-in">

        {{-- Search & Category Dropdown --}}
        <form id="search-form" action="{{ route('articles.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 bg-white p-2 rounded-2xl shadow-sm border border-slate-100 max-w-3xl mx-auto w-full">
            {{-- Keyword Input --}}
            <div class="flex-1 relative">
                <svg class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="2"/><path d="m21 21-4.3-4.3" stroke-width="2" stroke-linecap="round"/></svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Tìm bài viết..." class="w-full pl-11 pr-4 py-2.5 bg-transparent border-none focus:ring-0 text-slate-800 text-sm placeholder-slate-400 focus:outline-none font-bold">
            </div>

            {{-- Category Dropdown Selector --}}
            <div class="w-full md:w-60 relative border-t md:border-t-0 md:border-l border-slate-100 pt-2 md:pt-0 md:pl-3 flex items-center" id="custom-category-dropdown-container">
                <input type="hidden" name="category" id="hidden-category-input" value="{{ $selectedCategory }}">
                
                {{-- Trigger Button --}}
                <button type="button" id="category-dropdown-trigger" class="w-full flex items-center justify-between bg-transparent border-none focus:ring-0 text-slate-700 text-sm font-bold focus:outline-none pr-8 cursor-pointer select-none py-2 text-left">
                    <span id="selected-category-label" class="truncate">
                        {{ $articleCategories[$selectedCategory] ?? 'Tất cả danh mục' }}
                    </span>
                </button>
                
                <div class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 flex items-center text-slate-400 transition-transform duration-300" id="category-dropdown-arrow" style="transform-origin: center;">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </div>

                {{-- Dropdown Menu options --}}
                <div id="category-dropdown-menu" class="hidden absolute top-full left-0 right-0 mt-3 bg-white/95 backdrop-blur-md border border-slate-100 rounded-2xl shadow-xl z-50 overflow-hidden transition-all duration-200 transform origin-top scale-95 opacity-0">
                    <div class="py-2 max-h-60 overflow-y-auto">
                        <div class="dropdown-item px-4 py-2.5 text-slate-650 hover:bg-sky-50 hover:text-sky-600 text-xs font-bold cursor-pointer transition-all duration-200 flex items-center gap-2 {{ $selectedCategory === '' ? 'bg-sky-50/50 text-sky-600' : '' }}" data-value="">
                            <span class="w-2.5 h-2.5 rounded-full border border-slate-300 {{ $selectedCategory === '' ? 'bg-sky-500 border-sky-400 shadow-sm shadow-sky-200' : 'bg-transparent' }}"></span>
                            Tất cả danh mục
                        </div>
                        @foreach($articleCategories as $categoryKey => $categoryLabel)
                            @php
                                $isActive = $selectedCategory === $categoryKey;
                                $dotColor = 'bg-slate-300 border-slate-200';
                                if ($categoryKey === 'duoc-lieu-bai-thuoc') $dotColor = 'bg-emerald-500 border-emerald-400 shadow-emerald-100';
                                elseif ($categoryKey === 'benh-hoc-phuong-phap-dieu-tri') $dotColor = 'bg-blue-500 border-blue-400 shadow-blue-100';
                                elseif ($categoryKey === 'cham-soc-suc-khoe-duong-sinh') $dotColor = 'bg-cyan-500 border-cyan-400 shadow-cyan-100';
                                elseif ($categoryKey === 'tin-tuc-phong-kham') $dotColor = 'bg-indigo-500 border-indigo-400 shadow-indigo-100';
                                elseif ($categoryKey === 'y-hoc-hien-dai-ket-hop') $dotColor = 'bg-violet-500 border-violet-400 shadow-violet-100';
                            @endphp
                            <div class="dropdown-item px-4 py-2.5 text-slate-650 hover:bg-sky-50 hover:text-sky-600 text-xs font-bold cursor-pointer transition-all duration-200 flex items-center gap-2 {{ $isActive ? 'bg-sky-50/50 text-sky-600' : '' }}" data-value="{{ $categoryKey }}">
                                <span class="w-2.5 h-2.5 rounded-full border {{ $isActive ? $dotColor . ' shadow-sm' : 'bg-slate-300 border-slate-200' }}"></span>
                                {{ $categoryLabel }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-2 shrink-0 pt-2 md:pt-0">
                <button type="submit" class="flex-1 md:flex-initial px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-extrabold rounded-xl transition-all shadow-sm text-xs cursor-pointer">
                    Tra cứu
                </button>
                @if($search || $selectedCategory)
                    <a href="{{ route('articles.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-extrabold rounded-xl transition-all text-xs flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        {{-- Content Area --}}
        <div class="pt-2">
            @if($articles->count() > 0 || (isset($featuredArticles) && $featuredArticles->count() > 0))
                <div class="flex flex-col gap-10">
                    


                    {{-- Latest Articles --}}
                    @if(!$search && !$selectedCategory && isset($latestArticles) && $latestArticles->count() > 0)
                    <div>
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="flex items-center gap-3 text-lg font-black text-blue-950">
                                <span class="h-6 w-1 rounded-full bg-blue-600"></span>
                                Bài viết mới nhất
                            </h2>
                        </div>
                        <div class="grid gap-5 md:grid-cols-3">
                            @foreach($latestArticles as $article)
                                @php
                                    $isLiked = auth()->check()
                                        && $article->relationLoaded('likedByUsers')
                                        && $article->likedByUsers->contains('id', auth()->id());
                                @endphp
                                <article class="group overflow-hidden rounded-2xl border border-slate-100/80 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-[0_12px_30px_rgba(37,99,235,0.06)] relative flex flex-row sm:flex-col">
                                    <div class="relative w-28 h-24 sm:w-full sm:h-auto shrink-0">
                                        <a href="{{ route('articles.show', $article->slug) }}" class="block h-full sm:h-auto">
                                            <div class="relative h-full sm:h-auto sm:w-full sm:aspect-[3/2] overflow-hidden bg-blue-50">
                                                @if($article->featured_image)
                                                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                                                @else
                                                    <img src="{{ asset('images/y-hoc-co-truyen.png') }}" alt="{{ $article->title }}" class="h-full w-full object-cover object-center opacity-90 transition duration-700 group-hover:scale-105">
                                                @endif

                                                <div class="absolute bottom-3 left-3 rounded-full bg-white/95 px-2.5 py-1 text-xs font-black text-blue-600 shadow-sm hidden sm:block">
                                                    {{ $article->published_at ? $article->published_at->format('d/m/Y') : $article->created_at->format('d/m/Y') }}
                                                </div>

                                                <div class="absolute left-3 top-3 max-w-[74%] rounded-full bg-sky-50 border border-sky-100/50 px-2.5 py-1 text-[10px] font-bold text-sky-600 shadow-sm truncate hidden sm:block">
                                                    {{ $article->category_label }}
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                    <button type="button"
                                            class="article-like-btn absolute right-2 top-2 sm:right-3 sm:top-3 z-10 flex h-7 min-w-7 sm:h-9 sm:min-w-9 items-center justify-center gap-1 rounded-full border px-1.5 sm:px-2 text-[10px] sm:text-xs font-black shadow-md backdrop-blur transition {{ $isLiked ? 'border-rose-200 bg-rose-500 text-white' : 'border-white/70 bg-white/95 text-slate-500 hover:text-rose-500' }}"
                                            data-like-url="{{ route('articles.like', $article->id) }}"
                                            data-login-url="{{ route('login') }}"
                                            data-liked="{{ $isLiked ? '1' : '0' }}"
                                            data-authenticated="{{ auth()->check() ? '1' : '0' }}"
                                            title="{{ $isLiked ? 'Bỏ yêu thích' : 'Thêm vào yêu thích' }}">
                                        <svg class="h-3.5 w-3.5 sm:h-4.5 sm:w-4.5 {{ $isLiked ? 'fill-current' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21c-3.86-3.16-7.5-6.5-7.5-10.88A4.12 4.12 0 0 1 8.62 6c1.32 0 2.55.63 3.38 1.62A4.42 4.42 0 0 1 15.38 6a4.12 4.12 0 0 1 4.12 4.12C19.5 14.5 15.86 17.84 12 21Z"/>
                                        </svg>
                                        <span class="article-like-count text-[9px] sm:text-[11px]">{{ $article->liked_by_users_count }}</span>
                                    </button>

                                    <a href="{{ route('articles.show', $article->slug) }}" class="block flex-1">
                                        <div class="p-3 sm:p-4 h-full flex flex-col justify-between">
                                            <div>
                                                <span class="text-[9px] font-bold text-sky-600 sm:hidden block mb-0.5">
                                                    {{ $article->category_label }}
                                                </span>
                                                <h3 class="line-clamp-2 sm:min-h-[3rem] text-sm sm:text-base font-black leading-snug sm:leading-6 text-sky-600 transition group-hover:text-sky-500">
                                                    {{ $article->title }}
                                                </h3>
                                                <p class="mt-1 line-clamp-2 sm:line-clamp-3 sm:min-h-[3.75rem] text-[11px] sm:text-xs font-semibold leading-relaxed text-slate-650 hidden sm:block">
                                                    {{ $article->summary ?: $article->excerpt }}
                                                </p>
                                            </div>

                                            <div class="mt-2 sm:mt-4 flex items-center justify-between border-t border-slate-100 pt-2 sm:pt-3 text-[10px] sm:text-[11px] font-bold text-slate-500">
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4.5 9.75h15M6 5.25h12A1.5 1.5 0 0 1 19.5 6.75v12A1.5 1.5 0 0 1 18 20.25H6a1.5 1.5 0 0 1-1.5-1.5v-12A1.5 1.5 0 0 1 6 5.25Z"/></svg>
                                                    {{ $article->published_at ? $article->published_at->format('d/m/Y') : $article->created_at->format('d/m/Y') }}
                                                </span>
                                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-sky-500 hover:bg-sky-600 text-white font-extrabold rounded-full text-[10px] sm:text-xs shadow-sm hover:shadow transition duration-300 select-none">
                                                    Xem bài
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </article>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Main list --}}
                    <div>
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="flex items-center gap-3 text-lg font-black text-blue-950">
                                <span class="h-6 w-1 rounded-full bg-blue-600"></span>
                                Tất cả bài viết
                            </h2>
                        </div>

                        <div class="flex flex-col gap-5">
                            @foreach($articles as $article)
                                @php
                                    $isLiked = auth()->check()
                                        && $article->relationLoaded('likedByUsers')
                                        && $article->likedByUsers->contains('id', auth()->id());
                                @endphp
                                <article class="group overflow-hidden rounded-2xl border border-slate-100/80 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-[0_12px_30px_rgba(37,99,235,0.06)] flex flex-row">
                                    <div class="relative w-28 h-24 md:w-1/3 md:h-auto shrink-0">
                                        <a href="{{ route('articles.show', $article->slug) }}" class="block h-full">
                                            <div class="relative h-full overflow-hidden bg-blue-50">
                                                @if($article->featured_image)
                                                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                                                @else
                                                    <img src="{{ asset('images/y-hoc-co-truyen.png') }}" alt="{{ $article->title }}" class="h-full w-full object-cover object-center opacity-90 transition duration-700 group-hover:scale-105">
                                                @endif

                                                <div class="absolute bottom-3 left-3 rounded-full bg-white/95 px-2.5 py-1 text-xs font-black text-blue-600 shadow-sm hidden md:block">
                                                    {{ $article->published_at ? $article->published_at->format('d/m/Y') : $article->created_at->format('d/m/Y') }}
                                                </div>

                                                <div class="absolute left-3 top-3 max-w-[74%] rounded-full bg-sky-50 border border-sky-100/50 px-2.5 py-1 text-[10px] font-bold text-sky-600 shadow-sm truncate hidden md:block">
                                                    {{ $article->category_label }}
                                                </div>
                                            </div>
                                        </a>

                                        <button type="button"
                                                class="article-like-btn absolute right-2 top-2 md:right-3 md:top-3 z-10 flex h-7 min-w-7 md:h-9 md:min-w-9 items-center justify-center gap-1 rounded-full border px-1.5 md:px-2 text-[10px] md:text-xs font-black shadow-md backdrop-blur transition {{ $isLiked ? 'border-rose-200 bg-rose-500 text-white' : 'border-white/70 bg-white/95 text-slate-500 hover:text-rose-500' }}"
                                                data-like-url="{{ route('articles.like', $article->id) }}"
                                                data-login-url="{{ route('login') }}"
                                                data-liked="{{ $isLiked ? '1' : '0' }}"
                                                data-authenticated="{{ auth()->check() ? '1' : '0' }}"
                                                title="{{ $isLiked ? 'Bỏ yêu thích' : 'Thêm vào yêu thích' }}">
                                            <svg class="h-3.5 w-3.5 md:h-4.5 md:w-4.5 {{ $isLiked ? 'fill-current' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21c-3.86-3.16-7.5-6.5-7.5-10.88A4.12 4.12 0 0 1 8.62 6c1.32 0 2.55.63 3.38 1.62A4.42 4.42 0 0 1 15.38 6a4.12 4.12 0 0 1 4.12 4.12C19.5 14.5 15.86 17.84 12 21Z"/>
                                            </svg>
                                            <span class="article-like-count text-[9px] md:text-[11px]">{{ $article->liked_by_users_count }}</span>
                                        </button>
                                    </div>

                                    <div class="flex-1 flex flex-col justify-center">
                                        <a href="{{ route('articles.show', $article->slug) }}" class="block p-3 md:p-6 h-full flex flex-col justify-between">
                                            <div>
                                                <span class="text-[9px] font-bold text-sky-600 md:hidden block mb-0.5">
                                                    {{ $article->category_label }}
                                                </span>
                                                <h3 class="text-sm md:text-xl font-black leading-snug text-sky-600 transition group-hover:text-sky-500 mb-1 md:mb-3 line-clamp-2">
                                                    {{ $article->title }}
                                                </h3>
                                                <p class="text-xs md:text-sm font-semibold leading-relaxed text-slate-650 flex-1 line-clamp-3 hidden md:block">
                                                    {{ $article->summary ?: $article->excerpt }}
                                                </p>
                                            </div>

                                            <div class="mt-2 md:mt-5 flex items-center justify-between border-t border-slate-100 pt-2 md:pt-4 text-[10px] md:text-xs font-bold text-slate-500">
                                                <span class="inline-flex items-center gap-1 md:gap-1.5">
                                                    <svg class="h-3 w-3 md:h-4 md:w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4.5 9.75h15M6 5.25h12A1.5 1.5 0 0 1 19.5 6.75v12A1.5 1.5 0 0 1 18 20.25H6a1.5 1.5 0 0 1-1.5-1.5v-12A1.5 1.5 0 0 1 6 5.25Z"/></svg>
                                                    {{ $article->published_at ? $article->published_at->format('d/m/Y') : $article->created_at->format('d/m/Y') }}
                                                </span>
                                                <span class="inline-flex items-center gap-1.5 text-blue-600 group-hover:text-blue-800 transition">
                                                    <span class="hidden md:inline">Xem chi tiết</span>
                                                    <svg class="h-3.5 w-3.5 md:h-4 md:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                                                </span>
                                            </div>
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="mt-10">
                            {{ $articles->withQueryString()->links() }}
                        </div>
                    </div>

                </div>
            @else
                <div class="rounded-[2rem] border border-slate-100 bg-white p-12 text-center shadow-sm">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-50 text-blue-550">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 13a2 2 0 0 1-2-2V7m2 13a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2M7 16h6M7 8h6v4H7V8Z"/></svg>
                    </div>
                    <h3 class="text-lg font-black text-blue-950">Không tìm thấy bài viết phù hợp</h3>
                    <p class="mt-2 text-xs font-semibold text-slate-500">Thử đổi danh mục hoặc nhập từ khóa khác để tìm lại nhé.</p>
                    <a href="{{ route('articles.index') }}" class="mt-4 inline-flex rounded-2xl bg-sky-500 hover:bg-sky-600 px-5 py-2.5 text-xs font-black text-white shadow-sm">
                        Xem tất cả bài viết
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Like button handler
    document.querySelectorAll('.article-like-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            if (button.dataset.authenticated !== '1') {
                window.location.href = button.dataset.loginUrl;
                return;
            }

            button.disabled = true;

            fetch(button.dataset.likeUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(function (response) {
                if (response.status === 401) {
                    window.location.href = button.dataset.loginUrl;
                    return null;
                }

                return response.json();
            })
            .then(function (data) {
                if (!data) return;

                const icon = button.querySelector('svg');
                const count = button.querySelector('.article-like-count');

                button.dataset.liked = data.liked ? '1' : '0';
                button.title = data.liked ? 'Bỏ yêu thích' : 'Thêm vào yêu thích';
                if (count) count.textContent = data.likes_count;

                button.classList.toggle('border-rose-200', data.liked);
                button.classList.toggle('bg-rose-500', data.liked);
                button.classList.toggle('text-white', data.liked);
                button.classList.toggle('border-white/70', !data.liked);
                button.classList.toggle('bg-white/95', !data.liked);
                button.classList.toggle('text-slate-500', !data.liked);
                button.classList.toggle('hover:text-rose-500', !data.liked);

                if (icon) {
                    icon.classList.toggle('fill-current', data.liked);
                    icon.classList.toggle('fill-none', !data.liked);
                }
            })
            .finally(function () {
                button.disabled = false;
            });
        });
    });

    // Custom dropdown handler
    const trigger = document.getElementById('category-dropdown-trigger');
    const menu = document.getElementById('category-dropdown-menu');
    const arrow = document.getElementById('category-dropdown-arrow');
    const container = document.getElementById('custom-category-dropdown-container');
    const hiddenInput = document.getElementById('hidden-category-input');
    const form = document.getElementById('search-form');

    if (trigger && menu && arrow && container && hiddenInput && form) {
        function openMenu() {
            menu.classList.remove('hidden');
            setTimeout(() => {
                menu.classList.remove('scale-95', 'opacity-0');
                menu.classList.add('scale-100', 'opacity-100');
            }, 10);
            arrow.style.transform = 'translateY(-50%) rotate(180deg)';
        }

        function closeMenu() {
            menu.classList.remove('scale-100', 'opacity-100');
            menu.classList.add('scale-95', 'opacity-0');
            arrow.style.transform = 'translateY(-50%) rotate(0deg)';
            setTimeout(() => {
                if (menu.classList.contains('opacity-0')) {
                    menu.classList.add('hidden');
                }
            }, 200);
        }

        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            const isHidden = menu.classList.contains('hidden');
            if (isHidden) {
                openMenu();
            } else {
                closeMenu();
            }
        });

        document.addEventListener('click', function(e) {
            if (!container.contains(e.target)) {
                closeMenu();
            }
        });

        menu.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function() {
                const val = this.dataset.value;
                hiddenInput.value = val;
                closeMenu();
                form.submit();
            });
        });
    }
});
</script>
@endpush
