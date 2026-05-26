<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \Illuminate\Support\Carbon|null $visit_date
 * @property string|null $record_code
 */
class MedicalRecord extends Model
{
    use HasFactory;

    public const CASE_NORMAL = 'normal';
    public const CASE_MUSCULOSKELETAL = 'musculoskeletal';
    public const CASE_COMBINED = 'combined';

    public static function getCaseTypeLabels(): array
    {
        return [
            self::CASE_NORMAL => 'Khám thông thường',
            self::CASE_MUSCULOSKELETAL => 'Xương khớp / Trị liệu ngoài',
            self::CASE_COMBINED => 'Khám kết hợp',
        ];
    }


    protected $fillable = [
        'patient_id',
        'staff_id',
        'record_code',
        'visit_date',
        'weight',
        'height',
        'symptoms',
        'diagnosis',
        'treatment_plan',
        'doctor_note',
        'treatment_direction',
        'status',
        'referral_reason',
        'allergies',
        'underlying_diseases',
        'current_medications',
        // Khám xương khớp
        'case_type',
        'injury_type',
        'injury_location',
        'injury_cause',
        'clinical_signs',
        'palpation_result',
        'pain_level',
        'xray_image',
        'xray_note',
        'xray_file_path',
        // Legacy
        'is_legacy_data',
        'legacy_source',
        'legacy_note',
        'imported_at',
        'imported_by',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'weight' => 'decimal:1',
            'height' => 'decimal:1',
            'pain_level' => 'integer',
            'is_legacy_data' => 'boolean',
            'imported_at' => 'datetime',
        ];
    }

    // ── Auto Generate Record Code ────────────────────────────────

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($record) {
            if (empty($record->record_code)) {
                $record->record_code = self::generateCode();
            }
        });
    }

    /**
     * Tạo mã hồ sơ bệnh án tự động: BA0001, BA0002...
     */
    public static function generateCode(): string
    {
        $latest = self::where('record_code', 'like', 'BA%')
            ->orderByRaw("CAST(SUBSTRING(record_code, 3) AS UNSIGNED) DESC")
            ->value('record_code');

        if ($latest) {
            $num = (int) substr($latest, 2);
            return 'BA' . str_pad($num + 1, 4, '0', STR_PAD_LEFT);
        }

        return 'BA0001';
    }

    // ── Relationships ──────────────────────────────────────────

    /** Bệnh nhân được khám */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /** Nhân viên/bác sĩ thực hiện khám */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /** Đơn thuốc của bệnh án này */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    /** Đơn thuốc gần nhất */
    public function latestPrescription()
    {
        return $this->hasOne(Prescription::class)->latestOfMany();
    }

    /** File đính kèm của bệnh án */
    public function attachments()
    {
        return $this->hasMany(MedicalRecordAttachment::class);
    }
}

