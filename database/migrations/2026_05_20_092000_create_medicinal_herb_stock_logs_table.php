<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicinal_herb_stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicinal_herb_id')->constrained('medicinal_herbs')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('old_quantity', 10, 2);
            $table->decimal('new_quantity', 10, 2);
            $table->decimal('change_quantity', 10, 2);
            $table->string('action_type', 50)->comment('manual_update | prescription | retail | excel_import');
            $table->string('note', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicinal_herb_stock_logs');
    }
};
