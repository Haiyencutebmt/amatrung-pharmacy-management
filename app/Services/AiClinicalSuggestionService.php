<?php

namespace App\Services;

use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AiClinicalSuggestionService
 *
 * Cung cấp "Gợi ý AI hỗ trợ thầy thuốc" dựa trên bệnh án.
 *
 * NGUYÊN TẮC AN TOÀN:
 *  1. AI chỉ là công cụ gợi ý tham khảo, KHÔNG thay thế chẩn đoán.
 *  2. Không tự kê đơn, không tự lưu đơn, không tự trừ kho.
 *  3. Không trả về liều lượng cụ thể trong các trường riêng (chỉ ghi trong usage_note).
 *  4. Nếu treatment_direction = 'referral', gợi ý luôn rỗng.
 *  5. Không có fallback heuristic. Nếu AI lỗi → trả về status='ai_unavailable'.
 *  6. Không gửi PII bệnh nhân sang Gemini.
 *
 * LƯU Ý: File AiPrescriptionService.php được giữ nguyên để tương thích tạm thời
 * nhưng không còn chức năng kê đơn tự động trong module AI gợi ý tham khảo.
 */
class AiClinicalSuggestionService
{
    private AiClinicalContextBuilder $contextBuilder;

    public function __construct(AiClinicalContextBuilder $contextBuilder)
    {
        $this->contextBuilder = $contextBuilder;
    }

    /**
     * Sinh gợi ý AI từ bệnh án.
     *
     * @param MedicalRecord $record Bệnh án đã load eager (patient).
     * @return array  ['status'=>string, 'payload_sent'=>array, 'suggestions'=>array, 'disclaimer'=>string]
     */
    public function suggestFromRecord(MedicalRecord $record): array
    {
        // 1. Xây dựng payload lâm sàng an toàn
        $payload = $this->contextBuilder->build($record);

        // 2. Nếu referral → trả về gợi ý rỗng ngay
        if ($record->treatment_direction === 'referral') {
            return [
                'status'       => 'referral',
                'payload_sent' => $payload,
                'suggestions'  => [],
                'disclaimer'   => 'Bệnh nhân được chỉ định chuyển viện. Không có gợi ý thuốc.',
                'message'      => 'Trường hợp chuyển viện, AI không đưa ra gợi ý.',
            ];
        }

        // 3. Kiểm tra Gemini API Key
        $apiKey = config('services.gemini.api_key');
        $model  = config('services.gemini.model', 'gemini-1.5-flash');

        if (!$apiKey) {
            Log::warning('[AiClinicalSuggestion] Gemini API key chưa được cấu hình.');
            return [
                'status'       => 'ai_unavailable',
                'payload_sent' => $payload,
                'suggestions'  => [],
                'disclaimer'   => $this->getDisclaimer(),
                'message'      => 'Dịch vụ AI gợi ý tham khảo hiện không khả dụng (chưa cấu hình API key).',
            ];
        }

        // 4. Xây dựng prompt an toàn
        $promptText = $this->buildPrompt($payload);

        try {
            // 5. Gọi Gemini API
            $url      = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
            $response = Http::timeout(30)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [
                        ['parts' => [['text' => $promptText]]]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'responseSchema'   => $this->getResponseSchema(),
                    ],
                ]);

            if (!$response->successful()) {
                Log::error('[AiClinicalSuggestion] Gemini API lỗi HTTP: ' . $response->status() . ' - ' . $response->body());
                return $this->unavailableResponse($payload, 'Gemini API trả về lỗi HTTP ' . $response->status());
            }

            $responseBody = $response->json();
            $jsonText     = $responseBody['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$jsonText) {
                Log::error('[AiClinicalSuggestion] Gemini trả về phản hồi không có nội dung.');
                return $this->unavailableResponse($payload, 'Phản hồi từ Gemini không có dữ liệu.');
            }

            $data = json_decode($jsonText, true);

            if (!is_array($data)) {
                Log::error('[AiClinicalSuggestion] Gemini trả về JSON không hợp lệ: ' . $jsonText);
                return $this->unavailableResponse($payload, 'Phản hồi JSON từ Gemini không hợp lệ.');
            }

            // 6. Post-verification: lọc và chuẩn hoá suggestions
            $suggestions = $this->postVerify($data, $payload);

            return [
                'status'       => 'success',
                'payload_sent' => $payload,
                'suggestions'  => $suggestions,
                'disclaimer'   => $this->getDisclaimer(),
            ];

        } catch (\Exception $e) {
            Log::error('[AiClinicalSuggestion] Exception: ' . $e->getMessage());
            return $this->unavailableResponse($payload, 'Lỗi kết nối AI: ' . $e->getMessage());
        }
    }

    /**
     * Xây dựng prompt gửi Gemini.
     * Ngôn ngữ trung tính: "gợi ý tham khảo", không nói "kê đơn" hay "chẩn đoán".
     */
    private function buildPrompt(array $payload): string
    {
        $clinical  = $payload['clinical'];
        $age       = $payload['age'] ?? null;
        $gender    = $payload['gender'] ?? null;
        $inventory = $payload['available_inventory'] ?? [];

        // Danh sách kho
        $inventoryText = '';
        if (!empty($inventory)) {
            $lines = array_map(
                fn($h) => "- {$h['name']} (Đơn vị: {$h['unit']}, Tồn kho: {$h['available_qty']})",
                $inventory
            );
            $inventoryText = implode("\n", $lines);
        } else {
            $inventoryText = '(Không có dược liệu uống trong kho hoặc không áp dụng)';
        }

        $direction       = $clinical['treatment_direction'] ?? '';
        $caseType        = $clinical['case_type'] ?? '';
        $symptoms        = $clinical['symptoms'] ?? '';
        $diagnosis       = $clinical['diagnosis'] ?? '';
        $allergies       = $clinical['allergies'] ?? '';
        $underlying      = $clinical['underlying_diseases'] ?? '';
        $currentMeds     = $clinical['current_medications'] ?? '';
        $injuryType      = $clinical['injury_type'] ?? '';
        $injuryLocation  = $clinical['injury_location'] ?? '';
        $clinicalSigns   = $clinical['clinical_signs'] ?? '';
        $palpation       = $clinical['palpation_result'] ?? '';
        $painLevel       = isset($clinical['pain_level']) ? (string) $clinical['pain_level'] : '';
        $xrayNote        = $clinical['xray_note'] ?? '';
        $doctorNote      = $clinical['doctor_note'] ?? '';

        // Xác định phạm vi gợi ý theo hướng điều trị
        $directionGuide = match ($direction) {
            'oral_only'    => 'CHỈ gợi ý vị thuốc uống (oral_herbs). Mảng external_herbs và therapy_services PHẢI rỗng [].',
            'external_only'=> 'CHỈ gợi ý sản phẩm dùng ngoài (external_herbs) và dịch vụ trị liệu (therapy_services). Mảng oral_herbs PHẢI rỗng [].',
            'combined'     => 'Có thể gợi ý cả thuốc uống (oral_herbs), sản phẩm ngoài (external_herbs) và trị liệu (therapy_services).',
            default        => 'Gợi ý phù hợp với bối cảnh lâm sàng.',
        };

        return <<<PROMPT
Bạn là trợ lý AI hỗ trợ thầy thuốc Y học Cổ truyền tại Nhà thuốc AmaTrung.
Nhiệm vụ: Đưa ra GỢI Ý THAM KHẢO về dược liệu và dịch vụ trị liệu phù hợp.

QUAN TRỌNG:
- Đây là GỢI Ý THAM KHẢO, KHÔNG phải chẩn đoán hay kê đơn chính thức.
- Mọi quyết định điều trị đều thuộc thẩm quyền của thầy thuốc.
- Không tự điều chỉnh liều lượng dựa trên cân nặng hay chỉ số sinh tồn.
- CHỈ chọn dược liệu từ danh sách kho có sẵn bên dưới.

THÔNG TIN LÂM SÀNG (Đã ẩn danh):
- Triệu chứng: {$symptoms}
- Chẩn đoán sơ bộ: {$diagnosis}
- Loại ca bệnh: {$caseType}
- Hướng điều trị: {$direction}
- Tuổi bệnh nhân: {$age}
- Giới tính: {$gender}
- Dị ứng đã biết: {$allergies}
- Bệnh nền: {$underlying}
- Thuốc đang dùng: {$currentMeds}
- Loại chấn thương: {$injuryType}
- Vùng tổn thương: {$injuryLocation}
- Dấu hiệu lâm sàng: {$clinicalSigns}
- Kết quả nắn bóp: {$palpation}
- Mức độ đau (0-10): {$painLevel}
- Ghi chú X-quang: {$xrayNote}
- Ghi chú thầy thuốc: {$doctorNote}

QUY TẮC GỢI Ý THEO HƯỚNG ĐIỀU TRỊ:
{$directionGuide}

DANH SÁCH DƯỢC LIỆU KHẢ DỤNG TRONG KHO:
{$inventoryText}

Nếu danh sách kho rỗng hoặc không có dược liệu phù hợp, hãy trả về oral_herbs là mảng rỗng [].

YÊU CẦU ĐẦU RA:
Trả về JSON hợp lệ khớp chính xác schema được chỉ định. Không thêm văn bản ngoài JSON.
PROMPT;
    }

    /**
     * Schema phản hồi JSON cho Gemini (structured output).
     */
    private function getResponseSchema(): array
    {
        return [
            'type' => 'OBJECT',
            'properties' => [
                'reasoning' => ['type' => 'STRING'],
                'safety_note' => ['type' => 'STRING'],
                'follow_up_suggestion' => ['type' => 'STRING'],
                'oral_herbs' => [
                    'type'  => 'ARRAY',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'herb_name'  => ['type' => 'STRING'],
                            'usage_note' => ['type' => 'STRING'],
                        ],
                        'required' => ['herb_name', 'usage_note'],
                    ],
                ],
                'external_herbs' => [
                    'type'  => 'ARRAY',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'custom_name'       => ['type' => 'STRING'],
                            'usage_area'        => ['type' => 'STRING'],
                            'usage_instruction' => ['type' => 'STRING'],
                        ],
                        'required' => ['custom_name', 'usage_area', 'usage_instruction'],
                    ],
                ],
                'therapy_services' => [
                    'type'  => 'ARRAY',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'custom_name'       => ['type' => 'STRING'],
                            'usage_area'        => ['type' => 'STRING'],
                            'usage_instruction' => ['type' => 'STRING'],
                        ],
                        'required' => ['custom_name', 'usage_area', 'usage_instruction'],
                    ],
                ],
            ],
            'required' => [
                'reasoning',
                'safety_note',
                'oral_herbs',
                'external_herbs',
                'therapy_services',
            ],
        ];
    }

    /**
     * Post-verification: lọc gợi ý theo hướng điều trị và kho thực tế.
     * Đây là lớp bảo vệ bắt buộc, không phụ thuộc vào câu trả lời của AI.
     */
    private function postVerify(array $data, array $payload): array
    {
        $direction = $payload['clinical']['treatment_direction'] ?? '';

        // Lấy danh sách tên thuốc trong kho (lowercase)
        $inventoryNames = array_map(
            fn($h) => mb_strtolower(trim($h['name'])),
            $payload['available_inventory'] ?? []
        );

        // Lọc oral_herbs theo kho
        $oralHerbs = [];
        if (!empty($data['oral_herbs']) && is_array($data['oral_herbs'])) {
            foreach ($data['oral_herbs'] as $herb) {
                if (empty($herb['herb_name'])) continue;
                $herbNameLower = mb_strtolower(trim($herb['herb_name']));
                // Kiểm tra kho: tên herb phải khớp một phần với tên trong kho
                $inStock = !empty($inventoryNames) && (function () use ($herbNameLower, $inventoryNames) {
                    foreach ($inventoryNames as $inv) {
                        if (str_contains($inv, $herbNameLower) || str_contains($herbNameLower, $inv)) {
                            return true;
                        }
                    }
                    return false;
                })();

                if ($inStock) {
                    $oralHerbs[] = [
                        'herb_name'  => $herb['herb_name'],
                        'usage_note' => $herb['usage_note'] ?? '',
                    ];
                }
            }
        }

        $externalHerbs    = [];
        $therapyServices  = [];

        if (!empty($data['external_herbs']) && is_array($data['external_herbs'])) {
            foreach ($data['external_herbs'] as $item) {
                if (empty($item['custom_name'])) continue;
                $itemNameLower = mb_strtolower(trim($item['custom_name']));
                // Kiểm tra kho: tên item phải khớp một phần với tên trong kho
                $inStock = !empty($inventoryNames) && (function () use ($itemNameLower, $inventoryNames) {
                    foreach ($inventoryNames as $inv) {
                        if (str_contains($inv, $itemNameLower) || str_contains($itemNameLower, $inv)) {
                            return true;
                        }
                    }
                    return false;
                })();

                if ($inStock) {
                    $externalHerbs[] = [
                        'custom_name'       => $item['custom_name'],
                        'usage_area'        => $item['usage_area'] ?? '',
                        'usage_instruction' => $item['usage_instruction'] ?? '',
                    ];
                }
            }
        }

        if (!empty($data['therapy_services']) && is_array($data['therapy_services'])) {
            foreach ($data['therapy_services'] as $svc) {
                if (empty($svc['custom_name'])) continue;
                $therapyServices[] = [
                    'custom_name'       => $svc['custom_name'],
                    'usage_area'        => $svc['usage_area'] ?? '',
                    'usage_instruction' => $svc['usage_instruction'] ?? '',
                ];
            }
        }

        // Áp dụng bộ lọc hướng điều trị bắt buộc (post-enforcement)
        if ($direction === 'oral_only') {
            $externalHerbs   = [];
            $therapyServices = [];
        } elseif ($direction === 'external_only') {
            $oralHerbs = [];
        }

        // Nếu referral (phòng thủ thêm tầng)
        if ($direction === 'referral') {
            $oralHerbs       = [];
            $externalHerbs   = [];
            $therapyServices = [];
        }

        return [
            'reasoning'            => $data['reasoning'] ?? '',
            'safety_note'          => $data['safety_note'] ?? '',
            'follow_up_suggestion' => $data['follow_up_suggestion'] ?? '',
            'oral_herbs'           => array_values($oralHerbs),
            'external_herbs'       => array_values($externalHerbs),
            'therapy_services'     => array_values($therapyServices),
        ];
    }

    /**
     * Trả về response khi AI không khả dụng (không có fallback heuristic).
     */
    private function unavailableResponse(array $payload, string $errorDetail): array
    {
        return [
            'status'       => 'ai_unavailable',
            'payload_sent' => $payload,
            'suggestions'  => [],
            'disclaimer'   => $this->getDisclaimer(),
            'message'      => 'Dịch vụ AI gợi ý tham khảo tạm thời không khả dụng. ' . $errorDetail,
        ];
    }

    /**
     * Tuyên bố từ chối trách nhiệm bắt buộc hiển thị cùng mọi gợi ý.
     */
    public function getDisclaimer(): string
    {
        return 'Đây là GỢI Ý THAM KHẢO của AI hỗ trợ thầy thuốc. '
             . 'AI không thay thế chẩn đoán hoặc quyết định chuyên môn. '
             . 'Mọi quyết định điều trị đều thuộc thẩm quyền và trách nhiệm của thầy thuốc.';
    }
}
