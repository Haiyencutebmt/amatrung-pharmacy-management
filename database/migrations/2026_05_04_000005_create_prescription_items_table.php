<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->cascadeOnDelete();
            $table->foreignId('medicinal_herb_id')->constrained('medicinal_herbs');
            $table->decimal('quantity', 10, 2)->comment('Số lượng dùng cho đơn thuốc này');
            $table->string('unit', 50)->nullable()->comment('Đơn vị: g, lạng, viên (có thể khác đơn vị kho)');
            $table->string('dosage', 255)->nullable()->comment('Hướng dẫn liều dùng');
            $table->string('note', 255)->nullable()->comment('Ghi chú riêng cho vị thuốc này');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
