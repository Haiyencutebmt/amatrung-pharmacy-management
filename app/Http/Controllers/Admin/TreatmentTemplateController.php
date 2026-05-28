<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SamplePrescription;
use App\Models\SamplePrescriptionItem;
use App\Models\TherapyService;
use App\Models\MedicinalHerb;
use Illuminate\Support\Facades\DB;

class TreatmentTemplateController extends Controller
{
    public function index(Request $request)
    {
        // Xử lý Dịch vụ trị liệu
        $serviceQuery = TherapyService::query();
        if ($request->filled('search_service')) {
            $searchService = $request->search_service;
            $serviceQuery->where(function ($q) use ($searchService) {
                $q->where('name', 'like', "%{$searchService}%")
                  ->orWhere('default_instruction', 'like', "%{$searchService}%");
            });
        }
        $services = $serviceQuery->orderBy('name')->paginate(4);

        return view('admin.treatment_templates.index', compact('services'));
    }

    private function seedInitialSamplePrescriptions()
    {
        $initialFormulas = [
            'Bát Trân Thang' => [
                'condition' => 'Khí huyết lưỡng hư (suy nhược, thiếu máu, da xanh xao)',
                'instruction' => 'Sắc ngày 1 thang, uống chia 2 lần sáng/chiều sau ăn ấm.',
                'herbs' => [
                    'Đảng sâm' => 12, 'Bạch truật' => 12, 'Phục linh' => 12, 'Cam thảo' => 4,
                    'Đương quy' => 12, 'Xuyên khung' => 8, 'Bạch thược' => 12, 'Thục địa' => 12
                ]
            ],
            'Thập Toàn Đại Bổ' => [
                'condition' => 'Suy nhược cơ thể nặng, kém ăn, mất ngủ, sau ốm dậy',
                'instruction' => 'Sắc ngày 1 thang, uống chia 2 lần ấm sáng/tối.',
                'herbs' => [
                    'Đảng sâm' => 12, 'Bạch truật' => 12, 'Phục linh' => 12, 'Cam thảo' => 4,
                    'Đương quy' => 12, 'Xuyên khung' => 8, 'Bạch thược' => 12, 'Thục địa' => 12,
                    'Hoàng kỳ' => 12, 'Nhục quế' => 4
                ]
            ],
            'Độc Hoạt Tang Ký Sinh' => [
                'condition' => 'Đau nhức khớp mạn tính, thoái hóa khớp, đau thần kinh tọa thể phong hàn thấp tý',
                'instruction' => 'Sắc ngày 1 thang, uống ấm sau ăn chia 2 lần.',
                'herbs' => [
                    'Độc hoạt' => 8, 'Tang ký sinh' => 12, 'Phòng phong' => 8, 'Tần giao' => 8,
                    'Ngưu tất' => 12, 'Đỗ trọng' => 12, 'Đương quy' => 12, 'Bạch thược' => 12,
                    'Xuyên khung' => 8, 'Đảng sâm' => 12, 'Phục linh' => 12, 'Cam thảo' => 4,
                    'Nhục quế' => 4, 'Tế tân' => 2
                ]
            ],
            'Quyên Tý Thang' => [
                'condition' => 'Đau vai gáy, tê bì tay chân do lạnh',
                'instruction' => 'Sắc uống ngày 1 thang, uống ấm.',
                'herbs' => [
                    'Khương hoạt' => 8, 'Độc hoạt' => 8, 'Quế chi' => 8, 'Tần giao' => 8,
                    'Đương quy' => 12, 'Xuyên khung' => 8, 'Một dược' => 6, 'Nhũ hương' => 6,
                    'Phòng phong' => 8, 'Mộc hương' => 6, 'Cam thảo' => 4
                ]
            ],
            'Tiêu Dao Tán' => [
                'condition' => 'Can uất tỳ hư, ngực sườn đầy tức, kinh nguyệt không đều, mệt mỏi',
                'instruction' => 'Sắc uống ngày 1 thang, uống lúc ấm.',
                'herbs' => [
                    'Sài hồ' => 12, 'Đương quy' => 12, 'Bạch thược' => 12, 'Bạch truật' => 12,
                    'Phục linh' => 12, 'Cam thảo' => 4, 'Sinh khương' => 4, 'Bạc hà' => 4
                ]
            ],
            'Tứ Quân Tử Thang' => [
                'condition' => 'Tỳ vị hư nhược, đầy bụng, đi ngoài lỏng, chán ăn',
                'instruction' => 'Sắc ngày 1 thang, uống trước ăn.',
                'herbs' => [
                    'Đảng sâm' => 12, 'Bạch truật' => 12, 'Phục linh' => 12, 'Cam thảo' => 4
                ]
            ],
            'Lục Vị Địa Hoàng Hoàn' => [
                'condition' => 'Can thận âm hư, triều nhiệt, ra mồ hôi trộm, đau lưng mỏi gối',
                'instruction' => 'Sắc uống ngày 1 thang, chia 2 lần sáng/chiều.',
                'herbs' => [
                    'Thục địa' => 24, 'Sơn thù' => 12, 'Hoài sơn' => 12,
                    'Trạch tả' => 9, 'Phục linh' => 9, 'Đan bì' => 9
                ]
            ]
        ];

        foreach ($initialFormulas as $name => $data) {
            DB::transaction(function () use ($name, $data) {
                $sample = SamplePrescription::create([
                    'name' => $name,
                    'suggested_condition' => $data['condition'],
                    'usage_instruction' => $data['instruction'],
                    'preparation_type' => 'Thuốc sắc',
                    'default_packages' => 10,
                    'notes' => 'Uống khi thuốc còn ấm. Kiêng ăn đồ cay nóng, tanh, sống trong quá trình dùng thuốc.',
                ]);

                foreach ($data['herbs'] as $herbName => $qty) {
                    $herb = MedicinalHerb::where('name', 'like', "%{$herbName}%")->first();
                    if (!$herb) {
                        $herb = MedicinalHerb::create([
                            'name' => $herbName,
                            'category' => 'Dược liệu rời',
                            'usage_type' => 'Sắc uống',
                            'description' => 'Khởi tạo tự động cho bài thuốc mẫu',
                            'unit' => 'g',
                            'stock_quantity' => 1000.00,
                            'status' => 'active',
                        ]);
                    }

                    SamplePrescriptionItem::create([
                        'sample_prescription_id' => $sample->id,
                        'medicinal_herb_id' => $herb->id,
                        'quantity' => $qty,
                    ]);
                }
            });
        }
    }
}
