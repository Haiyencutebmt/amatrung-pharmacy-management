<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SamplePrescriptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sample_prescription_id',
        'medicinal_herb_id',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
        ];
    }

    public function samplePrescription()
    {
        return $this->belongsTo(SamplePrescription::class, 'sample_prescription_id');
    }

    public function medicinalHerb()
    {
        return $this->belongsTo(MedicinalHerb::class, 'medicinal_herb_id');
    }
}
