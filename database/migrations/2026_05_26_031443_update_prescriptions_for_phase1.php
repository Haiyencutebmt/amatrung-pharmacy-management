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
            if (!Schema::hasColumn('prescriptions', 'status')) {
                $table->string('status')->default('draft')->comment('draft, confirmed, dispensed, cancelled');
            }
        });
        
        Schema::table('prescription_items', function (Blueprint $table) {
            if (!Schema::hasColumn('prescription_items', 'inventory_item_id')) {
                $table->unsignedBigInteger('inventory_item_id')->nullable()->after('prescription_id');
                $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescription_items', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
            $table->dropColumn('inventory_item_id');
        });
        
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
