<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientUserLink extends Model
{
    protected $guarded = ['id'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
