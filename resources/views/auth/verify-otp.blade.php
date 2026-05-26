<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhập mã OTP — AmaTrung</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .otp-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 1.5rem 0 2rem;
        }
        .otp-inputs input {
            width: 56px;
            height: 64px;
            text-align: center;
            font-size: 1.75rem;
            font-weight: 800;
            border: 2px solid #e2e8f0;
            border-radius: 0.875rem;
            background: #f8fafc;
            color: #1e293b;
            transition: all 0.25s;
            font-family: 'Courier New', monospace;
        }
        .otp-inputs input:focus {
            outline: none;
            border-color: #0ea5e9;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
        }
    </style>
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

                    <h2 style="margin: 0 0 0.5rem; color: #0f172a; font-size: 1.5rem; font-weight: 800;">Nhập Mã Xác Thực</h2>
                    <p style="color: #64748b; margin: 0 0 0.5rem; font-size: 0.95rem; line-height: 1.6;">
                        Chúng tôi đã gửi mã OTP 6 chữ số đến email của bạn.
                    </p>

                    {{-- Flash --}}
                    @if(session('status'))
                        <div class="alert alert-success"><span>✅</span> {{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('password.verify') }}" id="otpForm">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email ?? session('otp_email') }}">
                        <input type="hidden" name="otp_code" id="otpHidden" value="">

                        {{-- 6 ô nhập OTP --}}
                        <div class="otp-inputs">
                            <input type="text" maxlength="1" class="otp-box" inputmode="numeric" pattern="[0-9]" autofocus>
                            <input type="text" maxlength="1" class="otp-box" inputmode="numeric" pattern="[0-9]">
                            <input type="text" maxlength="1" class="otp-box" inputmode="numeric" pattern="[0-9]">
                            <input type="text" maxlength="1" class="otp-box" inputmode="numeric" pattern="[0-9]">
                            <input type="text" maxlength="1" class="otp-box" inputmode="numeric" pattern="[0-9]">
                            <input type="text" maxlength="1" class="otp-box" inputmode="numeric" pattern="[0-9]">
                        </div>

                        @error('otp_code') <p class="form-error" style="text-align: center; margin-bottom: 1rem;">{{ $message }}</p> @enderror

                        <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                            Xác Nhận Mã OTP
                        </button>
                    </form>

                    {{-- Gửi lại OTP --}}
                    <div style="text-align: center; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid #f1f5f9;">
                        <p style="color: #94a3b8; font-size: 0.85rem; margin: 0 0 0.5rem;">Chưa nhận được mã?</p>
                        <form method="POST" action="{{ route('password.resend-otp') }}" style="display: inline;">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email ?? session('otp_email') }}">
                            <button type="submit" style="background: none; border: none; color: #2563eb; font-weight: 700; font-size: 0.9rem; cursor: pointer; text-decoration: underline;">
                                Gửi lại mã OTP
                            </button>
                        </form>
                    </div>

                    <div style="text-align: center; margin-top: 1rem;">
                        <a href="{{ url('/forgot-password') }}" style="color: #64748b; font-size: 0.85rem;">← Thay đổi email/số điện thoại</a>
                    </div>
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

    <script>
        // OTP input auto-focus và ghép giá trị
        const boxes = document.querySelectorAll('.otp-box');
        const hidden = document.getElementById('otpHidden');
        const form = document.getElementById('otpForm');

        function updateHidden() {
            hidden.value = Array.from(boxes).map(b => b.value).join('');
        }

        boxes.forEach((box, i) => {
            box.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                updateHidden();
                if (this.value && i < boxes.length - 1) boxes[i + 1].focus();
                // Auto submit khi đủ 6 số
                if (hidden.value.length === 6) form.submit();
            });

            box.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && i > 0) {
                    boxes[i - 1].focus();
                    boxes[i - 1].value = '';
                    updateHidden();
                }
            });

            // Hỗ trợ paste toàn bộ OTP
            box.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
                for (let j = 0; j < Math.min(pasted.length, 6); j++) {
                    boxes[j].value = pasted[j];
                }
                updateHidden();
                if (pasted.length >= 6) form.submit();
                else boxes[Math.min(pasted.length, 5)].focus();
            });
        });
    </script>
</body>
</html>
