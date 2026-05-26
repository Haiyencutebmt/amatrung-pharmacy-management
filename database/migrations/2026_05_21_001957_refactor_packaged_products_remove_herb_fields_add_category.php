<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packaged_products', function (Blueprint $table) {
            // Xóa foreign key trước khi xóa cột
            $table->dropForeign(['medicinal_herb_id']);

            // Xóa các cột không còn phù hợp
            $table->dropColumn([
                'medicinal_herb_id',
                'herb_quantity_per_unit',
                'herb_unit',
            ]);

            // Thêm cột phân loại sản phẩm
            $table->string('category')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('packaged_products', function (Blueprint $table) {
            $table->dropColumn('category');

            $table->foreignId('medicinal_herb_id')
                  ->nullable()
                  ->constrained('medicinal_herbs')
                  ->onDelete('restrict');
            $table->decimal('herb_quantity_per_unit', 10, 2)->default(0);
            $table->string('herb_unit')->default('gram');
        });
    }
};
