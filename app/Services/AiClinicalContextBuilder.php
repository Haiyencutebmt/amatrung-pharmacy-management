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
                $age = (int) $record->patient->age;
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

        $today = now()->toDateString();
        $query = DB::table('inventory_items')
            ->join('inventory_batches', 'inventory_items.id', '=', 'inventory_batches.inventory_item_id')
            ->where('inventory_items.is_active', true)
            ->where('inventory_batches.quantity_remaining', '>', 0)
            ->where('inventory_batches.status', 'available')
            ->whereNotNull('inventory_batches.expiry_date')
            ->where('inventory_batches.expiry_date', '>', $today);

        // Áp dụng quy tắc dùng usage_route:
        if ($treatmentDirection === 'oral_only') {
            $query->where('inventory_items.usage_route', 'oral');
        } elseif ($treatmentDirection === 'external_only') {
            $query->where('inventory_items.usage_route', 'external');
        } elseif ($treatmentDirection === 'combined') {
            $query->whereIn('inventory_items.usage_route', ['oral', 'external']);
        } else {
            $query->whereIn('inventory_items.usage_route', ['oral', 'external']);
        }

        $items = $query->select(
                'inventory_items.name',
                'inventory_items.unit',
                DB::raw('SUM(inventory_batches.quantity_remaining) as stock_quantity')
            )
            ->groupBy('inventory_items.id', 'inventory_items.name', 'inventory_items.unit')
            ->orderBy('inventory_items.name')
            ->get();

        return $items->map(fn($item) => [
            'name'          => $item->name,
            'unit'          => $item->unit,
            'available_qty' => (float) $item->stock_quantity,
        ])->toArray();
    }
}
