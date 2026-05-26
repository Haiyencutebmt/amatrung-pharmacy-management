<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    /**
     * Bước 1: Hiển thị form nhập email/phone.
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Bước 2: Tìm user, tạo OTP, gửi email.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string|max:150',
        ], [
            'identifier.required' => 'Vui lòng nhập email hoặc số điện thoại.',
        ]);

        $identifier = trim($request->input('identifier'));

        // Tìm user theo email hoặc phone
        $user = User::where('email', $identifier)
                     ->orWhere('phone', $identifier)
                     ->first();

        if (! $user) {
            return back()
                ->withInput()
                ->withErrors(['identifier' => 'Không tìm thấy tài khoản với thông tin này.']);
        }

        // Kiểm tra user có email không
        if (! $user->email) {
            return back()
                ->withInput()
                ->withErrors(['identifier' => 'Tài khoản chưa có email khôi phục. Vui lòng liên hệ Admin để được hỗ trợ.']);
        }

        // Tạo OTP 6 chữ số
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Lưu vào database
        $user->update([
            'reset_code'            => $otpCode,
            'reset_code_expires_at' => now()->addMinutes(10),
        ]);

        // Gửi email
        try {
            Mail::to($user->email)->send(new PasswordResetOtp($otpCode, $user->name));
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['identifier' => 'Không thể gửi email xác thực. Vui lòng kiểm tra lại cấu hình SMTP hoặc thử lại sau.']);
        }

        // Lưu email vào session (che bớt)
        $maskedEmail = $this->maskEmail($user->email);

        return redirect()
            ->route('password.verify.form')
            ->with('otp_email', $user->email)
            ->with('status', "Mã OTP đã được gửi đến {$maskedEmail}. Vui lòng kiểm tra hộp thư.");
    }

    /**
     * Bước 3: Hiển thị form nhập OTP.
     */
    public function showVerifyOtpForm(Request $request)
    {
        // Phải có email trong session
        $email = session('otp_email') ?? $request->input('email');

        if (! $email) {
            return redirect()->route('password.request')
                ->withErrors(['identifier' => 'Phiên làm việc đã hết hạn. Vui lòng thử lại.']);
        }

        return view('auth.verify-otp', ['email' => $email]);
    }

    /**
     * Bước 4: Xác thực OTP.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'otp_code' => 'required|digits:6',
        ], [
            'otp_code.required' => 'Vui lòng nhập mã OTP.',
            'otp_code.digits'   => 'Mã OTP phải gồm 6 chữ số.',
        ]);

        $user = User::where('email', $request->input('email'))
                     ->where('reset_code', $request->input('otp_code'))
                     ->first();

        if (! $user) {
            return back()
                ->withInput()
                ->with('email', $request->input('email'))
                ->withErrors(['otp_code' => 'Mã OTP không chính xác.']);
        }

        // Kiểm tra hết hạn
        if (! $user->reset_code_expires_at || now()->greaterThan($user->reset_code_expires_at)) {
            // Xóa OTP đã hết hạn
            $user->update(['reset_code' => null, 'reset_code_expires_at' => null]);

            return back()
                ->withInput()
                ->with('email', $request->input('email'))
                ->withErrors(['otp_code' => 'Mã OTP đã hết hạn. Vui lòng yêu cầu mã mới.']);
        }

        // OTP hợp lệ → chuyển sang trang đặt mật khẩu mới
        // Dùng session token tạm để bảo vệ bước đặt lại mật khẩu
        $resetToken = bin2hex(random_bytes(32));
        session(['password_reset_token' => $resetToken, 'password_reset_email' => $user->email]);

        return redirect()
            ->route('password.reset.form')
            ->with('reset_token', $resetToken);
    }

    /**
     * Bước 5: Hiển thị form đặt mật khẩu mới.
     */
    public function showResetPasswordForm(Request $request)
    {
        $token = session('password_reset_token') ?? $request->input('token');
        $email = session('password_reset_email');

        if (! $token || ! $email) {
            return redirect()->route('password.request')
                ->withErrors(['identifier' => 'Phiên xác thực đã hết hạn. Vui lòng thử lại.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Bước 6: Xử lý đổi mật khẩu.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required|string',
            'email'    => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required'  => 'Vui lòng nhập mật khẩu mới.',
            'password.min'       => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        // Kiểm tra session token
        $sessionToken = session('password_reset_token');
        if (! $sessionToken || $sessionToken !== $request->input('token')) {
            return redirect()->route('password.request')
                ->withErrors(['identifier' => 'Phiên xác thực không hợp lệ. Vui lòng thử lại.']);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (! $user) {
            return redirect()->route('password.request')
                ->withErrors(['identifier' => 'Không tìm thấy tài khoản.']);
        }

        // Đổi mật khẩu & xóa OTP
        $user->update([
            'password'              => Hash::make($request->input('password')),
            'reset_code'            => null,
            'reset_code_expires_at' => null,
        ]);

        // Xóa session
        session()->forget(['password_reset_token', 'password_reset_email', 'otp_email']);

        return redirect('/login')
            ->with('status', '🎉 Mật khẩu đã được đặt lại thành công. Vui lòng đăng nhập.');
    }

    /**
     * Gửi lại OTP (nút "Gửi lại mã").
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (! $user) {
            return back()->withErrors(['identifier' => 'Không tìm thấy tài khoản.']);
        }

        // Tạo OTP mới
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'reset_code'            => $otpCode,
            'reset_code_expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::to($user->email)->send(new PasswordResetOtp($otpCode, $user->name));
        } catch (\Exception $e) {
            return back()
                ->with('otp_email', $user->email)
                ->withErrors(['otp_code' => 'Không thể gửi lại mã OTP. Vui lòng kiểm tra cấu hình mail.']);
        }

        return back()
            ->with('otp_email', $user->email)
            ->with('status', 'Mã OTP mới đã được gửi lại. Vui lòng kiểm tra hộp thư.');
    }

    /**
     * Che bớt email: jo***@gmail.com
     */
    private function maskEmail(string $email): string
    {
        [$name, $domain] = explode('@', $email);
        $masked = substr($name, 0, 2) . str_repeat('*', max(strlen($name) - 2, 3));
        return $masked . '@' . $domain;
    }
}
