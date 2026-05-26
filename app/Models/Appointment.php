<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'appointment_date',
        'appointment_time',
        'reason',
        'status',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'cancelled' => 'Đã hủy',
            'completed' => 'Đã hoàn thành',
            default     => 'Chưa xác định',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'warning',
            'confirmed' => 'primary',
            'cancelled' => 'danger',
            'completed' => 'success',
            default     => 'secondary',
        };
    }

    /**
     * Tìm bệnh án liên quan dựa trên mã bệnh án (ví dụ BA0010) ghi trong lý do/ghi chú
     * hoặc dựa vào ngày hẹn khám của bệnh nhân.
     */
    public function getAssociatedMedicalRecord()
    {
        // 1. Tìm theo mã bệnh án dạng BAxxxx trong lý do hoặc ghi chú
        $searchText = ($this->reason ?? '') . ' ' . ($this->notes ?? '');
        if (preg_match('/BA\s*:\s*(BA\d+)/i', $searchText, $matches) || 
            preg_match('/(BA\d+)/i', $searchText, $matches)) {
            $code = strtoupper(str_replace(' ', '', $matches[1]));
            $record = \App\Models\MedicalRecord::where('record_code', $code)->first();
            if ($record) {
                return $record;
            }
        }

        // 2. Tìm theo ngày hẹn khám của cùng bệnh nhân
        $record = \App\Models\MedicalRecord::where('patient_id', $this->patient_id)
            ->whereDate('visit_date', $this->appointment_date)
            ->first();
        
        return $record;
    }
}
