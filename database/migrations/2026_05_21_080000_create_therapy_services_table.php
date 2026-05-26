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
        Schema::create('therapy_services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->integer('default_sessions')->nullable()->default(1);
            $table->text('default_instruction')->nullable();
            $table->string('status', 50)->default('active'); // active, inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapy_services');
    }
};
