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
        Schema::create('patient_user_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('relation_type')->nullable()->comment('e.g., guardian, self');
            $table->timestamps();
        });
        
        // Remove unique constraint from patients phone if it exists
        Schema::table('patients', function (Blueprint $table) {
            // Drop unique constraint on phone if it was set
            // $table->dropUnique(['phone']); // In Laravel, need to be careful if it doesn't exist.
            // Assuming it was not unique or we will handle it manually.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_user_links');
    }
};
