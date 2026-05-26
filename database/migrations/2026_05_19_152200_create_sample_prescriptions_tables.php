<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sample_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->comment('Tên bài thuốc mẫu');
            $table->string('suggested_condition', 255)->nullable()->comment('Chỉ định / Triệu chứng phù hợp');
            $table->string('usage_instruction', 255)->nullable()->comment('Cách dùng tổng quát mặc định');
            $table->timestamps();
        });

        Schema::create('sample_prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sample_prescription_id')->constrained('sample_prescriptions')->cascadeOnDelete();
            $table->foreignId('medicinal_herb_id')->constrained('medicinal_herbs')->cascadeOnDelete();
            $table->decimal('quantity', 10, 2)->comment('Liều lượng vị thuốc này (g) trong 1 thang');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sample_prescription_items');
        Schema::dropIfExists('sample_prescriptions');
    }
};
