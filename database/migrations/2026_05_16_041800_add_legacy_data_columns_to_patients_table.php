<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->boolean('is_legacy_data')->default(false)->after('note')->comment('Dữ liệu nhập từ hồ sơ cũ');
            $table->string('legacy_source', 50)->nullable()->after('is_legacy_data')->comment('Nguồn: paper_record, excel_import, csv_import');
            $table->text('legacy_note')->nullable()->after('legacy_source')->comment('Ghi chú riêng cho dữ liệu cũ');
            $table->timestamp('imported_at')->nullable()->after('legacy_note')->comment('Thời điểm nhập dữ liệu cũ');
            $table->unsignedBigInteger('imported_by')->nullable()->after('imported_at')->comment('ID người nhập dữ liệu');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['is_legacy_data', 'legacy_source', 'legacy_note', 'imported_at', 'imported_by']);
        });
    }
};
