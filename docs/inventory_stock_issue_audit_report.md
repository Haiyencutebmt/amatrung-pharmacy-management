# Báo cáo rà soát Quản lý kho và Phiếu xuất kho AmaTrung

Ngày rà soát: 26/05/2026

## 1. Phạm vi kiểm tra

Đợt rà soát này chỉ kiểm tra route, controller, model, migration, view và test hiện có để xác định nghiệp vụ kho, cấp thuốc theo đơn điều trị và phiếu xuất kho ngoài đơn. Chưa sửa code nghiệp vụ, chưa xóa module legacy.

Các nhóm đã kiểm tra:

- Quản lý kho: `WarehouseController`, `InventoryController`, view `admin/warehouse`, view `admin/inventory`.
- Phiếu xuất kho hiện tại: `RetailOrderController`, model `RetailOrder`, `RetailOrderItem`, view `admin/retail`.
- Đơn điều trị/Cấp thuốc: `PrescriptionController`, `PrescriptionService`, `InventoryService`, model `Prescription`, `PrescriptionItem`.
- Legacy data: `medicinal_herbs`, `packaged_products`, `retail_orders`, `retail_order_items`, `medicinal_herb_stock_logs`.

## 2. Hiện trạng route/controller/table

### 2.1. Quản lý kho

Route hiện có:

- `GET /admin/warehouse` -> `WarehouseController@index`.
- `GET /admin/inventory` -> `InventoryController@index`.
- `GET /admin/inventory/{id}` -> `InventoryController@show`.
- `POST /admin/inventory` -> `InventoryController@storeItem`.
- `POST /admin/inventory/{item_id}/batch` -> `InventoryController@storeBatch`.
- `PUT /admin/inventory/batch/{id}` -> `InventoryController@updateBatch`.
- `PATCH /admin/inventory/batch/{id}/toggle` -> `InventoryController@toggleBatchStatus`.

Hiện có hai tầng kho song song:

- `WarehouseController` đang dùng legacy `MedicinalHerb` và `PackagedProduct`, lấy tồn từ cột `stock_quantity`.
- `InventoryController` đang dùng kho mới gồm `inventory_items`, `inventory_batches`, `stock_movements`.

Điểm đáng chú ý:

- Sidebar hiện trỏ mục “Quản lý kho” vào route `admin.warehouse.index`, tức nguồn legacy.
- Kho mới `admin.inventory.index` đã tồn tại và dùng batch total, nhưng chưa thấy được đưa làm nguồn chính trên sidebar.

### 2.2. Phiếu xuất kho hiện tại

Route hiện có:

- `Route::resource('retail-orders', RetailOrderController::class)->except(['edit', 'update'])`.
- `POST /admin/retail-orders/herb-info` -> `RetailOrderController@herbInfo`.

Controller hiện tại:

- `RetailOrderController@index`: danh sách phiếu từ bảng `retail_orders`.
- `RetailOrderController@create`: lấy sản phẩm từ `packaged_products` với `status = active` và `stock_quantity > 0`.
- `RetailOrderController@store`: tạo `retail_orders`, tạo `retail_order_items`, sau đó trừ `packaged_products.stock_quantity`.
- `RetailOrderController@destroy`: xóa phiếu và cộng lại `packaged_products.stock_quantity`.

Table hiện tại:

- `retail_orders`: có `order_code`, `staff_id`, `customer_name`, `customer_phone`, `customer_address`, `note`, `total_amount`.
- `retail_order_items`: ban đầu dùng `medicinal_herb_id`, sau migration đã đổi sang `packaged_product_id`.

Kết luận hiện trạng:

- Module này đang là legacy “retail order” được đổi nhãn giao diện thành “Phiếu xuất kho”.
- Không dùng `inventory_items`.
- Không dùng `inventory_batches`.
- Không tạo `stock_movements`.
- Có nhiều thuật ngữ/lưu vết bán lẻ: `retail`, `Khách lẻ`, `customer_*`, `price`, `total_amount`, title “Phiếu bán lẻ”.

### 2.3. Đơn điều trị/Cấp thuốc

Route hiện có:

- `Route::resource('prescriptions', PrescriptionController::class)->except(['edit', 'update'])`.
- `GET /admin/medical-records/{medicalRecord}/prescriptions/create`.
- `GET /admin/prescriptions/{prescription}/print`.
- `POST /admin/prescriptions/{prescription}/dispense`.

Luồng nghiệp vụ hiện tại:

- `PrescriptionController@store` gọi `PrescriptionService@createPrescription`.
- Đơn mới được tạo trạng thái `confirmed`.
- Khi bấm “Cấp Thuốc (FEFO)”, `PrescriptionController@dispense` gọi `PrescriptionService@dispensePrescription`.
- `PrescriptionService@dispensePrescription` chỉ cho xử lý khi đơn đang `confirmed`.
- Với từng `PrescriptionItem` có `affects_stock = true`, có `inventory_item_id`, và `quantity > 0`, service gọi `InventoryService@deductStockFefo`.
- `InventoryService@deductStockFefo` trừ `inventory_batches.quantity_remaining` theo lô còn hạn và tạo `stock_movements` với `movement_type = dispense`, `quantity` âm, có `prescription_item_id`.
- Sau khi trừ kho thành công, đơn chuyển sang `dispensed`.

Kết luận hiện trạng:

- Luồng cấp thuốc theo đơn đã được xử lý trong `PrescriptionService`.
- Luồng này đã trừ kho FEFO trên kho mới và đã tạo `stock_movements`.
- Không thấy luồng này tự tạo `retail_orders`.

### 2.4. retail_orders legacy

`retail_orders` đang tồn tại như module legacy ngoài kho mới.

Đặc điểm:

- Dùng model `RetailOrder`, `RetailOrderItem`, `PackagedProduct`.
- Trừ/cộng tồn trực tiếp bằng `packaged_products.stock_quantity`.
- Không có trạng thái `draft/confirmed/cancelled`.
- Khi tạo phiếu là trừ tồn ngay.
- Khi xóa phiếu là cộng lại tồn ngay.
- Không có liên kết tới `stock_movements`.
- Không có liên kết tới `prescriptions` hoặc `prescription_items`.

## 3. Kiểm tra nguy cơ trừ kho hai lần

### 3.1. Khi đơn thuốc chuyển sang `dispensed`

Có. Hệ thống hiện tại đã trừ kho FEFO và tạo stock movement:

- Trừ từ `inventory_batches.quantity_remaining`.
- Ghi `stock_movements.movement_type = dispense`.
- Ghi `stock_movements.prescription_item_id`.
- Cập nhật `prescriptions.status = dispensed`.

Đây là luồng đúng cho “Cấp thuốc theo đơn điều trị”.

### 3.2. Phiếu xuất kho hiện tại có tiếp tục trừ kho cho cùng một đơn thuốc không?

Không thấy liên kết trực tiếp.

Trong code hiện tại, `retail_orders` không có `prescription_id`, `prescription_item_id`, hoặc bất kỳ route nào tạo phiếu xuất từ đơn đã `dispensed`. Vì vậy, không có bằng chứng code đang tự động trừ kho lần hai cho cùng một đơn thuốc.

Tuy nhiên, có rủi ro nghiệp vụ:

- Giao diện đang gọi module `retail-orders` là “Phiếu xuất kho”, dễ làm nhân viên hiểu đây là bước tiếp theo sau “Cấp thuốc”.
- Nếu nhân viên đã bấm “Cấp Thuốc (FEFO)” trong đơn điều trị rồi lại tạo một phiếu xuất kho thủ công cho cùng sản phẩm/số lượng, hệ thống sẽ trừ thêm tồn ở module legacy `packaged_products.stock_quantity`.
- Đây không phải double deduction trên cùng bảng `inventory_batches`, nhưng là double handling trên hai nguồn tồn khác nhau và có thể làm lệch báo cáo kho.

### 3.3. Có nguy cơ lệch tồn giữa kho mới và module legacy không?

Có.

Nguyên nhân:

- Cấp thuốc theo đơn trừ kho mới: `inventory_batches.quantity_remaining` và ghi `stock_movements`.
- Phiếu xuất kho hiện tại trừ kho legacy: `packaged_products.stock_quantity`.
- “Quản lý kho” trên sidebar đang trỏ `WarehouseController`, cũng dùng legacy `medicinal_herbs.stock_quantity` và `packaged_products.stock_quantity`.
- Kho mới có route riêng `/admin/inventory`, nhưng chưa phải nguồn giao diện chính trên sidebar.
- Có command `inventory:migrate-legacy` tạo `inventory_items`/`inventory_batches` từ legacy, nhưng sau khi đã migrate, nếu tiếp tục sửa/trừ ở legacy thì kho mới không tự đồng bộ ngược.

Kết luận rủi ro:

- Không phát hiện tự động trừ hai lần cho cùng một đơn điều trị trong cùng hệ kho mới.
- Có nguy cơ cao gây nhầm quy trình và lệch tồn do tồn tại song song kho mới và phiếu xuất kho legacy.

## 4. Module đang dùng dữ liệu legacy

Các module/điểm vẫn dùng legacy `stock_quantity`:

- `WarehouseController`: dùng `MedicinalHerb` và `PackagedProduct`.
- View `resources/views/admin/warehouse/index.blade.php`: hiển thị và form nhập/sửa `stock_quantity`.
- `RetailOrderController`: dùng `PackagedProduct.stock_quantity`.
- View `resources/views/admin/retail/*`: hiển thị tồn từ `packaged_products.stock_quantity`.
- `MedicinalHerbController`: CRUD/import/export dùng `medicinal_herbs.stock_quantity`.
- `PackagedProductController`: CRUD sản phẩm dùng `packaged_products.stock_quantity`.
- `DashboardController`: thống kê dược liệu thấp/hết hàng từ `medicinal_herbs.stock_quantity`.
- `AiPrescriptionService`: danh sách dược liệu khả dụng lấy từ `MedicinalHerb.stock_quantity`.
- Một số phần view bệnh án cũ/sample prescription vẫn đọc `MedicinalHerb.stock_quantity`.

Module đang dùng kho mới:

- `InventoryController`.
- View `resources/views/admin/inventory/index.blade.php`.
- View `resources/views/admin/inventory/show.blade.php`.
- `PrescriptionController@create/store/dispense`.
- `PrescriptionService`.
- `InventoryService`.
- `StockMovement`.

## 5. Đối chiếu yêu cầu nghiệp vụ

### 5.1. Cấp thuốc theo đơn điều trị

Hiện trạng phù hợp một phần:

- Đã xử lý trong `PrescriptionService`.
- Đã trừ kho FEFO khi `dispense`.
- Đã tạo `stock_movements`.
- Không yêu cầu tạo `retail_order`.

Cần bổ sung rõ trên giao diện:

- Nút/phiếu in từ đơn đã `dispensed` nên gọi là “Phiếu cấp thuốc theo đơn”.
- Phiếu in này chỉ đọc dữ liệu prescription đã cấp, không tạo thêm stock movement.

### 5.2. Phiếu xuất kho ngoài đơn điều trị

Hiện trạng chưa phù hợp:

- Module hiện tại là `retail_orders`, không có trạng thái draft/confirmed.
- Tạo phiếu là trừ kho ngay.
- Xóa phiếu là hoàn kho trực tiếp.
- Không dùng FEFO.
- Không dùng `inventory_batches`.
- Không ghi `stock_movements`.
- Có thuật ngữ bán lẻ/doanh thu/thanh toán/khách lẻ trong luồng chính.

### 5.3. Giao diện kho

Kho mới `admin/inventory` hiện đã đáp ứng nhiều điểm:

- Hiển thị từ `inventory_items`, `inventory_batches`, `stock_movements`.
- Có nhập lô mới.
- Có cập nhật hạn dùng.
- Có lọc `expired`, `unknown_expiry`, `near_expiry`, `external_products`.
- Có cảnh báo “Dùng ngoài da - Không được uống”.

Nhưng giao diện chính “Quản lý kho” trên sidebar hiện vẫn trỏ sang kho legacy `admin/warehouse`, nên người dùng dễ thao tác nhầm nguồn tồn chính.

## 6. Phương án xử lý đề xuất

### Phương án A - Giữ tạm module hiện tại, giảm hiểu nhầm ngay

Mục tiêu: ít ảnh hưởng giao diện và chức năng hiện tại.

Việc cần làm nếu được duyệt:

- Đổi nhãn sidebar từ “Phiếu xuất kho” thành “Phiếu xuất kho ngoài đơn điều trị”.
- Đổi mô tả trang retail từ “bán cho khách lẻ” sang “xuất kho ngoài đơn điều trị”.
- Thêm cảnh báo ở trang tạo phiếu: “Không dùng cho đơn điều trị đã cấp thuốc. Đơn điều trị được cấp tại nút Cấp Thuốc (FEFO).”
- Không tạo liên kết nào từ đơn điều trị sang `retail_orders`.
- Tạm bỏ/ẩn các cụm từ bán lẻ, doanh thu, thanh toán trong luồng chính.

Hạn chế:

- Vẫn còn lệch nguồn tồn vì `retail_orders` dùng `packaged_products.stock_quantity`.
- Chỉ là phương án giảm nhầm lẫn, chưa chuẩn hóa kho.

### Phương án B - Triển khai đúng Phiếu xuất kho ngoài đơn trên kho mới

Mục tiêu: chuẩn nghiệp vụ kho mới.

Đề xuất bảng:

- `stock_issue_vouchers`
  - `id`
  - `voucher_code`
  - `issue_type`: `outside_prescription`, `expired_disposal`, `damaged_disposal`, `adjustment_decrease`
  - `status`: `draft`, `confirmed`, `cancelled`
  - `reason`
  - `confirmed_at`, `confirmed_by`
  - `cancelled_at`, `cancelled_by`, `cancel_reason`
  - `created_by`
  - timestamps
- `stock_issue_voucher_items`
  - `id`
  - `stock_issue_voucher_id`
  - `inventory_item_id`
  - `quantity`
  - `unit`
  - `note`
  - timestamps

Đề xuất cập nhật `stock_movements`:

- Thêm nullable `stock_issue_voucher_item_id`, hoặc thêm cặp polymorphic `source_type`, `source_id`.
- Với phiếu ngoài đơn đã confirmed, movement phải liên kết về dòng phiếu xuất tương ứng.

Quy tắc:

- Phiếu `draft`: không trừ kho, không tạo stock movement.
- Phiếu `confirmed`: trừ kho FEFO từ `inventory_batches`, tạo `stock_movements`.
- Phiếu đã confirmed: không xóa trực tiếp.
- Nếu cần hủy/điều chỉnh: tạo trạng thái hủy/điều chỉnh có lý do và ghi movement đảo chiều nếu nghiệp vụ yêu cầu hoàn tồn.
- Không dùng `retail_orders` cho luồng kho chính.

### Phương án C - Loại khỏi phạm vi chính tạm thời

Nếu chưa triển khai được phiếu ngoài đơn chuẩn:

- Tạm không coi `retail_orders` là module kho chính.
- Ẩn hoặc đổi rõ nhãn “legacy/ngoài đơn” trong sidebar.
- Chỉ dùng “Cấp Thuốc (FEFO)” trong đơn điều trị để xuất theo đơn.
- Dùng kho mới `admin/inventory` làm nguồn kiểm tồn chính.

## 7. Danh sách file cần sửa nếu triển khai

Tối thiểu để giảm nhầm lẫn:

- `resources/views/layouts/admin.blade.php`
- `resources/views/admin/retail/index.blade.php`
- `resources/views/admin/retail/create.blade.php`
- `resources/views/admin/retail/show.blade.php`

Nếu chuyển Quản lý kho sang kho mới:

- `resources/views/layouts/admin.blade.php`
- `app/Http/Controllers/Admin/WarehouseController.php` hoặc cấu hình route sidebar để trỏ `admin.inventory.index`
- `resources/views/admin/warehouse/index.blade.php` nếu vẫn giữ trang legacy dạng phụ

Nếu triển khai phiếu xuất kho ngoài đơn chuẩn:

- `database/migrations/*_create_stock_issue_vouchers_table.php`
- `database/migrations/*_create_stock_issue_voucher_items_table.php`
- `database/migrations/*_add_stock_issue_reference_to_stock_movements_table.php`
- `app/Models/StockIssueVoucher.php`
- `app/Models/StockIssueVoucherItem.php`
- `app/Models/StockMovement.php`
- `app/Services/InventoryService.php`
- `app/Http/Controllers/Admin/StockIssueVoucherController.php`
- `routes/web.php`
- `resources/views/admin/stock_issue_vouchers/index.blade.php`
- `resources/views/admin/stock_issue_vouchers/create.blade.php`
- `resources/views/admin/stock_issue_vouchers/show.blade.php`

Nếu loại dần legacy khỏi nguồn tồn chính:

- `app/Http/Controllers/Admin/RetailOrderController.php`
- `app/Models/RetailOrder.php`
- `app/Models/RetailOrderItem.php`
- `app/Http/Controllers/Admin/PackagedProductController.php`
- `app/Http/Controllers/Admin/MedicinalHerbController.php`
- `app/Http/Controllers/Admin/DashboardController.php`
- `app/Services/AiPrescriptionService.php`
- Các view còn đọc `stock_quantity`.

## 8. Testcase bắt buộc nếu triển khai sửa

### 8.1. Cấp thuốc theo đơn điều trị

- Tạo đơn `confirmed` không trừ tồn.
- Gọi `dispense` trừ đúng `inventory_batches.quantity_remaining` theo FEFO.
- Gọi `dispense` tạo đúng số dòng `stock_movements` với `movement_type = dispense` và `prescription_item_id`.
- Không thể `dispense` đơn khác `confirmed`.
- Khi thiếu tồn hợp lệ, không trừ một phần và không đổi trạng thái sang `dispensed`.
- In “Phiếu cấp thuốc theo đơn” từ đơn `dispensed` không tạo thêm `stock_movements`.

### 8.2. Phiếu xuất kho ngoài đơn chuẩn

- Tạo phiếu `draft` không trừ tồn và không tạo `stock_movements`.
- Confirm phiếu trừ tồn FEFO và tạo `stock_movements` liên kết voucher item.
- Confirm phiếu với nhiều batch phải trừ lô gần hết hạn trước.
- Không dùng lô `expired`, `blocked`, `unknown_expiry` để trừ tự động.
- Không cho xóa trực tiếp phiếu đã `confirmed`.
- Hủy/điều chỉnh phiếu đã `confirmed` bắt buộc có lý do và lưu lịch sử.
- Phiếu ngoài đơn không nhận `prescription_id` và không xử lý thay nút “Cấp Thuốc (FEFO)”.

### 8.3. Chống nhầm/double deduction

- Sau khi `dispense` một đơn, số lượng kho mới chỉ giảm một lần.
- Không có `retail_orders` hoặc `stock_issue_vouchers` tự sinh khi bấm `dispense`, trừ khi đó chỉ là chứng từ đọc dữ liệu không tạo movement.
- Nếu tạo phiếu ngoài đơn cho cùng mặt hàng sau đó, movement phải là nghiệp vụ riêng, không gắn `prescription_item_id`.

### 8.4. Giao diện kho mới

- Trang kho chính hiển thị tổng tồn từ batch, không từ `stock_quantity` legacy.
- Lọc `expired`, `unknown_expiry`, `near_expiry` đúng.
- Nhập lô mới tạo `stock_movements` loại `import`.
- Chế phẩm dùng ngoài hiển thị cảnh báo “Dùng ngoài da - Không được uống”.

## 9. Kết luận chốt nghiệp vụ

Chốt đề xuất:

- “Cấp thuốc theo đơn điều trị” là luồng riêng trong `PrescriptionService`; không yêu cầu tạo thêm phiếu xuất kho thủ công.
- Nếu cần chứng từ, tạo “Phiếu cấp thuốc theo đơn” từ dữ liệu prescription đã `dispensed`; chứng từ này không trừ kho, không tạo `stock_movements`.
- “Phiếu xuất kho” nếu giữ trong hệ thống nên đổi nghĩa rõ thành “Phiếu xuất kho ngoài đơn điều trị”.
- Phiếu xuất kho ngoài đơn phải dùng kho mới `inventory_items`/`inventory_batches`/`stock_movements`, không dùng `packaged_products.stock_quantity` làm nguồn chính.
- Module `retail_orders` hiện tại nên được xem là legacy hoặc cần refactor mạnh trước khi dùng làm luồng chính.

Đánh giá rủi ro:

- Không phát hiện double deduction tự động cho cùng đơn điều trị trong code hiện tại.
- Có rủi ro nhầm thao tác và lệch tồn cao do `retail_orders` được đặt tên “Phiếu xuất kho” nhưng vẫn dùng kho legacy, trong khi “Cấp Thuốc (FEFO)” đã trừ kho mới.
