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
        Schema::table('medical_records', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_records', 'treatment_direction')) {
                $table->string('treatment_direction')->nullable()->comment('oral_only, external_only, combined, referral');
            }
            if (!Schema::hasColumn('medical_records', 'status')) {
                $table->string('status')->default('pending')->comment('pending, examining, completed, cancelled');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn(['treatment_direction', 'status']);
        });
    }
};
