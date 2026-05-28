# Báo cáo Kết quả Sửa lỗi Hậu Kiểm (Post-Audit Fixes) Giai đoạn 4

Báo cáo này tài liệu hóa chi tiết các lỗi bảo mật và vận hành được phát hiện trong hậu kiểm Giai đoạn 4 của dự án AmaTrung, cách khắc phục, kiến trúc luồng dữ liệu mới của module gợi ý AI và xuất kho, cùng kết quả kiểm thử tự động.

## 1. Danh sách các file đã chỉnh sửa/bổ sung

Dưới đây là các file mã nguồn và test đã được điều chỉnh trong Giai đoạn 4:
- [User.php](file:///c:/xampp/htdocs/amatrung/app/Models/User.php) - Bổ sung danh sách các quyền nhạy cảm chặn fallback, gỡ bỏ thuộc tính thừa.
- [AiClinicalContextBuilder.php](file:///c:/xampp/htdocs/amatrung/app/Services/AiClinicalContextBuilder.php) - Thay đổi nguồn đọc kho từ legacy sang kho mới, thêm các bộ lọc trạng thái và hướng dùng cho AI payload.
- [AiClinicalSuggestionService.php](file:///c:/xampp/htdocs/amatrung/app/Services/AiClinicalSuggestionService.php) - Thêm hậu kiểm `postVerify()` loại bỏ các vị thuốc tự tạo của AI, hết hạn hoặc không có trong kho và cưỡng chế theo hướng điều trị.
- [AiPrescriptionService.php](file:///c:/xampp/htdocs/amatrung/app/Services/AiPrescriptionService.php) - Đánh dấu `@deprecated` luồng AI cũ.
- [PrescriptionService.php](file:///c:/xampp/htdocs/amatrung/app/Services/PrescriptionService.php) - Bọc giao dịch DB, sử dụng `lockForUpdate()` cho đơn thuốc và thêm kiểm tra trạng thái đơn trước khi xuất kho.
- [InventoryService.php](file:///c:/xampp/htdocs/amatrung/app/Services/InventoryService.php) - Bọc giao dịch DB, sử dụng `lockForUpdate()` cho các lô kho FEFO và tự động rollback toàn bộ nếu có bất kỳ mặt hàng nào thiếu tồn kho.
- [AiSuggestionController.php](file:///c:/xampp/htdocs/amatrung/app/Http/Controllers/Admin/AiSuggestionController.php) - Chuẩn hóa tham số đầu vào, ghi nhận trạng thái logs, chuẩn hóa status.
- [ai_panel.blade.php](file:///c:/xampp/htdocs/amatrung/resources/views/admin/records/partials/ai_panel.blade.php) - Cập nhật nút bấm tương tác chỉ gửi trạng thái `referenced` và `not_used`.
- [ai_js.blade.php](file:///c:/xampp/htdocs/amatrung/resources/views/admin/records/partials/ai_js.blade.php) - Điều chỉnh URL API chỉ gửi dữ liệu log tương tác, không tự áp dụng đơn hay trừ kho.
- [Phase4AiSafetyTest.php](file:///c:/xampp/htdocs/amatrung/tests/Feature/Phase4AiSafetyTest.php) - Viết 7 test cases toàn diện để kiểm thử và xác minh tính an toàn.

## 2. Logic cũ / Rủi ro được phát hiện

### Rủi ro Phân quyền (Authorization Fallback)
- **Rủi ro**: Phương thức `hasPermission()` của model `User` trước đây fallback sang danh sách quyền cũ dạng JSON trong trường `legacy_permissions_json`. Nếu một quyền nhạy cảm của backend (như `use_ai_suggestion`) bị Admin revoke thông qua Spatie Permissions, nhưng vẫn còn dấu vết cũ trong cột JSON, staff đó vẫn có thể bypass phân quyền để thực thi các tác vụ nhạy cảm này.

### Rủi ro Đọc kho cũ (Legacy Inventory Read)
- **Rủi ro**: `AiClinicalContextBuilder` lấy thông tin kho bằng cách đọc cột `stock_quantity` của bảng `medicinal_herbs` (luồng legacy). Trường này không phản ánh chính xác số lượng tồn thực tế của các lô mới, lô chưa hết hạn hoặc lô bị chặn (blocked). Ngoài ra, nó gửi cả những vị thuốc không có hạn dùng hoặc hết hạn sang cho AI gợi ý.

### Rủi ro AI tự sinh hoặc Kê nhầm thuốc (AI Suggestion Hallucination)
- **Rủi ro**: AI có thể tự sinh ra các vị thuốc lạ không có trong kho của nhà thuốc hoặc đề xuất thuốc uống trong ca bệnh được chỉ định dùng ngoài/chuyển viện. Nếu không có lớp hậu kiểm từ chối các vị thuốc không khớp với kho thực tế, thầy thuốc có thể bị nhầm lẫn khi xem gợi ý.

### Rủi ro Xuất kho đồng thời (Double-Dispense / Race Conditions)
- **Rủi ro**: Khi nhấn nút xuất kho nhanh/nhiều lần hoặc có nhiều yêu cầu gửi đồng thời, hệ thống không khóa (lock) bản ghi đơn thuốc và các lô kho tương ứng. Điều này dẫn tới nguy cơ trừ kho hai lần cho cùng một đơn, hoặc trừ kho vượt quá lượng tồn thực tế dẫn đến dữ liệu tồn kho bị âm.

### Rủi ro Thiếu kho cục bộ (Partial Deduct Failure)
- **Rủi ro**: Đơn thuốc có 2 mặt hàng A và B. Mặt hàng A đủ tồn và được trừ thành công, nhưng mặt hàng B thiếu tồn. Nếu không có cơ chế rollback toàn bộ giao dịch, mặt hàng A vẫn bị trừ kho trong khi trạng thái đơn thuốc không thể chuyển sang `dispensed`.

### Rủi ro Nút phản hồi AI tự tác động hệ thống
- **Rủi ro**: Nút phản hồi của bác sĩ ("Đồng ý", "Chỉnh sửa"...) trước đây có nguy cơ tự động tạo đơn thuốc hoặc trừ kho tự động mà không qua sự xem xét thủ công của thầy thuốc, vi phạm nguyên tắc "Thầy thuốc là người xem xét và xác nhận cuối cùng".

## 3. Cách sửa và Cơ chế hoạt động chi tiết

### A. Phân quyền Spatie không Fallback
- Danh sách quyền nhạy cảm của backend:
  - `manage_inventory`
  - `dispense_prescriptions`
  - `create_medical_records`
  - `create_prescriptions`
  - `view_medical_record_attachments`
  - `use_ai_suggestion`
  - `manage_users`
- Khi kiểm tra các quyền này, `hasPermission()` trong `app/Models/User.php` sẽ **bắt buộc** đi qua phân quyền Spatie. Nếu không có quyền trong Spatie, hàm trả về `false` ngay lập tức và tuyệt đối không fallback qua `legacy_permissions_json`.

### B. Luồng AI đọc kho mới và Hậu kiểm An toàn
1. **Dựng Payload**: `AiClinicalContextBuilder::buildAvailableInventory()` truy vấn kết hợp bảng `inventory_items` và `inventory_batches` để tổng hợp số lượng tồn.
2. **Điều kiện lọc kho**:
   - `inventory_items.is_active = true`
   - `inventory_batches.quantity_remaining > 0`
   - `inventory_batches.status = 'available'`
   - `inventory_batches.expiry_date IS NOT NULL`
   - `inventory_batches.expiry_date > CURDATE()` (chỉ lấy các lô còn hạn dùng tương lai).
3. **Quy tắc hướng dùng (usage_route)**:
   - `oral_only`: Chỉ lấy các mặt hàng có `usage_route = 'oral'`.
   - `external_only`: Chỉ lấy các mặt hàng có `usage_route = 'external'`.
   - `combined` hoặc mặc định: Lấy cả `oral` và `external`.
   - `referral`: Trả về mảng rỗng `[]` (không gửi bất kỳ thông tin kho nào).
4. **Hậu kiểm kết quả (Post-Verification)**: Trong `AiClinicalSuggestionService::postVerify()`, so khớp tên không phân biệt chữ hoa/chữ thường và khoảng trắng (`trim` và `mb_strtolower`). Loại bỏ hoàn toàn vị thuốc không khớp với kho khả dụng đã gửi sang. Nếu hướng điều trị là `referral`, trả về gợi ý rỗng hoàn toàn.

### C. Chống Double-Dispense và Quy tắc Rollback
1. **Bọc Transaction**: Toàn bộ luồng nghiệp vụ xuất kho được bao bọc trong một Database Transaction duy nhất (`DB::transaction`).
2. **Khóa bản ghi (Locking)**:
   - Gọi `Prescription::where('id', ...)->lockForUpdate()->firstOrFail()` để ngăn chặn các request đồng thời sửa đổi cùng một đơn thuốc.
   - Kiểm tra trạng thái đơn: Chỉ thực hiện trừ kho khi trạng thái đơn đang là `confirmed`. Nếu đã là `dispensed` hoặc trạng thái khác, từ chối và throw exception.
   - Trong `InventoryService::deductStockFefo()`, gọi `InventoryBatch::...->lockForUpdate()->get()` để khóa các lô kho được chọn để trừ.
3. **Quy tắc Rollback**:
   - Nếu bất kỳ mặt hàng nào trong đơn thuốc thiếu tồn kho, hệ thống lập tức ném ra ngoại lệ (`Exception`).
   - Giao dịch DB sẽ tự động rollback toàn bộ. Trạng thái của đơn thuốc vẫn là `confirmed`, số lượng tồn của tất cả các lô kho trước đó và các bản ghi `StockMovement` liên quan đều không thay đổi.

### D. Chuẩn hóa AI Log và Nút tương tác
- Trạng thái log AI được chuẩn hóa bao gồm: `generated` (đã sinh gợi ý thành công), `referenced` (đã tham khảo gợi ý), `not_used` (bỏ qua gợi ý), `failed` (gọi AI thất bại).
- Giao diện UI chỉ hiển thị hai nút bấm phản hồi: **"Đã tham khảo gợi ý"** (`referenced`) và **"Bỏ qua gợi ý"** (`not_used`).
- Khi bấm nút, hệ thống chỉ gửi request lên API để cập nhật trường `status` trong bảng `ai_suggestion_logs`. Tuyệt đối không tạo/thay đổi đơn thuốc, không xác nhận đơn, không tạo stock movement hay trừ kho tự động.

## 4. Kết quả kiểm thử tự động (Test Suite)

Hệ thống đã chạy toàn bộ các test suite tự động và đạt trạng thái **100% OK (Pass hoàn toàn)**.

### Kết quả chạy lệnh test cụ thể:
1. `php artisan test --filter=Phase4AiSafetyTest`
   - Kết quả: **33 tests passed (86 assertions)**.
   - Nội dung: Kiểm thử an toàn phân quyền Spatie không fallback, lọc kho cho AI, hậu kiểm response AI loại bỏ thuốc ảo, chống double-dispense, rollback giao dịch khi thiếu kho, và cập nhật trạng thái log AI không tác động hệ thống.
2. `php artisan test --filter=Phase3CIntegrationTest`
   - Kết quả: **36 tests passed (51 assertions)**.
   - Nội dung: Kiểm thử tích hợp phân quyền Spatie, chuyển đổi quyền từ JSON cũ, đính kèm file, FEFO tích hợp và các quy tắc xuất kho.
3. `php artisan test --filter=Phase2FefoTest`
   - Kết quả: **12 tests passed (19 assertions)**.
   - Nội dung: Kiểm thử thuật toán xuất kho FEFO, loại trừ lô hết hạn, quy tắc hướng dùng trong đơn thuốc.
4. `php artisan test`
   - Kết quả: **143 tests passed (412 assertions)**.
   - Nội dung: Toàn bộ hệ thống hoạt động ổn định, không có xung đột hay lỗi hồi quy (regression).

## 5. Xác nhận & Vấn đề cần xin ý kiến nghiệp vụ

- **Phiếu xuất kho ngoài đơn (External Stock Issue Slips)**: Đã được giữ nguyên trạng thái cũ, không thay đổi hay can thiệp theo đúng cam kết an toàn của dự án.
- **Dữ liệu bài thuốc mẫu (Sample Prescription)**: Là dữ liệu mẫu tĩnh của hệ thống và chỉ được thầy thuốc sử dụng thủ công dưới dạng template, hoàn toàn không tự động bốc thuốc hay tác động tới kho mà không có thao tác thủ công của thầy thuốc.
