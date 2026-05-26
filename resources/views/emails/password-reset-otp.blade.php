<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã xác thực OTP</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', system-ui, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="480" cellpadding="0" cellspacing="0" style="background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); overflow: hidden;">
                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a5f, #0f172a); padding: 32px 40px; text-align: center;">
                            <div style="display: inline-block; width: 40px; height: 40px; background: #fff; border-radius: 10px; line-height: 40px; font-size: 22px; font-weight: 800; color: #2563eb; margin-bottom: 8px;">+</div>
                            <h1 style="color: #fff; font-size: 22px; margin: 8px 0 0; font-weight: 700; letter-spacing: 0.05em;">AMATRUNG</h1>
                            <p style="color: rgba(255,255,255,0.6); font-size: 12px; margin: 4px 0 0; text-transform: uppercase; letter-spacing: 0.1em;">Nhà thuốc Kỹ thuật số</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 40px;">
                            <p style="color: #1e293b; font-size: 16px; margin: 0 0 8px;">Xin chào <strong>{{ $userName }}</strong>,</p>
                            <p style="color: #64748b; font-size: 15px; line-height: 1.6; margin: 0 0 28px;">
                                Bạn vừa yêu cầu đặt lại mật khẩu tài khoản AmaTrung. Vui lòng sử dụng mã OTP bên dưới để xác thực:
                            </p>

                            {{-- OTP Code --}}
                            <div style="background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 12px; padding: 24px; text-align: center; margin-bottom: 28px;">
                                <p style="color: #64748b; font-size: 13px; margin: 0 0 10px; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Mã xác thực của bạn</p>
                                <div style="font-size: 36px; font-weight: 800; letter-spacing: 12px; color: #2563eb; font-family: 'Courier New', monospace;">
                                    {{ $otpCode }}
                                </div>
                            </div>

                            {{-- Warning --}}
                            <div style="background: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 0 8px 8px 0; padding: 14px 18px; margin-bottom: 28px;">
                                <p style="color: #92400e; font-size: 14px; margin: 0; line-height: 1.5;">
                                    ⏰ Mã có hiệu lực trong <strong>10 phút</strong>. Không chia sẻ mã này với bất kỳ ai.
                                </p>
                            </div>

                            <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0;">
                                Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này. Tài khoản của bạn vẫn an toàn.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background: #f8fafc; padding: 20px 40px; border-top: 1px solid #f1f5f9; text-align: center;">
                            <p style="color: #94a3b8; font-size: 12px; margin: 0;">
                                © {{ date('Y') }} AmaTrung · Nhà thuốc Y Học Cổ Truyền
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
