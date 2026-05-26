<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecordAttachment extends Model
{
    protected $guarded = ['id'];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
