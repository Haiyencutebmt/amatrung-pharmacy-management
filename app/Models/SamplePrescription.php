<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SamplePrescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'suggested_condition',
        'usage_instruction',
        'preparation_type',
        'default_packages',
        'notes',
    ];

    public function items()
    {
        return $this->hasMany(SamplePrescriptionItem::class, 'sample_prescription_id');
    }
}
