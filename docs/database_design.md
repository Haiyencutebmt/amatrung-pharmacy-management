# Database Design - AmaTrung

Database gồm 8 bảng chính:

1. users
2. patients
3. medical_records
4. prescriptions
5. prescription_items
6. medicinal_herbs
7. articles
8. comments

Quan hệ:
- users 1-n patients
- users 1-n articles
- users 1-n comments
- patients 1-n medical_records
- medical_records 1-n prescriptions
- prescriptions 1-n prescription_items
- medicinal_herbs 1-n prescription_items
- articles 1-n comments

Bảng patients:
- Không unique phone.
- Có patient_code unique.
- Có thông tin người giám hộ: guardian_name, guardian_phone, relationship.

Bảng medicinal_herbs:
- Dùng để quản lý cả dược liệu rời và chế phẩm thuốc.
- Có category, usage_type, unit, stock_quantity, expiry_date, warning_note, status.
- Không có price, revenue, invoice, payment.

AI:
- AI chỉ hỗ trợ tham khảo.
- Không thay thế thầy thuốc.
- Thầy thuốc quyết định cuối cùng.