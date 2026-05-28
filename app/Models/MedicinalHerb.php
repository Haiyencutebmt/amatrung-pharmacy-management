<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property \Illuminate\Support\Carbon|null $expiry_date
 */
class MedicinalHerb extends Model
{
    use HasFactory;

    public $stockLogType = 'manual_update';
    public $stockLogNote = null;
    public $_pendingStockLog = null;

    protected $fillable = [
        'name',
        'category',
        'usage_type',
        'description',
        'unit',
        'stock_quantity',
        'expiry_date',
        'warning_note',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'stock_quantity' => 'decimal:2',
            'expiry_date'    => 'datetime',
        ];
    }

    protected static function booted()
    {
        static::created(function ($herb) {
            $labels = [
                'name' => 'Tên dược liệu',
                'category' => 'Phân loại',
                'usage_type' => 'Cách dùng',
                'unit' => 'Đơn vị tính',
                'stock_quantity' => 'Số lượng tồn kho',
                'expiry_date' => 'Hạn sử dụng',
                'warning_note' => 'Cảnh báo đặc biệt',
                'status' => 'Trạng thái',
                'description' => 'Mô tả / Tác dụng',
            ];

            $details = [];
            foreach ($herb->getAttributes() as $key => $value) {
                if (in_array($key, ['id', 'created_at', 'updated_at']) || $value === null || $value === '') continue;
                
                $label = $labels[$key] ?? $key;
                $displayVal = $value;

                if ($key === 'expiry_date') {
                    $displayVal = $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '';
                } elseif ($key === 'status') {
                    $statusLabels = [
                        'active' => 'Đang sử dụng',
                        'out_of_stock' => 'Hết hàng',
                        'expired' => 'Hết hạn',
                    ];
                    $displayVal = $statusLabels[$value] ?? $value;
                }

                $details[] = [
                    'field' => $key,
                    'label' => $label,
                    'old' => null,
                    'new' => $displayVal,
                ];
            }

            \App\Models\MedicinalHerbStockLog::create([
                'medicinal_herb_id' => $herb->id,
                'user_id' => auth()->id(),
                'old_quantity' => 0,
                'new_quantity' => $herb->stock_quantity,
                'change_quantity' => $herb->stock_quantity,
                'action_type' => $herb->stockLogType ?? 'manual_update',
                'note' => $herb->stockLogNote ?? 'Khởi tạo dược liệu mới',
                'details' => $details,
            ]);
        });

        static::updating(function ($herb) {
            $dirty = $herb->getDirty();
            unset($dirty['updated_at']);

            if (!empty($dirty)) {
                $labels = [
                    'name' => 'Tên dược liệu',
                    'category' => 'Phân loại',
                    'usage_type' => 'Cách dùng',
                    'unit' => 'Đơn vị tính',
                    'stock_quantity' => 'Số lượng tồn kho',
                    'expiry_date' => 'Hạn sử dụng',
                    'warning_note' => 'Cảnh báo đặc biệt',
                    'status' => 'Trạng thái',
                    'description' => 'Mô tả / Tác dụng',
                ];

                $changes = [];
                foreach ($dirty as $key => $newValue) {
                    if (in_array($key, ['updated_at', 'created_at'])) continue;

                    $oldValue = $herb->getOriginal($key);
                    $label = $labels[$key] ?? $key;

                    $oldDisplay = $oldValue;
                    $newDisplay = $newValue;

                    if ($key === 'expiry_date') {
                        $oldDisplay = $oldValue ? \Carbon\Carbon::parse($oldValue)->format('d/m/Y') : 'Không có';
                        $newDisplay = $newValue ? \Carbon\Carbon::parse($newValue)->format('d/m/Y') : 'Không có';
                    } elseif ($key === 'status') {
                        $statusLabels = [
                            'active' => 'Đang sử dụng',
                            'out_of_stock' => 'Hết hàng',
                            'expired' => 'Hết hạn',
                        ];
                        $oldDisplay = $statusLabels[$oldValue] ?? $oldValue;
                        $newDisplay = $statusLabels[$newValue] ?? $newValue;
                    }

                    $changes[] = [
                        'field' => $key,
                        'label' => $label,
                        'old' => $oldDisplay,
                        'new' => $newDisplay,
                    ];
                }

                $oldQty = $herb->getOriginal('stock_quantity') ?? 0;
                $newQty = $herb->stock_quantity;
                $changeQty = $newQty - $oldQty;

                $herb->_pendingStockLog = [
                    'old_quantity' => $oldQty,
                    'new_quantity' => $newQty,
                    'change_quantity' => $changeQty,
                    'action_type' => $herb->stockLogType ?? 'manual_update',
                    'note' => $herb->stockLogNote,
                    'details' => $changes,
                ];
            }
        });

        static::updated(function ($herb) {
            if (isset($herb->_pendingStockLog)) {
                $logData = $herb->_pendingStockLog;
                
                if ($logData['action_type'] === 'manual_update' && empty($logData['note'])) {
                    $diff = $logData['change_quantity'];
                    $diffStr = ($diff > 0 ? '+' : '') . floatval($diff) . ' ' . $herb->unit;
                    $logData['note'] = "Điều chỉnh số lượng tồn kho ($diffStr)";
                }

                \App\Models\MedicinalHerbStockLog::create([
                    'medicinal_herb_id' => $herb->id,
                    'user_id' => auth()->id(),
                    'old_quantity' => $logData['old_quantity'],
                    'new_quantity' => $logData['new_quantity'],
                    'change_quantity' => $logData['change_quantity'],
                    'action_type' => $logData['action_type'],
                    'note' => $logData['note'],
                    'details' => $logData['details'] ?? null,
                ]);
                unset($herb->_pendingStockLog);
            }
        });
    }

    // ── Relationships ──────────────────────────────────────────

    /** Tất cả lần được kê trong đơn thuốc */
    public function prescriptionItems()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    // ── Scopes ────────────────────────────────────────────────

    /** Chỉ lấy dược liệu đang hoạt động */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /** Chỉ lấy dược liệu còn hàng */
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    // ── Helper ────────────────────────────────────────────────

    /** Kiểm tra còn hàng */
    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    /** Kiểm tra sắp hết hàng */
    public function isWarningStock(): bool
    {
        if ($this->stock_quantity <= 0) {
            return false;
        }

        if (trim($this->category) === 'Dược liệu bốc thuốc') {
            return $this->stock_quantity < 500;
        }

        return $this->stock_quantity <= 10;
    }

    /** Kiểm tra hết hạn */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /** Nhãn trạng thái tiếng Việt */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'       => 'Còn hàng',
            'out_of_stock' => 'Hết hàng',
            'expired'      => 'Hết hạn',
            default        => 'Không xác định',
        };
    }

    /** Lấy mã lô hàng mới nhất từ hệ thống kho mới để hiển thị/sửa trên UI */
    public function getLatestBatchCodeAttribute(): string
    {
        $item = \App\Models\InventoryItem::where('name', $this->name)->first();
        if ($item) {
            $batch = $item->batches()->orderBy('id', 'desc')->first();
            return $batch ? ($batch->batch_number ?? '') : '';
        }
        return '';
    }
}
