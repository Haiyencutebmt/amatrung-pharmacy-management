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
        Schema::table('patient_user_links', function (Blueprint $table) {
            $table->renameColumn('relation_type', 'relationship_type');
            $table->boolean('is_verified')->default(false);
            $table->unique(['user_id', 'patient_id']);
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->string('usage_route')->nullable()->comment('oral, external');
            $table->text('warning_note')->nullable();
            $table->string('legacy_source_table')->nullable();
            $table->unsignedBigInteger('legacy_source_id')->nullable();
            
            $table->unique(['legacy_source_table', 'legacy_source_id'], 'idx_legacy_source');
        });

        Schema::table('inventory_batches', function (Blueprint $table) {
            $table->renameColumn('quantity', 'quantity_remaining');
            // Status was created as string, it inherently supports available, unknown_expiry, blocked, etc.
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            // Movement type was created as string, supporting opening_balance
        });
        
        // Add quantity fields to prescription_items if they don't exist
        Schema::table('prescription_items', function (Blueprint $table) {
            if (!Schema::hasColumn('prescription_items', 'quantity_per_dose')) {
                $table->decimal('quantity_per_dose', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('prescription_items', 'number_of_doses')) {
                $table->integer('number_of_doses')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add down logic if needed for rollback
    }
};
