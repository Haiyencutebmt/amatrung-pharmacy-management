<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicinalHerbStockLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicinal_herb_id',
        'user_id',
        'old_quantity',
        'new_quantity',
        'change_quantity',
        'action_type',
        'note',
        'details',
    ];

    protected function casts(): array
    {
        return [
            'old_quantity' => 'decimal:2',
            'new_quantity' => 'decimal:2',
            'change_quantity' => 'decimal:2',
            'details' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function medicinalHerb()
    {
        return $this->belongsTo(MedicinalHerb::class);
    }

    public function getActionTypeLabelAttribute(): string
    {
        return match ($this->action_type) {
            'manual_update' => 'Điều chỉnh thủ công',
            'prescription'  => 'Cấp đơn thuốc',
            'retail'        => 'Bán lẻ',
            'excel_import'  => 'Nhập từ Excel',
            default         => 'Thao tác khác',
        };
    }
}
