<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retail_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 20)->unique()->comment('Mã phiếu, VD: BL0001');
            $table->foreignId('staff_id')->constrained('users')->comment('Nhân viên lập phiếu');
            $table->string('customer_name', 100)->default('Khách lẻ')->comment('Tên khách hàng');
            $table->string('customer_phone', 20)->nullable();
            $table->string('customer_address', 255)->nullable();
            $table->text('note')->nullable()->comment('Ghi chú phiếu bán');
            $table->decimal('total_amount', 12, 2)->default(0)->comment('Tổng tiền nếu cần');
            $table->timestamps();
        });

        Schema::create('retail_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retail_order_id')->constrained('retail_orders')->cascadeOnDelete();
            $table->foreignId('medicinal_herb_id')->constrained('medicinal_herbs');
            $table->decimal('quantity', 10, 2)->comment('Số lượng xuất');
            $table->string('unit', 50)->nullable()->comment('Đơn vị tính');
            $table->decimal('unit_price', 12, 2)->default(0)->comment('Đơn giá nếu cần');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retail_order_items');
        Schema::dropIfExists('retail_orders');
    }
};
