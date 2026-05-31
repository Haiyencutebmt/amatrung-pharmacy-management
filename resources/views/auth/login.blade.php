@extends('layouts.guest')

@section('title', 'Đăng nhập — AmaTrung')

@section('content')
<div class="min-h-[calc(100vh-100px)] bg-slate-50/50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="w-full max-w-6xl rounded-[2.5rem] shadow-2xl border border-sky-100/60 overflow-hidden flex flex-col md:flex-row relative min-h-[580px] bg-white bg-cover bg-left" style="background-image: url('{{ asset('images/login-imagecute.png') }}');">
        <div class="hidden md:block absolute left-[10%] bottom-8 z-20 w-[240px] lg:w-[285px] xl:w-[310px]">
            <div class="overflow-hidden rounded-[2rem] border-[5px] border-white bg-sky-100 shadow-[0_24px_55px_rgba(30,64,175,0.22)]">
                <img src="{{ asset('images/doctor.JPG') }}" alt="Thầy thuốc Y Hiếu Niê" class="w-full aspect-[4/5] object-cover object-center">
            </div>
            <div class="mt-3 text-center">
                <p class="text-white text-xl font-black drop-shadow-[0_2px_6px_rgba(30,64,175,0.35)]">Thầy thuốc Y Hiếu Niê</p>
                <p class="text-white/95 text-lg font-extrabold drop-shadow-[0_2px_6px_rgba(30,64,175,0.35)]">(AmaTrung)</p>
            </div>
        </div>
        
        {{-- Left: Text & Doc character --}}
        <div class="hidden md:flex md:w-[55%] flex-col justify-between p-12 pr-6 z-10 text-left">
            <div class="mt-8">
                <h2 class="text-3xl font-black text-[#1a5b8f] mb-4 tracking-wide leading-tight">Chào mừng bạn trở lại! 🍃</h2>
                <p class="text-slate-600 font-semibold text-sm max-w-sm leading-relaxed">
                    Đăng nhập để tiếp tục hành trình khám phá tri thức Y học cổ truyền cùng AmaTrung
                </p>
            </div>
            <!-- Space left for character painted on the background image -->
            <div class="h-48"></div>
        </div>

        {{-- Right: White Form Card --}}
        <div class="w-full md:w-[45%] flex items-center justify-center p-6 md:p-12 z-10">
            <!-- Floating White Card -->
            <div class="w-full max-w-md bg-white/95 backdrop-blur-md rounded-[2.5rem] shadow-[0_15px_40px_rgba(59,130,246,0.08)] border border-sky-100/50 p-8 md:p-10 flex flex-col justify-center">
                
                {{-- Security Icon --}}
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-[#e0f2fe]/80 rounded-full flex items-center justify-center border-4 border-white shadow-sm text-blue-500">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                </div>

                <h3 class="text-2xl font-black text-slate-800 text-center uppercase tracking-wide">Đăng nhập</h3>
                <p class="text-slate-400 text-xs font-semibold text-center mt-1 mb-6">Vui lòng đăng nhập để tiếp tục</p>

                <form method="POST" action="{{ url('/login') }}" class="space-y-5">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label for="login" class="block text-[10px] font-bold text-[#1a5b8f] uppercase tracking-wider mb-1.5 pl-1">Số điện thoại / Email</label>
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input type="text" id="login" name="login" class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-xl focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all font-medium text-slate-800 text-sm placeholder-slate-300" placeholder="Nhập email hoặc SĐT" value="{{ old('login') }}" required autofocus>
                        </div>
                        @error('login') <p class="text-xs text-red-500 font-medium mt-1 pl-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-[10px] font-bold text-[#1a5b8f] uppercase tracking-wider mb-1.5 pl-1">Mật khẩu</label>
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input type="password" id="password" name="password" class="w-full pl-12 pr-10 py-3 bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-xl focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all font-medium text-slate-800 text-sm placeholder-slate-300" placeholder="Nhập mật khẩu" required>
                            <button type="button" class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 outline-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                        @error('password') <p class="text-xs text-red-500 font-medium mt-1 pl-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex justify-between items-center text-xs pt-1">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-blue-500 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <span class="font-bold text-slate-400 group-hover:text-slate-600 transition-colors">Ghi nhớ đăng nhập</span>
                        </label>
                        <a href="{{ url('/forgot-password') }}" class="font-bold text-blue-500 hover:text-blue-700 transition-colors">Quên mật khẩu?</a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-extrabold text-sm rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 tracking-wide uppercase mt-8">
                        <span>Đăng Nhập</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </button>
                </form>

                {{-- Footer --}}
                <div class="text-center mt-10 border-t border-slate-100/80 pt-6">
                    <p class="text-xs text-slate-400 font-bold">
                        Chưa có tài khoản? 
                        <a href="{{ url('/register') }}" class="text-blue-500 font-extrabold hover:underline inline-flex items-center gap-1">
                            Đăng ký ngay 
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    // Toggle password visibility
    document.addEventListener('DOMContentLoaded', function() {
        const togglePasswordBtns = document.querySelectorAll('.toggle-password');
        togglePasswordBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.previousElementSibling;
                if (input && input.tagName === 'INPUT') {
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>';
                    } else {
                        input.type = 'password';
                        this.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>';
                    }
                }
            });
        });
    });
</script>
@endsection
