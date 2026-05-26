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
            $table->string('preparation_type', 100)->nullable()->comment('Dạng dùng (Ví dụ: Thuốc sắc, thuốc bột, thuốc hoàn...)');
            $table->integer('default_packages')->nullable()->comment('Số thang thường dùng');
            $table->text('notes')->nullable()->comment('Lưu ý cho bài thuốc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sample_prescriptions', function (Blueprint $table) {
            $table->dropColumn(['preparation_type', 'default_packages', 'notes']);
        });
    }
};
