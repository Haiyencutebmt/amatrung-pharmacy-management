<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'staff_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'note',
        'total_amount',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function items()
    {
        return $this->hasMany(RetailOrderItem::class);
    }

    // ── Helper ────────────────────────────────────────────────

    /**
     * Sinh mã phiếu bán lẻ tự động dạng BL0001
     */
    public static function generateCode(): string
    {
        $last = static::orderBy('id', 'desc')->first();
        $next = $last ? ((int) substr($last->order_code, 2)) + 1 : 1;
        return 'BL' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
