<?php

namespace App\Services;

use App\Models\MedicalRecord;
use App\Models\InventoryBatch;
use App\Models\MedicinalHerb;
use Illuminate\Support\Facades\DB;

/**
 * AiClinicalContextBuilder
 *
 * Xây dựng payload lâm sàng an toàn gửi sang AI.
 * NGUYÊN TẮC:
 *  - Chỉ dựng payload từ allowlist cụ thể, KHÔNG lấy toàn bộ object rồi unset.
 *  - Không gửi PII định danh (tên, ngày sinh, số điện thoại, địa chỉ bệnh nhân).
 *  - Nếu treatment_direction = 'referral', available_inventory trả về mảng rỗng.
 *  - Inventory chỉ lấy vị thuốc UỐNG (medicinal_herbs có tồn kho > 0 qua batch FEFO).
 */
class AiClinicalContextBuilder
{
    /**
     * Các trường lâm sàng được phép gửi sang AI (allowlist).
     * TUYỆT ĐỐI không thêm: id, patient_id, staff_id, record_code.
     */
    private const CLINICAL_ALLOWLIST = [
        'symptoms',
        'diagnosis',
        'treatment_plan',
        'treatment_direction',
        'case_type',
        'weight',
        'height',
        'allergies',
        'underlying_diseases',
        'current_medications',
        // Trường xương khớp (không có tên/số điện thoại)
        'injury_type',
        'injury_location',
        'injury_cause',
        'clinical_signs',
        'palpation_result',
        'pain_level',
        'xray_note',
        // Legacy (nếu có thêm bối cảnh)
        'doctor_note',
    ];

    /**
     * Xây dựng payload lâm sàng an toàn từ bệnh án.
     *
     * @param MedicalRecord $record  Bệnh án đã được load với patient (eager).
     * @return array  Mảng payload sẵn sàng ghi log và gửi AI.
     */
    public function build(MedicalRecord $record): array
    {
        // 1. Lấy dữ liệu lâm sàng từ allowlist
        $clinical = [];
        foreach (self::CLINICAL_ALLOWLIST as $field) {
            if (isset($record->$field) && $record->$field !== null && $record->$field !== '') {
                $clinical[$field] = $record->$field;
            }
        }

        // 2. Tính tuổi ẩn danh (không gửi ngày sinh chính xác)
        $age = null;
        if ($record->patient && $record->patient->date_of_birth) {
            try {
                $age = now()->diffInYears($record->patient->date_of_birth);
            } catch (\Exception $e) {
                $age = null;
            }
        }

        // 3. Giới tính (không định danh)
        $gender = $record->patient->gender ?? null;

        // 4. Kho khả dụng
        $availableInventory = $this->buildAvailableInventory($record->treatment_direction);

        return [
            'clinical'            => $clinical,
            'age'                 => $age,
            'gender'              => $gender,
            'available_inventory' => $availableInventory,
        ];
    }

    /**
     * Lấy danh sách vị thuốc uống còn tồn kho (qua bảng inventory_batches / FEFO).
     * Nếu treatment_direction = 'referral', trả về mảng rỗng.
     *
     * @param string|null $treatmentDirection
     * @return array  [['name'=>string,'unit'=>string,'available_qty'=>float], ...]
     */
    public function buildAvailableInventory(?string $treatmentDirection): array
    {
        // Quy tắc: referral → không gợi ý thuốc
        if ($treatmentDirection === 'referral') {
            return [];
        }

        // Chỉ lấy vị thuốc UỐNG (medicinal_herb) có tồn kho dương
        // Ưu tiên dùng inventory_batches FEFO nếu bảng tồn tại
        $hasBatchTable = \Illuminate\Support\Facades\Schema::hasTable('inventory_batches');

        if ($hasBatchTable) {
            // Tổng tồn theo herb từ inventory_batches chưa hết hạn
            $rows = DB::table('inventory_batches')
                ->join('medicinal_herbs', 'inventory_batches.medicinal_herb_id', '=', 'medicinal_herbs.id')
                ->where('medicinal_herbs.status', 'active')
                ->where(function ($q) {
                    $q->whereNull('inventory_batches.expiry_date')
                      ->orWhere('inventory_batches.expiry_date', '>', now()->toDateString());
                })
                ->where('inventory_batches.remaining_quantity', '>', 0)
                ->groupBy('medicinal_herbs.id', 'medicinal_herbs.name', 'medicinal_herbs.unit')
                ->select(
                    'medicinal_herbs.name',
                    'medicinal_herbs.unit',
                    DB::raw('SUM(inventory_batches.remaining_quantity) as available_qty')
                )
                ->orderBy('medicinal_herbs.name')
                ->get();

            return $rows->map(fn($r) => [
                'name'          => $r->name,
                'unit'          => $r->unit,
                'available_qty' => (float) $r->available_qty,
            ])->values()->toArray();
        }

        // Fallback: dùng stock_quantity từ bảng medicinal_herbs (legacy)
        return MedicinalHerb::where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->orderBy('name')
            ->get(['name', 'unit', 'stock_quantity'])
            ->map(fn($h) => [
                'name'          => $h->name,
                'unit'          => $h->unit,
                'available_qty' => (float) $h->stock_quantity,
            ])
            ->values()
            ->toArray();
    }
}
