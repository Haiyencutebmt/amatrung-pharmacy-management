@extends('layouts.guest')

@section('title', 'Bài Viết — AmaTrung')

@section('content')
<div class="bg-[var(--color-surface-bg)] min-h-screen pb-20">
    
    {{-- Hero Section --}}
    <section class="bg-primary-900 pt-16 pb-24 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Background elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-[20%] right-[10%] w-[50%] h-[50%] bg-primary-700/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-[10%] -left-[10%] w-[40%] h-[40%] bg-green-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-4xl mx-auto text-center relative z-10">
            <div class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-full bg-primary-800/60 border border-primary-700/50 mb-6 backdrop-blur-sm text-green-50 font-semibold text-sm">
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Góc thư viện
            </div>
            
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-6 leading-tight">
                Kiến thức <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-300 to-emerald-200">Y Học Cổ Truyền</span>
            </h1>
            
            <p class="text-xl text-primary-100/90 leading-relaxed max-w-2xl mx-auto">
                Cập nhật những thông tin y khoa chuẩn xác, các bài thuốc dân gian và phương pháp điều trị hiệu quả từ đội ngũ bác sĩ chuyên môn.
            </p>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-20">
        @if($articles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($articles as $article)
                    <article class="relative bg-white rounded-[2rem] overflow-hidden shadow-sm border border-slate-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 flex flex-col group h-full">
                        
                        {{-- Article Image / Decorative Header --}}
                        <div class="h-48 relative overflow-hidden flex-shrink-0 border-b border-slate-100">
                            @if($article->featured_image)
                                <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-primary-50 to-green-50 flex items-center justify-center">
                                    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, #047857 1px, transparent 0); background-size: 20px 20px;"></div>
                                </div>
                            @endif
                            
                            <div class="absolute bottom-4 left-6 flex gap-2 z-10">
                                <span class="bg-white/90 backdrop-blur-sm text-primary-700 text-xs font-extrabold px-3 py-1.5 rounded-full shadow-sm">
                                    {{ $article->published_at ? $article->published_at->format('d/m/Y') : $article->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>

                        {{-- Article Content --}}
                        <div class="p-8 flex flex-col flex-grow">
                            <h2 class="text-xl font-extrabold text-slate-800 mb-3 line-clamp-2 leading-snug group-hover:text-primary-600 transition-colors">
                                <a href="{{ route('articles.show', $article->slug) }}" class="focus:outline-none">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    {{ $article->title }}
                                </a>
                            </h2>
                            
                            <p class="text-slate-500 mb-6 line-clamp-3 leading-relaxed flex-grow">
                                {{ $article->excerpt }}
                            </p>
                            
                            {{-- Footer Info --}}
                            <div class="flex items-center justify-between pt-6 border-t border-slate-100 mt-auto relative z-10">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-primary-100 to-green-100 text-primary-700 font-bold flex items-center justify-center border border-white shadow-sm ring-2 ring-primary-50">
                                        {{ mb_substr($article->author->name ?? 'A', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">{{ $article->author->name ?? 'Quản trị viên' }}</p>
                                        <p class="text-xs text-slate-500">Tác giả</p>
                                    </div>
                                </div>
                                <div class="text-primary-600 group-hover:translate-x-1 transition-transform bg-primary-50 p-2 rounded-full">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-16 pagination-wrapper rounded-3xl shadow-sm">
                {{ $articles->links() }}
            </div>
        @else
            <div class="bg-white border border-slate-200 rounded-[2rem] p-16 text-center shadow-xl">
                <div class="w-24 h-24 bg-primary-50 rounded-full flex items-center justify-center mx-auto mb-6 text-primary-300">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 mb-3">Chưa có bài viết</h3>
                <p class="text-slate-500 text-lg">Hiện tại phòng khám chưa đăng tải bài viết nào. Vui lòng quay lại sau nhé!</p>
            </div>
        @endif
    </section>
</div>
@endsection
