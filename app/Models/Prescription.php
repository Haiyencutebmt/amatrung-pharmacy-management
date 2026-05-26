<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    public const RETURN_WINDOW_HOURS = 24;

    protected $fillable = [
        'medical_record_id',
        'staff_id',
        'treatment_type',
        'note',
        'num_of_doses',
        'usage_instruction',
        'course_days',
        'follow_up_date',
        'public_instruction',
        'internal_note',
        'ai_suggestion',
        'status',
        'is_legacy_data',
        'legacy_source',
        'legacy_note',
        'affect_stock',
    ];

    protected function casts(): array
    {
        return [
            'is_legacy_data' => 'boolean',
            'affect_stock' => 'boolean',
            'follow_up_date' => 'date',
            'num_of_doses' => 'integer',
            'course_days' => 'integer',
        ];
    }

    // ── Relationships ──────────────────────────────────────────

    /** Bệnh án thuộc đơn thuốc này */
    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    /** Nhân viên kê đơn */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /** Danh sách vị thuốc/dược liệu trong đơn */
    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    // ── Helper ────────────────────────────────────────────────

    /** Truy cập nhanh bệnh nhân qua bệnh án */
    public function getPatientAttribute()
    {
        return $this->medicalRecord?->patient;
    }

    /** Kiểm tra đơn có gợi ý AI không */
    public function hasAiSuggestion(): bool
    {
        return !empty($this->ai_suggestion);
    }

    /** Thời điểm cuối cùng được hủy đơn/trả thuốc để hoàn kho */
    public function returnWindowEndsAt()
    {
        return $this->created_at?->copy()->addHours(self::RETURN_WINDOW_HOURS);
    }

    /** Chỉ cho hủy đơn và hoàn kho trong 24 giờ đầu sau khi lập đơn */
    public function canBeReturnedOrDeleted(): bool
    {
        $deadline = $this->returnWindowEndsAt();

        return $deadline !== null && now()->lessThanOrEqualTo($deadline);
    }

    /** Đơn có thuốc uống dạng sắc hay không */
    public function hasOralHerbs(): bool
    {
        if ($this->relationLoaded('items')) {
            return $this->items->contains(fn ($item) => in_array($item->item_type, ['formula_herb', 'herb'], true));
        }

        return $this->items()->whereIn('item_type', ['formula_herb', 'herb'])->exists();
    }

    /** Đơn có thuốc dùng ngoài hoặc dịch vụ trị liệu hay không */
    public function hasExternalTreatmentItems(): bool
    {
        if ($this->relationLoaded('items')) {
            return $this->items->contains(fn ($item) => in_array($item->item_type, ['external_product', 'therapy_service', 'external_herb', 'service'], true));
        }

        return $this->items()->whereIn('item_type', ['external_product', 'therapy_service', 'external_herb', 'service'])->exists();
    }

    /** Chỉ ca khám thường hoặc vừa khám thường vừa xương khớp mới là nghiệp vụ bốc thuốc */
    public function isDispensingPrescription(): bool
    {
        $caseType = $this->medicalRecord?->case_type ?: MedicalRecord::CASE_NORMAL;

        return $this->hasOralHerbs() && in_array($caseType, [
            MedicalRecord::CASE_NORMAL,
            MedicalRecord::CASE_COMBINED,
            'general',
            'both',
        ], true);
    }

    /** Nhãn phiếu nội bộ theo nghiệp vụ thực tế */
    public function internalPrintLabel(): string
    {
        return $this->isDispensingPrescription() ? 'Bốc thuốc' : 'Trị liệu';
    }
}
