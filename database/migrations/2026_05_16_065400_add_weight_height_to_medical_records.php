<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->decimal('weight', 5, 1)->nullable()->after('visit_date')->comment('Cân nặng (kg) tại lần khám');
            $table->decimal('height', 5, 1)->nullable()->after('weight')->comment('Chiều cao (cm) tại lần khám');
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn(['weight', 'height']);
        });
    }
};
