<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->boolean('is_legacy_data')->default(false)->after('treatment_plan')->comment('Dữ liệu nhập từ hồ sơ cũ');
            $table->string('legacy_source', 50)->nullable()->after('is_legacy_data')->comment('Nguồn: paper_record, csv_import');
            $table->text('legacy_note')->nullable()->after('legacy_source')->comment('Ghi chú riêng cho dữ liệu cũ');
            $table->timestamp('imported_at')->nullable()->after('legacy_note');
            $table->unsignedBigInteger('imported_by')->nullable()->after('imported_at');
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->boolean('is_legacy_data')->default(false)->after('ai_suggestion')->comment('Đơn thuốc nhập từ hồ sơ cũ');
            $table->string('legacy_source', 50)->nullable()->after('is_legacy_data');
            $table->text('legacy_note')->nullable()->after('legacy_source');
            $table->boolean('affect_stock')->default(true)->after('legacy_note')->comment('false = không trừ tồn kho (đơn cũ)');
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn(['is_legacy_data', 'legacy_source', 'legacy_note', 'imported_at', 'imported_by']);
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn(['is_legacy_data', 'legacy_source', 'legacy_note', 'affect_stock']);
        });
    }
};
