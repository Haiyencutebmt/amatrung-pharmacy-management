<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->string('status', 30)->default('active')->after('treatment_type')->comment('Trạng thái đơn: active, cancelled');
        });

        Schema::table('prescription_items', function (Blueprint $table) {
            $table->string('item_type', 50)->default('oral_herb')->comment('oral_herb: thuốc uống sắc, external_product: thuốc ngoài, therapy_service: dịch vụ')->change();
        });

        \Illuminate\Support\Facades\DB::table('prescription_items')->where('item_type', 'herb')->update(['item_type' => 'oral_herb']);
        \Illuminate\Support\Facades\DB::table('prescription_items')->where('item_type', 'external_herb')->update(['item_type' => 'external_product']);
        \Illuminate\Support\Facades\DB::table('prescription_items')->where('item_type', 'service')->update(['item_type' => 'therapy_service']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('prescription_items')->where('item_type', 'oral_herb')->update(['item_type' => 'herb']);
        \Illuminate\Support\Facades\DB::table('prescription_items')->where('item_type', 'external_product')->update(['item_type' => 'external_herb']);
        \Illuminate\Support\Facades\DB::table('prescription_items')->where('item_type', 'therapy_service')->update(['item_type' => 'service']);

        Schema::table('prescription_items', function (Blueprint $table) {
            $table->string('item_type', 50)->default('herb')->comment('herb: thuốc uống, external_herb: thuốc bó/dùng ngoài, service: dịch vụ')->change();
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
