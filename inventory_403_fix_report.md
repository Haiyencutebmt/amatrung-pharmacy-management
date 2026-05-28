# Inventory 403 Fix Report

Ngày kiểm tra: 2026-05-26

## 1. Nguyên nhân lỗi 403

- Route kho mới là `admin.inventory.index` tại `/admin/inventory`.
- Route nằm trong group middleware `auth` và `staff`.
- `InventoryController@index` gọi:

```php
$this->authorize('manage_inventory', InventoryItem::class);
```

- Sidebar lại hiển thị menu bằng helper:

```php
auth()->user()->hasPermission('manage_inventory')
```

- Trên MySQL thật `db_amatrung`, tài khoản demo:
  - `id = 1`
  - `name = Y Hiếu (AmaTrung)`
  - `email = admin@amatrung.vn`
  - `users.role = admin`
  - trước sửa: không có Spatie role, không có direct permission, `can('manage_inventory') = false`
  - helper legacy trả `hasPermission('manage_inventory') = true` vì role cột `users.role` là `admin`

=> Menu hiện ra nhưng Laravel Gate/Spatie không cho qua `authorize()`, nên `/admin/inventory` bị `403 | This action is unauthorized`.

## 2. Cập nhật quyền trên database thật

Có cập nhật quyền trên database thật.

Đã chạy reset permission cache trước, nhưng lỗi vẫn còn vì thiếu dữ liệu role trong bảng Spatie.

Đã đồng bộ tối thiểu:

- Gán Spatie role `admin` cho user `admin@amatrung.vn`.
- Không cấp role/quyền cho các tài khoản khác.
- Không chạy migration.
- Không chạy `migrate:fresh`.

Trạng thái sau sửa:

- `model_has_roles = 1`
- `Y Hiếu (AmaTrung)` có Spatie role `admin`
- `Y Hiếu (AmaTrung)` có `can('manage_inventory') = true`
- Staff `Nguyễn Thị Lan` không có `manage_inventory` vẫn bị chặn.
- Staff `Trần Văn Minh` có direct permission `manage_inventory` truy cập được.
- User thường vẫn bị chặn.

## 3. File đã sửa

- `app/Models/InventoryItem.php`
  - Cập nhật accessor `available_batches`, `fefo_batch`, `total_available_quantity` để batch thiếu hạn dùng không được tính vào tồn khả dụng hoặc FEFO.

- `app/Http/Controllers/Admin/InventoryController.php`
  - Cập nhật filter `available` để chỉ lấy batch có `status = available`, còn số lượng, có `expiry_date`, và chưa hết hạn.
  - Cập nhật trạng thái hiển thị để batch `expiry_date = null` được xem là `unknown_expiry`.

- `inventory_403_fix_report.md`
  - File báo cáo này.

## 4. Kết quả truy cập `/admin/inventory`

Sau sửa:

- `Y Hiếu (AmaTrung)` truy cập controller `InventoryController@index` thành công.
- Kết quả trả view: `admin.inventory.index`.
- Route `admin.inventory.index` vẫn giữ authorization `manage_inventory`; không bỏ `authorize()`, không mở quyền toàn bộ tài khoản.

## 5. Kiểm tra dữ liệu kho mới và FEFO

Trang kho mới đang dùng:

- `inventory_items`
- `inventory_batches`
- `stock_movements`

Không dùng `medicinal_herbs.stock_quantity` hoặc `packaged_products.stock_quantity` làm nguồn tồn chính.

Render kiểm tra các cột chính:

- Tên mặt hàng: có
- Loại: có
- Đường dùng: có
- Đơn vị: có
- Tồn khả dụng: có
- Số lô: có
- Lô FEFO ưu tiên: có
- HSD gần nhất: có
- Trạng thái: có
- Nút `Xem lô`: có

Kiểm tra item dùng ngoài:

- Filter `external_products` hiển thị badge `Dùng ngoài da - Không được uống`.
- Trang chi tiết item dùng ngoài hiển thị cảnh báo `CẢNH BÁO: DÙNG NGOÀI DA - KHÔNG ĐƯỢC UỐNG`.
- Nút `Xem lô` mở được view chi tiết lô qua `InventoryController@show`.

## 6. Kiểm tra `unknown_expiry`

Trên DB thật:

- `available_null_expiry = 0`
- `unknown_expiry = 2`

Hai batch `unknown_expiry` hiện có:

- `Trà lá nam 10 vị (1kg = 10 túi nhỏ)`: `total_available = 0`, `fefo_batch = null`, vẫn có 1 batch trong trang chi tiết.
- `Rượu thuốc xoa bóp AmaTrung`: `total_available = 0`, `fefo_batch = null`, vẫn có 1 batch trong trang chi tiết.

Quy tắc sau sửa trên UI kho:

- Batch `unknown_expiry` hoặc `expiry_date = null` vẫn hiển thị ở trang chi tiết lô để cập nhật hạn dùng.
- Không tính vào tồn khả dụng.
- Không làm lô FEFO ưu tiên.
- Không làm hạn dùng gần nhất.
- Filter `Còn sử dụng được` không lấy batch thiếu hạn dùng.

Không sửa `InventoryService` theo đúng phạm vi yêu cầu.

## 7. Kết quả test

Đã chạy:

```bash
php artisan permission:cache-reset
php artisan optimize:clear
php artisan permission:cache-reset
php artisan test --filter=Phase3CIntegrationTest
php artisan test --filter=Phase2FefoTest
php artisan test --filter=Phase4AiSafetyTest
```

Kết quả:

- `Phase3CIntegrationTest`: PASS, 36 passed, 51 assertions.
- `Phase2FefoTest`: PASS, 12 passed, 19 assertions.
- `Phase4AiSafetyTest`: PASS, 33 passed, 86 assertions.

Lưu ý: test chạy trên SQLite `:memory:` theo `phpunit.xml`; DB demo thật vẫn là MySQL `db_amatrung`.

## 8. Xác nhận phạm vi không sửa

Trong lượt sửa này:

- Không sửa AI.
- Không sửa Phiếu xuất kho ngoài đơn.
- Không sửa `importExcel` / CSV.
- Không xóa module legacy.
- Không tạo migration mới.
- Không chạy `migrate:fresh`.
- Không commit hoặc push Git.

