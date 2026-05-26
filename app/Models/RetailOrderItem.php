<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'retail_order_id',
        'packaged_product_id',
        'quantity',
        'unit',
        'unit_price',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'quantity'   => 'decimal:2',
            'unit_price' => 'decimal:2',
        ];
    }

    // ── Relationships ──────────────────────────────────────────

    public function retailOrder()
    {
        return $this->belongsTo(RetailOrder::class);
    }

    public function packagedProduct()
    {
        return $this->belongsTo(PackagedProduct::class);
    }
}
