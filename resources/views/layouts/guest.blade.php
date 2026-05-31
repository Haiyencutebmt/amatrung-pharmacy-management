<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AmaTrung — Phòng khám Y Học Cổ Truyền">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AmaTrung — Y Học Cổ Truyền')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/amatrung_logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .goog-te-banner-frame,
        .goog-te-banner-frame.skiptranslate,
        iframe.skiptranslate,
        .skiptranslate iframe,
        .goog-te-gadget,
        #google_translate_element {
            display: none !important;
        }

        body {
            top: 0 !important;
        }
    </style>
</head>
<body class="bg-[var(--color-surface-bg)] text-slate-800 font-sans antialiased min-h-screen flex flex-col">

    {{-- Floating Header --}}
    <div class="w-full max-w-[1400px] mx-auto px-4 pt-4 sticky top-0 z-[999] pointer-events-none">
        <header class="bg-white/90 backdrop-blur-md rounded-full shadow-[0_12px_40px_rgba(59,130,246,0.1)] border border-sky-100/60 px-8 py-3.5 flex items-center justify-between pointer-events-auto transition-all duration-300">
            <!-- Left side: Mobile Menu & Logo & Brand Title -->
            <div class="flex items-center gap-3">
                <!-- Mobile Menu Button -->
                <div class="sm:hidden relative">
                    <button id="mobile-menu-btn" class="p-1.5 text-gray-500 hover:text-blue-500 focus:outline-none">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <!-- Mobile Dropdown -->
                    <div id="mobile-menu-dropdown" class="absolute left-0 mt-3 w-56 bg-white rounded-2xl shadow-lg py-2.5 z-50 hidden border border-gray-100">
                        <a href="{{ url('/') }}" class="block px-5 py-3 text-base font-bold text-gray-700 hover:bg-blue-50 hover:text-blue-500">Trang chủ</a>
                        <a href="{{ url('/bai-viet') }}" class="block px-5 py-3 text-base font-bold text-gray-700 hover:bg-blue-50 hover:text-blue-500">Bài viết</a>
                        <a href="{{ url('/tu-dien-thuoc-nam') }}" class="block px-5 py-3 text-base font-bold text-gray-700 hover:bg-blue-50 hover:text-blue-500">Từ điển dược liệu</a>
                        <button onclick="document.getElementById('chatbot-toggle').click();" class="w-full text-left block px-5 py-3 text-base font-bold text-gray-700 hover:bg-blue-50 hover:text-blue-500">Tư vấn AI</button>
                    </div>
                </div>

                <!-- Logo & Brand info -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/amatrung_logo.png') }}" class="w-12 h-12 object-contain group-hover:scale-105 transition-transform rounded-full" alt="AmaTrung">
                    <div class="flex flex-col leading-tight">
                        <span class="text-xl font-black text-blue-600 tracking-wide">AMATRUNG</span>
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Nhà thuốc Y học cổ truyền</span>
                    </div>
                </a>
            </div>

            <!-- Center/Navbar: Pill Navigation Links -->
            <div class="hidden sm:flex items-center gap-2.5">
                <!-- Trang chủ -->
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 px-5.5 py-2.5 text-base font-bold rounded-full transition-all duration-300 {{ Request::is('/') ? 'bg-blue-500 text-white shadow-md shadow-blue-500/10' : 'bg-white border border-slate-200 text-slate-600 hover:text-blue-500 hover:border-blue-200 shadow-sm' }}">
                    <svg class="w-4.5 h-4.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    <span>Trang chủ</span>
                </a>
                
                <!-- Bài viết -->
                <a href="{{ url('/bai-viet') }}" class="inline-flex items-center gap-1.5 px-5.5 py-2.5 text-base font-bold rounded-full transition-all duration-300 {{ Request::is('bai-viet*') ? 'bg-blue-500 text-white shadow-md shadow-blue-500/10' : 'bg-white border border-slate-200 text-slate-600 hover:text-blue-500 hover:border-blue-200 shadow-sm' }}">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    <span>Bài viết</span>
                </a>
                
                <!-- Từ điển dược liệu -->
                <a href="{{ url('/tu-dien-thuoc-nam') }}" class="inline-flex items-center gap-1.5 px-5.5 py-2.5 text-base font-bold rounded-full transition-all duration-300 {{ Request::is('tu-dien-thuoc-nam*') ? 'bg-blue-500 text-white shadow-md shadow-blue-500/10' : 'bg-white border border-slate-200 text-slate-600 hover:text-blue-500 hover:border-blue-200 shadow-sm' }}">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>Từ điển dược liệu</span>
                </a>
                
                <!-- Tư vấn AI -->
                <button onclick="document.getElementById('chatbot-toggle').click();" class="inline-flex items-center gap-1.5 px-5.5 py-2.5 text-base font-bold bg-white border border-slate-200 text-slate-600 hover:text-blue-500 hover:border-blue-200 rounded-full transition-all duration-300 shadow-sm focus:outline-none relative group">
                    <svg class="w-4.5 h-4.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <span>Tư vấn AI</span>
                    <span class="text-amber-500 animate-pulse text-sm">✨</span>
                </button>
            </div>

            <!-- Right Actions: Account, Settings -->
            <div class="flex items-center gap-3.5">
                @auth
                    <!-- Tài khoản dropdown pill -->
                    <div class="relative group/user">
                        <button class="inline-flex items-center gap-1.5 pl-1.5 pr-4 py-1 text-base font-bold bg-white border border-slate-200 hover:border-blue-200 text-slate-700 rounded-full transition-all shadow-sm focus:outline-none">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="w-9 h-9 rounded-full object-cover">
                            @else
                                <div class="w-9 h-9 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-black">
                                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                            <span class="max-w-[100px] truncate">{{ auth()->user()->name }}</span>
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <!-- User Dropdown Menu -->
                        <div class="absolute right-0 pt-2.5 w-52 z-50 hidden group-hover/user:block">
                            <div class="bg-white rounded-2xl shadow-xl py-2 border border-slate-100 overflow-hidden">
                                <a href="{{ url('/dashboard') }}" class="block px-5 py-3 text-base font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">Bảng điều khiển</a>
                                <div class="border-t border-slate-100 my-1"></div>
                                <form action="{{ url('/logout') }}" method="POST" class="block w-full">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-5 py-3 text-base font-bold text-slate-700 hover:bg-red-50 hover:text-red-600 transition-colors focus:outline-none">
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Login Button -->
                    <a href="{{ url('/login') }}" class="inline-flex items-center gap-1.5 px-6 py-2.5 text-base font-bold bg-blue-500 hover:bg-blue-600 text-white rounded-full transition-all shadow-md shadow-blue-500/10">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        <span>Đăng nhập</span>
                    </a>
                @endauth

                <!-- Favorites Heart Button -->
                <a href="{{ route('profile.favorites') }}" class="w-11 h-11 rounded-full bg-slate-50 hover:bg-rose-50 flex items-center justify-center text-slate-400 hover:text-rose-500 border border-slate-100 transition-all focus:outline-none relative group/fav cursor-pointer" title="Mục yêu thích">
                    <svg class="w-6 h-6 fill-none group-hover/fav:fill-rose-500 transition-all" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    @auth
                        @php
                            $favCount = auth()->user()->likedArticles()->count() + auth()->user()->herbDictionaryFavorites()->count();
                        @endphp
                        @if($favCount > 0)
                            <span class="absolute -top-1.5 -right-1.5 bg-rose-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full shrink-0 min-w-[18px] border border-white">
                                {{ $favCount }}
                            </span>
                        @endif
                    @endauth
                </a>

                <!-- Settings/Theme Toggle Dropdown -->
                <div class="relative group/settings">
                    <button class="w-11 h-11 rounded-full bg-slate-50 hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-blue-500 border border-slate-100 transition-all focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </button>
                    <div class="absolute right-0 pt-2.5 w-52 z-50 hidden group-hover/settings:block">
                        <div class="bg-white rounded-2xl shadow-xl py-3 border border-slate-100">
                            <div class="px-5 py-2 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Ngôn ngữ</div>
                            <a href="#" onclick="changeLanguage('vi'); return false;" class="block px-5 py-2.5 text-base font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-500">🇻🇳 Tiếng Việt</a>
                            <a href="#" onclick="changeLanguage('en'); return false;" class="block px-5 py-2.5 text-base font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-500">🇬🇧 Tiếng Anh</a>
                            <a href="#" onclick="changeLanguage('zh-CN'); return false;" class="block px-5 py-2.5 text-base font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-500">🇨🇳 Tiếng Trung</a>
                            <div class="border-t border-slate-100 my-2.5"></div>
                            <div class="px-5 py-2 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Giao diện</div>
                            <button id="theme-toggle" class="w-full text-left flex items-center px-5 py-2.5 text-base font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-500 focus:outline-none">
                                <svg id="theme-toggle-light-icon" class="w-5.5 h-5.5 mr-2.5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 2.366a1 1 0 011.415 0l.707.707a1 1 0 01-1.414 1.415l-.708-.707a1 1 0 010-1.415zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-2.574 4.22a1 1 0 010 1.415l-.707.707a1 1 0 01-1.415-1.414l.707-.707a1 1 0 011.415 0zM11 17a1 1 0 10-2 0v1a1 1 0 102 0v-1zm-4.22-2.366a1 1 0 01-1.415 0l-.707-.707a1 1 0 011.414-1.415l.708.707a1 1 0 010 1.415zM3 9a1 1 0 100 2h1a1 1 0 100-2H3zm2.574-4.22a1 1 0 010-1.415l.707-.707a1 1 0 011.415 1.414l-.707.707a1 1 0 01-1.415 0zM10 6a4 4 0 100 8 4 4 0 000-8z" clip-rule="evenodd"></path></svg>
                                <svg id="theme-toggle-dark-icon" class="w-5.5 h-5.5 mr-2.5 hidden text-gray-700" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                                <span class="theme-text">Đổi Sáng/Tối</span>
                            </button>
                        </div>
                    </div>
                </div>
        </header>
    </div>

    {{-- Floating Toast Notifications (Bottom Right) --}}
    <div id="toast-container" class="fixed bottom-6 right-6 z-[99999] flex flex-col gap-3.5 max-w-sm w-full pointer-events-none">
        @if(session('status'))
            <div class="toast-item bg-emerald-500 text-white px-5 py-4 rounded-2xl shadow-xl flex items-center gap-3 pointer-events-auto transition-all duration-500 translate-y-10 opacity-0 border border-emerald-400/30">
                <span class="w-7 h-7 rounded-full bg-white/20 text-white flex items-center justify-center shrink-0 font-black">✓</span>
                <div class="text-sm font-bold tracking-wide">{{ session('status') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="toast-item bg-rose-500 text-white px-5 py-4 rounded-2xl shadow-xl flex items-center gap-3 pointer-events-auto transition-all duration-500 translate-y-10 opacity-0 border border-rose-400/30">
                <span class="w-7 h-7 rounded-full bg-white/20 text-white flex items-center justify-center shrink-0 font-black">✕</span>
                <div class="text-sm font-bold tracking-wide">{{ session('error') }}</div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toasts = document.querySelectorAll('.toast-item');
            toasts.forEach(toast => {
                // Show toast with slide & fade
                setTimeout(() => {
                    toast.classList.remove('translate-y-10', 'opacity-0');
                }, 150);

                // Auto hide after 5 seconds
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'translate-y-[-20px]');
                    setTimeout(() => {
                        toast.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>

    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-primary-900 text-white mt-20 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <img src="{{ asset('images/amatrung_logo.png') }}" alt="AmaTrung Logo" class="h-10 w-10 object-contain drop-shadow-md rounded-full bg-white p-1">
                        <span class="text-2xl font-extrabold text-white tracking-tight block">AmaTrung</span>
                    </div>
                    <p class="text-primary-100/80 text-sm leading-relaxed max-w-sm mb-6">
                        © {{ date('Y') }} AmaTrung Việt Nam. Traditional Medicine.<br>
                        All rights reserved. Medical Disclaimer:<br>
                        Information provided is for educational purposes only.
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider mb-4 text-primary-100">Liên kết nhanh</h3>
                    <ul class="space-y-3 text-sm text-primary-100/80">
                        <li><a href="{{ url('/') }}" class="hover:text-white transition-colors">Trang chủ</a></li>
                        <li><a href="{{ url('/bai-viet') }}" class="hover:text-white transition-colors">Kiến thức y khoa</a></li>
                        <li><a href="{{ route('herb-dictionary.index') }}" class="hover:text-white transition-colors">Từ điển dược liệu</a></li>
                        <li><a href="{{ route('privacy-policy') }}" class="hover:text-white transition-colors">Chính sách bảo mật</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider mb-4 text-primary-100">Thông tin liên hệ</h3>
                    <ul class="space-y-4 text-sm text-primary-100/80">
                        <li class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 shrink-0 text-sky-300 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>54/36 Ama Jhao, Phường Tân Lập, Buôn Ma Thuột, Đắk Lắk</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <a href="tel:0983009748" class="hover:text-white transition-colors font-semibold">0983.009.748 (Zalo)</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-primary-800 pt-8 flex flex-col md:flex-row justify-between items-center text-xs text-primary-100/60">
                <p>Designed for AmaTrung Clinic.</p>
                <div class="flex space-x-6 mt-4 md:mt-0 text-sm">
                    <a href="https://www.facebook.com/hieu.trieu.3382" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors font-bold">Facebook</a>
                    <a href="https://www.tiktok.com/@nhyn.ieaie.04?lang=vi-VN" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors font-bold">Tiktok</a>
                    <a href="https://zalo.me/0983009748" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors font-bold">Zalo</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
    
    <!-- Google Translate Script -->
    <div id="google_translate_element" class="hidden"></div>
    <script type="text/javascript">
        const AmaTrungLanguage = {
            defaultLanguage: 'vi',
            supportedLanguages: ['vi', 'en', 'zh-CN'],
            storageKey: 'amatrung-language',

            cookieValue(langCode) {
                return `/vi/${langCode}`;
            },

            canUseDomainCookie() {
                return window.location.hostname
                    && window.location.hostname !== 'localhost'
                    && !/^\d{1,3}(\.\d{1,3}){3}$/.test(window.location.hostname);
            },

            setCookie(langCode) {
                const expires = new Date(Date.now() + 365 * 24 * 60 * 60 * 1000).toUTCString();
                const value = this.cookieValue(langCode);

                document.cookie = `googtrans=${value}; expires=${expires}; path=/`;

                if (this.canUseDomainCookie()) {
                    document.cookie = `googtrans=${value}; expires=${expires}; domain=.${window.location.hostname}; path=/`;
                }
            },

            clearCookie() {
                document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';

                if (this.canUseDomainCookie()) {
                    document.cookie = `googtrans=; expires=Thu, 01 Jan 1970 00:00:00 GMT; domain=.${window.location.hostname}; path=/`;
                }
            },

            getStoredLanguage() {
                const stored = localStorage.getItem(this.storageKey);
                return this.supportedLanguages.includes(stored) ? stored : this.defaultLanguage;
            },

            remember(langCode) {
                if (langCode === this.defaultLanguage) {
                    localStorage.removeItem(this.storageKey);
                    this.clearCookie();
                    return;
                }

                localStorage.setItem(this.storageKey, langCode);
                this.setCookie(langCode);
            },

            apply(langCode, retry = 0) {
                if (langCode === this.defaultLanguage) {
                    return;
                }

                const selectField = document.querySelector('.goog-te-combo');

                if (selectField) {
                    selectField.value = langCode;
                    selectField.dispatchEvent(new Event('change', { bubbles: true }));
                    return;
                }

                if (retry < 10) {
                    window.setTimeout(() => this.apply(langCode, retry + 1), 300);
                }
            },

            change(langCode) {
                if (!this.supportedLanguages.includes(langCode)) {
                    return;
                }

                this.remember(langCode);

                if (langCode === this.defaultLanguage) {
                    window.location.reload();
                    return;
                }

                const selectField = document.querySelector('.goog-te-combo');
                if (selectField) {
                    selectField.value = langCode;
                    selectField.dispatchEvent(new Event('change', { bubbles: true }));
                    return;
                }

                window.setTimeout(() => window.location.reload(), 150);
            },
        };

        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'vi',
                includedLanguages: 'en,vi,zh-CN',
                autoDisplay: false,
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE
            }, 'google_translate_element');

            window.setTimeout(() => {
                AmaTrungLanguage.apply(AmaTrungLanguage.getStoredLanguage());
            }, 500);
        }

        function changeLanguage(langCode) {
            AmaTrungLanguage.change(langCode);
        }
    </script>
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <!-- Dark Mode Script -->
    <script>
        var themeToggleBtn = document.getElementById('theme-toggle');
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        if (themeToggleBtn) {
            if (localStorage.getItem('color-theme') === 'dark') {
                document.documentElement.classList.add('dark');
                themeToggleLightIcon.classList.add('hidden');
                themeToggleDarkIcon.classList.remove('hidden');
            }

            themeToggleBtn.addEventListener('click', function() {
                themeToggleDarkIcon.classList.toggle('hidden');
                themeToggleLightIcon.classList.toggle('hidden');

                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            });
        }

        // Mobile Menu Toggle
        var mobileMenuBtn = document.getElementById('mobile-menu-btn');
        var mobileMenuDropdown = document.getElementById('mobile-menu-dropdown');
        if (mobileMenuBtn && mobileMenuDropdown) {
            mobileMenuBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                mobileMenuDropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', function(e) {
                if (!mobileMenuBtn.contains(e.target) && !mobileMenuDropdown.contains(e.target)) {
                    mobileMenuDropdown.classList.add('hidden');
                }
            });
        }
    </script>

    <!-- AI Chatbot -->
    <x-chatbot />

    <!-- Mouse Wind/Smoke Effect Canvas -->
    <canvas id="mouse-smoke-canvas" class="fixed inset-0 pointer-events-none z-[9998] w-full h-full"></canvas>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('mouse-smoke-canvas');
            const ctx = canvas.getContext('2d');
            
            let width = canvas.width = window.innerWidth;
            let height = canvas.height = window.innerHeight;
            
            window.addEventListener('resize', () => {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            });

            // 3 dải khói (ribbons) để tạo hiệu ứng gió đa tầng
            const ribbons = [
                { points: [], phase: 0, speed: 1.2, widthFactor: 1.0, color: '165, 215, 255' },
                { points: [], phase: 2.5, speed: 1.6, widthFactor: 0.6, color: '200, 230, 255' },
                { points: [], phase: 4.2, speed: 0.8, widthFactor: 1.4, color: '135, 190, 245' }
            ];

            let time = 0;
            let lastX = null, lastY = null;
            let mouseTimeout;

            window.addEventListener('mousemove', (e) => {
                // Điền thêm các điểm giữa 2 lần di chuột để dải khói không bị đứt đoạn khi di nhanh
                if (lastX !== null && lastY !== null) {
                    const dx = e.clientX - lastX;
                    const dy = e.clientY - lastY;
                    const dist = Math.sqrt(dx*dx + dy*dy);
                    const steps = Math.min(Math.floor(dist / 8), 12); 
                    
                    for (let i = 1; i <= steps; i++) {
                        let ix = lastX + (dx * i) / steps;
                        let iy = lastY + (dy * i) / steps;
                        ribbons.forEach(ribbon => ribbon.points.push({ x: ix, y: iy, age: 0 }));
                    }
                }
                
                ribbons.forEach(ribbon => ribbon.points.push({ x: e.clientX, y: e.clientY, age: 0 }));
                
                lastX = e.clientX;
                lastY = e.clientY;

                clearTimeout(mouseTimeout);
                mouseTimeout = setTimeout(() => { lastX = null; lastY = null; }, 100);
            });

            window.addEventListener('mouseout', () => { lastX = null; lastY = null; });

            function animate() {
                ctx.clearRect(0, 0, width, height);
                time += 0.05;

                ribbons.forEach(ribbon => {
                    // Cập nhật vị trí các điểm (bay lên và uốn lượn)
                    for (let i = 0; i < ribbon.points.length; i++) {
                        let p = ribbon.points[i];
                        p.age += 1;
                        p.y -= ribbon.speed; // Gió bay lên
                        // Tạo độ lượn sóng cho khói
                        p.x += Math.sin(time + p.age * 0.05 + ribbon.phase) * (1.5 * ribbon.speed); 
                    }

                    // Xoá các điểm đã già (mờ hẳn)
                    ribbon.points = ribbon.points.filter(p => p.age < 80);

                    // Vẽ dải khói
                    if (ribbon.points.length > 1) {
                        ctx.lineCap = 'round';
                        ctx.lineJoin = 'round';

                        for (let i = 0; i < ribbon.points.length - 1; i++) {
                            let p1 = ribbon.points[i];
                            let p2 = ribbon.points[i + 1];
                            let life = Math.max(0, 1 - (p1.age / 80));
                            
                            ctx.beginPath();
                            ctx.moveTo(p1.x, p1.y);
                            ctx.lineTo(p2.x, p2.y);
                            
                            // Càng về đuôi khói càng mờ dần và loãng ra
                            ctx.strokeStyle = `rgba(${ribbon.color}, ${Math.pow(life, 1.5) * 0.18})`;
                            ctx.lineWidth = (8 + p1.age * 0.6) * ribbon.widthFactor; 
                            ctx.stroke();
                        }
                    }
                });

                requestAnimationFrame(animate);
            }
            
            animate();
        });
    </script>
    <!-- Floating Social Contacts Sidebar -->
    <div id="floating-social-contacts" data-floating-social class="fixed right-4 md:right-6 top-1/2 -translate-y-1/2 z-[9990] flex flex-col items-center gap-4 bg-white/90 backdrop-blur-md rounded-full py-6 px-3 shadow-[0_15px_45px_rgba(0,0,0,0.12)] border border-sky-100/70 hover:shadow-[0_20px_55px_rgba(0,0,0,0.18)] transition-all duration-300">
        <!-- Facebook -->
        <a href="https://www.facebook.com/hieu.trieu.3382" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full flex items-center justify-center bg-[#1877F2]/10 hover:bg-[#1877F2] text-[#1877F2] hover:text-white transition-all duration-300 hover:scale-110 shadow-sm" title="Facebook">
            <svg class="w-5.5 h-5.5 fill-current" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
        </a>

        <!-- Tiktok -->
        <a href="https://www.tiktok.com/@nhyn.ieaie.04?lang=vi-VN" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full flex items-center justify-center bg-slate-900/10 hover:bg-slate-900 text-slate-800 hover:text-white transition-all duration-300 hover:scale-110 shadow-sm" title="Tiktok">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                <path d="M12.525.07c1.147-.033 2.274.015 3.398.113V4.02c-.87-.044-1.745-.035-2.613.018-.323.018-.633.097-.899.273-.385.253-.559.684-.58 1.127v2.775c1.32-.014 2.641.002 3.962-.008.125 1.25.132 2.505.132 3.757-.013.132-.036.262-.061.393-.23 1.306-.217 2.628-.088 3.939-.029.351-.106.716-.338 1.01-.806 1.017-2.15 1.45-3.414 1.45-1.442-.023-2.825-.584-3.778-1.63-.842-.938-1.22-2.222-1.074-3.483.155-1.392.977-2.611 2.219-3.23.402-.204.835-.306 1.272-.315v3.832c-.347.03-.699.162-.953.42-.351.357-.454.896-.273 1.36.195.49.681.802 1.201.8.525-.01.99-.344 1.15-.833.09-.27.1-.55.1-.83V.07h-3.32v3.742c-.01.939-.297 1.868-.868 2.607-.828 1.066-2.17 1.584-3.509 1.47-1.464-.117-2.766-.966-3.447-2.269-.718-1.378-.636-3.132.23-4.417C6.096 1.171 7.697.35 9.38.332c.691-.013 1.383.072 2.052.24.364.088.697.26 1 .485.034-.33.064-.662.096-.99z"/>
            </svg>
        </a>

        <!-- Zalo -->
        <a href="https://zalo.me/0983009748" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full flex items-center justify-center bg-[#0068FF]/10 hover:bg-[#0068FF] text-[#0068FF] hover:text-white transition-all duration-300 hover:scale-110 shadow-sm" title="Zalo">
            <span class="font-extrabold text-[10px] tracking-tight">Zalo</span>
        </a>

        <!-- Call Hotline -->
        <a href="tel:0983009748" class="w-10 h-10 rounded-full flex items-center justify-center bg-emerald-500/10 hover:bg-emerald-500 text-emerald-500 hover:text-white transition-all duration-300 hover:scale-110 shadow-sm animate-pulse" title="Gọi hotline">
            <svg class="w-5 h-5 fill-none stroke-current" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
        </a>
    </div>

    </div>
</html>
