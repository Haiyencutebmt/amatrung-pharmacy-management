<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->string('case_type', 50)->default('general')->after('doctor_note')->comment('Loại ca khám: general, musculoskeletal');
            $table->string('injury_type', 100)->nullable()->after('case_type')->comment('Loại tổn thương: bong_gan, trat_khop, nghi_gay_xuong, dau_vai_gay, dau_lung, dau_goi, khac');
            $table->string('injury_location', 255)->nullable()->after('injury_type')->comment('Vị trí đau/chấn thương');
            $table->text('injury_cause')->nullable()->after('injury_location')->comment('Nguyên nhân chấn thương');
            $table->text('clinical_signs')->nullable()->after('injury_cause')->comment('Dấu hiệu lâm sàng: sưng, bầm, biến dạng...');
            $table->text('palpation_result')->nullable()->after('clinical_signs')->comment('Kết quả thăm khám/nắn/chạm');
            $table->tinyInteger('pain_level')->nullable()->after('palpation_result')->comment('Mức độ đau 0-10');
            $table->string('xray_image', 500)->nullable()->after('pain_level')->comment('Đường dẫn ảnh phim/chụp');
            $table->text('xray_note')->nullable()->after('xray_image')->comment('Ghi chú ảnh phim');
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn([
                'case_type', 'injury_type', 'injury_location', 'injury_cause',
                'clinical_signs', 'palpation_result', 'pain_level',
                'xray_image', 'xray_note',
            ]);
        });
    }
};
