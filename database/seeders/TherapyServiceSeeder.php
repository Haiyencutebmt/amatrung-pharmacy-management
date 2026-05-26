<?php

namespace Database\Seeders;

use App\Models\TherapyService;
use Illuminate\Database\Seeder;

class TherapyServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Nắn chỉnh khớp xương',
                'default_sessions' => 3,
                'default_instruction' => 'Nghỉ ngơi 15 phút sau khi nắn bóp, kiêng tắm nước lạnh trong 2 giờ.',
                'status' => 'active',
            ],
            [
                'name' => 'Xoa bóp bấm huyệt trị liệu',
                'default_sessions' => 3,
                'default_instruction' => 'Tránh vận động quá sức ngay sau khi xoa bóp.',
                'status' => 'active',
            ],
            [
                'name' => 'Cứu ngải cứu / Ôn châm',
                'default_sessions' => 3,
                'default_instruction' => 'Giữ ấm vùng cứu, tránh gió lùa.',
                'status' => 'active',
            ],
            [
                'name' => 'Theo dõi phục hồi vận động',
                'default_sessions' => 1,
                'default_instruction' => 'Tập luyện nhẹ nhàng theo hướng dẫn tại nhà.',
                'status' => 'active',
            ],
            [
                'name' => 'Châm cứu thông kinh hoạt lạc',
                'default_sessions' => 3,
                'default_instruction' => 'Nghỉ ngơi yên tĩnh trong suốt quá trình châm cứu.',
                'status' => 'active',
            ],
        ];

        foreach ($services as $service) {
            TherapyService::updateOrCreate(
                ['name' => $service['name']],
                $service
            );
        }
    }
}
