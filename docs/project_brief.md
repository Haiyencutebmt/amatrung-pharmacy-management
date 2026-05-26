# AmaTrung - Project Brief

Đề tài: Xây dựng website quản lý nhà thuốc y học cổ truyền AmaTrung.

Mô hình thực tế:
- Nhà thuốc gia đình.
- Một thầy thuốc khám, kê đơn, bốc thuốc.
- Hiện quản lý bằng giấy.
- Có nhiều bệnh nhân tái khám lâu dài.
- Cần quản lý dược liệu, chế phẩm thuốc, số lượng tồn và hạn dùng.

Mục tiêu hệ thống:
- Số hóa quản lý bệnh nhân.
- Quản lý hồ sơ bệnh án theo từng lần khám.
- Quản lý đơn thuốc và chi tiết vị thuốc.
- Quản lý kho dược liệu và chế phẩm thuốc.
- Có bài viết kiến thức và bình luận.
- Có AI hỗ trợ gợi ý đơn thuốc ở mức tham khảo.
- Giao diện đơn giản, chữ to, dễ nhìn, phù hợp người lớn tuổi.

Công nghệ:
- Laravel
- MySQL
- XAMPP
- Blade
- Bootstrap hoặc Tailwind CSS
- Postman để test API
- AI API có thể tích hợp sau qua service riêng

Vai trò người dùng:
- Admin: toàn quyền.
- Staff: vào khu vực quản trị theo quyền được cấp.
- User: xem bài viết, bình luận, xem thông tin cá nhân/hồ sơ liên kết.

Lưu ý nghiệp vụ:
- Mỗi bệnh nhân có mã bệnh nhân riêng.
- Số điện thoại không phải là khóa duy nhất.
- Một số điện thoại có thể dùng chung cho nhiều bệnh nhân, ví dụ trẻ em/người già dùng số người thân.
- Khi tìm theo số điện thoại, hệ thống phải hiển thị danh sách hồ sơ liên quan.
- Kho thuốc gồm dược liệu rời và chế phẩm thuốc như thuốc lọ, thuốc tắm, rượu thuốc uống, rượu xoa bóp.
- Rượu xoa bóp phải có cảnh báo: chỉ dùng ngoài da, không được uống.
- Không làm hóa đơn, doanh thu, thanh toán trong phiên bản khóa luận chính.