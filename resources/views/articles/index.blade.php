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

<div class="bg-[var(--color-surface-bg)] min-h-screen py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-6 animate-fade-in">



        {{-- Search & Category Dropdown --}}
        <form action="{{ route('articles.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 bg-white p-2 rounded-2xl shadow-sm border border-slate-100 max-w-3xl mx-auto w-full">
            {{-- Keyword Input --}}
            <div class="flex-1 relative">
                <svg class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8" stroke-width="2"/><path d="m21 21-4.3-4.3" stroke-width="2" stroke-linecap="round"/></svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Tìm bài viết..." class="w-full pl-11 pr-4 py-2.5 bg-transparent border-none focus:ring-0 text-slate-800 text-sm placeholder-slate-400 focus:outline-none font-bold">
            </div>

            {{-- Category Dropdown Selector --}}
            <div class="w-full md:w-60 relative border-t md:border-t-0 md:border-l border-slate-100 pt-2 md:pt-0 md:pl-3 flex items-center">
                <select name="category" onchange="this.form.submit()" class="w-full bg-transparent border-none focus:ring-0 text-slate-700 text-sm font-bold focus:outline-none appearance-none pr-8 cursor-pointer">
                    <option value="">Tất cả danh mục</option>
                    @foreach($articleCategories as $categoryKey => $categoryLabel)
                        <option value="{{ $categoryKey }}" {{ $selectedCategory === $categoryKey ? 'selected' : '' }}>
                            {{ $categoryLabel }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 flex items-center text-slate-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
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
                    
                    {{-- Featured Articles --}}
                    @if(!$search && !$selectedCategory && isset($featuredArticles) && $featuredArticles->count() > 0)
                    <div>
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="flex items-center gap-3 text-lg font-black text-blue-950">
                                <span class="h-6 w-1 rounded-full bg-blue-600"></span>
                                Bài viết nổi bật
                            </h2>
                        </div>
                        <div class="grid gap-5 md:grid-cols-3">
                            @foreach($featuredArticles as $index => $article)
                                @php
                                    $isLiked = auth()->check()
                                        && $article->relationLoaded('likedByUsers')
                                        && $article->likedByUsers->contains('id', auth()->id());
                                @endphp
                                <article class="group overflow-hidden rounded-2xl border border-slate-100/80 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-[0_12px_30px_rgba(37,99,235,0.06)] relative">
                                    {{-- Ranking Badge --}}
                                    <div class="absolute top-0 left-0 z-20 w-10 h-10 flex items-center justify-center font-black text-sky-700 text-lg rounded-br-2xl shadow-md bg-sky-200">
                                        {{ $loop->iteration }}
                                    </div>
                                    <div class="relative">
                                        <a href="{{ route('articles.show', $article->slug) }}" class="block">
                                            <div class="relative aspect-[4/2.6] overflow-hidden bg-blue-50">
                                                @if($article->featured_image)
                                                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                                                @else
                                                    <img src="{{ asset('images/y-hoc-co-truyen.png') }}" alt="{{ $article->title }}" class="h-full w-full object-cover object-center opacity-90 transition duration-700 group-hover:scale-105">
                                                @endif

                                                <div class="absolute bottom-3 left-3 rounded-full bg-white/95 px-2.5 py-1 text-xs font-black text-blue-600 shadow-sm">
                                                    {{ $article->published_at ? $article->published_at->format('d/m/Y') : $article->created_at->format('d/m/Y') }}
                                                </div>

                                                <div class="absolute left-3 top-3 ml-10 max-w-[50%] rounded-full bg-sky-100 px-2.5 py-1 text-[10px] font-black text-sky-600 shadow-sm truncate">
                                                    {{ $article->category_label }}
                                                </div>
                                            </div>
                                        </a>

                                        <button type="button"
                                                class="article-like-btn absolute right-3 top-3 z-10 flex h-9 min-w-9 items-center justify-center gap-1 rounded-full border px-2 text-xs font-black shadow-md backdrop-blur transition {{ $isLiked ? 'border-rose-200 bg-rose-500 text-white' : 'border-white/70 bg-white/95 text-slate-500 hover:text-rose-500' }}"
                                                data-like-url="{{ route('articles.like', $article->id) }}"
                                                data-login-url="{{ route('login') }}"
                                                data-liked="{{ $isLiked ? '1' : '0' }}"
                                                data-authenticated="{{ auth()->check() ? '1' : '0' }}"
                                                title="{{ $isLiked ? 'Bỏ yêu thích' : 'Thêm vào yêu thích' }}">
                                            <svg class="h-4.5 w-4.5 {{ $isLiked ? 'fill-current' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21c-3.86-3.16-7.5-6.5-7.5-10.88A4.12 4.12 0 0 1 8.62 6c1.32 0 2.55.63 3.38 1.62A4.42 4.42 0 0 1 15.38 6a4.12 4.12 0 0 1 4.12 4.12C19.5 14.5 15.86 17.84 12 21Z"/>
                                            </svg>
                                            <span class="article-like-count text-[11px]">{{ $article->liked_by_users_count }}</span>
                                        </button>
                                    </div>

                                    <a href="{{ route('articles.show', $article->slug) }}" class="block">
                                        <div class="p-4">
                                            <h3 class="line-clamp-2 min-h-[3rem] text-base font-black leading-6 text-blue-950 transition group-hover:text-blue-700">
                                                {{ $article->title }}
                                            </h3>
                                            <p class="mt-2 line-clamp-3 min-h-[3.75rem] text-xs font-semibold leading-relaxed text-slate-650">
                                                {{ $article->summary ?: $article->excerpt }}
                                            </p>

                                            <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 text-[11px] font-bold text-slate-500">
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4.5 9.75h15M6 5.25h12A1.5 1.5 0 0 1 19.5 6.75v12A1.5 1.5 0 0 1 18 20.25H6a1.5 1.5 0 0 1-1.5-1.5v-12A1.5 1.5 0 0 1 6 5.25Z"/></svg>
                                                    {{ $article->published_at ? $article->published_at->format('d/m/Y') : $article->created_at->format('d/m/Y') }}
                                                </span>
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 12s3.5-6.75 9.75-6.75S21.75 12 21.75 12 18.25 18.75 12 18.75 2.25 12 2.25 12Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15.25a3.25 3.25 0 1 0 0-6.5 3.25 3.25 0 0 0 0 6.5Z"/></svg>
                                                    Xem bài
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
                                <article class="group overflow-hidden rounded-2xl border border-slate-100/80 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-[0_12px_30px_rgba(37,99,235,0.06)] flex flex-col md:flex-row">
                                    <div class="relative md:w-1/3 shrink-0">
                                        <a href="{{ route('articles.show', $article->slug) }}" class="block h-full">
                                            <div class="relative h-48 md:h-full overflow-hidden bg-blue-50">
                                                @if($article->featured_image)
                                                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                                                @else
                                                    <img src="{{ asset('images/y-hoc-co-truyen.png') }}" alt="{{ $article->title }}" class="h-full w-full object-cover object-center opacity-90 transition duration-700 group-hover:scale-105">
                                                @endif

                                                <div class="absolute bottom-3 left-3 rounded-full bg-white/95 px-2.5 py-1 text-xs font-black text-blue-600 shadow-sm">
                                                    {{ $article->published_at ? $article->published_at->format('d/m/Y') : $article->created_at->format('d/m/Y') }}
                                                </div>

                                                <div class="absolute left-3 top-3 max-w-[74%] rounded-full bg-sky-100 px-2.5 py-1 text-[10px] font-black text-sky-600 shadow-sm truncate">
                                                    {{ $article->category_label }}
                                                </div>
                                            </div>
                                        </a>

                                        <button type="button"
                                                class="article-like-btn absolute right-3 top-3 z-10 flex h-9 min-w-9 items-center justify-center gap-1 rounded-full border px-2 text-xs font-black shadow-md backdrop-blur transition {{ $isLiked ? 'border-rose-200 bg-rose-500 text-white' : 'border-white/70 bg-white/95 text-slate-500 hover:text-rose-500' }}"
                                                data-like-url="{{ route('articles.like', $article->id) }}"
                                                data-login-url="{{ route('login') }}"
                                                data-liked="{{ $isLiked ? '1' : '0' }}"
                                                data-authenticated="{{ auth()->check() ? '1' : '0' }}"
                                                title="{{ $isLiked ? 'Bỏ yêu thích' : 'Thêm vào yêu thích' }}">
                                            <svg class="h-4.5 w-4.5 {{ $isLiked ? 'fill-current' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21c-3.86-3.16-7.5-6.5-7.5-10.88A4.12 4.12 0 0 1 8.62 6c1.32 0 2.55.63 3.38 1.62A4.42 4.42 0 0 1 15.38 6a4.12 4.12 0 0 1 4.12 4.12C19.5 14.5 15.86 17.84 12 21Z"/>
                                            </svg>
                                            <span class="article-like-count text-[11px]">{{ $article->liked_by_users_count }}</span>
                                        </button>
                                    </div>

                                    <div class="flex-1 flex flex-col justify-center">
                                        <a href="{{ route('articles.show', $article->slug) }}" class="block p-5 md:p-6 h-full flex flex-col">
                                            <h3 class="text-lg md:text-xl font-black leading-snug text-blue-950 transition group-hover:text-blue-700 mb-3 line-clamp-2">
                                                {{ $article->title }}
                                            </h3>
                                            <p class="text-sm font-semibold leading-relaxed text-slate-650 flex-1 line-clamp-3">
                                                {{ $article->summary ?: $article->excerpt }}
                                            </p>

                                            <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4 text-xs font-bold text-slate-500">
                                                <span class="inline-flex items-center gap-1.5">
                                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4.5 9.75h15M6 5.25h12A1.5 1.5 0 0 1 19.5 6.75v12A1.5 1.5 0 0 1 18 20.25H6a1.5 1.5 0 0 1-1.5-1.5v-12A1.5 1.5 0 0 1 6 5.25Z"/></svg>
                                                    {{ $article->published_at ? $article->published_at->format('d/m/Y') : $article->created_at->format('d/m/Y') }}
                                                </span>
                                                <span class="inline-flex items-center gap-1.5 text-blue-600 group-hover:text-blue-800 transition">
                                                    Xem chi tiết
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
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
});
</script>
@endpush
