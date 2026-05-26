<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('retail_order_items')->truncate();
        DB::table('retail_orders')->truncate();
        Schema::enableForeignKeyConstraints();

        Schema::table('retail_order_items', function (Blueprint $table) {
            // Xóa foreign key cũ
            $table->dropForeign(['medicinal_herb_id']);
            $table->dropColumn('medicinal_herb_id');

            // Thêm foreign key mới → packaged_products
            $table->foreignId('packaged_product_id')
                  ->after('retail_order_id')
                  ->constrained('packaged_products')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        DB::table('retail_order_items')->truncate();
        DB::table('retail_orders')->truncate();

        Schema::table('retail_order_items', function (Blueprint $table) {
            $table->dropForeign(['packaged_product_id']);
            $table->dropColumn('packaged_product_id');

            $table->foreignId('medicinal_herb_id')
                  ->after('retail_order_id')
                  ->constrained('medicinal_herbs')
                  ->onDelete('restrict');
        });
    }
};
