# Báo cáo kiểm tra Chương 2 - AmaTrung

Ngày rà soát: 28/05/2026  
Phạm vi: chỉ đọc code, route, model, migration và schema DB hiện tại. Không sửa code chức năng, không chạy migration, không thay đổi database.

Nguồn kiểm tra chính:
- `routes/web.php`
- `app/Http/Controllers/**`
- `app/Models/**`
- `app/Services/**`
- `resources/views/**`
- `database/migrations/**`
- Schema MySQL hiện tại qua kết nối Laravel: `db_amatrung`

## A. Danh sách chức năng thực tế

### A1. Khu vực sử dụng

**Phía public/user**
- Trang chủ `/`.
- Bài viết công khai `/bai-viet`, `/bai-viet/{slug}`.
- 5 trang Ngũ hành dạng route tĩnh: `/bai-viet/ngu-hanh-kim`, `moc`, `thuy`, `hoa`, `tho`.
- Từ điển dược liệu public `/tu-dien-thuoc-nam`; xem chi tiết và yêu thích yêu cầu đăng nhập.
- Đăng ký, đăng nhập, quên mật khẩu OTP, hồ sơ cá nhân, danh sách yêu thích.
- Chatbot public `POST /api/chatbot/chat`.
- Gửi liên hệ `POST /lien-he`.

**Phía admin/staff**
- Dashboard `/admin/dashboard`.
- Quản lý bệnh nhân, bệnh án, đơn điều trị, lịch hẹn.
- Quản lý kho mới `/admin/inventory` và kho legacy `/admin/warehouse`, `/admin/medicinal-herbs`, `/admin/packaged-products`.
- Quản lý dịch vụ trị liệu và bài thuốc mẫu.
- Quản lý bài viết, bình luận, yêu cầu hỗ trợ, từ điển dược liệu.
- Quản lý tài khoản nhân viên/phân quyền, chỉ admin được vào nhóm `/admin/users`.
- AI hỗ trợ thầy thuốc qua `POST /admin/api/ai-suggest`.

### A2. Chức năng theo nhóm

| Nhóm chức năng | Lưu dữ liệu thật hay chỉ giao diện | Route liên quan | Controller liên quan | View liên quan | Model/bảng liên quan | Logic chính |
|---|---|---|---|---|---|---|
| Tài khoản public | Lưu thật | `/login`, `/register`, `/forgot-password`, `/reset-password`, `/logout`, `/profile`, `/profile/password`, `/yeu-thich` | `Auth\LoginController`, `Auth\RegisterController`, `Auth\ForgotPasswordController`, `ProfileController` | `auth/*.blade.php`, `user/dashboard.blade.php`, `user/favorites.blade.php` | `User`, `users`, `password_reset_tokens` | Đăng nhập bằng email hoặc phone; đăng ký role `user`; OTP lưu ở `users.reset_code`; hồ sơ cá nhân lưu avatar/phone/name. |
| Phân quyền admin/staff | Lưu thật | `/admin/users`, `/admin/users/create`, `/admin/users/{user}/edit`, patch khóa/reset | `Admin\UserController`, middleware `StaffMiddleware`, `AdminMiddleware`, `CheckPermission` | `admin/users/*.blade.php`, `layouts/admin.blade.php` | `User`, Spatie `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`; cột legacy `users.legacy_permissions_json` | Dùng Spatie permission kết hợp legacy permission JSON. Admin có quyền toàn hệ thống qua `User::hasPermission()`. Staff chỉ dùng được chức năng được cấp. |
| Bệnh nhân | Lưu thật | `/admin/patients`, CRUD resource, `patients-check-duplicate`, `patients-import`, `patients/export-excel`, `patients/print-list`, `patients-legacy/create` | `Admin\PatientController` | `admin/patients/*.blade.php` | `Patient`, `patients`, `appointments`, `users` | CRUD bệnh nhân, nhập hồ sơ giấy, import CSV/Excel, kiểm tra trùng, in/xuất Excel, không cho xóa bệnh nhân đã có bệnh án. |
| Hồ sơ bệnh án | Lưu thật | `/admin/medical-records`, `/admin/patients/{patient}/medical-records/create`, `medical-records-legacy`, `medical-records/{id}/print`, `medical-records/{id}/xray`, attachments | `Admin\MedicalRecordController` | `admin/records/*.blade.php`, `admin/records/partials/ai_panel.blade.php`, `ai_js.blade.php` | `MedicalRecord`, `MedicalRecordAttachment`, `Patient`, `Prescription`, `medical_records`, `medical_record_attachments` | Tạo bệnh án theo bệnh nhân, lưu triệu chứng/chẩn đoán/hướng điều trị, hỗ trợ ca thường/xương khớp/kết hợp, upload file private, nhập bệnh án cũ không trừ kho. |
| Đơn điều trị | Lưu thật | `/admin/prescriptions`, `/admin/medical-records/{medicalRecord}/prescriptions/create`, `/admin/prescriptions/{id}/dispense`, `/print`, DELETE | `Admin\PrescriptionController`, `PrescriptionService` | `admin/prescriptions/*.blade.php` | `Prescription`, `PrescriptionItem`, `InventoryItem`, `Appointment`, `prescriptions`, `prescription_items`, `appointments` | Mỗi bệnh án chỉ có một đơn chính chưa hủy; đơn tạo trạng thái `confirmed`; `dispense` mới trừ kho; hủy trong 24h có hoàn kho theo `stock_movements`. |
| Thuốc uống dạng thang | Lưu thật | Nằm trong route tạo đơn điều trị | `Admin\PrescriptionController@create/store`, `PrescriptionService` | `admin/prescriptions/create.blade.php` | `InventoryItem`, `InventoryBatch`, `PrescriptionItem`, `StockMovement` | Item `herb`, thường `usage_route = oral`; tổng lượng = lượng mỗi thang x số thang; ảnh hưởng tồn kho khi `affects_stock = true`. |
| Thuốc/chế phẩm dùng ngoài | Lưu thật, nhưng có cả bảng legacy | Nằm trong route tạo đơn điều trị; legacy CRUD `/admin/packaged-products`; kho mới `/admin/inventory` | `PrescriptionController`, `InventoryController`, `PackagedProductController` | `admin/prescriptions/create.blade.php`, `admin/inventory/*.blade.php`, `admin/warehouse/index.blade.php` | `inventory_items` với `usage_route = external`, `prescription_items`; legacy `packaged_products` | Kê theo `inventory_item_id` trong kho mới; dịch vụ/legacy vẫn còn để tương thích. Chế phẩm dùng ngoài không phải thuốc uống. |
| Dịch vụ trị liệu | Lưu thật | `/admin/treatment-templates`, resource `/admin/therapy-services` | `Admin\TreatmentTemplateController`, `Admin\TherapyServiceController` | `admin/treatment_templates/index.blade.php`, `admin/therapy_services/index.blade.php` | `TherapyService`, `therapy_services`, `prescription_items` | Quản lý dịch vụ trị liệu, số buổi mặc định, hướng dẫn mặc định. Khi đưa vào đơn là hạng mục không trừ kho (`affects_stock = false`). |
| Kho/dược liệu mới | Lưu thật | `/admin/inventory`, `/admin/inventory/{id}`, nhập lô, cập nhật lô, khóa/mở lô, xóa hàng loạt | `Admin\InventoryController`, `InventoryService` | `admin/inventory/index.blade.php`, `admin/inventory/show.blade.php` | `InventoryItem`, `InventoryBatch`, `StockMovement`, `inventory_items`, `inventory_batches`, `stock_movements` | Kho theo mặt hàng/lô/hạn dùng. Tồn khả dụng và FEFO trong model loại batch không có hạn; `dispense` tạo `stock_movements`. |
| Kho/dược liệu legacy | Lưu thật, đang giữ tương thích | `/admin/warehouse`, `/admin/medicinal-herbs`, `/admin/packaged-products` | `WarehouseController`, `MedicinalHerbController`, `PackagedProductController` | `admin/warehouse/index.blade.php`, `admin/medicinal_herbs/*.blade.php`, `admin/packaged_products/*.blade.php` | `MedicinalHerb`, `MedicinalHerbStockLog`, `PackagedProduct`, `medicinal_herbs`, `medicinal_herb_stock_logs`, `packaged_products` | `medicinal_herbs.stock_quantity` vẫn dùng ở kho cũ, import/export, chatbot legacy. Khi tạo/cập nhật dược liệu có đồng bộ một phần sang kho mới. |
| Bài viết và bình luận | Lưu thật | Public `/bai-viet`; admin `/admin/articles`; comments `/bai-viet/{id}/comments`, `/admin/comments`; like `/bai-viet/{id}/like` | `ArticleController`, `CommentController`, `Admin\ArticleController`, `Admin\CommentController` | `articles/*.blade.php`, `admin/articles/*.blade.php`, `admin/comments/index.blade.php` | `Article`, `Comment`, `article_likes`, `articles`, `comments` | 5 danh mục cố định trong `Article::CATEGORIES`, summary/tags/featured image, bình luận hiển thị ngay, admin có quản lý bình luận. |
| Từ điển dược liệu | Lưu thật | Public `/tu-dien-thuoc-nam`; admin `/admin/herb-dictionary`, import, ảnh, xóa ảnh, xóa hàng loạt | `HerbDictionaryController`, `Admin\HerbDictionaryController` | `herb_dictionary/*.blade.php`, `admin/herb_dictionary/*.blade.php` | `HerbDictionaryEntry`, `HerbDictionaryImage`, `HerbDictionaryFavorite`, `herb_dictionary_entries`, `herb_dictionary_images`, `herb_dictionary_favorites` | CRUD mục từ điển, mỗi mục có ảnh 1-5, trạng thái published/draft, yêu thích theo user, import Excel/CSV. |
| Dashboard/thống kê | Lấy dữ liệu thật | `/admin/dashboard` | `Admin\DashboardController` | `admin/dashboard.blade.php` | `patients`, `medical_records`, `prescriptions`, `inventory_items`, `appointments` | Thống kê bệnh nhân, lượt khám hôm nay/hôm qua, đơn điều trị, dược liệu sắp hết, lịch hẹn, hoạt động gần đây, biểu đồ theo tháng/năm từ DB thật. |
| Chatbot AI phía user | Lưu liên hệ thì có; chat không lưu hội thoại | `POST /api/chatbot/chat` | `ChatbotController` | `components/chatbot.blade.php` | Đọc `articles`, `medicinal_herbs`, `herb_dictionary_entries`; không lưu chat | Chuẩn hóa tiếng Việt, tìm context website, kiểm tra câu hỏi nguy hiểm/kê đơn, gọi Gemini nếu có key, trả lời tham khảo an toàn. |
| AI hỗ trợ thầy thuốc phía admin | Lưu log thật | `POST /admin/api/ai-suggest`, `/admin/api/ai-suggest/log-status` | `Admin\AiSuggestionController`, `AiClinicalContextBuilder`, `AiClinicalSuggestionService` | `admin/records/partials/ai_panel.blade.php`, `ai_js.blade.php`, có dùng ở luồng kê đơn/bệnh án | `AiSuggestionLog`, `MedicalRecord`, `InventoryItem`, `InventoryBatch`, `ai_suggestion_logs` | Chỉ nhận `medical_record_id`, kiểm tra quyền, dựng payload ẩn danh, lấy tồn kho khả dụng, gọi Gemini, post-verify, ghi log. Không tự lưu đơn, không tự trừ kho. |
| Liên hệ/yêu cầu hỗ trợ | Lưu thật | `POST /lien-he`, admin `/admin/contact-messages` | `ContactMessageController`, `Admin\ContactMessageController` | `home.blade.php`, `admin/contact_messages/index.blade.php` | `ContactMessage`, `contact_messages` | User gửi tên/email/nội dung; admin xem, đổi trạng thái pending/resolved, xóa. |
| Lịch hẹn | Lưu thật | `/admin/appointments`, `/admin/appointments/day/{date}`, `/date/{date}`, status patch | `Admin\AppointmentController` | `admin/appointments/*.blade.php` | `Appointment`, `appointments`, `patients` | Xem theo tháng/ngày, thêm/xóa lịch, cập nhật trạng thái, lịch tái khám có thể tạo từ đơn điều trị. |
| 5 bài Ngũ hành | Chủ yếu giao diện tĩnh | `/bai-viet/ngu-hanh-kim`, `moc`, `thuy`, `hoa`, `tho` | Closure trong `routes/web.php` | `bai-viet/ngu-hanh.blade.php` | Không dùng bảng `articles` | Đây không phải bài viết trong DB, nên không xuất hiện trong trang quản lý bài viết để sửa bằng CMS. |

## B. Vai trò và phân quyền

### B1. Vai trò hiện có

Trong bảng `users.role` có đúng 3 role legacy:
- `admin`
- `staff`
- `user`

Trong Spatie `roles` hiện có 4 role:
- `admin`
- `practitioner`
- `staff`
- `user`

Kết quả đọc DB hiện tại:
- `users_by_role = {"admin":1,"staff":2,"user":4}`
- `roles = ["admin","practitioner","staff","user"]`

Vì vậy khi viết báo cáo nên trình bày: hệ thống nghiệp vụ chính dùng 3 nhóm tài khoản Admin/Staff/User, nhưng tầng Spatie hiện có thêm role `practitioner` để phục vụ phân quyền chi tiết.

### B2. Staff được cấp quyền chi tiết đến đâu

Staff đi qua middleware chung `auth` + `staff`. `User::isStaff()` trả true nếu user có Spatie role `admin`, `practitioner`, `staff` hoặc legacy role `admin/staff`.

Các controller admin dùng thêm permission chi tiết:
- Bệnh nhân: `patients.view`, `patients.create`, `patients.edit`, `patients.delete`.
- Bệnh án: `medical_records.view/create/edit/delete`, `upload_medical_record_attachments`, `view_medical_record_attachments`.
- Đơn điều trị: `prescriptions.view/create/delete`, `dispense_prescriptions`.
- Kho mới: policy/authorize `manage_inventory`.
- Bài viết/bình luận/từ điển: `articles.manage`, `comments.manage`, `herb_dictionary.manage`.
- AI nội bộ: `use_ai_suggestion`.
- Quản lý user: route group `admin`, chỉ admin.

Lưu ý quan trọng: DB `permissions` hiện có các permission như `view_patients`, `create_patients`, `manage_articles`, `moderate_comments`, nhưng một số controller đang gọi tên dạng `patients.view`, `articles.manage`, `comments.manage`, `herb_dictionary.manage`. Với Admin vẫn qua được vì `User::hasPermission()` cho admin true. Với Staff, permission nhạy cảm bắt buộc phải có trong Spatie, còn permission không nhạy cảm có thể fallback sang `legacy_permissions_json`. Đây là điểm cần ghi rõ nếu mô tả phân quyền Staff.

### B3. Middleware, package, bảng liên quan

Middleware:
- `auth`, `guest`
- `staff` => `App\Http\Middleware\StaffMiddleware`
- `admin` => `App\Http\Middleware\AdminMiddleware`
- `permission` => `App\Http\Middleware\CheckPermission`

Package:
- `spatie/laravel-permission`

Bảng phân quyền:
- `users`
- `roles`
- `permissions`
- `model_has_roles`
- `model_has_permissions`
- `role_has_permissions`

## C. Database thực tế

### C1. Migration/model đã đọc

Các nhóm migration thực tế gồm:
- Laravel nền: users, cache, jobs, sessions, password reset.
- Giai đoạn ban đầu: patients, medical_records, prescriptions, prescription_items, medicinal_herbs, articles, comments.
- Mở rộng nghiệp vụ: appointments, retail_orders, packaged_products, sample_prescriptions, therapy_services, herb_dictionary.
- Phase kho/AI/phân quyền: inventory_items, inventory_batches, stock_movements, ai_suggestion_logs, permission tables, medical_record_attachments.
- Bài viết/user: article_likes, contact_messages, patient_user_links, avatar, category/summary/tags.

Model chính đang tồn tại:
`User`, `Patient`, `MedicalRecord`, `MedicalRecordAttachment`, `Prescription`, `PrescriptionItem`, `MedicinalHerb`, `MedicinalHerbStockLog`, `PackagedProduct`, `RetailOrder`, `RetailOrderItem`, `InventoryItem`, `InventoryBatch`, `StockMovement`, `Article`, `Comment`, `ContactMessage`, `Appointment`, `SamplePrescription`, `SamplePrescriptionItem`, `TherapyService`, `HerbDictionaryEntry`, `HerbDictionaryImage`, `HerbDictionaryFavorite`, `AiSuggestionLog`, `PatientUserLink`.

### C2. Danh sách bảng thực tế

Tổng bảng đọc được từ DB thật: 43 bảng.

Bảng nghiệp vụ chính:
- `users`
- `patients`
- `medical_records`
- `medical_record_attachments`
- `prescriptions`
- `prescription_items`
- `appointments`
- `medicinal_herbs`
- `medicinal_herb_stock_logs`
- `inventory_items`
- `inventory_batches`
- `stock_movements`
- `packaged_products`
- `retail_orders`
- `retail_order_items`
- `sample_prescriptions`
- `sample_prescription_items`
- `therapy_services`
- `articles`
- `article_likes`
- `comments`
- `contact_messages`
- `herb_dictionary_entries`
- `herb_dictionary_images`
- `herb_dictionary_favorites`
- `ai_suggestion_logs`
- `patient_user_links`

Bảng phân quyền:
- `roles`
- `permissions`
- `model_has_roles`
- `model_has_permissions`
- `role_has_permissions`

Bảng Laravel hệ thống:
- `migrations`
- `cache`
- `cache_locks`
- `jobs`
- `job_batches`
- `failed_jobs`
- `sessions`
- `password_reset_tokens`

### C3. Cột, kiểu dữ liệu, khóa chính, khóa ngoại

Ghi chú: `[PK]` là khóa chính, `[FK -> bang.cot]` là khóa ngoại, `[UNIQUE]` là ràng buộc duy nhất.

#### ai_suggestion_logs
- id: bigint unsigned NOT NULL [PK]
- user_id: bigint unsigned NULL [FK -> users.id]
- medical_record_id: bigint unsigned NULL [FK -> medical_records.id]
- payload: longtext NOT NULL
- response: longtext NULL
- error_message: text NULL
- status: varchar(255) NOT NULL
- created_at, updated_at: timestamp NULL

#### appointments
- id: bigint unsigned NOT NULL [PK]
- patient_id: bigint unsigned NOT NULL [FK -> patients.id]
- appointment_date: date NOT NULL
- appointment_time: time NOT NULL
- reason: varchar(255) NULL
- status: enum('pending','confirmed','cancelled','completed') NOT NULL
- notes: text NULL
- created_at, updated_at: timestamp NULL

#### article_likes
- id: bigint unsigned NOT NULL [PK]
- article_id: bigint unsigned NOT NULL [FK -> articles.id]
- user_id: bigint unsigned NOT NULL [FK -> users.id]
- created_at, updated_at: timestamp NULL
- unique: `article_id + user_id`

#### articles
- id: bigint unsigned NOT NULL [PK]
- user_id: bigint unsigned NOT NULL [FK -> users.id]
- title: varchar(255) NOT NULL
- summary: text NULL
- slug: varchar(255) NOT NULL [UNIQUE]
- content: longtext NOT NULL
- featured_image: varchar(255) NULL
- category: varchar(100) NULL
- tags: longtext NULL
- is_published: tinyint NOT NULL
- published_at, created_at, updated_at: timestamp NULL

#### comments
- id: bigint unsigned NOT NULL [PK]
- article_id: bigint unsigned NOT NULL [FK -> articles.id]
- user_id: bigint unsigned NOT NULL [FK -> users.id]
- content: text NOT NULL
- rating: tinyint NULL
- is_approved: tinyint NOT NULL
- created_at, updated_at: timestamp NULL

#### contact_messages
- id: bigint unsigned NOT NULL [PK]
- name: varchar(255) NOT NULL
- email: varchar(255) NOT NULL
- message: text NOT NULL
- status: varchar(255) NOT NULL
- created_at, updated_at: timestamp NULL

#### herb_dictionary_entries
- id: bigint unsigned NOT NULL [PK]
- created_by: bigint unsigned NULL [FK -> users.id]
- name: varchar(150) NOT NULL
- slug: varchar(180) NOT NULL [UNIQUE]
- scientific_name: varchar(180) NULL
- other_names: varchar(255) NULL
- family: varchar(150) NULL
- plant_part: varchar(150) NULL
- properties: varchar(255) NULL
- basic_info: text NULL
- effects: text NULL
- usage_notes: text NULL
- safety_warning: text NULL
- status: varchar(30) NOT NULL
- created_at, updated_at: timestamp NULL

#### herb_dictionary_favorites
- id: bigint unsigned NOT NULL [PK]
- user_id: bigint unsigned NOT NULL [FK -> users.id]
- entry_id: bigint unsigned NOT NULL [FK -> herb_dictionary_entries.id]
- created_at, updated_at: timestamp NULL

#### herb_dictionary_images
- id: bigint unsigned NOT NULL [PK]
- entry_id: bigint unsigned NOT NULL [FK -> herb_dictionary_entries.id]
- image_path: varchar(500) NOT NULL
- caption: varchar(255) NULL
- sort_order: tinyint unsigned NOT NULL
- created_at, updated_at: timestamp NULL

#### inventory_items
- id: bigint unsigned NOT NULL [PK]
- name: varchar(255) NOT NULL
- item_type: varchar(255) NOT NULL
- unit: varchar(255) NULL
- description: text NULL
- is_active: tinyint(1) NOT NULL
- usage_route: varchar(255) NULL
- warning_note: text NULL
- legacy_source_table: varchar(255) NULL
- legacy_source_id: bigint unsigned NULL
- created_at, updated_at: timestamp NULL

#### inventory_batches
- id: bigint unsigned NOT NULL [PK]
- inventory_item_id: bigint unsigned NOT NULL [FK -> inventory_items.id]
- batch_number: varchar(255) NULL
- expiry_date: date NULL
- quantity_remaining: decimal(10,2) NOT NULL
- status: varchar(255) NOT NULL
- created_at, updated_at: timestamp NULL

#### medical_record_attachments
- id: bigint unsigned NOT NULL [PK]
- medical_record_id: bigint unsigned NOT NULL [FK -> medical_records.id]
- uploaded_by: bigint unsigned NULL [FK -> users.id]
- file_name: varchar(255) NOT NULL
- file_path: varchar(255) NOT NULL
- file_type: varchar(255) NULL
- file_size: int NULL
- description: text NULL
- created_at, updated_at: timestamp NULL

#### medical_records
- id: bigint unsigned NOT NULL [PK]
- record_code: varchar(20) NULL [UNIQUE]
- patient_id: bigint unsigned NOT NULL [FK -> patients.id]
- staff_id: bigint unsigned NOT NULL [FK -> users.id]
- visit_date: date NOT NULL
- weight: decimal(5,1) NULL
- height: decimal(5,1) NULL
- symptoms: text NOT NULL
- diagnosis: text NOT NULL
- treatment_plan: text NULL
- doctor_note: text NULL
- case_type: varchar(50) NOT NULL
- injury_type: varchar(100) NULL
- injury_location: varchar(255) NULL
- injury_cause: text NULL
- clinical_signs: text NULL
- palpation_result: text NULL
- pain_level: tinyint NULL
- xray_image: varchar(500) NULL
- xray_note: text NULL
- xray_file_path: varchar(255) NULL
- treatment_direction: varchar(255) NULL
- status: varchar(255) NOT NULL
- referral_reason: varchar(255) NULL
- allergies: text NULL
- underlying_diseases: text NULL
- current_medications: text NULL
- is_legacy_data: tinyint(1) NOT NULL
- legacy_source: varchar(50) NULL
- legacy_note: text NULL
- imported_at: timestamp NULL
- imported_by: bigint unsigned NULL
- created_at, updated_at: timestamp NULL

#### medicinal_herbs
- id: bigint unsigned NOT NULL [PK]
- name: varchar(150) NOT NULL
- category: varchar(100) NULL
- usage_type: varchar(100) NULL
- description: text NULL
- unit: varchar(50) NOT NULL
- stock_quantity: decimal(10,2) NOT NULL
- expiry_date: date NULL
- warning_note: text NULL
- status: varchar(50) NULL
- created_at, updated_at: timestamp NULL

#### medicinal_herb_stock_logs
- id: bigint unsigned NOT NULL [PK]
- medicinal_herb_id: bigint unsigned NOT NULL [FK -> medicinal_herbs.id]
- user_id: bigint unsigned NULL [FK -> users.id]
- old_quantity: decimal(10,2) NOT NULL
- new_quantity: decimal(10,2) NOT NULL
- change_quantity: decimal(10,2) NOT NULL
- action_type: varchar(50) NOT NULL
- note: varchar(255) NULL
- details: longtext NULL
- created_at, updated_at: timestamp NULL

#### packaged_products
- id: bigint unsigned NOT NULL [PK]
- name: varchar(255) NOT NULL
- description: text NULL
- category: varchar(255) NULL
- sku: varchar(255) NULL [UNIQUE]
- unit: varchar(255) NOT NULL
- stock_quantity: decimal(10,2) NOT NULL
- expiry_date: date NULL
- price: decimal(12,2) NOT NULL
- status: enum('active','inactive') NOT NULL
- created_at, updated_at: timestamp NULL

#### patient_user_links
- id: bigint unsigned NOT NULL [PK]
- patient_id: bigint unsigned NOT NULL [FK -> patients.id]
- user_id: bigint unsigned NOT NULL [FK -> users.id]
- relationship_type: varchar(255) NULL
- is_verified: tinyint(1) NOT NULL
- created_at, updated_at: timestamp NULL

#### patients
- id: bigint unsigned NOT NULL [PK]
- patient_code: varchar(20) NOT NULL [UNIQUE]
- user_id: bigint unsigned NULL [FK -> users.id]
- full_name: varchar(100) NOT NULL
- phone: varchar(15) NULL
- date_of_birth: date NULL
- gender: enum('male','female','other') NULL
- address: text NULL
- guardian_name: varchar(100) NULL
- guardian_phone: varchar(15) NULL
- relationship: varchar(50) NULL
- note: text NULL
- is_legacy_data: tinyint(1) NOT NULL
- legacy_source: varchar(50) NULL
- legacy_note: text NULL
- legacy_date: date NULL
- imported_at: timestamp NULL
- imported_by: bigint unsigned NULL
- created_at, updated_at: timestamp NULL

#### prescription_items
- id: bigint unsigned NOT NULL [PK]
- prescription_id: bigint unsigned NOT NULL [FK -> prescriptions.id]
- inventory_item_id: bigint unsigned NULL [FK -> inventory_items.id]
- medicinal_herb_id: bigint unsigned NULL [FK -> medicinal_herbs.id]
- packaged_product_id: bigint unsigned NULL [FK -> packaged_products.id]
- item_type: varchar(50) NOT NULL
- custom_name: varchar(255) NULL
- quantity: decimal(10,2) NOT NULL
- quantity_per_dose: decimal(10,2) NULL
- number_of_doses: int NULL
- unit: varchar(50) NULL
- dosage: varchar(255) NULL
- note: varchar(255) NULL
- is_secret_formula: tinyint(1) NOT NULL
- affects_stock: tinyint(1) NOT NULL
- usage_area: varchar(255) NULL
- sessions: int unsigned NULL
- usage_instruction: text NULL
- created_at, updated_at: timestamp NULL

#### prescriptions
- id: bigint unsigned NOT NULL [PK]
- medical_record_id: bigint unsigned NOT NULL [FK -> medical_records.id]
- staff_id: bigint unsigned NOT NULL [FK -> users.id]
- treatment_type: varchar(50) NOT NULL
- status: varchar(30) NOT NULL
- note: text NULL
- public_instruction: text NULL
- internal_note: text NULL
- num_of_doses: int unsigned NOT NULL
- usage_instruction: text NULL
- course_days: int unsigned NULL
- follow_up_date: date NULL
- ai_suggestion: text NULL
- is_legacy_data: tinyint(1) NOT NULL
- legacy_source: varchar(50) NULL
- legacy_note: text NULL
- affect_stock: tinyint(1) NOT NULL
- created_at, updated_at: timestamp NULL

#### retail_orders
- id: bigint unsigned NOT NULL [PK]
- order_code: varchar(20) NOT NULL [UNIQUE]
- staff_id: bigint unsigned NOT NULL [FK -> users.id]
- customer_name: varchar(100) NOT NULL
- customer_phone: varchar(20) NULL
- customer_address: varchar(255) NULL
- note: text NULL
- total_amount: decimal(12,2) NOT NULL
- created_at, updated_at: timestamp NULL

#### retail_order_items
- id: bigint unsigned NOT NULL [PK]
- retail_order_id: bigint unsigned NOT NULL [FK -> retail_orders.id]
- packaged_product_id: bigint unsigned NOT NULL [FK -> packaged_products.id]
- quantity: decimal(10,2) NOT NULL
- unit: varchar(50) NULL
- unit_price: decimal(12,2) NOT NULL
- note: text NULL
- created_at, updated_at: timestamp NULL

#### sample_prescriptions
- id: bigint unsigned NOT NULL [PK]
- name: varchar(150) NOT NULL
- suggested_condition: text NULL
- usage_instruction: text NULL
- preparation_type: varchar(100) NULL
- default_packages: int NULL
- notes: text NULL
- created_at, updated_at: timestamp NULL

#### sample_prescription_items
- id: bigint unsigned NOT NULL [PK]
- sample_prescription_id: bigint unsigned NOT NULL [FK -> sample_prescriptions.id]
- medicinal_herb_id: bigint unsigned NOT NULL [FK -> medicinal_herbs.id]
- quantity: decimal(10,2) NOT NULL
- created_at, updated_at: timestamp NULL

#### stock_movements
- id: bigint unsigned NOT NULL [PK]
- inventory_batch_id: bigint unsigned NOT NULL [FK -> inventory_batches.id]
- prescription_item_id: bigint unsigned NULL [FK -> prescription_items.id]
- performed_by: bigint unsigned NULL [FK -> users.id]
- movement_type: varchar(255) NOT NULL
- quantity: decimal(10,2) NOT NULL
- note: text NULL
- created_at, updated_at: timestamp NULL

#### therapy_services
- id: bigint unsigned NOT NULL [PK]
- name: varchar(150) NOT NULL [UNIQUE]
- default_sessions: int NULL
- default_instruction: text NULL
- status: varchar(50) NOT NULL
- created_at, updated_at: timestamp NULL

#### users
- id: bigint unsigned NOT NULL [PK]
- name: varchar(100) NOT NULL
- email: varchar(150) NULL [UNIQUE]
- phone: varchar(15) NULL [UNIQUE]
- email_verified_at: timestamp NULL
- password: varchar(255) NOT NULL
- avatar: varchar(255) NULL
- role: enum('admin','staff','user') NOT NULL
- legacy_permissions_json: longtext NULL
- is_active: tinyint NOT NULL
- remember_token: varchar(100) NULL
- reset_code: varchar(6) NULL
- reset_code_expires_at: timestamp NULL
- created_at, updated_at: timestamp NULL

#### roles / permissions / pivot Spatie
- `roles`: id [PK], name, guard_name, created_at, updated_at.
- `permissions`: id [PK], name, guard_name, created_at, updated_at.
- `model_has_roles`: role_id [FK -> roles.id], model_type, model_id.
- `model_has_permissions`: permission_id [FK -> permissions.id], model_type, model_id.
- `role_has_permissions`: permission_id [FK -> permissions.id], role_id [FK -> roles.id].

#### Bảng hệ thống Laravel
- `migrations`: id, migration, batch.
- `cache`, `cache_locks`: lưu cache.
- `jobs`, `job_batches`, `failed_jobs`: queue.
- `sessions`: session database.
- `password_reset_tokens`: reset password mặc định Laravel, hiện luồng OTP chính lại lưu ở `users.reset_code`.

### C4. Bảng phát sinh thêm so với thiết kế ban đầu

Nếu thiết kế ban đầu chỉ có 8 bảng thường gặp (`users`, `patients`, `medical_records`, `prescriptions`, `prescription_items`, `medicinal_herbs`, `articles`, `comments`) thì DB thực tế đã phát sinh thêm nhiều bảng:
- Lịch hẹn: `appointments`.
- Bán lẻ/legacy: `retail_orders`, `retail_order_items`.
- Chế phẩm/sản phẩm đóng gói: `packaged_products`.
- Bài thuốc mẫu/dịch vụ: `sample_prescriptions`, `sample_prescription_items`, `therapy_services`.
- Nhật ký tồn kho legacy: `medicinal_herb_stock_logs`.
- Từ điển dược liệu: `herb_dictionary_entries`, `herb_dictionary_images`, `herb_dictionary_favorites`.
- Kho mới theo lô FEFO: `inventory_items`, `inventory_batches`, `stock_movements`.
- AI nội bộ: `ai_suggestion_logs`.
- File bệnh án: `medical_record_attachments`.
- Liên kết user-bệnh nhân: `patient_user_links`.
- Like bài viết/liên hệ: `article_likes`, `contact_messages`.
- Phân quyền Spatie: `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`.

### C5. Kho đang quản lý theo medicinal_herbs hay inventory_batches?

Kết luận chính xác từ code hiện tại:
- Kho mới và luồng cấp thuốc FEFO dùng `inventory_items`, `inventory_batches`, `stock_movements`.
- Trang `/admin/inventory` hiển thị từ `InventoryController`, không lấy `medicinal_herbs.stock_quantity` làm nguồn tồn chính.
- Khi đơn điều trị chuyển sang `dispensed`, `PrescriptionService::dispensePrescription()` gọi `InventoryService::deductStockFefo()` để trừ `inventory_batches.quantity_remaining` và ghi `stock_movements`.
- Kho legacy vẫn tồn tại: `/admin/warehouse`, `/admin/medicinal-herbs`, `/admin/packaged-products` dùng `medicinal_herbs.stock_quantity` và `packaged_products.stock_quantity`.
- Chatbot public vẫn đọc thêm `medicinal_herbs` và `herb_dictionary_entries` để tra cứu thông tin, không dùng chúng làm nguồn trừ kho FEFO.

## D. Thông tin để vẽ sơ đồ

### D1. Sơ đồ phân rã chức năng

File Mermaid: `docs/diagrams_chuong_2/01_phan_ra_chuc_nang.mmd`

Nội dung nên có:
- Public/user: trang chủ, tài khoản, bài viết, từ điển dược liệu, chatbot public.
- Admin/staff: dashboard, bệnh nhân, bệnh án, đơn điều trị, kho, bài viết/bình luận, từ điển, tài khoản/phân quyền, AI hỗ trợ thầy thuốc.
- Ghi chú riêng: 5 bài Ngũ hành là route tĩnh, không phải bài viết DB.

### D2. Sơ đồ mức ngữ cảnh

File Mermaid: `docs/diagrams_chuong_2/02_so_do_muc_ngu_canh.mmd`

Tác nhân ngoài:
- Khách truy cập.
- Người dùng đăng nhập.
- Staff/thầy thuốc.
- Admin.
- Google Gemini API.
- SMTP Email.

Hệ thống trung tâm:
- Website quản lý nhà thuốc YHCT AmaTrung.

### D3. DFD mức 0

File Mermaid: `docs/diagrams_chuong_2/03_dfd_muc_0.mmd`

Các tiến trình chính:
1. Tài khoản và phân quyền.
2. Nội dung công khai.
3. Bệnh nhân và bệnh án.
4. Đơn điều trị và kho.
5. AI tra cứu và hỗ trợ.
6. Dashboard và báo cáo.

Kho dữ liệu:
- `users`, `roles`, `permissions`
- `articles`, `comments`, `article_likes`
- `herb_dictionary_entries`, `images`, `favorites`
- `patients`, `medical_records`, `attachments`
- `prescriptions`, `prescription_items`, `appointments`
- `inventory_items`, `inventory_batches`, `stock_movements`
- legacy `medicinal_herbs`, `packaged_products`, `therapy_services`
- `ai_suggestion_logs`, `contact_messages`

### D4. DFD mức 1 - Quản lý bệnh nhân và hồ sơ bệnh án

File Mermaid: `docs/diagrams_chuong_2/04_dfd_muc_1_benh_nhan_benh_an.mmd`

Luồng đúng:
- Staff/Admin nhập/tìm bệnh nhân.
- Hệ thống kiểm tra trùng, import hồ sơ giấy nếu có.
- Tạo/cập nhật bệnh án từ `patients`.
- Lưu `medical_records`, file đính kèm vào `medical_record_attachments`.
- Có thể tạo lịch tái khám ở `appointments`.

### D5. DFD mức 1 - Lập đơn điều trị và cập nhật kho

File Mermaid: `docs/diagrams_chuong_2/05_dfd_muc_1_don_dieu_tri_kho.mmd`

Luồng đúng:
- Mở trang kê đơn từ bệnh án.
- Chọn thuốc thang uống từ `inventory_items`.
- Chọn thuốc/chế phẩm dùng ngoài từ `inventory_items` có `usage_route = external`.
- Chọn dịch vụ trị liệu từ `therapy_services`.
- Lưu `prescriptions` và `prescription_items`.
- Chỉ khi bấm cấp thuốc/dispense mới trừ `inventory_batches` theo FEFO và ghi `stock_movements`.
- Dịch vụ trị liệu không trừ kho.

### D6. DFD mức 1 - Hai luồng AI

File Mermaid: `docs/diagrams_chuong_2/06_dfd_muc_1_ai.mmd`

Phân biệt rõ:
- Chatbot public: nhận câu hỏi text, chuẩn hóa, tìm `articles`, `medicinal_herbs`, `herb_dictionary_entries`, gọi Gemini, trả lời tham khảo. Không truy cập bệnh án cá nhân.
- AI thầy thuốc: chỉ nhận `medical_record_id`, kiểm tra quyền, dựng payload ẩn danh từ bệnh án và kho mới, gọi Gemini, post-verify, ghi `ai_suggestion_logs`. Không tự tạo đơn và không tự trừ kho.

### D7. Sơ đồ ERD

File Mermaid: `docs/diagrams_chuong_2/07_erd.mmd`

ERD nên tập trung vào các cụm:
- User/role/permission.
- Bệnh nhân/bệnh án/đơn điều trị.
- Kho mới FEFO.
- Bài viết/bình luận/yêu thích.
- Từ điển dược liệu.
- AI log.
- Các bảng legacy/tương thích.

Chức năng chỉ là định hướng/chưa triển khai đầy đủ:
- `retail_orders` và `retail_order_items` còn bảng/model nhưng không thấy route/controller active cho bán lẻ trong `routes/web.php`.
- 5 bài Ngũ hành là view tĩnh, không thuộc quản lý bài viết DB.
- `patient_user_links` có bảng/model nhưng chưa thấy luồng UI quản trị rõ trong route hiện tại.

## E. Danh sách ảnh giao diện cần chụp

### E1. Thiết kế giao diện Chương 2

Nên chụp:
- Trang chủ: `/`
- Trang bài viết: `/bai-viet`
- Chi tiết bài viết: `/bai-viet/{slug}`
- Từ điển dược liệu public: `/tu-dien-thuoc-nam`
- Chatbot public mở ở góc màn hình: `/` hoặc `/bai-viet`
- Đăng nhập: `/login`
- Đăng ký: `/register`
- Quên mật khẩu: `/forgot-password`
- Dashboard người dùng: `/dashboard`
- Dashboard admin: `/admin/dashboard`
- Sidebar admin để thể hiện nhóm chức năng.

### E2. Kết quả xây dựng chức năng ở chương sau

Nên chụp:
- Quản lý bệnh nhân: `/admin/patients`
- Thêm bệnh nhân: `/admin/patients/create`
- Chi tiết bệnh nhân: `/admin/patients/{id}`
- Danh sách bệnh án: `/admin/medical-records`
- Tạo bệnh án theo bệnh nhân: `/admin/patients/{patient}/medical-records/create`
- Chi tiết bệnh án có panel AI: `/admin/medical-records/{id}`
- Kê đơn điều trị: `/admin/medical-records/{medicalRecord}/prescriptions/create`
- Danh sách đơn điều trị: `/admin/prescriptions`
- In/chi tiết đơn: `/admin/prescriptions/{id}` và `/admin/prescriptions/{id}/print`
- Kho mới: `/admin/inventory`
- Chi tiết lô kho: `/admin/inventory/{id}`
- Kho legacy tổng hợp: `/admin/warehouse`
- Dịch vụ trị liệu/bài thuốc mẫu: `/admin/treatment-templates`
- Bài viết admin: `/admin/articles`
- Soạn bài viết: `/admin/articles/create`
- Bình luận admin: `/admin/comments`
- Từ điển dược liệu admin: `/admin/herb-dictionary`
- Tạo/sửa mục từ điển: `/admin/herb-dictionary/create`, `/admin/herb-dictionary/{id}/edit`
- Tài khoản nhân viên: `/admin/users`
- Tạo tài khoản nhân viên và cấp quyền: `/admin/users/create`
- Lịch hẹn: `/admin/appointments`
- Yêu cầu hỗ trợ: `/admin/contact-messages`

## F. Cảnh báo sai lệch cần tránh trong khóa luận

1. **Không ghi database chỉ có 8 bảng.**  
   DB thực tế có 43 bảng, trong đó nhiều bảng phát sinh cho kho FEFO, từ điển, AI, phân quyền, dịch vụ trị liệu, liên hệ, like, lịch hẹn.

2. **Kho hiện có hai lớp: mới và legacy.**  
   `/admin/inventory` và cấp thuốc theo đơn dùng `inventory_items`, `inventory_batches`, `stock_movements`.  
   `/admin/warehouse`, `/admin/medicinal-herbs`, `/admin/packaged-products` vẫn dùng `medicinal_herbs.stock_quantity` và `packaged_products.stock_quantity`.

3. **Không mô tả phiếu/bán lẻ là luồng chính nếu route chưa active.**  
   `retail_orders`/`retail_order_items` còn bảng/model legacy, nhưng không thấy route CRUD active trong `routes/web.php`.

4. **Thuốc dùng ngoài đã có trong đơn điều trị nhưng cần mô tả đúng nguồn.**  
   Luồng kê đơn hiện ưu tiên `inventory_items` có `usage_route = external`; legacy `packaged_products` vẫn tồn tại nhưng không phải nguồn chính của kho FEFO.

5. **Dịch vụ trị liệu là hạng mục không trừ kho.**  
   Khi vào đơn, dịch vụ trị liệu lưu ở `prescription_items` với `affects_stock = false`.

6. **AI có hai loại khác nhau.**  
   Chatbot public chỉ tra cứu bài viết/dược liệu và trả lời tham khảo. AI nội bộ cho thầy thuốc đọc bệnh án ẩn danh, gợi ý tham khảo và ghi log. Không gộp hai AI này làm một trong sơ đồ.

7. **AI không tự tạo đơn, không tự lưu đơn, không tự trừ tồn kho.**  
   Trừ kho chỉ xảy ra khi `PrescriptionService::dispensePrescription()` gọi `InventoryService::deductStockFefo()`.

8. **Từ điển dược liệu không phải bảng `medicinal_herbs`.**  
   Từ điển dùng `herb_dictionary_entries`, `herb_dictionary_images`, `herb_dictionary_favorites`. `medicinal_herbs` là kho/legacy và cũng được chatbot đọc làm nguồn thông tin.

9. **5 bài Kim Mộc Thủy Hỏa Thổ không nằm trong bảng bài viết.**  
   Chúng là route closure dùng chung view `bai-viet/ngu-hanh.blade.php`, nên không sửa bằng trang admin bài viết.

10. **Phân quyền Staff có điểm giao thoa Spatie và legacy.**  
    Code dùng cả `legacy_permissions_json` và Spatie. Một số permission trong middleware chưa trùng tên với permission đang có trong DB/seeder, nên khi mô tả cần ghi theo cơ chế hiện tại thay vì giả định chỉ dùng một hệ thống.

11. **Unknown expiry cần mô tả thận trọng.**  
    `InventoryItem::available_batches` loại batch không có `expiry_date` khỏi tồn khả dụng/FEFO. `AiClinicalContextBuilder` cũng yêu cầu `expiry_date` không null và còn hạn. Riêng `InventoryService::deductStockFefo()` có nhánh cho phép `expiry_date = null` nếu `status = available`, đây là điểm nên kiểm tra tiếp nếu viết phần kiểm thử kho thật.

12. **Dashboard hiện là thống kê thật.**  
    `DashboardController` tính số bệnh nhân, lượt khám, đơn điều trị, biểu đồ theo ngày/tháng từ DB. Không nên ghi là dữ liệu mẫu tĩnh.

## Danh sách file sơ đồ đã xuất

- `docs/diagrams_chuong_2/01_phan_ra_chuc_nang.mmd`
- `docs/diagrams_chuong_2/02_so_do_muc_ngu_canh.mmd`
- `docs/diagrams_chuong_2/03_dfd_muc_0.mmd`
- `docs/diagrams_chuong_2/04_dfd_muc_1_benh_nhan_benh_an.mmd`
- `docs/diagrams_chuong_2/05_dfd_muc_1_don_dieu_tri_kho.mmd`
- `docs/diagrams_chuong_2/06_dfd_muc_1_ai.mmd`
- `docs/diagrams_chuong_2/07_erd.mmd`
