<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->string('treatment_type', 50)->default('combined')->after('staff_id')->comment('Loại điều trị: herbal_only, external_only, service_only, combined');
            $table->unsignedInteger('course_days')->nullable()->after('usage_instruction')->comment('Liệu trình số ngày');
            $table->text('public_instruction')->nullable()->after('note')->comment('Hướng dẫn công khai cho bệnh nhân (in trên phiếu)');
            $table->text('internal_note')->nullable()->after('public_instruction')->comment('Ghi chú nội bộ cho nhân viên (không in)');
        });
    }

    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn(['treatment_type', 'course_days', 'public_instruction', 'internal_note']);
        });
    }
};
