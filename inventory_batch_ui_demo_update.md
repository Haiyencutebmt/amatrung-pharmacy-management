# Báo cáo Cập nhật Giao diện Kết nối Kho Mới

Tài liệu này tổng hợp các thay đổi về giao diện hiển thị, cấu trúc dữ liệu và kết quả kiểm thử sau khi hoàn thành kết nối hệ thống kho mới dựa trên các lô hàng (`inventory_batches`) thay thế cho kho legacy.

## 1. Danh sách File và Route đã Chỉnh Sửa

- **Sidebar Layout**:
  - [admin.blade.php](file:///C:/xampp/htdocs/amatrung/resources/views/layouts/admin.blade.php) - Đã thay đổi liên kết của mục "Quản lý kho" từ `admin.warehouse.index` sang route mới `admin.inventory.index` (URL: `/admin/inventory`).
- **Backend Model**:
  - [InventoryItem.php](file:///C:/xampp/htdocs/amatrung/app/Models/InventoryItem.php) - Bổ sung các accessor tính toán động bao gồm:
    - `available_batches` (lọc các lô khả dụng, còn hạn dùng hoặc chưa rõ hạn, số lượng tồn > 0 theo đúng logic trừ kho FEFO của `InventoryService`).
    - `fefo_batch` (lấy lô hàng ưu tiên cấp thuốc đầu tiên theo thứ tự FEFO, tie-breaker bằng ID).
    - `total_available_quantity` (tổng tồn của các lô khả dụng).
- **Backend Controller**:
  - [InventoryController.php](file:///C:/xampp/htdocs/amatrung/app/Http/Controllers/Admin/InventoryController.php) - Chuyển sang eager load toàn bộ danh sách `batches` để đếm tổng số lô chính xác (tránh N+1 query), đồng thời gán tồn khả dụng chuẩn hóa vào `total_quantity`.
- **View Danh sách Kho**:
  - [index.blade.php](file:///C:/xampp/htdocs/amatrung/resources/views/admin/inventory/index.blade.php) - Bổ sung đầy đủ 10 cột dữ liệu theo yêu cầu và thiết lập badge cảnh báo dùng ngoài da.
- **View Chi tiết Lô**:
  - [show.blade.php](file:///C:/xampp/htdocs/amatrung/resources/views/admin/inventory/show.blade.php) - Chuẩn hóa hiển thị trạng thái và cảnh báo của lô hàng theo 5 phân loại chuẩn.

---

## 2. URL Truy cập Giao diện Kho Mới

- **URL chính**: `http://127.0.0.1:8000/admin/inventory`
- **URL chi tiết lô**: `http://127.0.0.1:8000/admin/inventory/{id}` (Truy cập bằng cách bấm nút **"Xem lô"** từ trang danh sách).

---

## 3. Nguồn Dữ Liệu Sử Dụng

Toàn bộ thông tin hiển thị trên giao diện kho mới được truy xuất từ các bảng CSDL mới:
- Bảng `inventory_items` (lưu trữ tên, loại mặt hàng, đường dùng, đơn vị tính).
- Bảng `inventory_batches` (lưu trữ mã lô, số lượng tồn thực tế của từng lô, hạn sử dụng và trạng thái khóa/mở).
- Bảng `stock_movements` (lưu trữ lịch sử giao dịch nhập/xuất/điều chỉnh).

*Cam kết: Tuyệt đối không sử dụng cột legacy `medicinal_herbs.stock_quantity` hoặc `packaged_products.stock_quantity` làm nguồn tồn kho chính.*

---

## 4. Mô tả Các Cột Mới trên Bảng Danh Sách

Bảng danh sách kho mới gồm đầy đủ 10 cột thông tin:
1. **Tên mặt hàng**: Tên vị thuốc/chế phẩm uống/chế phẩm dùng ngoài.
2. **Loại**: Phân loại theo mục đích sử dụng (`Dược liệu uống`, `Chế phẩm uống`, `Chế phẩm dùng ngoài`).
3. **Đường dùng**: Hiển thị badge xanh `Uống` hoặc badge đỏ nổi bật `"Dùng ngoài da - Không được uống"` (đối với mặt hàng dùng ngoài da như Bó thuốc nam, Rượu xoa bóp, Thuốc bó xương khớp).
4. **Đơn vị**: Đơn vị tính (VD: g, kg, gói, lọ...).
5. **Tồn khả dụng**: Tổng số lượng còn lại của tất cả các lô hàng đủ điều kiện xuất kho (khả dụng & còn hạn).
6. **Số lô**: Tổng số lô hàng hiện tại của mặt hàng (bao gồm cả lô hết hạn, chưa rõ hạn và bị khóa).
7. **Lô FEFO ưu tiên**: Hiển thị mã của lô hàng khả dụng có hạn dùng gần nhất (hoặc "Chưa có lô khả dụng" nếu không có lô nào đạt yêu cầu).
8. **HSD gần nhất**: Hạn dùng tương ứng của Lô FEFO ưu tiên.
9. **Trạng thái**: Trạng thái cảnh báo tổng quát của mặt hàng (`Khả dụng`, `Sắp hết hạn`, `Có lô hết hạn`, `Chưa rõ hạn dùng`).
10. **Thao tác**: Nút **"Xem lô"** dẫn sang trang chi tiết các lô hàng và lịch sử biến động.

---

## 5. Phân Loại Trạng Thái/Cảnh Báo Lô (Trang Chi Tiết)

Tại trang xem chi tiết, trạng thái/cảnh báo của từng lô hàng được hiển thị theo đúng thứ tự phân loại:
- **Bị khóa**: Lô hàng có trạng thái `blocked` (không cho phép cấp phát).
- **Chưa rõ hạn dùng**: Lô hàng có trạng thái `unknown_expiry` hoặc không có `expiry_date`.
- **Đã hết hạn**: Lô hàng có trạng thái `expired` hoặc có ngày hết hạn trước ngày hiện tại.
- **Sắp hết hạn**: Lô hàng khả dụng còn hạn dùng dưới hoặc bằng 30 ngày.
- **Còn sử dụng**: Lô hàng khả dụng có hạn sử dụng trên 30 ngày.

---

## 6. Kết quả Kiểm thử Tự động (Test Suites)

Tất cả các test case tích hợp và bảo mật liên quan đều vượt qua **100% OK**:

1. `php artisan test --filter=Phase3CIntegrationTest` -> **36/36 tests Passed**
2. `php artisan test --filter=Phase2FefoTest` -> **12/12 tests Passed**
3. `php artisan test --filter=Phase4AiSafetyTest` -> **33/33 tests Passed**
4. `php artisan test --filter=HerbDictionaryTest` -> **9/9 tests Passed**

---

## 7. Xác nhận Phạm vi Ranh giới An toàn

Chúng tôi xác nhận **KHÔNG** thực hiện bất kỳ chỉnh sửa nào đối với các phần sau nhằm bảo vệ an toàn nghiệp vụ:
- Không chỉnh sửa phương thức `MedicinalHerbController::importExcel()`.
- Không can thiệp vào logic gợi ý AI hay làm lệch payload gửi sang AI.
- Không chỉnh sửa hay tác động tới Phiếu xuất kho ngoài đơn (`retail-orders`).
- Không xóa hay thay đổi bất kỳ thành phần legacy nào của module `warehouse` cũ.
- Không chạy hay thêm file migrations mới.
