<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AmaTrung — Phòng khám Y Học Cổ Truyền">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tài khoản — AmaTrung')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    {{-- Header --}}
    <header class="bg-white sticky top-0 z-50 border-b border-slate-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center gap-3 decoration-transparent">
                        <img src="{{ asset('images/amatrung_logo.png') }}" alt="AmaTrung Logo" class="h-10 w-10 md:h-12 md:w-12 object-contain rounded-full">
                        <span class="text-2xl md:text-3xl font-extrabold text-[#59A8ED] uppercase tracking-wide">AMATRUNG</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex space-x-4">
                    <a href="{{ url('/') }}" class="px-5 py-2 bg-[#59A8ED] text-white rounded-full font-bold text-sm hover:bg-blue-500 transition-colors shadow-sm">Trang chủ</a>
                    <a href="{{ url('/bai-viet') }}" class="px-5 py-2 bg-[#59A8ED] text-white rounded-full font-bold text-sm hover:bg-blue-500 transition-colors shadow-sm">Bài viết</a>
                    <a href="{{ route('herb-dictionary.index') }}" class="px-5 py-2 bg-[#59A8ED] text-white rounded-full font-bold text-sm hover:bg-blue-500 transition-colors shadow-sm">Từ điển thuốc nam</a>
                </nav>

                <!-- Right Actions -->
                <div class="flex items-center space-x-3">
                    <a href="{{ url('/dashboard') }}" class="relative inline-flex items-center justify-center pl-10 pr-5 py-1.5 bg-[#59A8ED] text-white rounded-full font-bold text-sm hover:bg-blue-500 transition-colors shadow-sm hidden md:inline-flex">
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-sm border border-blue-100">
                            <svg class="w-4 h-4 text-[#59A8ED]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                        </div>
                        TÀI KHOẢN
                    </a>
                    
                    @if(auth()->user()->isStaff())
                        <a href="{{ url('/admin/dashboard') }}" class="text-sm font-bold text-primary-600 hover:text-primary-800 hidden md:inline-block">Quản trị</a>
                    @endif

                    <form action="{{ url('/logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm font-bold text-gray-500 hover:text-gray-700 ml-2 hidden md:inline-block">Đăng xuất</button>
                    </form>

                    <!-- Language Switcher -->
                    <div class="relative group">
                        <button class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-[#59A8ED] transition-colors" title="Ngôn ngữ">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path></svg>
                        </button>
                        <div class="absolute right-0 mt-2 w-36 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block border border-gray-100">
                            <a href="#" onclick="changeLanguage('vi'); return false;" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-[#59A8ED]">🇻🇳 Tiếng Việt</a>
                            <a href="#" onclick="changeLanguage('en'); return false;" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-[#59A8ED]">🇬🇧 Tiếng Anh</a>
                            <a href="#" onclick="changeLanguage('zh-CN'); return false;" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-[#59A8ED]">🇨🇳 Tiếng Trung</a>
                        </div>
                    </div>

                    <!-- Sun/Moon Icon (Theme Toggle) -->
                    <button id="theme-toggle" class="w-8 h-8 flex items-center justify-center text-yellow-400 hover:text-yellow-500 transition-colors" title="Chế độ giao diện">
                        <svg id="theme-toggle-light-icon" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 2.366a1 1 0 011.415 0l.707.707a1 1 0 01-1.414 1.415l-.708-.707a1 1 0 010-1.415zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-2.574 4.22a1 1 0 010 1.415l-.707.707a1 1 0 01-1.415-1.414l.707-.707a1 1 0 011.415 0zM11 17a1 1 0 10-2 0v1a1 1 0 102 0v-1zm-4.22-2.366a1 1 0 01-1.415 0l-.707-.707a1 1 0 011.414-1.415l.708.707a1 1 0 010 1.415zM3 9a1 1 0 100 2h1a1 1 0 100-2H3zm2.574-4.22a1 1 0 010-1.415l.707-.707a1 1 0 011.415 1.414l-.707.707a1 1 0 01-1.415 0zM10 6a4 4 0 100 8 4 4 0 000-8z" clip-rule="evenodd"></path></svg>
                        <svg id="theme-toggle-dark-icon" class="w-6 h-6 hidden text-gray-700" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div style="max-width: 1000px; margin: 0 auto; padding: 0 1rem;">
        @if(session('status'))
            <div class="alert alert-success" style="margin-top: 1rem;"><span>✅</span> {{ session('status') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger" style="margin-top: 1rem;"><span>❌</span> {{ session('error') }}</div>
        @endif
    </div>

    <main style="max-width: 1000px; margin: 2rem auto; padding: 0 1rem;">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 1rem; text-align: center;">
            <p style="font-size: 1.125rem; font-weight: 600; color: #fff; margin-bottom: 0.5rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <img src="{{ asset('images/amatrung_logo.png') }}" alt="AmaTrung Logo" style="width: 24px; height: 24px; object-fit: contain; border-radius: 50%; background: #fff; padding: 2px;">
                AmaTrung — Phòng Khám Y Học Cổ Truyền
            </p>
            <p style="font-size: 0.85rem; opacity: 0.7;">© {{ date('Y') }} AmaTrung. Bảo lưu mọi quyền.</p>
        </div>
    </footer>

    @stack('scripts')
    
    <!-- Google Translate Script -->
    <div id="google_translate_element" class="hidden"></div>
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({pageLanguage: 'vi', includedLanguages: 'en,vi,zh-CN', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
        }
        function changeLanguage(langCode) {
            var selectField = document.querySelector(".goog-te-combo");
            if (selectField) {
                selectField.value = langCode;
                selectField.dispatchEvent(new Event('change'));
            }
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

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
    </script>

    <x-chatbot />
</body>
</html>
