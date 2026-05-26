<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagedProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'sku',
        'category',
        'unit',
        'stock_quantity',
        'expiry_date',
        'price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'stock_quantity' => 'decimal:2',
            'expiry_date'    => 'date',
            'price'          => 'decimal:2',
        ];
    }

    // ── Helpers ────────────────────────────────────────────────

    /** Danh sách phân loại thuốc dùng ngoài/Trà thảo mộc */
    public static function categories(): array
    {
        return [
            'Trà thảo mộc',
            'Ngâm rượu',
            'Cao dán',
            'Tinh dầu',
            'Thực phẩm chức năng',
            'Mỹ phẩm thiên nhiên',
            'Khác',
        ];
    }

    /**
     * Tự động sinh SKU: SP0001, SP0002...
     */
    public static function generateSku(): string
    {
        $last = static::orderBy('id', 'desc')->first();
        $next = $last ? ((int) substr($last->sku ?? 'SP0000', 2)) + 1 : 1;
        return 'SP' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'   => 'Còn hàng',
            'inactive' => 'Hết hàng',
            default    => 'Không xác định',
        };
    }
}
