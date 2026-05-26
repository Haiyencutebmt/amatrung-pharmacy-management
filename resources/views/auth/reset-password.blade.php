<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu — AmaTrung</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="auth-page">
        <div class="auth-form-side">
            <div class="auth-card">
                {{-- Tabs --}}
                <div class="auth-tabs">
                    <a href="{{ url('/login') }}" class="auth-tab">Đăng Nhập</a>
                    <a href="{{ url('/register') }}" class="auth-tab">Đăng Ký</a>
                    <a href="{{ url('/forgot-password') }}" class="auth-tab active">Quên Mật Khẩu</a>
                </div>

                <div class="auth-card-body">
                    <div class="auth-logo">
                        <div class="logo-icon">+</div>
                        <span class="logo-text">AmaTrung Digital</span>
                    </div>

                    <h2 style="margin: 0 0 0.5rem; color: #0f172a; font-size: 1.5rem; font-weight: 800;">Đặt Mật Khẩu Mới</h2>
                    <p style="color: #64748b; margin: 0 0 2rem; font-size: 0.95rem; line-height: 1.6;">
                        Xác thực thành công! Vui lòng nhập mật khẩu mới cho tài khoản của bạn.
                    </p>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">

                        <div class="form-group">
                            <label for="password" class="form-label">Mật khẩu mới</label>
                            <div class="form-input-icon">
                                <span class="icon">🔒</span>
                                <input type="password" id="password" name="password" class="form-input"
                                       placeholder="Tối thiểu 8 ký tự" autofocus required minlength="8">
                            </div>
                            @error('password') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                            <div class="form-input-icon">
                                <span class="icon">🔒</span>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="form-input" placeholder="Nhập lại mật khẩu mới" required minlength="8">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                            Đặt Lại Mật Khẩu
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="auth-illustration-side">
            <h2 style="color: #1e3a5f; font-size: 1.75rem; text-align: center; margin-bottom: 2rem; font-style: italic; line-height: 1.4;">
                Chào mừng đến với nhà thuốc<br>kỹ thuật số AmaTrung
            </h2>
            <img src="{{ asset('images/login-illustration.png') }}"
                 alt="AmaTrung" style="max-width: 420px; width: 100%; border-radius: 1rem;">
        </div>
    </div>
</body>
</html>
