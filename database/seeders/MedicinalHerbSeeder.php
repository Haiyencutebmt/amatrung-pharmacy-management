<?php

namespace Database\Seeders;

use App\Models\MedicinalHerb;
use Illuminate\Database\Seeder;

class MedicinalHerbSeeder extends Seeder
{
    public function run(): void
    {
        $herbs = [
            // Dược liệu bốc thuốc
            ['name' => 'Hoàng Kỳ',        'category' => 'Dược liệu bốc thuốc',  'usage_type' => 'Sắc uống',    'description' => 'Bổ khí, tăng cường miễn dịch, hỗ trợ điều trị mệt mỏi suy nhược.', 'unit' => 'gram',   'stock_quantity' => 5000, 'expiry_date' => '2026-12-31', 'status' => 'active'],
            ['name' => 'Đương Quy',        'category' => 'Dược liệu bốc thuốc',  'usage_type' => 'Sắc uống',    'description' => 'Bổ huyết, hoạt huyết, điều kinh.', 'unit' => 'gram',   'stock_quantity' => 3000, 'expiry_date' => '2026-12-31', 'status' => 'active'],
            ['name' => 'Cam Thảo',         'category' => 'Dược liệu bốc thuốc',  'usage_type' => 'Sắc uống',    'description' => 'Bổ tỳ vị, giải độc, điều hòa các vị thuốc.', 'unit' => 'kg',     'stock_quantity' => 10, 'expiry_date' => '2026-12-31', 'status' => 'active'],
            ['name' => 'Bạch Truật',       'category' => 'Dược liệu bốc thuốc',  'usage_type' => 'Sắc uống',    'description' => 'Kiện tỳ táo thấp, ích khí.', 'unit' => 'kg',     'stock_quantity' => 5,  'expiry_date' => '2026-11-30', 'status' => 'active'],
            ['name' => 'Thục Địa',         'category' => 'Dược liệu bốc thuốc',  'usage_type' => 'Sắc uống',    'description' => 'Bổ âm, dưỡng huyết, hỗ trợ thận.', 'unit' => 'gram',   'stock_quantity' => 2000,  'expiry_date' => '2026-10-31', 'status' => 'active'],

            // Thuốc gói có sẵn
            ['name' => 'Trà Dây Lọc',      'category' => 'Thuốc gói có sẵn',     'usage_type' => 'Pha uống',    'description' => 'Hỗ trợ viêm loét dạ dày, tá tràng.', 'unit' => 'gói',    'stock_quantity' => 150, 'expiry_date' => '2026-06-30', 'status' => 'active'],
            ['name' => 'Cảm Mạo Xuyên Bối','category' => 'Thuốc gói có sẵn',     'usage_type' => 'Pha uống',    'description' => 'Hỗ trợ cảm cúm, giảm ho, long đờm.', 'unit' => 'gói',    'stock_quantity' => 200, 'expiry_date' => '2026-08-30', 'status' => 'active'],

            // Thuốc lọ gia công
            ['name' => 'Cao Gắm (Gia công)','category' => 'Thuốc lọ gia công',   'usage_type' => 'Uống',        'description' => 'Hỗ trợ giảm acid uric, điều trị gout.', 'unit' => 'lọ',     'stock_quantity' => 30, 'expiry_date' => '2026-09-30', 'status' => 'active'],
            ['name' => 'Viên Nghệ Mật Ong','category' => 'Thuốc lọ gia công',    'usage_type' => 'Uống',        'description' => 'Hỗ trợ tiêu hóa, dạ dày.', 'unit' => 'lọ',     'stock_quantity' => 50,  'expiry_date' => '2025-06-30', 'status' => 'active'],

            // Thuốc dùng ngoài
            ['name' => 'Cồn Xoa Bóp Khớp', 'category' => 'Thuốc dùng ngoài',     'usage_type' => 'Xoa bóp',     'description' => 'Giảm đau nhức xương khớp, tê bì tay chân.', 'unit' => 'chai',   'stock_quantity' => 45, 'expiry_date' => '2026-12-31', 'warning_note' => 'Không được uống. Tránh xa tầm tay trẻ em.', 'status' => 'active'],
            ['name' => 'Mỡ Tử Vân',        'category' => 'Thuốc dùng ngoài',     'usage_type' => 'Bôi ngoài',   'description' => 'Dưỡng da, mờ sẹo, hỗ trợ bỏng nhẹ.', 'unit' => 'lọ',     'stock_quantity' => 25, 'expiry_date' => '2026-12-31', 'status' => 'active'],
            ['name' => 'Bó thuốc nam',     'category' => 'Thuốc dùng ngoài',     'usage_type' => 'Đắp ngoài',   'description' => 'Thuốc bó nam dùng để bó ngoài da giảm đau xương khớp, chấn thương.', 'unit' => 'gói',    'stock_quantity' => 1000, 'expiry_date' => '2026-12-31', 'status' => 'active'],
            ['name' => 'Lọ rượu thuốc xoa bóp', 'category' => 'Thuốc dùng ngoài', 'usage_type' => 'Xoa bóp',     'description' => 'Rượu thuốc dùng để xoa bóp ngoài da kết hợp với bó thuốc nam.', 'unit' => 'lọ',     'stock_quantity' => 500,  'expiry_date' => '2026-12-31', 'status' => 'active'],


            // Lá xông / tắm
            ['name' => 'Lá Xông Cảm',      'category' => 'Lá xông/tắm',          'usage_type' => 'Xông',        'description' => 'Xông giải cảm, giải độc, toát mồ hôi.', 'unit' => 'bó',     'stock_quantity' => 100, 'expiry_date' => '2025-12-31', 'status' => 'active'],
            ['name' => 'Lá Tắm Sản Phụ',   'category' => 'Lá xông/tắm',          'usage_type' => 'Tắm',         'description' => 'Làm sạch cơ thể, phục hồi sức khỏe sau sinh.', 'unit' => 'bó',     'stock_quantity' => 60,  'expiry_date' => '2026-05-30', 'status' => 'active'],

            // Thuốc ngâm rượu
            ['name' => 'Thang Ngâm Rượu Bổ Thận', 'category' => 'Thuốc ngâm rượu', 'usage_type' => 'Ngâm rượu uống','description' => 'Bổ thận tráng dương, tăng cường sinh lực.', 'unit' => 'thang',  'stock_quantity' => 15, 'expiry_date' => '2026-12-31', 'status' => 'active'],
            ['name' => 'Ba Kích Tím',      'category' => 'Thuốc ngâm rượu',      'usage_type' => 'Ngâm rượu',   'description' => 'Bổ thận, cường gân cốt.', 'unit' => 'kg',     'stock_quantity' => 8, 'expiry_date' => '2026-12-31', 'status' => 'active'],

            // Dược liệu bán lẻ
            ['name' => 'Kỷ Tử',             'category' => 'Dược liệu bán lẻ',     'usage_type' => 'Pha trà, Nấu ăn', 'description' => 'Sáng mắt, bổ gan thận, tăng cường đề kháng.', 'unit' => 'gram',   'stock_quantity' => 1500, 'expiry_date' => '2026-12-31', 'status' => 'active'],
            ['name' => 'Táo Đỏ',           'category' => 'Dược liệu bán lẻ',     'usage_type' => 'Pha trà, Nấu ăn', 'description' => 'Bổ máu, an thần, kiện tỳ.', 'unit' => 'kg',     'stock_quantity' => 20, 'expiry_date' => '2026-12-31', 'status' => 'active'],
        ];

        foreach ($herbs as $herb) {
            MedicinalHerb::create($herb);
        }

        $this->command->info('✅ Đã tạo các danh mục dược liệu phân loại theo thực tế');
    }
}
