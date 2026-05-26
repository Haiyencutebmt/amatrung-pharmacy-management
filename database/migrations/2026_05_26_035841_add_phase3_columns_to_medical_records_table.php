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
            $table->string('referral_reason')->nullable();
            $table->text('allergies')->nullable();
            $table->text('underlying_diseases')->nullable();
            $table->text('current_medications')->nullable();
            
            // X-ray file column (for private storage paths)
            $table->string('xray_file_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn(['referral_reason', 'allergies', 'underlying_diseases', 'current_medications', 'xray_file_path']);
        });
    }
};
