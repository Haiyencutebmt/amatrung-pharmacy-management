<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiPrescriptionService
{
    private array $availableHerbs = [];

    private function getAvailableHerbs(): array
    {
        if (empty($this->availableHerbs)) {
            $this->availableHerbs = \App\Models\MedicinalHerb::where('status', 'active')
                ->pluck('name')
                ->map(fn($name) => mb_strtolower(trim($name)))
                ->toArray();
        }
        return $this->availableHerbs;
    }

    private function filterHerbs(array $herbs): array
    {
        $available = $this->getAvailableHerbs();
        return array_filter($herbs, function ($herb) use ($available) {
            $name = mb_strtolower(trim($herb['herb_name']));
            foreach ($available as $avail) {
                if (str_contains($avail, $name) || str_contains($name, $avail)) {
                    return true;
                }
            }
            return false;
        });
    }

    /**
     * Gợi ý phác đồ điều trị dựa trên triệu chứng và chẩn đoán toàn diện
     */
    public function suggest(
        string $symptoms,
        string $diagnosis,
        ?string $treatmentMethod = null,
        ?string $patientSummary = null,
        ?string $caseType = null,
        array $context = []
    ): array {
        $caseTypeLower = $caseType ? mb_strtolower(trim($caseType)) : null;
        $isMusculoskeletal = $caseTypeLower === 'musculoskeletal';
        $isCombined = $caseTypeLower === 'combined' || $caseTypeLower === 'both';
        $isNormal = $caseTypeLower === 'general' || $caseTypeLower === 'normal';

        // Ưu tiên tìm bài thuốc mẫu trong DB (chỉ áp dụng nếu không phải ca chỉ khám xương khớp)
        $sample = null;
        if (!$isMusculoskeletal) {
            $sample = \App\Models\SamplePrescription::with('items.medicinalHerb')
                ->where(function($q) use ($diagnosis, $symptoms) {
                    if ($diagnosis) {
                        $q->where('suggested_condition', 'like', "%{$diagnosis}%")
                          ->orWhere('name', 'like', "%{$diagnosis}%");
                    }
                    if ($symptoms) {
                        $q->orWhere('suggested_condition', 'like', "%{$symptoms}%")
                          ->orWhere('name', 'like', "%{$symptoms}%");
                    }
                })
                ->first();
        }

        if ($sample) {
            $oralHerbs = [];
            foreach ($sample->items as $item) {
                if ($item->medicinalHerb && $item->medicinalHerb->status === 'active') {
                    $oralHerbs[] = [
                        'herb_name' => $item->medicinalHerb->name,
                        'dosage' => (string) $item->quantity,
                        'usage_note' => $item->note ?? 'Gợi ý từ bài thuốc mẫu'
                    ];
                }
            }
            
            if (!empty($oralHerbs)) {
                return [
                    'status' => 'success',
                    'suggested_formula_name' => $sample->name,
                    'suggested_condition' => $sample->suggested_condition ?: $diagnosis,
                    'reasoning' => 'Đã tìm thấy bài thuốc mẫu phù hợp trong hệ thống nhà thuốc dựa trên triệu chứng và chẩn đoán.',
                    'treatment_method' => 'Áp dụng bài thuốc mẫu.',
                    'course_days' => 5,
                    'follow_up_days' => 5,
                    'patient_guidelines' => $sample->usage_instruction ?: 'Theo dõi và uống thuốc đúng chỉ định.',
                    'internal_notes' => 'Bài thuốc mẫu được lấy trực tiếp từ dữ liệu của nhà thuốc.',
                    'safety_warning' => '',
                    'oral_herbs' => $oralHerbs,
                    'external_herbs' => [],
                    'therapy_services' => []
                ];
            }
        }

        // 1. Kiểm tra cấu hình Gemini API
        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model', 'gemini-1.5-flash');

        if (!$apiKey) {
            Log::info('Gemini API Key is not configured. Falling back to local heuristics.');
            return $this->getMockHeuristics($symptoms, $diagnosis, $caseType, $context);
        }

        try {
            // 2. Tính toán BMI nếu có cân nặng & chiều cao
            $weight = isset($context['weight']) ? (float) $context['weight'] : null;
            $height = isset($context['height']) ? (float) $context['height'] : null;
            $gender = isset($context['gender']) ? trim($context['gender']) : 'N/A';
            $age = isset($context['age']) ? (int) $context['age'] : null;

            $bmi = null;
            $bmiCategory = 'Bình thường';
            if ($weight && $height) {
                $heightInMeters = $height / 100;
                $bmi = $weight / ($heightInMeters * $heightInMeters);
                if ($bmi < 18.5) {
                    $bmiCategory = 'Gầy';
                } elseif ($bmi < 25) {
                    $bmiCategory = 'Bình thường';
                } elseif ($bmi < 30) {
                    $bmiCategory = 'Thừa cân';
                } else {
                    $bmiCategory = 'Béo phì';
                }
            }

            // 3. Lấy mẫu lịch sử kê đơn thành công (Few-Shot examples)
            $historicalPrescriptions = \App\Models\Prescription::with(['items', 'medicalRecord'])
                ->whereHas('medicalRecord', function ($q) use ($caseType) {
                    if ($caseType) {
                        $q->where('case_type', $caseType);
                    }
                })
                ->orderBy('id', 'desc')
                ->take(3)
                ->get();

            $examplesText = "";
            $idx = 1;
            foreach ($historicalPrescriptions as $hist) {
                $hRecord = $hist->medicalRecord;
                if (!$hRecord) continue;
                $hHerbs = $hist->items->where('item_type', 'formula_herb')->map(fn($item) => $item->display_name . ' (' . floatval($item->quantity) . $item->unit . ')')->join(', ');
                $hExternals = $hist->items->where('item_type', 'external_product')->map(fn($item) => $item->display_name . ' (' . floatval($item->quantity) . $item->unit . ')')->join(', ');
                $hServices = $hist->items->whereIn('item_type', ['service', 'therapy_service'])->map(fn($item) => $item->display_name . ' (' . $item->sessions . ' buổi)')->join(', ');

                $examplesText .= "Ví dụ {$idx}:\n";
                $examplesText .= "- Triệu chứng: {$hRecord->symptoms}\n";
                $examplesText .= "- Chẩn đoán: {$hRecord->diagnosis}\n";
                $examplesText .= "- Tên bài thuốc: " . ($hist->note ?: 'N/A') . "\n";
                if ($hHerbs) $examplesText .= "- Vị thuốc uống: {$hHerbs}\n";
                if ($hExternals) $examplesText .= "- Thuốc dùng ngoài: {$hExternals}\n";
                if ($hServices) $examplesText .= "- Trị liệu ngoại khoa: {$hServices}\n";
                $examplesText .= "\n";
                $idx++;
            }

            // 4. Lấy danh sách vị thuốc khả dụng trong kho
            $activeHerbsList = \App\Models\MedicinalHerb::where('status', 'active')
                ->select(['name', 'unit', 'stock_quantity'])
                ->get()
                ->map(fn($h) => "- {$h->name} (Đơn vị: {$h->unit}, Tồn kho: " . floatval($h->stock_quantity) . ")")
                ->join("\n");

            // 5. Xây dựng prompt cho Gemini
            $promptText = "Bạn là một Trợ lý Y khoa AI có chuyên môn cao về Y học Cổ truyền và Vật lý trị liệu tại Nhà thuốc Đông Y AmaTrung.
Nhiệm vụ của bạn là đưa ra đề xuất phác đồ điều trị và đơn thuốc cá nhân hóa tối ưu cho bệnh nhân dựa trên các thông tin lâm sàng sau:

THÔNG TIN BỆNH NHÂN:
- Triệu chứng: {$symptoms}
- Chẩn đoán: {$diagnosis}
- Loại ca bệnh: {$caseType}
- Giới tính: {$gender}
- Tuổi: " . ($age ? "{$age} tuổi" : "N/A") . "
- Chỉ số thể trạng: Cân nặng " . ($weight ? "{$weight} kg" : "N/A") . ", Chiều cao " . ($height ? "{$height} cm" : "N/A") . ". BMI: " . ($bmi ? number_format($bmi, 1) . " ({$bmiCategory})" : "Không rõ") . "

QUY TẮC PHÁC ĐỒ BẮT BUỘC:
1. LOẠI CA BỆNH VÀ PHẠM VI ĐIỀU TRỊ (CỰC KỲ QUAN TRỌNG):
" . ($isMusculoskeletal ? "- Đây là CA BỆNH CHỈ KHÁM XƯƠNG KHỚP/TRỊ LIỆU. Bạn CHỈ ĐƯỢC đề xuất các sản phẩm dùng ngoài như Rượu xoa bóp/Bó thuốc nam (external_herbs) và Dịch vụ Trị liệu như Châm cứu, Xoa bóp bấm huyệt, Nắn chỉnh cột sống (therapy_services). Bạn TUYỆT ĐỐI KHÔNG ĐƯỢC kê bất kỳ vị thuốc sắc nào (oral_herbs bắt buộc phải là mảng rỗng [], suggested_formula_name bắt buộc phải là null)." : ($isNormal ? "- Đây là CA BỆNH CHỈ KHÁM NỘI KHOA/THÔNG THƯỜNG (UỐNG THUỐC). Bạn CHỈ ĐƯỢC đề xuất các vị thuốc sắc (oral_herbs). Bạn TUYỆT ĐỐI KHÔNG ĐƯỢC đề xuất sản phẩm dùng ngoài (external_herbs bắt buộc phải là mảng rỗng []) hoặc dịch vụ trị liệu nào (therapy_services bắt buộc phải là mảng rỗng [])." : "- Đây là CA BỆNH KHÁM KẾT HỢP CẢ HAI. Bạn được phép đề xuất cả hai phương pháp bao gồm thuốc sắc (oral_herbs) VÀ các liệu pháp ngoài/vật lý trị liệu (external_herbs, therapy_services).")) . "

2. CHỈ được chọn các vị thuốc sắc từ danh sách dược liệu đang có sẵn trong kho dưới đây. KHÔNG tự ý kê dược liệu khác không có trong danh sách.
Danh sách dược liệu khả dụng trong kho:
{$activeHerbsList}

3. ĐIỀU CHỈNH BMI (RẤT QUAN TRỌNG):
" . ($bmi && $bmi >= 25 ? "- BỆNH NHÂN THỪA CÂN/BÉO PHÌ: Bạn PHẢI hạn chế tối đa các vị thuốc ngọt, nịch trệ (như Cam Thảo, Đại Táo - nếu dùng chỉ tối đa 2-4g hoặc loại bỏ hoàn toàn). Lời dặn bệnh nhân (patient_guidelines) PHẢI có hướng dẫn kiêng đường, giảm tinh bột ngọt để tránh tăng cân và tích nước." : "- Nếu bệnh nhân có dấu hiệu thừa cân hoặc béo phì, cần giảm thiểu Cam Thảo, Đại Táo và hướng dẫn chế độ kiêng đường.") . "

4. ĐIỀU CHỈNH TUỔI VÀ GIỚI TÍNH:
- Trẻ em hoặc Người cao tuổi (trên 65 tuổi): Kê liều lượng nhẹ nhàng, các vị thuốc ôn hòa tính bình, tránh tính quá hàn hoặc quá nhiệt.
- Nam giới/Nữ giới: Điều chỉnh các lời khuyên sinh hoạt phù hợp với giới tính nếu cần thiết.

5. CẢNH BÁO AN TOÀN CHO XƯƠNG KHỚP / CHẤN THƯƠNG:
- Nếu chẩn đoán là chấn thương cấp tính (bong gân, trật khớp, sưng đau bầm tím cấp), bạn PHẢI đưa ra cảnh báo an toàn trong 'safety_warning' yêu cầu chụp X-quang loại trừ gãy xương trước khi xoa bóp mạnh.
- Nếu có đề xuất xương khớp, chỉ định rõ vùng tổn thương trong 'usage_area' của sản phẩm ngoài da và dịch vụ trị liệu.

" . (!empty($examplesText) ? "VÍ DỤ THÀNH CÔNG TRONG QUÁ KHỨ (Hãy tham khảo cấu trúc và cách kê đơn của các ca thành công tương tự):
{$examplesText}" : "") . "

YÊU CẦU ĐẦU RA:
Bạn phải trả về một đối tượng JSON khớp chính xác với định dạng yêu cầu. Không thêm bất kỳ văn bản giải thích nào khác ngoài JSON.";

            // 6. Gọi Gemini API
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
            $response = Http::timeout(30)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $promptText]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'responseSchema' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'suggested_formula_name' => ['type' => 'STRING'],
                                'suggested_condition' => ['type' => 'STRING'],
                                'reasoning' => ['type' => 'STRING'],
                                'treatment_method' => ['type' => 'STRING'],
                                'course_days' => ['type' => 'INTEGER'],
                                'follow_up_days' => ['type' => 'INTEGER'],
                                'patient_guidelines' => ['type' => 'STRING'],
                                'internal_notes' => ['type' => 'STRING'],
                                'safety_warning' => ['type' => 'STRING'],
                                'oral_herbs' => [
                                    'type' => 'ARRAY',
                                    'items' => [
                                        'type' => 'OBJECT',
                                        'properties' => [
                                            'herb_name' => ['type' => 'STRING'],
                                            'dosage' => ['type' => 'STRING'],
                                            'usage_note' => ['type' => 'STRING']
                                        ],
                                        'required' => ['herb_name', 'dosage', 'usage_note']
                                    ]
                                ],
                                'external_herbs' => [
                                    'type' => 'ARRAY',
                                    'items' => [
                                        'type' => 'OBJECT',
                                        'properties' => [
                                            'custom_name' => ['type' => 'STRING'],
                                            'quantity' => ['type' => 'INTEGER'],
                                            'unit' => ['type' => 'STRING'],
                                            'usage_area' => ['type' => 'STRING'],
                                            'usage_instruction' => ['type' => 'STRING']
                                        ],
                                        'required' => ['custom_name', 'quantity', 'unit', 'usage_area', 'usage_instruction']
                                    ]
                                ],
                                'therapy_services' => [
                                    'type' => 'ARRAY',
                                    'items' => [
                                        'type' => 'OBJECT',
                                        'properties' => [
                                            'custom_name' => ['type' => 'STRING'],
                                            'sessions' => ['type' => 'INTEGER'],
                                            'usage_area' => ['type' => 'STRING'],
                                            'usage_instruction' => ['type' => 'STRING']
                                        ],
                                        'required' => ['custom_name', 'sessions', 'usage_area', 'usage_instruction']
                                    ]
                                ]
                            ],
                            'required' => [
                                'suggested_condition',
                                'reasoning',
                                'treatment_method',
                                'course_days',
                                'follow_up_days',
                                'patient_guidelines',
                                'internal_notes',
                                'safety_warning',
                                'oral_herbs',
                                'external_herbs',
                                'therapy_services'
                            ]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $responseBody = $response->json();
                if (isset($responseBody['candidates'][0]['content']['parts'][0]['text'])) {
                    $jsonText = $responseBody['candidates'][0]['content']['parts'][0]['text'];
                    $data = json_decode($jsonText, true);
                    if (is_array($data)) {
                        // Lọc các vị thuốc theo tồn kho thực tế
                        if (!empty($data['oral_herbs'])) {
                            $data['oral_herbs'] = $this->filterHerbs($data['oral_herbs']);
                        }
                        
                        // Hậu xử lý nghiêm ngặt theo case_type để tránh sai sót của AI
                        if ($isMusculoskeletal) {
                            $data['oral_herbs'] = [];
                            $data['suggested_formula_name'] = null;
                        } elseif ($isNormal) {
                            $data['external_herbs'] = [];
                            $data['therapy_services'] = [];
                        }

                        // Định dạng thành công
                        return array_merge([
                            'status' => 'success',
                        ], $data);
                    }
                }
            }

            Log::error('Gemini API call failed or returned invalid response format: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Exception during Gemini API call: ' . $e->getMessage());
        }

        // Fallback to local rules
        Log::info('Falling back to local heuristics after failure.');
        return $this->getMockHeuristics($symptoms, $diagnosis, $caseType, $context);
    }

    /**
     * Fallback to local heuristics
     */
    private function getMockHeuristics(
        string $symptoms,
        string $diagnosis,
        ?string $caseType = null,
        array $context = []
    ): array {
        // Chuyển chẩn đoán và triệu chứng về chữ thường để so sánh
        $diagLower = mb_strtolower($diagnosis);
        $sympLower = mb_strtolower($symptoms);
        $caseTypeLower = $caseType ? mb_strtolower(trim($caseType)) : null;
        $isMusculoskeletal = $caseTypeLower === 'musculoskeletal';
        $isNormal = $caseTypeLower === 'general' || $caseTypeLower === 'normal';

        $result = [];

        // 0. Nhóm gù lưng, cong vẹo cột sống, kéo giãn nắn chỉnh cột sống
        if ($this->containsAny($diagLower, ['gù', 'vẹo', 'lệch cột sống', 'kéo lưng', 'nắn lưng', 'posture', 'hunchback']) || 
            $this->containsAny($sympLower, ['gù', 'vẹo', 'lệch cột sống', 'kéo lưng', 'nắn lưng'])) {
            $result = $this->getMockSpineAlignment($diagnosis, $symptoms);
        }

        // 1. Nhóm chấn thương xương khớp cấp tính (Bong gân, trật khớp, chấn thương...)
        elseif ($this->containsAny($diagLower, ['bong gân', 'trật khớp', 'rạn xương', 'gãy xương', 'chấn thương', 'ngã']) || 
            $this->containsAny($sympLower, ['bong gân', 'trật khớp', 'rạn xương', 'chấn thương', 'té ngã'])) {
            $result = $this->getMockInjury($diagnosis, $symptoms, true, $context);
        }

        // 2. Nhóm đau xương khớp mạn tính (Đau vai gáy, đau lưng, thoái hóa, khớp...)
        elseif ($this->containsAny($diagLower, ['xương khớp', 'đau lưng', 'thoái hóa', 'gout', 'khớp', 'vai gáy', 'tọa', 'đau cột sống'])) {
            $result = $this->getMockJointPain($diagnosis, true, $context);
        }

        // 3. Nhóm cảm mạo, phong hàn
        elseif ($this->containsAny($diagLower, ['cảm mạo', 'phong hàn', 'sốt', 'cúm', 'ho'])) {
            $result = $this->getMockCold();
        }

        // 4. Nhóm bệnh tiêu hóa
        elseif ($this->containsAny($diagLower, ['tiêu hóa', 'đau bụng', 'dạ dày', 'đại tràng', 'tỳ vị'])) {
            $result = $this->getMockDigestive();
        }

        // 5. Nhóm mất ngủ, suy nhược thần kinh
        elseif ($this->containsAny($diagLower, ['mất ngủ', 'khó ngủ', 'stress', 'an thần', 'suy nhược thần kinh'])) {
            $result = $this->getMockInsomnia();
        }

        // 6. Nhóm mệt mỏi, suy nhược cơ thể
        elseif ($this->containsAny($diagLower, ['suy nhược', 'mệt mỏi', 'thiếu máu', 'kém ăn'])) {
            $result = $this->getMockFatigue();
        }

        // Mặc định nếu không khớp rõ ràng
        else {
            $result = [
                'status' => 'unknown',
                'suggested_condition' => 'Chưa xác định rõ ràng chứng bệnh',
                'reasoning' => 'Dữ liệu triệu chứng và chẩn đoán lâm sàng hiện tại chưa đủ để AI đưa ra phác đồ chuyên biệt.',
                'treatment_method' => 'Chỉ định thăm khám lâm sàng thêm',
                'course_days' => 5,
                'follow_up_days' => 5,
                'patient_guidelines' => 'Nghỉ ngơi điều độ, tránh làm việc nặng, uống nhiều nước ấm và theo dõi biến chuyển cơ thể.',
                'internal_notes' => 'Cần khai thác kỹ hơn tiền sử bệnh, kiểm tra mạch tượng và lưỡi của bệnh nhân.',
                'safety_warning' => 'Cần kiểm tra kỹ để loại trừ các biến chứng cấp tính.',
                'oral_herbs' => [],
                'external_herbs' => [],
                'therapy_services' => []
            ];
        }

        // Áp dụng bộ lọc nghiêm ngặt theo case_type cho kết quả fallback
        if ($isMusculoskeletal) {
            $result['oral_herbs'] = [];
            $result['suggested_formula_name'] = null;
        } elseif ($isNormal) {
            $result['external_herbs'] = [];
            $result['therapy_services'] = [];
        }

        return $result;
    }

    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) return true;
        }
        return false;
    }

    /**
     * MOCK: Chấn thương xương khớp cấp tính (Bong gân, trật khớp...)
     */
    private function getMockInjury(string $diagnosis, string $symptoms, bool $includeOralHerbs = true, array $context = []): array
    {
        // Nhận diện vùng chấn thương để gợi ý thuốc bó và vị trí
        $area = 'vùng tổn thương';
        $location = mb_strtolower((string) ($context['injury_location'] ?? ''));
        if ($location !== '') {
            $area = $context['injury_location'];
        } elseif (str_contains(mb_strtolower($diagnosis), 'cổ chân') || str_contains(mb_strtolower($symptoms), 'cổ chân')) {
            $area = 'vùng cổ chân';
        } elseif (str_contains(mb_strtolower($diagnosis), 'khớp gối') || str_contains(mb_strtolower($symptoms), 'khớp gối')) {
            $area = 'vùng khớp gối';
        } elseif (str_contains(mb_strtolower($diagnosis), 'cổ tay') || str_contains(mb_strtolower($symptoms), 'cổ tay')) {
            $area = 'vùng cổ tay';
        } elseif (str_contains(mb_strtolower($diagnosis), 'vai') || str_contains(mb_strtolower($symptoms), 'vai')) {
            $area = 'vùng vai gáy';
        }

        $isSevere = $this->containsAny(mb_strtolower($diagnosis), ['gãy xương', 'rạn xương', 'biến dạng', 'nặng']) || 
                    $this->containsAny(mb_strtolower($symptoms), ['bầm tím nặng', 'sưng to', 'không đi được', 'lệch']);

        $warning = '⚠️ CẢNH BÁO AN TOÀN: Có dấu hiệu chấn thương cơ học cấp tính hoặc bầm sưng nặng. Yêu cầu thầy thuốc kiểm tra phim X-quang kỹ lưỡng để loại trừ gãy xương, rạn xương hoặc rách dây chằng độ 3 trước khi chỉ định bất kỳ thủ thuật nắn bóp thô bạo nào.';

        return [
            'status' => 'success',
            'suggested_formula_name' => $includeOralHerbs ? 'Tứ Vật Thang gia giảm' : null,
            'suggested_condition' => 'Chấn thương phần mềm cấp tính thể Phong hàn thấp ngưng trệ',
            'reasoning' => 'Tình trạng sưng đau vùng chấn thương cục bộ sau va chạm hoặc vận động sai tư thế, gây kinh lạc bế tắc, ứ huyết sinh sưng đau.',
            'treatment_method' => $includeOralHerbs
                ? 'Hoạt huyết hóa ứ, thông kinh hoạt lạc kết hợp tiêu sưng giảm đau bằng thuốc bó tươi.'
                : 'Tiêu sưng giảm đau tại chỗ bằng thuốc bó dùng ngoài, rượu thuốc xoa bóp và trị liệu nắn chỉnh phù hợp.',
            'course_days' => 7,
            'follow_up_days' => 7,
            'patient_guidelines' => 'Hạn chế đi lại và mang vác nặng ở chân/tay tổn thương. Kê cao chi khi ngủ để giảm sưng nề. Tránh xoa bóp bằng dầu nóng hay rượu thuốc trực tiếp lên vùng đang sưng nóng cấp tính.',
            'internal_notes' => $includeOralHerbs
                ? 'Theo dõi tình trạng giảm sưng sau 3 ngày đắp thuốc bó. Nếu sưng đau tăng tiến hoặc mất tầm vận động hoàn toàn, cần chuyển ngay viện Tây y chụp MRI.'
                : 'Ca khám xương khớp: không bốc thuốc sắc. Theo dõi tình trạng giảm sưng sau 3 ngày đắp thuốc bó. Nếu sưng đau tăng tiến hoặc mất tầm vận động hoàn toàn, cần chuyển ngay viện Tây y chụp MRI.',
            'safety_warning' => $warning,
            'oral_herbs' => $includeOralHerbs ? $this->filterHerbs([
                ['herb_name' => 'Đương Quy', 'dosage' => '12', 'usage_note' => 'Bổ huyết hoạt huyết, hóa ứ giảm đau'],
                ['herb_name' => 'Xuyên Khung', 'dosage' => '8', 'usage_note' => 'Hành khí hoạt huyết thông kinh'],
                ['herb_name' => 'Bạch Thược', 'dosage' => '12', 'usage_note' => 'Hòa huyết, hoãn cấp chỉ thống (giảm co thắt đau)'],
                ['herb_name' => 'Cam Thảo', 'dosage' => '4', 'usage_note' => 'Điều hòa các vị thuốc'],
            ]) : [],
            'external_herbs' => [
                [
                    'custom_name' => 'Bó thuốc nam',
                    'quantity' => 3,
                    'unit' => 'gói',
                    'usage_area' => $area,
                    'usage_instruction' => 'Đắp thuốc ấm cố định bằng băng vải 2 lần trong ngày, lần 1 xong rồi bỏ ra xao lại đắp tiếp lần 2, có thể xao lại nhiều lần 1 ngày nếu muốn, lần nào xao cũng đổ thêm rượu thuốc xoa bóp vào nữa (hoặc đến phòng khám bó thuốc hàng ngày)'
                ],
                [
                    'custom_name' => 'Lọ rượu thuốc xoa bóp',
                    'quantity' => 1,
                    'unit' => 'lọ',
                    'usage_area' => $area,
                    'usage_instruction' => 'Dùng xoa bóp ngoài da kết hợp với bó thuốc nam (đổ thêm vào thuốc đắp khi xao lại).'
                ]
            ],
            'therapy_services' => [
                [
                    'custom_name' => 'Nắn chỉnh khớp xương',
                    'sessions' => 3,
                    'usage_area' => $area,
                    'usage_instruction' => 'Xoa vuốt nhẹ nhàng vùng cơ lân cận để giải tỏa co thắt, tuyệt đối không dùng lực bẻ/nắn trực tiếp lên ổ viêm cấp.'
                ]
            ]
        ];
    }

    /**
     * MOCK: Đau nhức xương khớp mạn tính (Thoái hóa, đau lưng, đau vai gáy...)
     */
    private function getMockJointPain(string $diagnosis, bool $includeOralHerbs = true, array $context = []): array
    {
        $area = 'vùng vai gáy và cột sống';
        $location = mb_strtolower((string) ($context['injury_location'] ?? ''));
        if ($location !== '') {
            $area = $context['injury_location'];
        } elseif (str_contains(mb_strtolower($diagnosis), 'lưng') || str_contains(mb_strtolower($diagnosis), 'cột sống')) {
            $area = 'vùng thắt lưng';
        } elseif (str_contains(mb_strtolower($diagnosis), 'gối')) {
            $area = 'vùng khớp gối';
        }

        return [
            'status' => 'success',
            'suggested_formula_name' => $includeOralHerbs ? 'Độc Hoạt Tang Ký Sinh Thang gia giảm' : null,
            'suggested_condition' => 'Đau nhức xương khớp mạn tính thể Phong hàn thấp tý',
            'reasoning' => 'Đau khớp mạn tính dai dẳng, tăng lên khi thời tiết thay đổi hoặc nhiễm lạnh, kèm hạn chế tầm vận động nhẹ, thuộc hội chứng Can thận hư tổn gân cốt thất dưỡng.',
            'treatment_method' => $includeOralHerbs
                ? 'Khu phong trừ thấp, ôn kinh thông hoạt lạc kết hợp bổ can thận, mạnh gân cốt.'
                : 'Giảm đau và phục hồi vận động bằng thuốc dùng ngoài, xoa bóp bấm huyệt và nắn chỉnh cơ xương khớp.',
            'course_days' => 10,
            'follow_up_days' => 10,
            'patient_guidelines' => 'Giữ ấm các khớp tổn thương khi trời lạnh, tránh ngồi phòng máy lạnh quá lâu. Vận động kéo giãn nhẹ nhàng vùng cổ/lưng hàng ngày.',
            'internal_notes' => $includeOralHerbs
                ? 'Tránh bấm huyệt thô bạo ở cột sống cổ nếu bệnh nhân có thoái hóa đốt sống kèm hẹp ống sống nặng.'
                : 'Ca khám xương khớp: không bốc thuốc sắc. Tránh bấm huyệt thô bạo ở cột sống cổ nếu bệnh nhân có thoái hóa đốt sống kèm hẹp ống sống nặng.',
            'safety_warning' => '⚠️ KHUYẾN CÁO: Thầy thuốc luôn kiểm tra phim X-quang cột sống để phát hiện trượt đốt sống hoặc xẹp lún đốt sống trước khi thực hiện các thủ thuật kéo giãn cơ học mạnh.',
            'oral_herbs' => $includeOralHerbs ? $this->filterHerbs([
                ['herb_name' => 'Độc Hoạt', 'dosage' => '8', 'usage_note' => 'Trừ phong thấp hạ tiêu, giảm đau nhức xương khớp'],
                ['herb_name' => 'Tang Ký Sinh', 'dosage' => '12', 'usage_note' => 'Bổ can thận, mạnh gân xương, ích khí dưỡng huyết'],
                ['herb_name' => 'Tần Giao', 'dosage' => '8', 'usage_note' => 'Khu phong trừ thấp, hòa huyết thư cân'],
                ['herb_name' => 'Đỗ Trọng', 'dosage' => '12', 'usage_note' => 'Ôn bổ can thận, mạnh gân xương cốt'],
                ['herb_name' => 'Đương Quy', 'dosage' => '12', 'usage_note' => 'Bổ huyết hoạt huyết dưỡng huyết'],
                ['herb_name' => 'Bạch Thược', 'dosage' => '12', 'usage_note' => 'Dưỡng huyết nhuận táo, hoãn cấp chỉ thống'],
                ['herb_name' => 'Cam Thảo', 'dosage' => '4', 'usage_note' => 'Điều hòa tính vị các dược liệu'],
            ]) : [],
            'external_herbs' => [
                [
                    'custom_name' => 'Bó thuốc nam',
                    'quantity' => 3,
                    'unit' => 'gói',
                    'usage_area' => $area,
                    'usage_instruction' => 'Đắp thuốc ấm cố định bằng băng vải 2 lần trong ngày, lần 1 xong rồi bỏ ra xao lại đắp tiếp lần 2, có thể xao lại nhiều lần 1 ngày nếu muốn, lần nào xao cũng đổ thêm rượu thuốc xoa bóp vào nữa (hoặc đến phòng khám bó thuốc hàng ngày)'
                ],
                [
                    'custom_name' => 'Lọ rượu thuốc xoa bóp',
                    'quantity' => 1,
                    'unit' => 'lọ',
                    'usage_area' => $area,
                    'usage_instruction' => 'Dùng xoa bóp ngoài da kết hợp với bó thuốc nam (đổ thêm vào thuốc đắp khi xao lại).'
                ]
            ],
            'therapy_services' => [
                [
                    'custom_name' => 'Xoa bóp bấm huyệt trị liệu',
                    'sessions' => 5,
                    'usage_area' => $area,
                    'usage_instruction' => 'Thực hiện xoa, xát, miết, day, ấn các huyệt vị vùng tổn thương để thư giãn gân cơ bế tắc.'
                ]
            ]
        ];
    }

    /**
     * MOCK: Cảm mạo phong hàn
     */
    private function getMockCold(): array
    {
        return [
            'status' => 'success',
            'suggested_formula_name' => 'Quế Chi Thang gia giảm',
            'suggested_condition' => 'Cảm mạo phong hàn ngoại cảm (Cảm lạnh)',
            'reasoning' => 'Bệnh nhân nhiễm phong hàn tà bên ngoài gây sốt nhẹ sợ lạnh, nhức đầu nghẹt mũi, mạch phù khẩn, kinh phế bế tắc.',
            'treatment_method' => 'Tân ôn giải biểu, phát hãn giải cơ phế.',
            'course_days' => 3,
            'follow_up_days' => 3,
            'patient_guidelines' => 'Ăn cháo hành nóng pha chút tía tô gừng ấm sau khi uống thuốc để tăng phát hãn giải cảm. Tránh gió lạnh, không tắm nước lạnh trong thời gian điều trị.',
            'internal_notes' => 'Theo dõi cơn sốt. Nếu sốt cao tăng dần cần kết hợp thuốc hạ sốt hoặc chuyển y tế tuyến đầu.',
            'safety_warning' => '⚠️ LƯU Ý: Nếu bệnh nhân sốt cao liên tục > 38.5 độ kèm khó thở hoặc đau tức ngực, cần làm xét nghiệm máu hoặc chuyển viện Tây y đề phòng cúm biến chứng phổi.',
            'oral_herbs' => $this->filterHerbs([
                ['herb_name' => 'Quế Chi', 'dosage' => '12', 'usage_note' => 'Tân ôn giải biểu, phát hãn giải cơ'],
                ['herb_name' => 'Bạch Thược', 'dosage' => '12', 'usage_note' => 'Hòa vinh dưỡng âm, điều hòa cơ biểu'],
                ['herb_name' => 'Cam Thảo', 'dosage' => '4', 'usage_note' => 'Điều hòa tỳ vị vị thuốc'],
            ]),
            'external_herbs' => [], // Cảm mạo thông thường không cần thuốc bó ngoài
            'therapy_services' => [] // Không chỉ định nắn chỉnh chấn thương khi cảm sốt cấp
        ];
    }

    /**
     * MOCK: Rối loạn tiêu hóa
     */
    private function getMockDigestive(): array
    {
        return [
            'status' => 'success',
            'suggested_formula_name' => 'Tứ Quân Tử Thang gia giảm',
            'suggested_condition' => 'Rối loạn tiêu hóa thể Tỳ vị hư hàn',
            'reasoning' => 'Tỳ vị vận hóa kém dẫn đến thức ăn đình trệ, sinh đầy bụng, đại tiện lỏng nát, đau bụng âm ỉ thích ấm.',
            'treatment_method' => 'Ôn trung kiện tỳ, lý khí hòa trung.',
            'course_days' => 5,
            'follow_up_days' => 5,
            'patient_guidelines' => 'Kiêng ăn đồ sống lạnh, thức ăn nhiều dầu mỡ khó tiêu. Uống nước ấm tỏi hoặc trà gừng mật ong.',
            'internal_notes' => 'Kiểm tra xem có đau khu trú vùng hố chậu phải hay không để loại trừ viêm ruột thừa cấp.',
            'safety_warning' => '⚠️ CẢNH BÁO: Không thực hiện các thủ thuật nắn bóp mạnh ở vùng bụng. Nếu có đau bụng dữ dội phản ứng thành bụng dương tính, phải chuyển cấp cứu ngoại khoa ngay lập tức.',
            'oral_herbs' => $this->filterHerbs([
                ['herb_name' => 'Đảng Sâm', 'dosage' => '12', 'usage_note' => 'Bổ khí kiện tỳ ích vị'],
                ['herb_name' => 'Bạch Truật', 'dosage' => '12', 'usage_note' => 'Táo thấp kiện tỳ ích khí'],
                ['herb_name' => 'Phục Linh', 'dosage' => '12', 'usage_note' => 'Thẩm thấp kiện tỳ an thần'],
                ['herb_name' => 'Cam Thảo', 'dosage' => '4', 'usage_note' => 'Bổ trung hòa vị'],
            ]),
            'external_herbs' => [],
            'therapy_services' => []
        ];
    }

    /**
     * MOCK: Mất ngủ
     */
    private function getMockInsomnia(): array
    {
        return [
            'status' => 'success',
            'suggested_formula_name' => 'Quy Tỳ Thang gia giảm',
            'suggested_condition' => 'Mất ngủ thể Tâm tỳ lưỡng hư',
            'reasoning' => 'Tâm thần bất an do huyết hư không dưỡng tâm, kèm tỳ vị suy nhược không sinh được huyết, gây khó ngủ, trằn trọc hồi hộp.',
            'treatment_method' => 'Bổ ích tâm tỳ, dưỡng huyết an thần.',
            'course_days' => 7,
            'follow_up_days' => 7,
            'patient_guidelines' => 'Hạn chế sử dụng điện thoại, trà đặc hoặc cà phê sau 15h00. Ngâm chân bằng nước ấm gừng thảo dược trước khi đi ngủ 30 phút.',
            'internal_notes' => 'Nên kết hợp châm cứu hoặc trị liệu xoa bóp vùng đầu mặt cổ nhẹ nhàng.',
            'safety_warning' => '⚠️ KHUYẾN CÁO: Đảm bảo giấc ngủ sâu tự nhiên, tránh lạm dụng các thuốc ngủ Tây y gây phụ thuộc.',
            'oral_herbs' => $this->filterHerbs([
                ['herb_name' => 'Phục Linh', 'dosage' => '12', 'usage_note' => 'Kiện tỳ thẩm thấp an thần'],
                ['herb_name' => 'Đương Quy', 'dosage' => '12', 'usage_note' => 'Bổ huyết dưỡng huyết hoạt huyết'],
                ['herb_name' => 'Bạch Thược', 'dosage' => '12', 'usage_note' => 'Dưỡng huyết liễm âm an thần'],
                ['herb_name' => 'Cam Thảo', 'dosage' => '4', 'usage_note' => 'Điều hòa khí huyết vị thuốc'],
            ]),
            'external_herbs' => [],
            'therapy_services' => [
                [
                    'custom_name' => 'Châm cứu thông kinh hoạt lạc',
                    'sessions' => 3,
                    'usage_area' => 'vùng đầu mặt cổ',
                    'usage_instruction' => 'Xoa day các huyệt Bách hội, Ấn đường, Thái dương, Phong trì để thư giãn đầu óc hỗ trợ đi vào giấc ngủ.'
                ]
            ]
        ];
    }

    /**
     * MOCK: Suy nhược mệt mỏi
     */
    private function getMockFatigue(): array
    {
        return [
            'status' => 'success',
            'suggested_formula_name' => 'Bát Trân Thang gia giảm',
            'suggested_condition' => 'Suy nhược cơ thể thể Khí huyết lưỡng hư',
            'reasoning' => 'Khí huyết hao tổn sau ốm dậy hoặc làm việc quá sức làm tạng phủ mất nuôi dưỡng, mệt mỏi hụt hơi sắc mặt kém.',
            'treatment_method' => 'Đại bổ khí huyết, kiện tỳ dưỡng tâm.',
            'course_days' => 10,
            'follow_up_days' => 10,
            'patient_guidelines' => 'Sinh hoạt làm việc nhẹ nhàng, tránh lao động thể lực nặng. Bổ sung dinh dưỡng đầy đủ chất, dễ tiêu hóa.',
            'internal_notes' => 'Kiểm tra mạch tượng trầm nhược hay sác nhược để điều chỉnh gia giảm phù hợp.',
            'safety_warning' => '⚠️ KHUYẾN CÁO: Nếu mệt mỏi kiệt sức đột ngột kèm tụt huyết áp nặng, cần khám cấp cứu truyền dịch hoặc hồi sức cấp cứu kịp thời.',
            'oral_herbs' => $this->filterHerbs([
                ['herb_name' => 'Đảng Sâm', 'dosage' => '12', 'usage_note' => 'Bổ trung ích khí sinh tân'],
                ['herb_name' => 'Bạch Truật', 'dosage' => '12', 'usage_note' => 'Kiện tỳ bổ khí táo thấp'],
                ['herb_name' => 'Phục Linh', 'dosage' => '12', 'usage_note' => 'Kiện tỳ thẩm thấp an thần'],
                ['herb_name' => 'Cam Thảo', 'dosage' => '4', 'usage_note' => 'Bổ tỳ bổ trung'],
                ['herb_name' => 'Đương Quy', 'dosage' => '12', 'usage_note' => 'Bổ huyết hoạt huyết sinh huyết'],
                ['herb_name' => 'Thục Địa', 'dosage' => '12', 'usage_note' => 'Dưỡng âm bổ huyết bổ thận tinh'],
                ['herb_name' => 'Bạch Thược', 'dosage' => '12', 'usage_note' => 'Dưỡng huyết điều kinh chỉ thống'],
                ['herb_name' => 'Xuyên Khung', 'dosage' => '8', 'usage_note' => 'Hành khí hoạt huyết khứ phong'],
            ]),
            'external_herbs' => [],
            'therapy_services' => []
        ];
    }

    /**
     * MOCK: Gù lưng, cong vẹo cột sống, kéo giãn định hình cột sống
     */
    private function getMockSpineAlignment(string $diagnosis, string $symptoms): array
    {
        $area = 'vùng cột sống lưng';
        if (str_contains(mb_strtolower($diagnosis), 'cổ') || str_contains(mb_strtolower($symptoms), 'cổ')) {
            $area = 'vùng cột sống cổ';
        }

        return [
            'status' => 'success',
            'suggested_formula_name' => null,
            'suggested_condition' => 'Tật gù lưng / Cong vẹo cột sống do sai lệch tư thế',
            'reasoning' => 'Tình trạng cong vẹo, gù lưng cơ học do sai tư thế kéo dài hoặc thoái hóa dây chằng cột sống, cần tập trung nắn chỉnh cơ xương khớp và kéo giãn giải tỏa chèn ép cơ học.',
            'treatment_method' => 'Nắn chỉnh cột sống giải chèn ép, phục hồi vận động cơ và kéo giãn cột sống cơ học.',
            'course_days' => 10,
            'follow_up_days' => 10,
            'patient_guidelines' => 'Tập các tư thế đúng khi ngồi làm việc và đi đứng. Tránh cúi khom lưng đột ngột hoặc mang vác vật nặng. Thực hành bài tập kéo xà đơn hoặc bơi lội để cải thiện đường cong sinh lý cột sống.',
            'internal_notes' => 'Không bốc thuốc uống. Tập trung trị liệu nắn chỉnh khớp xương, phục hồi chức năng vận động cơ lưng.',
            'safety_warning' => '⚠️ KHUYẾN CÁO: Luôn kiểm tra phim chụp X-quang cột sống thẳng nghiêng để loại trừ trường hợp gù vẹo do cấu trúc bẩm sinh nặng hoặc xẹp lún đốt sống trước khi thực hiện các thủ thuật kéo giãn cơ học.',
            'oral_herbs' => [],
            'external_herbs' => [],
            'therapy_services' => [
                [
                    'custom_name' => 'Nắn chỉnh khớp xương',
                    'sessions' => 5,
                    'usage_area' => $area,
                    'usage_instruction' => 'Thực hiện nắn chỉnh cột sống gù vẹo, giải tỏa co thắt cơ dựng gai.'
                ],
                [
                    'custom_name' => 'Theo dõi phục hồi vận động',
                    'sessions' => 5,
                    'usage_area' => 'cơ lưng',
                    'usage_instruction' => 'Tập vận động phục hồi độ cong sinh lý cột sống, tập nhóm cơ core và lưng.'
                ]
            ]
        ];
    }
}
