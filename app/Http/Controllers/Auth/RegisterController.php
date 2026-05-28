<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Hiển thị form đăng ký.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký tài khoản.
     * Yêu cầu email (để quên mật khẩu). Phone tùy chọn (liên kết hồ sơ bệnh nhân).
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:100',
            'email'         => 'required|string|email|max:150|unique:users,email',
            'phone'         => 'required|string|max:15|unique:users,phone',
            'password'      => 'required|string|min:6|confirmed',
            'agree_privacy' => 'accepted',
        ], [
            'name.required'          => 'Vui lòng nhập họ tên.',
            'email.required'         => 'Vui lòng nhập email.',
            'email.email'            => 'Email không hợp lệ.',
            'email.unique'           => 'Email đã được sử dụng.',
            'phone.required'         => 'Vui lòng nhập số điện thoại.',
            'phone.unique'           => 'Số điện thoại đã được sử dụng.',
            'password.required'      => 'Vui lòng nhập mật khẩu.',
            'password.min'           => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed'     => 'Mật khẩu xác nhận không khớp.',
            'agree_privacy.accepted' => 'Bạn vui lòng tích chọn đồng ý với chính sách bảo mật để tiếp tục.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'user',
            'is_active'=> 1,
        ]);

        Auth::login($user);

        return redirect('/dashboard')->with('status', 'Đăng ký thành công! Chào mừng bạn đến với AmaTrung.');
    }
}
