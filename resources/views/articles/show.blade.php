@extends('layouts.guest')

@section('title', $article->title . ' — AmaTrung')

@section('content')
<div class="relative bg-gradient-to-b from-sky-50 via-white to-sky-100 min-h-screen pb-20 overflow-hidden font-sans">
    
    <!-- Decorative Floating Leaf/Flower (Abstract CSS shapes & gradients) -->
    <div class="absolute top-20 left-10 w-72 h-72 bg-sky-200/30 rounded-full blur-3xl pointer-events-none z-0"></div>
    <div class="absolute bottom-40 right-10 w-96 h-96 bg-green-100/40 rounded-full blur-3xl pointer-events-none z-0"></div>

    <!-- Hero Title Section -->
    <div class="max-w-[1400px] mx-auto px-4 mt-8 md:mt-12 relative z-10">
        <div class="bg-gradient-to-r from-[#1a5b8f] to-[#4292d6] rounded-[2.5rem] py-12 md:py-16 px-6 md:px-12 text-center md:text-left shadow-lg border border-sky-100 text-white relative overflow-hidden">
            <!-- Decorative background pattern overlay -->
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>
            
            <div class="max-w-4xl relative z-10 flex flex-col md:flex-row items-center gap-6 md:gap-8">
                <div class="bg-white/10 backdrop-blur-sm p-4 rounded-full border border-white/20">
                    <img src="{{ asset('images/amatrung_logo.png') }}" class="w-16 h-16 md:w-20 md:h-20 object-contain rounded-full" alt="Logo">
                </div>
                <div>
                    <!-- Badge -->
                    <span class="inline-block bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full text-xs md:text-sm font-bold uppercase tracking-wider mb-3 border border-white/20">
                        KIẾN THỨC Y KHOA — AMATRUNG
                    </span>
                    <h1 class="text-2xl md:text-4xl lg:text-5xl font-black tracking-tight mb-4 leading-tight">
                        {{ $article->title }}
                    </h1>
                    
                    <div class="flex flex-wrap justify-center md:justify-start items-center gap-6 text-sm text-sky-100 font-medium">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs">
                                {{ mb_substr($article->author->name ?? 'A', 0, 1) }}
                            </div>
                            <span>Tác giả: {{ $article->author->name ?? 'AmaTrung' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Ngày đăng: {{ $article->published_at ? $article->published_at->format('d/m/Y') : $article->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="max-w-[1400px] mx-auto px-4 mt-6 relative z-10">
        <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 text-[#1a5b8f] hover:text-[#2978c4] font-bold transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại danh sách bài viết
        </a>
    </div>

    <!-- Main Content Layout -->
    <div class="max-w-[1400px] mx-auto px-4 mt-6 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-12">
            
            <!-- Left Side: Article Content & Comments (Column span 8) -->
            <div class="lg:col-span-8 flex flex-col gap-8">
                
                <!-- Main Article Content Card -->
                <article class="bg-white rounded-[2.5rem] shadow-[0_10px_35px_rgba(0,0,0,0.03)] border border-sky-100 overflow-hidden">
                    
                    {{-- Featured Image Banner --}}
                    @if($article->featured_image)
                        <div class="w-full relative overflow-hidden border-b border-slate-100 max-h-[480px]">
                            <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    {{-- Body Content --}}
                    <div class="p-8 md:p-12 prose prose-lg prose-slate max-w-none prose-headings:font-black prose-headings:text-[#1a5b8f] prose-a:text-blue-600 prose-img:rounded-[2rem] prose-img:shadow-md prose-blockquote:border-l-4 prose-blockquote:border-blue-400 prose-blockquote:bg-blue-50/50 prose-blockquote:p-4 prose-blockquote:rounded-r-2xl">
                    {!! $article->content !!}
                    </div>

                    <!-- Like / Useful Button Section -->
                    <div class="px-8 md:px-12 pb-8 border-t border-slate-100 pt-6 flex justify-between items-center bg-slate-50/50">
                        <div class="flex items-center gap-2">
                            @auth
                                <button id="like-btn" data-article-id="{{ $article->id }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border-2 transition-all font-bold text-sm {{ $article->isLikedBy(auth()->user()) ? 'bg-[#1a5b8f] border-[#1a5b8f] text-white shadow-md shadow-blue-500/10' : 'border-sky-200 text-[#1a5b8f] hover:bg-sky-50' }} shadow-sm">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                        <path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/>
                                    </svg>
                                    <span id="like-text">{{ $article->isLikedBy(auth()->user()) ? 'Đã thích bài viết' : 'Bài viết hữu ích' }}</span>
                                    <span id="like-count" class="bg-white/20 px-2 py-0.5 rounded-full text-xs font-black">{{ $article->likedByUsers()->count() }}</span>
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border-2 border-sky-200 text-[#1a5b8f] hover:bg-sky-50 transition-all font-bold text-sm shadow-sm">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                        <path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/>
                                    </svg>
                                    <span>Bài viết hữu ích</span>
                                    <span class="bg-sky-100 px-2 py-0.5 rounded-full text-xs font-black">{{ $article->likedByUsers()->count() }}</span>
                                </a>
                            @endauth
                        </div>
                        
                        <div class="text-xs text-slate-400 font-semibold flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Thời gian đọc: {{ max(1, ceil(str_word_count(strip_tags($article->content)) / 200)) }} phút</span>
                        </div>
                    </div>

                </article>

                <!-- Comments Section Card -->
                <section class="bg-white rounded-[2.5rem] p-6 md:p-10 shadow-[0_10px_35px_rgba(0,0,0,0.03)] border border-sky-100">
                    <h3 class="text-2xl font-black text-slate-800 mb-8 flex items-center gap-3 border-b border-slate-100 pb-4">
                        <span class="bg-blue-50 text-[#1a5b8f] p-2 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                        </span>
                        Ý kiến bạn đọc ({{ $article->approvedComments->count() }})
                    </h3>

                    {{-- Form: Add Comment --}}
                    <div class="bg-sky-50/50 rounded-[2rem] p-6 md:p-8 mb-10 border border-sky-100 shadow-sm">
                        @auth
                            <form action="{{ route('comments.store', $article->id) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="content" class="block text-sm font-bold text-slate-700 mb-2">Để lại bình luận của bạn *</label>
                                    
                                    <!-- Suggested Quick Tags -->
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <span class="text-xs text-slate-400 font-bold self-center mr-1">Gợi ý nhanh:</span>
                                        <button type="button" class="comment-tag bg-white border border-sky-100 hover:border-blue-300 text-slate-600 hover:text-[#1a5b8f] rounded-full px-3.5 py-1.5 text-xs font-bold transition-all shadow-sm focus:outline-none">
                                            Bài viết rất hữu ích!
                                        </button>
                                        <button type="button" class="comment-tag bg-white border border-sky-100 hover:border-blue-300 text-slate-600 hover:text-[#1a5b8f] rounded-full px-3 py-1.5 text-xs font-bold transition-all shadow-sm focus:outline-none">
                                            Cảm ơn thông tin chia sẻ từ thầy thuốc!
                                        </button>
                                        <button type="button" class="comment-tag bg-white border border-sky-100 hover:border-blue-300 text-slate-600 hover:text-[#1a5b8f] rounded-full px-3 py-1.5 text-xs font-bold transition-all shadow-sm focus:outline-none">
                                            Thông tin rất dễ hiểu và khoa học.
                                        </button>
                                    </div>
                                    
                                    <textarea id="content" name="content" rows="4" placeholder="Chia sẻ cảm nghĩ hoặc thắc mắc của bạn về bài viết này..." required class="w-full rounded-2xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition-all p-4 resize-y shadow-sm bg-white"></textarea>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" class="px-8 py-3 bg-[#1a5b8f] hover:bg-[#2978c4] text-white font-bold rounded-xl transition-all shadow-md shadow-blue-500/10">
                                        Gửi bình luận
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="text-center py-6">
                                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-500">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                                </div>
                                <p class="text-slate-600 font-bold mb-4">Bạn cần đăng nhập để gửi ý kiến phản hồi về bài viết này.</p>
                                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-white text-[#1a5b8f] border-2 border-sky-100 font-bold rounded-xl hover:bg-sky-50 hover:border-sky-200 transition-all shadow-sm">
                                    Đăng nhập ngay
                                </a>
                            </div>
                        @endauth
                    </div>

                    {{-- List of Comments --}}
                    @if($article->approvedComments->count() > 0)
                        <div class="space-y-6">
                            @foreach($article->approvedComments as $comment)
                                <div class="flex gap-4 items-start">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-blue-100 to-sky-100 text-[#1a5b8f] flex items-center justify-center font-bold text-lg flex-shrink-0 shadow-sm border-2 border-white ring-2 ring-sky-50">
                                        {{ mb_substr($comment->user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div class="flex-grow bg-slate-50 border border-slate-100 rounded-2xl p-5 shadow-sm relative">
                                        <!-- Speech bubble arrow -->
                                        <div class="absolute w-3 h-3 bg-slate-50 border-l border-t border-slate-100 rotate-[-45deg] left-[-7px] top-4"></div>
                                        
                                        <div class="flex justify-between items-baseline mb-2">
                                            <strong class="text-slate-800 font-bold">{{ $comment->user->name ?? 'Người dùng' }}</strong>
                                            <span class="text-slate-400 text-xs font-semibold">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-slate-600 text-sm leading-relaxed">{{ $comment->content }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 bg-slate-50 rounded-2xl border border-slate-200 border-dashed">
                            <p class="text-slate-400 font-medium italic text-sm">Chưa có bình luận nào cho bài viết này. Hãy là người đầu tiên chia sẻ cảm nghĩ nhé!</p>
                        </div>
                    @endif
                </section>

            </div>

            <!-- Right Side: Author Card & Recents (Column span 4) -->
            <div class="lg:col-span-4 flex flex-col gap-8">
                
                <!-- Author Profile Card (Earthy / High-end design similar to doctor specs card) -->
                <div class="bg-white rounded-[2.5rem] p-6 shadow-[0_10px_35px_rgba(0,0,0,0.03)] border border-sky-100 flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-[#1a5b8f] to-[#4292d6] text-white font-black flex items-center justify-center text-2xl shadow-md border-4 border-white ring-4 ring-sky-100">
                        {{ mb_substr($article->author->name ?? 'A', 0, 1) }}
                    </div>
                    
                    <h3 class="mt-4 text-xl font-black text-[#1a5b8f] tracking-wide">{{ $article->author->name ?? 'Quản trị viên' }}</h3>
                    <span class="mt-1 bg-sky-50 text-[#1a5b8f] font-bold text-xs px-3 py-1 rounded-full border border-sky-100 uppercase tracking-wider">
                        Tác giả chuyên môn
                    </span>
                    
                    <div class="w-16 h-0.5 bg-sky-100 my-4"></div>
                    
                    <p class="text-xs text-slate-500 leading-relaxed px-4">
                        Chuyên viên biên soạn thông tin và biên dịch các tài liệu Y học cổ truyền, cây thuốc nam phục vụ sức khỏe cộng đồng tại AmaTrung.
                    </p>
                </div>

                <!-- Recent Articles Card -->
                <div class="bg-white rounded-[2.5rem] p-6 shadow-[0_10px_35px_rgba(0,0,0,0.03)] border border-sky-100">
                    <h3 class="text-lg font-black text-slate-800 mb-6 pb-3 border-b border-slate-100 flex items-center gap-2">
                        <span class="bg-blue-50 text-[#1a5b8f] p-1.5 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        </span>
                        Bài viết mới nhất
                    </h3>
                    
                    <div class="space-y-5">
                        @if(isset($recentArticles) && $recentArticles->count() > 0)
                            @foreach($recentArticles as $recent)
                                <a href="{{ route('articles.show', $recent->slug) }}" class="group flex gap-3.5 items-start">
                                    <div class="w-14 h-14 rounded-2xl overflow-hidden flex-shrink-0 border border-slate-100 bg-slate-50 shadow-sm relative">
                                        @if($recent->featured_image)
                                            <img src="{{ Storage::url($recent->featured_image) }}" alt="{{ $recent->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-[#1a5b8f]/40 bg-sky-50 group-hover:bg-[#1a5b8f] group-hover:text-white transition-colors duration-300">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow min-w-0">
                                        <h4 class="font-bold text-slate-800 text-sm line-clamp-2 group-hover:text-blue-500 transition-colors mb-1 leading-snug">
                                            {{ $recent->title }}
                                        </h4>
                                        <span class="text-xs text-slate-400 font-semibold">
                                            {{ $recent->published_at ? $recent->published_at->format('d/m/Y') : $recent->created_at->format('d/m/Y') }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <p class="text-slate-400 text-xs italic">Không có bài viết nào khác.</p>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Like button functionality
    const likeBtn = document.getElementById('like-btn');
    if (likeBtn) {
        likeBtn.addEventListener('click', function () {
            const articleId = this.getAttribute('data-article-id');
            
            fetch(`/bai-viet/${articleId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (response.status === 401) {
                    window.location.href = '/login';
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (!data) return;
                const isLiked = data.liked;
                const count = data.likes_count;
                
                const likeCountSpan = document.getElementById('like-count');
                const likeTextSpan = document.getElementById('like-text');
                
                if (likeCountSpan) likeCountSpan.textContent = count;
                
                if (isLiked) {
                    likeBtn.classList.remove('border-sky-200', 'text-[#1a5b8f]', 'hover:bg-sky-50');
                    likeBtn.classList.add('bg-[#1a5b8f]', 'border-[#1a5b8f]', 'text-white', 'shadow-md', 'shadow-blue-500/10');
                    if (likeTextSpan) likeTextSpan.textContent = 'Đã thích bài viết';
                } else {
                    likeBtn.classList.remove('bg-[#1a5b8f]', 'border-[#1a5b8f]', 'text-white', 'shadow-md', 'shadow-blue-500/10');
                    likeBtn.classList.add('border-sky-200', 'text-[#1a5b8f]', 'hover:bg-sky-50');
                    if (likeTextSpan) likeTextSpan.textContent = 'Bài viết hữu ích';
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }

    // Suggested quick tags click functionality
    const textarea = document.getElementById('content');
    const tags = document.querySelectorAll('.comment-tag');
    tags.forEach(tag => {
        tag.addEventListener('click', function() {
            if (textarea) {
                textarea.value = this.textContent.trim();
                textarea.focus();
            }
        });
    });
});
</script>
@endpush


