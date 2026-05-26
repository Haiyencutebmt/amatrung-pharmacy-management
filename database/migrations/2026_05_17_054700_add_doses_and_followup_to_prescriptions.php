<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->unsignedInteger('num_of_doses')->default(1)->after('note')->comment('Số thang thuốc');
            $table->text('usage_instruction')->nullable()->after('num_of_doses')->comment('Cách sắc/dùng thuốc');
            $table->date('follow_up_date')->nullable()->after('usage_instruction')->comment('Ngày hẹn tái khám');
        });
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn(['num_of_doses', 'usage_instruction', 'follow_up_date']);
        });
    }
};
