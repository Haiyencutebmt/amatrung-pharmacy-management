<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $guarded = ['id'];

    public function batches()
    {
        return $this->hasMany(InventoryBatch::class);
    }
}
