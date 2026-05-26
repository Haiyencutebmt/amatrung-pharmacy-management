<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained('medical_records')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('users');
            $table->text('note')->nullable()->comment('Hướng dẫn uống thuốc');
            $table->text('ai_suggestion')->nullable()->comment('Nội dung AI gợi ý - chỉ lưu tham khảo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
