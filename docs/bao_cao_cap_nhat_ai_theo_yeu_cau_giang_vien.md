# Báo cáo cập nhật AI theo yêu cầu giảng viên

## 1. Phạm vi thực hiện

Đã tách AI phía quản trị thành 2 luồng riêng:

- AI hỗ trợ nhận định sơ bộ tại trang chi tiết bệnh án.
- AI hỗ trợ gợi ý điều trị sau khi bệnh án đã có chẩn đoán chính thức.

Chatbot AI phía người dùng vẫn giữ route và logic public hiện tại, không trộn với AI nội bộ.

## 2. File đã sửa / thêm

- `app/Models/MedicalRecord.php`
- `app/Http/Controllers/Admin/MedicalRecordController.php`
- `app/Http/Controllers/Admin/AiSuggestionController.php`
- `app/Services/AiClinicalSuggestionService.php`
- `app/Services/AiPreliminaryAssessmentService.php`
- `routes/web.php`
- `resources/views/admin/records/show.blade.php`
- `resources/views/admin/records/create.blade.php`
- `resources/views/admin/records/index.blade.php`
- `resources/views/admin/patients/show.blade.php`
- `resources/views/admin/records/partials/ai_panel.blade.php`
- `resources/views/admin/records/partials/ai_js.blade.php`
- `resources/views/admin/records/partials/ai_preliminary_panel.blade.php`
- `resources/views/admin/records/partials/ai_preliminary_js.blade.php`
- `docs/bao_cao_cap_nhat_ai_theo_yeu_cau_giang_vien.md`

## 3. Migration / database

Không tạo migration mới.

Không chạy migration.

Không thay đổi cấu trúc database.

Nhật ký AI vẫn dùng bảng `ai_suggestion_logs` hiện có. Để phân biệt loại AI, hệ thống lưu thêm khóa `ai_flow` trong payload:

- `preliminary_assessment`: AI nhận định sơ bộ.
- `treatment_suggestion`: AI gợi ý điều trị.

## 4. Route/API mới hoặc đã sửa

Route nội bộ mới:

- `POST /admin/api/ai-preliminary-assessment`
  - Tên route: `admin.ai.preliminary-assessment`
  - Controller: `Admin\AiSuggestionController@preliminaryAssessment`

- `POST /admin/api/ai-preliminary-assessment/apply-diagnosis`
  - Tên route: `admin.ai.preliminary-assessment.apply-diagnosis`
  - Controller: `Admin\AiSuggestionController@applyDiagnosis`

Route AI điều trị cũ vẫn giữ:

- `POST /admin/api/ai-suggest`
- `POST /admin/api/ai-suggest/log-status`

Route chatbot user vẫn giữ:

- `POST /api/chatbot/chat`

## 5. Controller / service / view đã cập nhật

`MedicalRecordController`

- Cho phép tạo/cập nhật bệnh án khi chưa nhập chẩn đoán.
- Nếu chẩn đoán để trống, hệ thống lưu giá trị mặc định: `Chưa chẩn đoán`.

`MedicalRecord`

- Thêm hằng `PENDING_DIAGNOSIS`.
- Thêm helper `hasConfirmedDiagnosis()` để xác định bệnh án đã có chẩn đoán chính thức hay chưa.
- Thêm helper `displayDiagnosis()`.

`AiPreliminaryAssessmentService`

- Service mới cho AI nhận định sơ bộ.
- Chỉ nhận dữ liệu bệnh án đã được xử lý qua `AiClinicalContextBuilder`.
- Không gửi danh sách tồn kho.
- Không dùng chẩn đoán làm đầu vào.
- Prompt bắt buộc AI không chẩn đoán chắc chắn, không kê đơn, không tự tạo đơn, không trừ kho.
- Có retry ngắn khi Gemini quá tải. Nếu Gemini trả 429/503/504, hệ thống hiển thị nhận định dự phòng an toàn dựa trên từ khóa triệu chứng để không làm gián đoạn demo; payload log ghi `ai_provider = local_fallback`.

`AiClinicalSuggestionService`

- Luồng gợi ý điều trị chỉ chạy khi bệnh án đã có chẩn đoán chính thức.
- Nếu bệnh án vẫn là `Chưa chẩn đoán`, service trả về trạng thái `diagnosis_required`.
- Prompt đổi sang dùng "Chẩn đoán đã được thầy thuốc xác nhận".

`records/show.blade.php`

- Hiển thị rõ trạng thái bệnh án chưa có chẩn đoán chính thức.
- Thêm khối "AI hỗ trợ nhận định sơ bộ".
- Giữ khối "AI hỗ trợ gợi ý điều trị", nhưng chỉ bật khi đã có chẩn đoán chính thức.

## 6. Quyền truy cập AI sau khi sửa

Cả 2 luồng AI nội bộ đều nằm trong middleware:

- `auth`
- `staff`
- `permission:use_ai_suggestion`

Riêng thao tác "Áp dụng chẩn đoán" cần thêm:

- `permission:medical_records.edit`

Luồng kiểm tra bản ghi:

- Admin / thầy thuốc chính được dùng AI.
- Staff chỉ dùng được khi có quyền `use_ai_suggestion` và được phân công vào bệnh án.
- Staff không có quyền AI sẽ bị chặn bởi middleware.
- User public không truy cập được API AI nội bộ.

## 7. Cách hoạt động của AI nhận định sơ bộ

Luồng mới:

1. Thầy thuốc tạo bệnh án chỉ cần nhập triệu chứng và thông tin khám ban đầu.
2. Nếu chưa nhập chẩn đoán, bệnh án được lưu là `Chưa chẩn đoán`.
3. Tại trang chi tiết bệnh án, thầy thuốc bấm "Phân tích sơ bộ".
4. AI trả về:
   - Tóm tắt nhận định.
   - Danh sách hướng nhận định tham khảo.
   - Mức độ phù hợp tham khảo theo phần trăm.
   - Lý do gợi ý.
   - Cảnh báo dấu hiệu nguy hiểm nếu có.
   - Lời dặn tham khảo.
5. AI không tự cập nhật bệnh án.
6. Chỉ khi thầy thuốc bấm "Áp dụng chẩn đoán", hệ thống mới cập nhật trường chẩn đoán.
7. Lời dặn có nút sao chép để thầy thuốc tự chỉnh lại.

Giao diện có cảnh báo:

> Kết quả AI chỉ mang tính hỗ trợ tham khảo. Quyết định chẩn đoán và điều trị thuộc về thầy thuốc.

## 8. Cách hoạt động của AI gợi ý điều trị

Luồng điều trị chỉ dùng sau khi bệnh án có chẩn đoán chính thức:

1. Service kiểm tra `MedicalRecord::hasConfirmedDiagnosis()`.
2. Nếu chưa có chẩn đoán chính thức, AI không chạy và giao diện báo cần chẩn đoán trước.
3. Nếu đã có chẩn đoán, AI dùng triệu chứng, chẩn đoán đã xác nhận, thông tin khám và dữ liệu khả dụng theo logic hiện có.
4. AI trả về nhận xét tham khảo, gợi ý dược liệu/dịch vụ, gợi ý theo dõi.
5. AI không tự đưa item vào đơn, không tự tạo đơn, không tự cấp thuốc, không trừ kho.

## 9. Kiểm tra luồng kho

Đã rà trong code AI mới:

- `AiPreliminaryAssessmentService` không gọi `InventoryService`.
- `AiPreliminaryAssessmentService` không gọi `PrescriptionService`.
- `AiSuggestionController` không gọi dispense/cấp thuốc.
- JavaScript của AI nhận định sơ bộ chỉ gọi route phân tích và route áp dụng chẩn đoán.

Luồng kho cũ vẫn giữ:

- Lưu đơn điều trị trước.
- Chỉ khi cấp thuốc/dispense mới trừ kho.
- Dịch vụ trị liệu không trừ kho.

## 10. Kết quả kiểm thử

Đã chạy:

- `php -l app\Services\AiPreliminaryAssessmentService.php`: pass.
- `php -l app\Http\Controllers\Admin\AiSuggestionController.php`: pass.
- `php -l app\Services\AiClinicalSuggestionService.php`: pass.
- `php -l app\Http\Controllers\Admin\MedicalRecordController.php`: pass.
- `php artisan route:list --name=ai`: thấy đủ 4 route AI nội bộ.
- Rà `routes/web.php`: chatbot user vẫn là `/api/chatbot/chat`.
- Rà code AI nhận định sơ bộ: không có tham chiếu dispense/kho/stock movement.

Chưa chạy feature test có tác động database vì yêu cầu không chạy migration và không thay đổi database. Các test Laravel trong dự án có khả năng dùng refresh database/migration test, nên cần chạy riêng khi cậu cho phép dùng database test.

Checklist nghiệp vụ cần kiểm tra thủ công trên giao diện:

- Tạo bệnh án chỉ nhập triệu chứng, chẩn đoán để trống.
- Vào chi tiết bệnh án thấy trạng thái "Chưa có chẩn đoán chính thức".
- Bấm AI nhận định sơ bộ và thấy nhiều hướng nhận định tham khảo.
- Không bấm áp dụng thì chẩn đoán không đổi.
- Bấm "Áp dụng chẩn đoán" thì bệnh án cập nhật đúng.
- AI gợi ý điều trị chỉ bật sau khi đã có chẩn đoán chính thức.
- Tạo đơn/cấp thuốc vẫn phải thao tác thủ công.
- User chatbot vẫn hoạt động public và không truy cập bệnh án.
- Staff không có `use_ai_suggestion` không dùng được AI nội bộ.
- Staff có quyền và được phân công thì dùng được trong phạm vi bệnh án của mình.

## 11. Màn hình nên chụp cho khóa luận

- Form tạo bệnh án mới với chẩn đoán có thể để trống.
- Trang chi tiết bệnh án khi đang "Chưa có chẩn đoán chính thức".
- Khối "AI hỗ trợ nhận định sơ bộ".
- Kết quả AI nhận định sơ bộ có phần trăm mức độ phù hợp tham khảo.
- Nút "Áp dụng chẩn đoán" và "Sao chép lời dặn".
- Trang chi tiết sau khi áp dụng chẩn đoán.
- Khối "AI hỗ trợ gợi ý điều trị" sau khi có chẩn đoán.
- Trang kê đơn để chứng minh AI không tự thêm thuốc vào đơn.
- Chatbot public phía user để chứng minh tách biệt với AI nội bộ.

## 12. Điểm cần cập nhật trong Chương 2 và sơ đồ

- Sơ đồ chức năng nên tách rõ:
  - Chatbot AI phía người dùng.
  - AI nhận định sơ bộ phía thầy thuốc.
  - AI gợi ý điều trị phía thầy thuốc.

- Sơ đồ luồng dữ liệu cần cập nhật:
  - Bệnh án có thể được tạo khi chưa có chẩn đoán chính thức.
  - AI nhận định sơ bộ trả kết quả tham khảo, không tự lưu.
  - Thầy thuốc xác nhận mới cập nhật chẩn đoán.
  - AI gợi ý điều trị chỉ chạy sau chẩn đoán đã xác nhận.
  - Lập đơn và cấp thuốc vẫn là luồng thủ công.

- ERD không cần đổi nếu chỉ mô tả theo database hiện tại.
  - `ai_suggestion_logs.payload` có thể ghi chú thêm `ai_flow`.

- Phần phân quyền cần ghi:
  - Admin/thầy thuốc chính có quyền AI.
  - Staff không mặc định có quyền chuyên môn AI.
  - Staff phải được cấp `use_ai_suggestion`.

## 13. Xác nhận phạm vi không thay đổi

- Không sửa chatbot AI phía User.
- Không làm AI tự động kê đơn.
- Không làm AI tự động trừ kho.
- Không sửa logic dispense.
- Không sửa import Excel/CSV.
- Không xóa chức năng đang hoạt động.
- Không tạo migration.
- Không thay đổi database.
