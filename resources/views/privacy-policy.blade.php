<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chính sách bảo mật — AmaTrung</title>
    <!-- Google Fonts: Load Be Vietnam Pro hỗ trợ tiếng Việt 100% cực đẹp và chuẩn xác -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #5eb542;
            --primary-light: #eef9ee;
            --primary-dark: #468c30;
            --bg: #f2faf3;
            --text-dark: #1e293b;
            --text-muted: #475569;
            --card-bg: #ffffff;
            --pastel-blue: #eff6ff;
            --pastel-orange: #fff7ed;
            --pastel-purple: #faf5ff;
            --pastel-pink: #fff1f2;
            --pastel-green: #f0fdf4;
            --border-radius-lg: 1.75rem;
            --border-radius-md: 1.25rem;
        }

        body {
            font-family: 'Be Vietnam Pro', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: var(--bg);
            color: var(--text-dark);
            margin: 0;
            padding: 3rem 1.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .policy-container {
            max-width: 900px;
            width: 100%;
            background: var(--card-bg);
            border-radius: var(--border-radius-lg);
            box-shadow: 0 20px 50px rgba(94, 181, 66, 0.06), 0 4px 12px rgba(0,0,0,0.02);
            border: 2px solid var(--primary-light);
            padding: 3.5rem;
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
        }

        /* Trang trí cute */
        .decor-leaf {
            position: absolute;
            font-size: 8rem;
            opacity: 0.05;
            user-select: none;
            pointer-events: none;
            z-index: 1;
        }
        .leaf-top { top: -20px; right: -20px; transform: rotate(45deg); }
        .leaf-bottom { bottom: -20px; left: -20px; transform: rotate(-135deg); }

        .policy-header {
            position: relative;
            z-index: 2;
            text-align: center;
            margin-bottom: 3.5rem;
            border-bottom: 2px dashed var(--primary-light);
            padding-bottom: 2.5rem;
        }

        .policy-icon {
            font-size: 3.5rem;
            margin-bottom: 0.75rem;
            display: inline-block;
            animation: float-leaf 4s ease-in-out infinite;
        }

        @keyframes float-leaf {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(10deg); }
        }

        .policy-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--text-dark);
            margin: 0;
            letter-spacing: -0.02em;
            line-height: 1.3;
        }

        .policy-subtitle {
            margin: 0.75rem 0 0;
            color: var(--text-muted);
            font-size: 1.1rem;
            font-weight: 500;
            line-height: 1.4;
        }

        .update-time {
            display: inline-block;
            margin-top: 1rem;
            background: var(--primary-light);
            color: var(--primary-dark);
            padding: 0.35rem 1rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .policy-section {
            position: relative;
            z-index: 2;
            margin-bottom: 3.5rem;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 1.25rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            line-height: 1.3;
            border-left: 4px solid var(--primary);
            padding-left: 0.75rem;
        }

        .section-title span.emoji {
            font-size: 1.6rem;
            line-height: 1;
        }

        .policy-text {
            font-size: 0.975rem;
            line-height: 1.7;
            color: var(--text-muted);
            margin: 0 0 1.5rem 0;
            font-weight: 400;
            text-align: justify;
        }

        .policy-text-sub {
            font-size: 0.95rem;
            line-height: 1.6;
            color: var(--text-muted);
            margin: 0 0 0.75rem 0;
            padding-left: 1.5rem;
            position: relative;
            font-weight: 500;
        }

        .policy-text-sub::before {
            content: "•";
            color: var(--primary);
            font-size: 1.2rem;
            position: absolute;
            left: 0.5rem;
            top: -2px;
        }

        .grid-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 650px) {
            .grid-cards {
                grid-template-columns: 1fr;
            }
        }

        .info-card {
            padding: 1.75rem;
            border-radius: var(--border-radius-md);
            border: 1.5px solid transparent;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .info-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(94, 181, 66, 0.05);
        }

        .info-card.email { background: var(--pastel-blue); border-color: #dbeafe; }
        .info-card.phone { background: var(--pastel-orange); border-color: #ffedd5; }
        .info-card.bio { background: var(--pastel-purple); border-color: #f3e8ff; }
        .info-card.medical { background: var(--pastel-pink); border-color: #ffe4e6; }

        .card-header-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-emoji {
            font-size: 1.5rem;
            background: #ffffff;
            padding: 0.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.02);
            line-height: 1;
        }

        .card-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .card-desc {
            margin: 0;
            font-size: 0.925rem;
            line-height: 1.6;
            color: var(--text-muted);
            font-weight: 450;
        }

        /* List group style */
        .list-group {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .list-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: var(--border-radius-md);
            padding: 1.5rem;
            display: flex;
            gap: 1.25rem;
            align-items: flex-start;
            transition: all 0.3s ease;
        }

        .list-item:hover {
            border-color: var(--primary-light);
            background: #fdfefe;
            transform: translateX(4px);
        }

        .list-num {
            background: var(--primary);
            color: white;
            font-weight: 700;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.95rem;
            box-shadow: 0 4px 10px rgba(94,181,66,0.25);
        }

        .list-content h4 {
            margin: 0 0 0.5rem 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .list-content p {
            margin: 0;
            font-size: 0.925rem;
            line-height: 1.6;
            color: var(--text-muted);
            font-weight: 450;
        }

        .badge-container {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }

        .badge-cute {
            background: #ffffff;
            border: 1.5px solid var(--primary-light);
            color: var(--primary-dark);
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0,0,0,0.01);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .policy-footer {
            position: relative;
            z-index: 2;
            margin-top: 4rem;
            text-align: center;
            border-top: 2px dashed var(--primary-light);
            padding-top: 3rem;
        }

        .btn-back {
            background: var(--primary);
            color: #ffffff;
            padding: 1.1rem 3rem;
            font-size: 1.05rem;
            font-weight: 700;
            border-radius: var(--border-radius-md);
            border: none;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(94, 181, 66, 0.2);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-back:hover {
            background: var(--primary-dark);
            box-shadow: 0 10px 30px rgba(94, 181, 66, 0.3);
            transform: translateY(-2px);
        }

        .brand-note {
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: #94a3b8;
            font-weight: 500;
        }

        /* Thêm style cho phần trích dẫn nổi bật */
        .highlight-box {
            background: var(--pastel-green);
            border-left: 4px solid var(--primary);
            padding: 1.5rem;
            border-radius: 0 1rem 1rem 0;
            margin-bottom: 2rem;
        }

        .highlight-box p {
            margin: 0;
            font-size: 0.975rem;
            line-height: 1.6;
            color: var(--primary-dark);
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="policy-container">
        <div class="decor-leaf leaf-top">🌿</div>
        <div class="decor-leaf leaf-bottom">🌿</div>

        <!-- Header -->
        <div class="policy-header">
            <span class="policy-icon">🌱</span>
            <h1 class="policy-title">Lá Thư Bảo Mật Từ AmaTrung</h1>
            <p class="policy-subtitle">Sự an tâm và an toàn dữ liệu sức khỏe của bạn là cam kết lớn nhất của chúng tôi</p>
            <span class="update-time">Cập nhật mới nhất: Ngày 22 tháng 5, 2026</span>
        </div>

        <!-- Lời mở đầu -->
        <div class="highlight-box">
            <p>Chào mừng bạn đến với AmaTrung! Chúng tôi hiểu rằng thông tin sức khỏe và bệnh án là những dữ liệu cực kỳ nhạy cảm và riêng tư. AmaTrung cam kết áp dụng các biện pháp bảo mật tối tân để bảo vệ hồ sơ của bạn, giúp bạn hoàn toàn an tâm trong suốt hành trình chăm sóc sức khỏe bằng Y học cổ truyền.</p>
        </div>

        <!-- Section 1: Thu thập thông tin -->
        <div class="policy-section">
            <h2 class="section-title">
                <span class="emoji">📝</span> 1. Dữ liệu cá nhân chúng tôi thu thập
            </h2>
            <p class="policy-text">
                Để phục vụ việc lưu trữ hồ sơ bệnh án số hóa, theo dõi quá trình trị liệu và cá nhân hóa việc bốc thuốc, hệ thống AmaTrung thu thập và xử lý các nhóm dữ liệu sau:
            </p>
            
            <div class="grid-cards">
                <!-- Email -->
                <div class="info-card email">
                    <div class="card-header-group">
                        <div class="card-emoji">📧</div>
                        <h3 class="card-title">Tài khoản & Email</h3>
                    </div>
                    <p class="card-desc">Sử dụng làm định danh duy nhất để đăng nhập hệ thống, gửi mã OTP khôi phục mật khẩu bảo mật và nhận thông báo quan trọng từ phòng khám.</p>
                </div>
                <!-- Phone -->
                <div class="info-card phone">
                    <div class="card-header-group">
                        <div class="card-emoji">📱</div>
                        <h3 class="card-title">Số điện thoại</h3>
                    </div>
                    <p class="card-desc">Phương tiện liên lạc chính giúp bác sĩ liên kết dữ liệu bệnh án cũ từ hồ sơ giấy, hỗ trợ liên lạc tư vấn sắc thuốc và nhắc lịch hẹn châm cứu.</p>
                </div>
                <!-- Bio -->
                <div class="info-card bio">
                    <div class="card-header-group">
                        <div class="card-emoji">👤</div>
                        <h3 class="card-title">Họ tên & Nhân trắc học</h3>
                    </div>
                    <p class="card-desc">Gồm Họ tên, Ngày sinh, Giới tính, Chiều cao và Cân nặng nhằm tính toán chính xác chỉ số BMI để đánh giá thể trạng toàn diện của người bệnh.</p>
                </div>
                <!-- Medical records -->
                <div class="info-card medical">
                    <div class="card-header-group">
                        <div class="card-emoji">🩺</div>
                        <h3 class="card-title">Hồ sơ bệnh học</h3>
                    </div>
                    <p class="card-desc">Ghi nhận triệu chứng lâm sàng (vọng, văn, vấn, thiết), kết quả chẩn đoán mạch lý, lịch sử các bài thuốc đã kê và tiến trình châm cứu, bấm huyệt.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Phương thức thu thập -->
        <div class="policy-section">
            <h2 class="section-title">
                <span class="emoji">📥</span> 2. Phương thức chúng tôi thu thập dữ liệu
            </h2>
            <p class="policy-text">
                Thông tin của bạn được ghi nhận vào hệ thống thông qua các hình thức minh bạch sau:
            </p>
            <p class="policy-text-sub"><strong>Khai báo chủ động:</strong> Bạn cung cấp trực tiếp khi đăng ký tài khoản, điền thông tin cá nhân trong trang cấu hình hồ sơ cá nhân.</p>
            <p class="policy-text-sub"><strong>Nhập liệu chuyên môn:</strong> Bác sĩ hoặc nhân viên phòng khám điền thông tin mạch chẩn, chẩn đoán triệu chứng và đơn thuốc trong quá trình thăm khám trực tiếp.</p>
            <p class="policy-text-sub"><strong>Số hóa hồ sơ cũ:</strong> Nhân viên phòng khám nhập liệu từ các sổ khám bệnh, hồ sơ bệnh án giấy lịch sử của bạn để đồng bộ lên hệ thống số.</p>
            <p class="policy-text-sub"><strong>Dữ liệu tự động:</strong> Hệ thống tự động ghi nhận nhật ký thao tác (log) khi có sự thay đổi trạng thái đơn thuốc hoặc thao tác trên tài khoản để bảo vệ an toàn thông tin.</p>
        </div>

        <!-- Section 3: Mục đích sử dụng -->
        <div class="policy-section">
            <h2 class="section-title">
                <span class="emoji">🎯</span> 3. Mục đích sử dụng dữ liệu của bạn
            </h2>
            <p class="policy-text">
                Chúng tôi cam kết chỉ sử dụng dữ liệu của bạn cho các mục đích phục vụ y tế và nâng cao chất lượng dịch vụ tại AmaTrung, tuyệt đối không dùng cho hoạt động thương mại khác:
            </p>
            
            <div class="list-group">
                <div class="list-item">
                    <div class="list-num">1</div>
                    <div class="list-content">
                        <h4>Xây dựng phác đồ điều trị và đơn thuốc cá nhân hóa</h4>
                        <p>Dựa trên triệu chứng, mạch lý và thể trạng (BMI), bác sĩ sẽ gia giảm liều lượng các vị thuốc nam/thuốc bắc trong bài thuốc mẫu để tạo ra đơn thuốc tối ưu và an toàn nhất cho cơ địa của riêng bạn.</p>
                    </div>
                </div>
                <div class="list-item">
                    <div class="list-num">2</div>
                    <div class="list-content">
                        <h4>Quản lý và vận hành quy trình bốc thuốc, sắc thuốc</h4>
                        <p>Hồ sơ đơn thuốc được gửi trực tiếp đến bộ phận kho dược để cân thảo dược chính xác và ghi nhận trạng thái sắc thuốc, đóng gói sản phẩm rồi thông báo tới bạn khi hoàn thành.</p>
                    </div>
                </div>
                <div class="list-item">
                    <div class="list-num">3</div>
                    <div class="list-content">
                        <h4>Gợi ý kiêng cữ và chế độ sinh hoạt bằng Trợ lý AI</h4>
                        <p>Hệ thống sử dụng các chỉ số sinh học cơ bản để Trợ lý AI hỗ trợ đề xuất danh mục các món ăn nên dùng hoặc cần kiêng cữ tương ứng với tình trạng bệnh (ví dụ: hạn chế đồ cay nóng cho người nhiệt độc).</p>
                    </div>
                </div>
                <div class="list-item">
                    <div class="list-num">4</div>
                    <div class="list-content">
                        <h4>Liên lạc khẩn cấp và chăm sóc khách hàng</h4>
                        <p>Số điện thoại được dùng để liên hệ đặt lịch khám, nhắc lịch trị liệu định kỳ hoặc thông báo khẩn cấp về việc điều chỉnh liều lượng dược liệu nếu có phát hiện lâm sàng mới.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Thời gian lưu trữ dữ liệu -->
        <div class="policy-section">
            <h2 class="section-title">
                <span class="emoji">⏳</span> 4. Thời gian lưu trữ thông tin bệnh án
            </h2>
            <p class="policy-text">
                Thời hạn lưu giữ thông tin được thiết lập nghiêm ngặt tuân thủ theo Luật Khám bệnh, chữa bệnh của Việt Nam và các tiêu chuẩn bảo mật hệ thống:
            </p>
            <div class="list-group">
                <div class="list-item">
                    <div class="list-num">⏳</div>
                    <div class="list-content">
                        <h4>Thông tin tài khoản cá nhân</h4>
                        <p>Lưu trữ hoạt động liên tục trong suốt quá trình bạn sử dụng dịch vụ. Tài khoản sẽ được ẩn hoặc xóa sau 5 năm nếu hoàn toàn không có hoạt động đăng nhập nào và không gắn liền với bệnh án hiện hành.</p>
                    </div>
                </div>
                <div class="list-item">
                    <div class="list-num">🏥</div>
                    <div class="list-content">
                        <h4>Hồ sơ bệnh án và đơn thuốc y tế</h4>
                        <p>Lưu trữ bắt buộc **tối thiểu 10 năm** đối với hồ sơ bệnh án điều trị ngoại trú theo quy định của Bộ Y Tế Việt Nam để phục vụ công tác tra cứu lịch sử bệnh học, chẩn trị lâu dài và phục hồi sức khỏe.</p>
                    </div>
                </div>
                <div class="list-item">
                    <div class="list-num">📝</div>
                    <div class="list-content">
                        <h4>Nhật ký hệ thống (System Logs)</h4>
                        <p>Lưu trữ tối thiểu 2 năm đối với các log đăng nhập, lịch sử thay đổi thông tin bệnh án và lịch sử thao tác của nhân viên y tế để phục vụ kiểm toán bảo mật và phòng chống xâm nhập trái phép.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Bảo mật tuyệt đối & Phân quyền -->
        <div class="policy-section">
            <h2 class="section-title">
                <span class="emoji">🛡️</span> 5. Cam kết bảo vệ dữ liệu & Cơ chế phân quyền
            </h2>
            <p class="policy-text">
                Chúng tôi áp dụng các tiêu chuẩn an ninh mạng nghiêm ngặt nhất để ngăn chặn việc rò rỉ hoặc truy cập trái phép vào hồ sơ sức khỏe của bạn:
            </p>
            
            <div class="list-group">
                <div class="list-item" style="background: #f0fdf4; border-color: #bbf7d0;">
                    <div class="list-num" style="background: var(--primary);">✓</div>
                    <div class="list-content">
                        <h4>Mã hóa dữ liệu & Mật khẩu một chiều</h4>
                        <p>Mật khẩu của bạn được mã hóa bằng thuật toán Bcrypt mạnh mẽ trước khi lưu vào cơ sở dữ liệu. Không ai, kể cả Admin hệ thống hay lập trình viên, có thể đọc được mật khẩu gốc của bạn.</p>
                    </div>
                </div>
                <div class="list-item" style="background: #f0fdf4; border-color: #bbf7d0;">
                    <div class="list-num" style="background: var(--primary);">✓</div>
                    <div class="list-content">
                        <h4>Phân quyền nghiêm ngặt trong nội bộ phòng khám</h4>
                        <p>Nhân viên y tế (Staff) chỉ được truy cập các thông tin nằm trong phạm vi nghiệp vụ được giao. Đặc biệt, tài khoản bệnh nhân (User) được bảo vệ tuyệt đối: Admin phòng khám chỉ có quyền xem danh sách người dùng để hỗ trợ kỹ thuật, hoàn toàn không có quyền can thiệp, chỉnh sửa hay đổi mật khẩu của bạn (chỉ được thực hiện Reset mật khẩu về chuỗi mặc định bảo mật khi được yêu cầu và hệ thống sẽ tự động ghi nhật ký).</p>
                    </div>
                </div>
                <div class="list-item" style="background: #f0fdf4; border-color: #bbf7d0;">
                    <div class="list-num" style="background: var(--primary);">✓</div>
                    <div class="list-content">
                        <h4>Cam kết "3 KHÔNG" về dữ liệu cá nhân</h4>
                        <p>Chúng tôi cam kết thực hiện đầy đủ các tiêu chuẩn bảo mật dữ liệu y tế: KHÔNG bán hay chia sẻ thông tin cho bên thứ ba vì mục đích thương mại; KHÔNG gửi tin nhắn rác hoặc quảng cáo làm phiền; KHÔNG lưu mật khẩu dạng văn bản rõ.</p>
                    </div>
                </div>
            </div>

            <div class="badge-container">
                <div class="badge-cute">🔒 Kết nối mã hóa SSL/HTTPS</div>
                <div class="badge-cute">🛡️ Tiêu chuẩn bảo mật HIPAA</div>
                <div class="badge-cute">🚫 Không chia sẻ thông tin bệnh lý</div>
                <div class="badge-cute">💻 Giám sát an toàn hệ thống 24/7</div>
            </div>
        </div>

        <!-- Section 6: Bảo mật khi tích hợp tư vấn AI -->
        <div class="policy-section">
            <h2 class="section-title">
                <span class="emoji">🤖</span> 6. Nguyên tắc bảo mật khi tương tác với Trợ lý AI
            </h2>
            <p class="policy-text">
                AmaTrung ứng dụng trí tuệ nhân tạo (AI) tiên tiến để hỗ trợ phân tích sức khỏe. Để đảm bảo quyền riêng tư tối đa, chúng tôi áp dụng cơ chế bảo mật nghiêm ngặt như sau:
            </p>
            <div class="list-group">
                <div class="list-item" style="background: #eff6ff; border-color: #bfdbfe;">
                    <div class="list-num" style="background: #3b82f6;">🛡️</div>
                    <div class="list-content">
                        <h4>Ẩn danh hóa dữ liệu triệt để (Anonymization)</h4>
                        <p>Trước khi gửi thông tin triệu chứng hay chỉ số thể chất sang API của mô hình ngôn ngữ lớn để nhận gợi ý điều trị, hệ thống AmaTrung sẽ tự động bóc tách và loại bỏ 100% dữ liệu định danh cá nhân của bạn như Họ tên, Số điện thoại, Email, Địa chỉ. AI chỉ nhận được các thông số sinh học thuần túy (chiều cao, cân nặng, nhịp mạch, mô tả triệu chứng vô danh).</p>
                    </div>
                </div>
                <div class="list-item" style="background: #eff6ff; border-color: #bfdbfe;">
                    <div class="list-num" style="background: #3b82f6;">🚫</div>
                    <div class="list-content">
                        <h4>Cam kết không dùng để huấn luyện công cộng</h4>
                        <p>Mọi dữ liệu truyền tải đều thông qua cổng kết nối bảo mật dành cho doanh nghiệp (Enterprise API). Nhà cung cấp dịch vụ AI cam kết không lưu trữ lâu dài và không sử dụng dữ liệu lâm sàng này để huấn luyện cho các mô hình AI công cộng khác.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 7: Cookies và LocalStorage -->
        <div class="policy-section">
            <h2 class="section-title">
                <span class="emoji">🍪</span> 7. Sử dụng Cookies và Công nghệ lưu trữ
            </h2>
            <p class="policy-text">
                Website AmaTrung sử dụng Cookies kỹ thuật nhỏ và bộ nhớ LocalStorage của trình duyệt để tối ưu hóa trải nghiệm người dùng:
            </p>
            <p class="policy-text-sub"><strong>Duy trì phiên làm việc:</strong> Lưu giữ khóa bảo mật phiên đăng nhập tạm thời, giúp bạn không cần nhập lại mật khẩu liên tục khi chuyển hướng trang.</p>
            <p class="policy-text-sub"><strong>Bảo mật CSRF:</strong> Sử dụng Token chống giả mạo yêu cầu chéo trang để bảo vệ biểu mẫu đăng ký, đăng nhập trước các cuộc tấn công của hacker.</p>
            <p class="policy-text-sub"><strong>Lưu trữ tùy chọn:</strong> Ghi nhớ danh sách các thảo dược bạn yêu thích từ Từ điển dược liệu để hiển thị nhanh mà không cần tải lại từ máy chủ.</p>
        </div>

        <!-- Section 8: Quyền lợi của bạn -->
        <div class="policy-section">
            <h2 class="section-title">
                <span class="emoji">🌟</span> 8. Quyền lợi của chủ thể dữ liệu
            </h2>
            <p class="policy-text">
                Đối với thông tin cá nhân của mình, bạn có toàn quyền quyết định theo các quy định bảo mật hiện hành:
            </p>
            <p class="policy-text-sub"><strong>Quyền truy cập & kiểm tra:</strong> Bạn có quyền tự đăng nhập vào hệ thống để tra cứu đầy đủ lịch sử khám bệnh, đơn thuốc và các chỉ số sức khỏe cá nhân bất kỳ lúc nào.</p>
            <p class="policy-text-sub"><strong>Quyền chỉnh sửa:</strong> Bạn có quyền tự cập nhật thông tin cá nhân như họ tên, số điện thoại hoặc email liên lạc tại trang Quản lý tài khoản.</p>
            <p class="policy-text-sub"><strong>Quyền yêu cầu xóa:</strong> Bạn có quyền yêu cầu phòng khám khóa tài khoản hoặc ẩn thông tin cá nhân không bắt buộc lưu trữ. (Các thông tin thuộc về bệnh án y khoa bắt buộc phải giữ lại đủ 10 năm theo luật khám chữa bệnh).</p>
        </div>

        <!-- Section 9: Nghĩa vụ của bạn -->
        <div class="policy-section">
            <h2 class="section-title">
                <span class="emoji">🤝</span> 9. Quyền và Trách nhiệm từ phía người dùng
            </h2>
            <p class="policy-text">
                Hệ thống bảo mật chỉ hoạt động hiệu quả nhất khi có sự phối hợp từ phía bạn. Người dùng cam kết thực hiện các điều khoản sau:
            </p>
            <p class="policy-text-sub"><strong>Cung cấp thông tin chính xác:</strong> Việc cung cấp đúng họ tên và số điện thoại giúp bác sĩ kết nối chính xác hồ sơ sức khỏe cũ, tránh nhầm lẫn thông tin y khoa giữa các người bệnh.</p>
            <p class="policy-text-sub"><strong>Tự bảo mật tài khoản:</strong> Bạn có trách nhiệm bảo mật mật khẩu của mình, không chia sẻ thông tin đăng nhập cho người khác. Nhân viên phòng khám AmaTrung cam kết không bao giờ yêu cầu bạn cung cấp mật khẩu dưới bất kỳ hình thức nào.</p>
            <p class="policy-text-sub"><strong>Thông báo sự cố bảo mật:</strong> Nếu phát hiện bất kỳ dấu hiệu truy cập trái phép nào vào tài khoản của mình, xin hãy đổi mật khẩu ngay lập tức và báo cho chúng tôi để hỗ trợ kịp thời.</p>
        </div>

        <!-- Section 10: Liên hệ -->
        <div class="policy-section">
            <h2 class="section-title">
                <span class="emoji">📞</span> 10. Thông tin liên hệ và xử lý khiếu nại
            </h2>
            <p class="policy-text">
                Nếu bạn có bất kỳ câu hỏi nào về chính sách này, hoặc muốn thực hiện các quyền đối với dữ liệu cá nhân của mình, xin vui lòng liên hệ Ban quản trị an toàn thông tin AmaTrung:
            </p>
            <div class="badge-container" style="margin-top: 0.5rem; display: flex; flex-direction: column; gap: 0.75rem; align-items: flex-start;">
                <div class="badge-cute" style="border-radius: 12px; padding: 0.6rem 1.25rem;">📞 Đường dây nóng: 0912 345 678 (Hỗ trợ từ 8:00 - 17:30 hằng ngày)</div>
                <div class="badge-cute" style="border-radius: 12px; padding: 0.6rem 1.25rem;">📧 Hộp thư bảo mật: baomat@amatrung.vn</div>
                <div class="badge-cute" style="border-radius: 12px; padding: 0.6rem 1.25rem;">🏢 Địa chỉ phòng khám: Số 123 Đường Hoa Hồng, Phường 2, TP. Đà Lạt, Tỉnh Lâm Đồng</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="policy-footer">
            <a href="javascript:window.close();" class="btn-back" id="closeBtn">Tôi đã hiểu & Đồng ý</a>
            <p class="brand-note">AmaTrung — Y Học Cổ Truyền Kỹ Thuật Số 🌿</p>
        </div>
    </div>

    <script>
        document.getElementById('closeBtn').addEventListener('click', function(e) {
            e.preventDefault();
            if (window.opener || window.history.length > 1) {
                if (window.opener) {
                    window.close();
                } else {
                    window.history.back();
                }
            } else {
                window.location.href = "{{ route('register') }}";
            }
        });
    </script>
</body>
</html>
