@extends('layouts.guest')

@section('title', 'Đăng ký — AmaTrung')

@section('content')
<div class="min-h-[calc(100vh-80px)] bg-slate-50/50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="flex flex-col md:flex-row w-full max-w-5xl relative items-center justify-center">
        
        {{-- Left: Image Card (Poster) --}}
        <div class="w-full md:w-[45%] h-auto md:h-[600px] rounded-2xl shadow-2xl z-10 relative md:translate-x-12 mb-8 md:mb-0 overflow-hidden bg-blue-50 border-4 border-white">
            <img src="{{ asset('images/water_poster.jpg') }}" alt="Ý nghĩa của Nước trong Y học cổ truyền" class="w-full h-full object-cover">
            
            <!-- Nút Về Trang Chủ đặt nổi trên ảnh -->
            <a href="{{ url('/') }}" class="absolute top-6 left-6 inline-flex items-center gap-2 px-4 py-2 bg-white/70 border border-white/50 rounded-full text-sm font-bold text-blue-600 hover:bg-white transition-colors backdrop-blur-md shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Trang Chủ
            </a>
        </div>

        {{-- Right: White Form Card --}}
        <div class="w-full md:w-[55%] min-h-[600px] bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.05)] p-10 md:p-12 md:pl-20 relative z-0 flex flex-col justify-center border border-gray-100 my-8 md:my-0">
            
            <div class="max-w-sm w-full mx-auto md:mx-0">
                <h3 class="text-[2.5rem] font-bold text-teal-500 mb-3 tracking-tight">Tạo Tài Khoản</h3>
                <p class="text-gray-400 text-sm mb-6 leading-relaxed font-medium">
                    Vui lòng điền thông tin để đăng ký tài khoản quản lý hồ sơ và theo dõi sức khỏe của bạn.
                </p>

                <form method="POST" action="{{ url('/register') }}" class="space-y-4.5">
                    @csrf

                    <!-- Name Input -->
                    <div class="group bg-slate-50 border-l-4 border-transparent focus-within:bg-white focus-within:shadow-md focus-within:border-teal-500 transition-all rounded-r-lg p-2.5 pt-1.5">
                        <label for="name" class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block mb-0.5">Họ và tên</label>
                        <input type="text" id="name" name="name" class="w-full bg-transparent border-none focus:ring-0 p-0 text-sm font-bold text-gray-800 placeholder-gray-300 outline-none" placeholder="Nhập họ và tên..." value="{{ old('name') }}" required autofocus>
                    </div>
                    @error('name') <p class="text-xs text-red-500 font-medium -mt-2">{{ $message }}</p> @enderror

                    <!-- Email Input -->
                    <div class="group bg-slate-50 border-l-4 border-transparent focus-within:bg-white focus-within:shadow-md focus-within:border-teal-500 transition-all rounded-r-lg p-2.5 pt-1.5">
                        <label for="email" class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block mb-0.5">Số điện thoại / Email</label>
                        <input type="text" id="email" name="email" class="w-full bg-transparent border-none focus:ring-0 p-0 text-sm font-bold text-gray-800 placeholder-gray-300 outline-none" placeholder="Nhập email hoặc SĐT" value="{{ old('email') }}" required>
                    </div>
                    @error('email') <p class="text-xs text-red-500 font-medium -mt-2">{{ $message }}</p> @enderror

                    <!-- Password Input -->
                    <div class="group bg-slate-50 border-l-4 border-transparent focus-within:bg-white focus-within:shadow-md focus-within:border-teal-500 transition-all rounded-r-lg p-2.5 pt-1.5 relative">
                        <label for="password" class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block mb-0.5">Mật khẩu</label>
                        <input type="password" id="password" name="password" class="w-full bg-transparent border-none focus:ring-0 p-0 text-sm font-bold text-gray-800 placeholder-gray-300 outline-none pr-8" placeholder="Nhập mật khẩu" required>
                        <button type="button" class="toggle-password absolute right-3 bottom-2 text-gray-400 hover:text-gray-600 outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                    @error('password') <p class="text-xs text-red-500 font-medium -mt-2">{{ $message }}</p> @enderror

                    <!-- Password Confirmation -->
                    <div class="group bg-slate-50 border-l-4 border-transparent focus-within:bg-white focus-within:shadow-md focus-within:border-teal-500 transition-all rounded-r-lg p-2.5 pt-1.5 relative">
                        <label for="password_confirmation" class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block mb-0.5">Xác nhận mật khẩu</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="w-full bg-transparent border-none focus:ring-0 p-0 text-sm font-bold text-gray-800 placeholder-gray-300 outline-none pr-8" placeholder="Nhập lại mật khẩu" required>
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start pt-1">
                        <label class="flex items-start gap-2 cursor-pointer group">
                            <input type="checkbox" name="agree" class="mt-0.5 w-4 h-4 text-teal-500 border-gray-300 rounded focus:ring-teal-500 focus:ring-2" required>
                            <span class="text-xs font-medium text-gray-500 group-hover:text-gray-700 transition-colors leading-relaxed">
                                Tôi đồng ý với <a href="#" class="text-teal-500 font-bold hover:underline">Điều khoản</a> và <a href="#" class="text-teal-500 font-bold hover:underline">Chính sách bảo mật</a>
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-blue-400 to-blue-600 hover:from-blue-500 hover:to-blue-700 text-white font-bold text-sm rounded-lg shadow-lg shadow-teal-500/30 transition-all hover:-translate-y-0.5 mt-4 tracking-wide uppercase">
                        Đăng Ký
                    </button>
                </form>
                
                <div class="mt-8 border-t border-slate-100/80 pt-5 text-center md:text-left">
                    <p class="text-sm text-gray-500 font-medium">
                        Đã có tài khoản? <a href="{{ url('/login') }}" class="text-blue-500 font-bold hover:underline">Đăng nhập ngay</a>
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
                        this.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>';
                    } else {
                        input.type = 'password';
                        this.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>';
                    }
                }
            });
        });
    });
</script>
@endsection
