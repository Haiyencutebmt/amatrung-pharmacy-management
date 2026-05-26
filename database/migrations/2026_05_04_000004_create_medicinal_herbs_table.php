<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicinal_herbs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->comment('Tên dược liệu hoặc chế phẩm');
            $table->string('category', 100)->nullable()->comment('VD: Dược liệu rời, Chế phẩm đông y');
            $table->string('usage_type', 100)->nullable()->comment('VD: Sắc uống, Ngâm rượu, Đắp ngoài');
            $table->text('description')->nullable()->comment('Công dụng, mô tả');
            $table->string('unit', 50)->comment('Đơn vị: g, kg, lạng, viên, gói, hộp');
            $table->decimal('stock_quantity', 10, 2)->default(0)->comment('Số lượng tồn kho');
            $table->date('expiry_date')->nullable()->comment('Hạn sử dụng');
            $table->text('warning_note')->nullable()->comment('Cảnh báo, chống chỉ định');
            $table->string('status', 50)->nullable()->default('active')
                ->comment('active | out_of_stock | expired');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicinal_herbs');
    }
};
