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
        Schema::table('sample_prescriptions', function (Blueprint $table) {
            $table->text('suggested_condition')->nullable()->change();
            $table->text('usage_instruction')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sample_prescriptions', function (Blueprint $table) {
            $table->string('suggested_condition', 255)->nullable()->change();
            $table->string('usage_instruction', 255)->nullable()->change();
        });
    }
};
