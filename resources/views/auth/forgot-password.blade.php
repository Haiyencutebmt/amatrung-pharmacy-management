@extends('layouts.guest')

@section('title', 'Quên mật khẩu — AmaTrung')

@section('content')
<div class="min-h-[calc(100vh-80px)] bg-slate-50/50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="flex flex-col md:flex-row w-full max-w-5xl relative items-center justify-center">
        
        {{-- Left: Image Card (Poster) --}}
        <div class="w-full md:w-[45%] h-auto md:h-[550px] rounded-2xl shadow-2xl z-10 relative md:translate-x-12 mb-8 md:mb-0 overflow-hidden bg-blue-50 border-4 border-white">
            <img src="{{ asset('images/water_poster.jpg') }}" alt="Ý nghĩa của Nước trong Y học cổ truyền" class="w-full h-full object-cover">
            
            <!-- Nút Về Trang Chủ đặt nổi trên ảnh -->
            <a href="{{ url('/') }}" class="absolute top-6 left-6 inline-flex items-center gap-2 px-4 py-2 bg-white/70 border border-white/50 rounded-full text-sm font-bold text-blue-600 hover:bg-white transition-colors backdrop-blur-md shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Trang Chủ
            </a>
        </div>

        {{-- Right: White Form Card --}}
        <div class="w-full md:w-[55%] min-h-[550px] bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.05)] p-10 md:p-12 md:pl-20 relative z-0 flex flex-col justify-center border border-gray-100">
            
            <div class="max-w-sm w-full mx-auto md:mx-0">
                <h3 class="text-[2.5rem] font-bold text-teal-500 mb-3 tracking-tight">Quên Mật Khẩu?</h3>
                <p class="text-gray-400 text-sm mb-10 leading-relaxed font-medium">
                    Nhập email hoặc số điện thoại của bạn, chúng tôi sẽ gửi mã OTP để đặt lại mật khẩu.
                </p>

                {{-- Flash messages --}}
                @if(session('status'))
                    <div class="bg-teal-50 border-l-4 border-teal-500 text-teal-700 p-4 mb-6 text-sm font-medium">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.send-otp') }}" class="space-y-6">
                    @csrf

                    <!-- Identifier Input -->
                    <div class="group bg-slate-50 border-l-4 border-transparent focus-within:bg-white focus-within:shadow-md focus-within:border-teal-500 transition-all rounded-r-lg p-3 pt-2">
                        <label for="identifier" class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block mb-0.5">Số điện thoại / Email</label>
                        <input type="text" id="identifier" name="identifier" class="w-full bg-transparent border-none focus:ring-0 p-0 text-sm font-bold text-gray-800 placeholder-gray-300 outline-none" placeholder="Nhập email hoặc SĐT" value="{{ old('identifier') }}" required autofocus>
                    </div>
                    @error('identifier') <p class="text-xs text-red-500 font-medium -mt-3">{{ $message }}</p> @enderror

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-blue-400 to-blue-600 hover:from-blue-500 hover:to-blue-700 text-white font-bold text-sm rounded-lg shadow-lg shadow-teal-500/30 transition-all hover:-translate-y-0.5 mt-4 tracking-wide uppercase">
                        Gửi Mã Xác Thực (OTP)
                    </button>
                </form>
                
                <div class="mt-8 border-t border-slate-100/80 pt-5 text-center md:text-left">
                    <a href="{{ url('/login') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 font-bold hover:text-blue-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Quay lại đăng nhập
                    </a>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection
