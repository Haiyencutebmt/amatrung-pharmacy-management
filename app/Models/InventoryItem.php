<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['total_available_quantity'];

    public function batches()
    {
        return $this->hasMany(InventoryBatch::class);
    }

    /**
     * Get all batches that are eligible for dispensing under FEFO rules.
     */
    public function getAvailableBatchesAttribute()
    {
        $today = now()->startOfDay();
        return $this->batches->filter(function ($batch) use ($today) {
            return $batch->status === 'available'
                && $batch->quantity_remaining > 0
                && $batch->expiry_date !== null
                && $batch->expiry_date->startOfDay()->gte($today);
        });
    }

    /**
     * Get the preferred FEFO batch (first batch to be dispensed).
     */
    public function getFefoBatchAttribute()
    {
        return $this->available_batches
            ->sort(function ($a, $b) {
                $cmp = $a->expiry_date->timestamp <=> $b->expiry_date->timestamp;
                if ($cmp !== 0) {
                    return $cmp;
                }

                return $a->id <=> $b->id;
            })
            ->first();
    }

    /**
     * Get the total available quantity from eligible batches.
     */
    public function getTotalAvailableQuantityAttribute()
    {
        return (float) $this->available_batches->sum('quantity_remaining');
    }
}
