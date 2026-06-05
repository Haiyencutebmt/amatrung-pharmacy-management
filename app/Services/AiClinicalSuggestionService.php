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
 *  1. AI chỉ là công cụ gợi ý đơn nháp tham khảo, KHÔNG thay thế thầy thuốc.
 *  2. Không tự kê đơn chính thức, không tự lưu đơn, không tự trừ kho.
 *  3. Liều lượng nếu có chỉ là draft_dosage để thầy thuốc rà soát/chỉnh sửa.
 *  4. Nếu treatment_direction = 'referral', gợi ý luôn rỗng.
 *  5. Không có fallback heuristic. Nếu AI lỗi → trả về status='ai_unavailable'.
 *  6. Không gửi PII bệnh nhân sang Gemini.
 *  7. AI preliminary/treatment suggestions only support reference analysis; final
 *     diagnosis, prescription, and inventory decisions belong to the practitioner.
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
        $payload['ai_flow'] = 'treatment_suggestion';

        if (!$record->hasConfirmedDiagnosis()) {
            return [
                'status'       => 'diagnosis_required',
                'payload_sent' => $payload,
                'suggestions'  => [],
                'disclaimer'   => $this->getDisclaimer(),
                'message'      => 'Cần có chẩn đoán chính thức trước khi dùng AI gợi ý điều trị.',
            ];
        }

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
     * Ngôn ngữ trung tính: "gợi ý đơn nháp tham khảo", không lặp lại nhận định sơ bộ.
     */
    private function buildPrompt(array $payload): string
    {
        $clinical  = $payload['clinical'];
        $age       = $payload['age'] ?? null;
        $gender    = $payload['gender'] ?? null;
        $inventory = $payload['available_inventory'] ?? [];
        $services  = $payload['available_services'] ?? [];

        // Danh sách kho
        $inventoryText = '';
        if (!empty($inventory)) {
            $lines = array_map(
                fn($h) => "- {$h['name']} (Loại: {$h['type']}, Đường dùng: {$h['usage_route']}, Đơn vị: {$h['unit']}, Tồn kho: {$h['available_qty']})",
                $inventory
            );
            $inventoryText = implode("\n", $lines);
        } else {
            $inventoryText = '(Không có dữ liệu kho phù hợp hoặc không áp dụng)';
        }

        $serviceText = '';
        if (!empty($services)) {
            $serviceText = implode("\n", array_map(
                fn($s) => "- {$s['name']} (Hướng dẫn mặc định: " . ($s['default_instruction'] ?? 'Không có') . ")",
                $services
            ));
        } else {
            $serviceText = '(Không có dịch vụ phù hợp hoặc không áp dụng)';
        }

        $direction       = $clinical['treatment_direction'] ?? '';
        $caseType        = $clinical['case_type'] ?? '';
        $symptoms        = $clinical['symptoms'] ?? '';
        $additionalSymptoms = $clinical['additional_symptoms'] ?? '';
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
            'oral_only'    => 'CHỈ gợi ý item type="herb" / thuốc uống. Mảng external_herbs và therapy_services PHẢI rỗng [].',
            'external_only'=> 'CHỈ gợi ý item type="external_product" và type="service". Mảng oral_herbs PHẢI rỗng [].',
            'combined'     => 'Có thể gợi ý item thuốc uống, sản phẩm dùng ngoài và dịch vụ. Phải chia nhóm rõ ràng trong suggested_items.',
            default        => 'Gợi ý phù hợp với bối cảnh lâm sàng.',
        };

        return <<<PROMPT
Bạn là trợ lý AI hỗ trợ thầy thuốc Y học Cổ truyền tại Nhà thuốc AmaTrung trên màn hình LẬP ĐƠN ĐIỀU TRỊ.
Nhiệm vụ: Dựa trên chẩn đoán đã được thầy thuốc xác nhận để đưa ra GỢI Ý ĐƠN NHÁP / DỊCH VỤ NHÁP cho thầy thuốc xem, chỉnh sửa và quyết định.

QUAN TRỌNG:
- Đây là GỢI Ý THAM KHẢO, KHÔNG phải kê đơn chính thức.
- Mọi quyết định điều trị đều thuộc thẩm quyền của thầy thuốc.
- Không phân tích bệnh học dài dòng, không lặp lại nhận định sơ bộ, không đưa kết luận chắc chắn.
- Khối "pre_prescription_note" chỉ tóm tắt ngắn tình trạng đã xác nhận và điểm cần kiểm tra; KHÔNG liệt kê tên dược liệu/dịch vụ.
- "treatment_principles" chỉ ghi pháp trị/nguyên tắc điều trị, KHÔNG ghi bài thuốc cụ thể.
- "suggested_items" mới được liệt kê dược liệu/dịch vụ. Mỗi item phải có vai trò, liều nháp nếu phù hợp, đơn vị, lưu ý an toàn, trạng thái kho.
- CHỈ chọn thuốc/chế phẩm từ danh sách kho phù hợp bên dưới.
- CHỈ chọn dịch vụ từ danh mục dịch vụ nếu hướng điều trị cho phép.
- Nếu thiếu thông tin quan trọng, ghi rõ: "Cần thầy thuốc kiểm tra thêm trước khi áp dụng."

THÔNG TIN LÂM SÀNG (Đã ẩn danh):
- Triệu chứng: {$symptoms}
- Các triệu chứng khác / thông tin bổ sung: {$additionalSymptoms}
- Chẩn đoán đã được thầy thuốc xác nhận: {$diagnosis}
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

DANH SÁCH THUỐC / CHẾ PHẨM KHẢ DỤNG TRONG KHO:
{$inventoryText}

DANH MỤC DỊCH VỤ TRỊ LIỆU ĐANG HOẠT ĐỘNG:
{$serviceText}

Nếu danh sách kho rỗng hoặc không có thuốc/chế phẩm phù hợp, không đưa item thuốc đó vào suggested_items.

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
                'pre_prescription_note' => ['type' => 'STRING'],
                'treatment_principles' => [
                    'type'  => 'ARRAY',
                    'items' => ['type' => 'STRING'],
                ],
                'suggested_items' => [
                    'type' => 'ARRAY',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'type' => [
                                'type' => 'STRING',
                                'enum' => ['herb', 'service', 'external_product'],
                            ],
                            'name' => ['type' => 'STRING'],
                            'role' => ['type' => 'STRING'],
                            'draft_dosage' => ['type' => 'STRING'],
                            'unit' => ['type' => 'STRING'],
                            'safety_note' => ['type' => 'STRING'],
                            'inventory_status' => [
                                'type' => 'STRING',
                                'enum' => ['Còn kho', 'Sắp hết', 'Hết kho', 'Không rõ tồn kho'],
                            ],
                        ],
                        'required' => ['type', 'name', 'role', 'draft_dosage', 'unit', 'safety_note', 'inventory_status'],
                    ],
                ],
                'safety_and_followup' => [
                    'type'  => 'ARRAY',
                    'items' => ['type' => 'STRING'],
                ],
            ],
            'required' => [
                'pre_prescription_note',
                'treatment_principles',
                'suggested_items',
                'safety_and_followup',
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
        $inventory = $payload['available_inventory'] ?? [];
        $services = $payload['available_services'] ?? [];

        $prePrescriptionNote = $this->compactText(
            $data['pre_prescription_note']
            ?? $data['reasoning']
            ?? 'Cần thầy thuốc kiểm tra thêm trước khi áp dụng.'
        );

        $oralHerbs = [];
        $externalHerbs = [];
        $therapyServices = [];
        $suggestedItems = [];
        $seen = [];

        $addSuggestedItem = function (array $item) use (&$suggestedItems, &$seen): bool {
            $type = $item['type'] ?? '';
            $name = $this->compactText($item['name'] ?? '', 90);
            if ($type === '' || $name === '') {
                return false;
            }

            $key = $type . '|' . mb_strtolower($name);
            if (isset($seen[$key])) {
                return false;
            }

            $seen[$key] = true;
            $suggestedItems[] = [
                'type' => $type,
                'name' => $name,
                'role' => $this->compactText($item['role'] ?? 'Cần thầy thuốc kiểm tra thêm trước khi áp dụng.', 180),
                'draft_dosage' => $this->compactText($item['draft_dosage'] ?? 'Thầy thuốc chỉnh liều khi lập đơn nháp.', 80),
                'unit' => $this->compactText($item['unit'] ?? '', 40),
                'safety_note' => $this->compactText($item['safety_note'] ?? 'Cần thầy thuốc kiểm tra thêm trước khi áp dụng.', 180),
                'inventory_status' => $item['inventory_status'] ?? 'Không rõ tồn kho',
            ];

            return true;
        };

        $addHerb = function (string $name, string $role = '', ?string $draftDosage = null, string $safetyNote = '') use (&$oralHerbs, $inventory, $addSuggestedItem): void {
            $match = $this->findInventoryMatch($name, $inventory, 'oral');
            if (!$match) {
                return;
            }

            $role = $this->compactText($role ?: 'Vai trò hỗ trợ theo nguyên tắc điều trị tham khảo.', 180);
            $safetyNote = $this->compactText($safetyNote ?: 'Cần kiểm tra dị ứng, bệnh nền, thuốc đang dùng và chỉnh liều trước khi áp dụng.', 180);
            $draftDosage = $this->compactText($draftDosage ?: 'Thầy thuốc chỉnh liều', 80);

            $added = $addSuggestedItem([
                'type' => 'herb',
                'name' => $match['name'],
                'role' => $role,
                'draft_dosage' => $draftDosage,
                'unit' => $match['unit'] ?? '',
                'safety_note' => $safetyNote,
                'inventory_status' => $this->inventoryStatus($match),
            ]);

            if ($added) {
                $oralHerbs[] = [
                    'herb_name' => $match['name'],
                    'usage_note' => $role,
                    'draft_dosage' => $draftDosage,
                    'unit' => $match['unit'] ?? '',
                    'inventory_status' => $this->inventoryStatus($match),
                    'safety_note' => $safetyNote,
                ];
            }
        };

        $addExternal = function (string $name, string $role = '', ?string $draftDosage = null, string $safetyNote = '') use (&$externalHerbs, $inventory, $addSuggestedItem): void {
            $match = $this->findInventoryMatch($name, $inventory, 'external');
            if (!$match) {
                return;
            }

            $role = $this->compactText($role ?: 'Dùng ngoài / hỗ trợ trị liệu theo chỉ định thầy thuốc.', 180);
            $safetyNote = $this->compactText($safetyNote ?: 'Chỉ dùng ngoài theo hướng dẫn; kiểm tra kích ứng da và chống chỉ định trước khi áp dụng.', 180);
            $draftDosage = $this->compactText($draftDosage ?: 'Thầy thuốc chỉnh số lượng', 80);

            $added = $addSuggestedItem([
                'type' => 'external_product',
                'name' => $match['name'],
                'role' => $role,
                'draft_dosage' => $draftDosage,
                'unit' => $match['unit'] ?? '',
                'safety_note' => $safetyNote,
                'inventory_status' => $this->inventoryStatus($match),
            ]);

            if ($added) {
                $externalHerbs[] = [
                    'custom_name' => $match['name'],
                    'usage_area' => $role,
                    'usage_instruction' => $safetyNote,
                    'draft_dosage' => $draftDosage,
                    'unit' => $match['unit'] ?? '',
                    'inventory_status' => $this->inventoryStatus($match),
                ];
            }
        };

        $addService = function (string $name, string $role = '', ?string $draftDosage = null, string $safetyNote = '') use (&$therapyServices, $services, $addSuggestedItem): void {
            $serviceName = $this->resolveServiceName($name, $services);
            if ($serviceName === '') {
                return;
            }

            $role = $this->compactText($role ?: 'Dịch vụ trị liệu hỗ trợ theo chẩn đoán đã xác nhận.', 180);
            $safetyNote = $this->compactText($safetyNote ?: 'Thầy thuốc kiểm tra chống chỉ định và đáp ứng sau trị liệu.', 180);
            $draftDosage = $this->compactText($draftDosage ?: '1 lần', 80);

            $added = $addSuggestedItem([
                'type' => 'service',
                'name' => $serviceName,
                'role' => $role,
                'draft_dosage' => $draftDosage,
                'unit' => 'lần',
                'safety_note' => $safetyNote,
                'inventory_status' => 'Không rõ tồn kho',
            ]);

            if ($added) {
                $therapyServices[] = [
                    'custom_name' => $serviceName,
                    'usage_area' => $role,
                    'usage_instruction' => $safetyNote,
                    'draft_dosage' => $draftDosage,
                    'unit' => 'lần',
                    'inventory_status' => 'Không rõ tồn kho',
                ];
            }
        };

        if (!empty($data['suggested_items']) && is_array($data['suggested_items'])) {
            foreach ($data['suggested_items'] as $item) {
                $type = $this->normalizeSuggestedType($item['type'] ?? '');
                $name = trim((string) ($item['name'] ?? ''));
                if ($name === '') {
                    continue;
                }

                if ($type === 'herb') {
                    $addHerb($name, $item['role'] ?? '', $item['draft_dosage'] ?? null, $item['safety_note'] ?? '');
                } elseif ($type === 'external_product') {
                    $addExternal($name, $item['role'] ?? '', $item['draft_dosage'] ?? null, $item['safety_note'] ?? '');
                } elseif ($type === 'service') {
                    $addService($name, $item['role'] ?? '', $item['draft_dosage'] ?? null, $item['safety_note'] ?? '');
                }
            }
        }

        if (!empty($data['oral_herbs']) && is_array($data['oral_herbs'])) {
            foreach ($data['oral_herbs'] as $herb) {
                if (empty($herb['herb_name'])) continue;
                $addHerb(
                    (string) $herb['herb_name'],
                    (string) ($herb['usage_note'] ?? ''),
                    isset($herb['draft_dosage']) ? (string) $herb['draft_dosage'] : (isset($herb['dosage']) ? (string) $herb['dosage'] : null),
                    (string) ($herb['safety_note'] ?? '')
                );
            }
        }

        if (!empty($data['external_herbs']) && is_array($data['external_herbs'])) {
            foreach ($data['external_herbs'] as $item) {
                if (empty($item['custom_name'])) continue;
                $addExternal(
                    (string) $item['custom_name'],
                    (string) ($item['usage_area'] ?? ''),
                    isset($item['draft_dosage']) ? (string) $item['draft_dosage'] : null,
                    (string) ($item['usage_instruction'] ?? '')
                );
            }
        }

        if (!empty($data['therapy_services']) && is_array($data['therapy_services'])) {
            foreach ($data['therapy_services'] as $svc) {
                if (empty($svc['custom_name'])) continue;
                $addService(
                    (string) $svc['custom_name'],
                    (string) ($svc['usage_area'] ?? ''),
                    isset($svc['draft_dosage']) ? (string) $svc['draft_dosage'] : null,
                    (string) ($svc['usage_instruction'] ?? '')
                );
            }
        }

        // Áp dụng bộ lọc hướng điều trị bắt buộc (post-enforcement)
        if ($direction === 'oral_only') {
            $externalHerbs   = [];
            $therapyServices = [];
            $suggestedItems = array_values(array_filter($suggestedItems, fn($item) => $item['type'] === 'herb'));
        } elseif ($direction === 'external_only') {
            $oralHerbs = [];
            $suggestedItems = array_values(array_filter($suggestedItems, fn($item) => in_array($item['type'], ['external_product', 'service'], true)));
        }

        // Nếu referral (phòng thủ thêm tầng)
        if ($direction === 'referral') {
            $oralHerbs       = [];
            $externalHerbs   = [];
            $therapyServices = [];
            $suggestedItems  = [];
        }

        $treatmentPrinciples = $this->normalizeTextList($data['treatment_principles'] ?? []);
        if (empty($treatmentPrinciples)) {
            $treatmentPrinciples = ['Cần thầy thuốc kiểm tra thêm trước khi áp dụng.'];
        }

        $safetyAndFollowup = $this->normalizeTextList($data['safety_and_followup'] ?? []);
        if (!empty($data['safety_note'])) {
            $safetyAndFollowup[] = $this->compactText($data['safety_note']);
        }
        if (!empty($data['follow_up_suggestion'])) {
            $safetyAndFollowup[] = $this->compactText($data['follow_up_suggestion']);
        }
        $safetyAndFollowup = array_values(array_unique(array_filter($safetyAndFollowup)));

        if (empty($safetyAndFollowup)) {
            $safetyAndFollowup = [
                'Gợi ý AI chỉ mang tính tham khảo. Thầy thuốc cần kiểm tra, chỉnh sửa và xác nhận trước khi lập đơn.',
                'Cần kiểm tra dị ứng, bệnh nền, thuốc đang dùng và dấu hiệu cần chuyển khám nếu có.',
            ];
        }

        return [
            'pre_prescription_note' => $prePrescriptionNote,
            'treatment_principles'  => array_slice($treatmentPrinciples, 0, 5),
            'suggested_items'       => array_values($suggestedItems),
            'safety_and_followup'   => array_slice($safetyAndFollowup, 0, 6),
            'reasoning'            => $prePrescriptionNote,
            'safety_note'          => $safetyAndFollowup[0] ?? '',
            'follow_up_suggestion' => $safetyAndFollowup[1] ?? '',
            'oral_herbs'           => array_values($oralHerbs),
            'external_herbs'       => array_values($externalHerbs),
            'therapy_services'     => array_values($therapyServices),
        ];
    }

    private function compactText(?string $text, int $maxLength = 260): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', (string) $text));
        if ($text === '') {
            return '';
        }

        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $maxLength), " \t\n\r\0\x0B.,;:") . '...';
    }

    private function normalizeTextList(mixed $value, int $maxItems = 6): array
    {
        $items = is_array($value) ? $value : ($value ? [$value] : []);

        return array_values(array_slice(array_filter(array_map(
            fn($item) => $this->compactText((string) $item),
            $items
        )), 0, $maxItems));
    }

    private function normalizeSuggestedType(?string $type): string
    {
        $type = mb_strtolower(trim((string) $type));

        return match ($type) {
            'herb', 'oral_herb', 'oral' => 'herb',
            'external', 'external_herb', 'external_product', 'packaged_product' => 'external_product',
            'service', 'therapy', 'therapy_service' => 'service',
            default => '',
        };
    }

    private function findInventoryMatch(string $name, array $inventory, ?string $usageRoute = null): ?array
    {
        $needle = mb_strtolower(trim($name));
        if ($needle === '') {
            return null;
        }

        foreach ($inventory as $item) {
            if ($usageRoute && (($item['usage_route'] ?? null) !== $usageRoute)) {
                continue;
            }

            $candidate = mb_strtolower(trim((string) ($item['name'] ?? '')));
            if ($candidate === '') {
                continue;
            }

            if ($candidate === $needle || str_contains($candidate, $needle) || str_contains($needle, $candidate)) {
                return $item;
            }
        }

        return null;
    }

    private function inventoryStatus(array $item): string
    {
        $quantity = (float) ($item['available_qty'] ?? 0);

        if ($quantity <= 0) {
            return 'Hết kho';
        }

        return $quantity <= 50 ? 'Sắp hết' : 'Còn kho';
    }

    private function resolveServiceName(string $name, array $services): string
    {
        $name = trim($name);
        if ($name === '') {
            return '';
        }

        if (empty($services)) {
            return $name;
        }

        $needle = mb_strtolower($name);
        foreach ($services as $service) {
            $candidate = trim((string) ($service['name'] ?? ''));
            $candidateLower = mb_strtolower($candidate);
            if ($candidate !== '' && ($candidateLower === $needle || str_contains($candidateLower, $needle) || str_contains($needle, $candidateLower))) {
                return $candidate;
            }
        }

        return $name;
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
        return 'Gợi ý AI chỉ mang tính tham khảo (GỢI Ý THAM KHẢO). '
             . 'Thầy thuốc cần kiểm tra, chỉnh sửa và xác nhận trước khi lập đơn.';
    }
}
