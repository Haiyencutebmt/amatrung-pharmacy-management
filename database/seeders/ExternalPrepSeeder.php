<?php

namespace Database\Seeders;

use App\Models\MedicinalHerb;
use Illuminate\Database\Seeder;

class ExternalPrepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'Bó thuốc nam',
                'category' => 'Thuốc dùng ngoài',
                'usage_type' => 'Đắp ngoài',
                'description' => 'Thuốc bó nam dùng để bó ngoài da giảm đau xương khớp, chấn thương.',
                'unit' => 'gói',
                'stock_quantity' => 1000,
                'expiry_date' => '2026-12-31',
                'status' => 'active',
            ],
            [
                'name' => 'Lọ rượu thuốc xoa bóp',
                'category' => 'Thuốc dùng ngoài',
                'usage_type' => 'Xoa bóp',
                'description' => 'Rượu thuốc dùng để xoa bóp ngoài da kết hợp với bó thuốc nam.',
                'unit' => 'lọ',
                'stock_quantity' => 500,
                'expiry_date' => '2026-12-31',
                'status' => 'active',
            ],
        ];

        foreach ($items as $item) {
            MedicinalHerb::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
