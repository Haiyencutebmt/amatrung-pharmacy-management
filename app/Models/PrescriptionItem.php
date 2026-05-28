<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'inventory_item_id',
        'medicinal_herb_id',
        'packaged_product_id',
        'item_type',
        'custom_name',
        'quantity',
        'quantity_per_dose',
        'number_of_doses',
        'unit',
        'dosage',
        'note',
        'is_secret_formula',
        'affects_stock',
        'usage_area',
        'sessions',
        'usage_instruction',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'is_secret_formula' => 'boolean',
            'affects_stock' => 'boolean',
            'sessions' => 'integer',
        ];
    }

    // ── Relationships ──────────────────────────────────────────

    /** Đơn điều trị chứa item này */
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function medicinalHerb()
    {
        return $this->belongsTo(MedicinalHerb::class);
    }

    public function packagedProduct()
    {
        return $this->belongsTo(PackagedProduct::class);
    }

    // ── Helpers ───────────────────────────────────────────────

    /** Lấy tên hiển thị: ưu tiên tên kho, fallback custom_name */
    public function getDisplayNameAttribute(): string
    {
        if ($this->inventoryItem) {
            return $this->inventoryItem->name;
        }
        if ($this->packagedProduct) {
            return $this->packagedProduct->name;
        }
        if ($this->medicinalHerb) {
            return $this->medicinalHerb->name;
        }
        return $this->custom_name ?? 'Không xác định';
    }

    /** Nhãn loại hạng mục tiếng Việt */
    public function getItemTypeLabelAttribute(): string
    {
        return match ($this->item_type) {
            'formula_herb', 'herb'     => 'Thuốc uống',
            'external_product', 'packaged_product' => 'Thuốc dùng ngoài/Trà thảo mộc',
            'therapy_service', 'service'  => 'Dịch vụ trị liệu',
            default            => 'Thuốc uống',
        };
    }
}
