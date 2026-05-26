<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packaged_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                     // Tên thuốc dùng ngoài/Trà thảo mộc
            $table->text('description')->nullable();                    // Mô tả / thành phần
            $table->string('sku')->nullable()->unique();                // Mã SKU sản phẩm
            $table->foreignId('medicinal_herb_id')                     // Dược liệu nguồn
                  ->constrained('medicinal_herbs')
                  ->onDelete('restrict');
            $table->decimal('herb_quantity_per_unit', 10, 2)->default(0); // Lượng dược liệu / 1 đơn vị sp
            $table->string('herb_unit')->default('gram');               // Đơn vị dược liệu (gram, kg...)
            $table->string('unit')->default('gói');                    // Đơn vị sản phẩm (gói, hộp, lọ...)
            $table->decimal('stock_quantity', 10, 2)->default(0);      // Tồn kho số lượng sản phẩm
            $table->decimal('price', 12, 2)->default(0);               // Giá bán
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packaged_products');
    }
};
