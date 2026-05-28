@extends('layouts.guest')

@section('title', 'Quên mật khẩu — AmaTrung')

@section('content')
<div class="min-h-[calc(100vh-100px)] bg-slate-50/50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="w-full max-w-6xl rounded-[2.5rem] shadow-2xl border border-sky-100/60 overflow-hidden flex flex-col md:flex-row relative min-h-[580px] bg-white bg-cover bg-left" style="background-image: url('{{ asset('images/login-imagecute.png') }}');">
        
        {{-- Left: Text & Doc character --}}
        <div class="hidden md:flex md:w-[55%] flex-col justify-between p-12 pr-6 z-10 text-left">
            <div class="mt-8">
                <h2 class="text-3xl font-black text-[#1a5b8f] mb-4 tracking-wide leading-tight">Đừng lo lắng! 🌿</h2>
                <p class="text-slate-600 font-semibold text-sm max-w-sm leading-relaxed">
                    Chúng tôi sẽ giúp bạn lấy lại quyền truy cập vào tài khoản AmaTrung một cách dễ dàng.
                </p>
            </div>
            <!-- Space left for character painted on the background image -->
            <div class="h-48"></div>
        </div>

        {{-- Right: White Form Card --}}
        <div class="w-full md:w-[45%] flex items-center justify-center p-6 md:p-12 z-10">
            <!-- Floating White Card -->
            <div class="w-full max-w-md bg-white/95 backdrop-blur-md rounded-[2.5rem] shadow-[0_15px_40px_rgba(59,130,246,0.08)] border border-sky-100/50 p-8 md:p-10 flex flex-col justify-center">
                
                {{-- Key Icon --}}
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-[#e0f2fe]/80 rounded-full flex items-center justify-center border-4 border-white shadow-sm text-blue-500">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                </div>

                <h3 class="text-2xl font-black text-slate-800 text-center uppercase tracking-wide">Quên Mật Khẩu?</h3>
                <p class="text-slate-400 text-xs font-semibold text-center mt-1 mb-8">Nhập email hoặc số điện thoại của bạn, chúng tôi sẽ gửi mã OTP để đặt lại mật khẩu.</p>

                {{-- Flash messages --}}
                @if(session('status'))
                    <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 text-sm font-medium rounded-r-xl">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.send-otp') }}" class="space-y-5">
                    @csrf

                    <!-- Identifier Input -->
                    <div>
                        <label for="identifier" class="block text-[10px] font-bold text-[#1a5b8f] uppercase tracking-wider mb-1.5 pl-1">Số điện thoại / Email</label>
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input type="text" id="identifier" name="identifier" class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-xl focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-100 transition-all font-medium text-slate-800 text-sm placeholder-slate-300" placeholder="Nhập email hoặc SĐT" value="{{ old('identifier') }}" required autofocus>
                        </div>
                        @error('identifier') <p class="text-xs text-red-500 font-medium mt-1 pl-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-extrabold text-sm rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 tracking-wide uppercase mt-8">
                        <span>Gửi Mã Xác Thực (OTP)</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </button>
                </form>

                {{-- Footer --}}
                <div class="text-center mt-10 border-t border-slate-100/80 pt-6">
                    <p class="text-xs text-slate-400 font-bold">
                        <a href="{{ url('/login') }}" class="text-blue-500 font-extrabold hover:underline inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            Quay lại đăng nhập
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
