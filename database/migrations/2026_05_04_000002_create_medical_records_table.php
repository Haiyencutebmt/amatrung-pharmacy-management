<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('users');
            $table->date('visit_date');
            $table->text('symptoms')->comment('Triệu chứng');
            $table->text('diagnosis')->comment('Chẩn đoán');
            $table->text('treatment_plan')->nullable()->comment('Phác đồ điều trị');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
