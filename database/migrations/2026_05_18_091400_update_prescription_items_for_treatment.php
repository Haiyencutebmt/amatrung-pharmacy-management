<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prescription_items', function (Blueprint $table) {
            // Chuyển medicinal_herb_id thành nullable để hỗ trợ dịch vụ trị liệu
            $table->foreignId('medicinal_herb_id')->nullable()->change();

            // Thêm các cột mới cho đơn điều trị
            $table->string('item_type', 50)->default('herb')->after('medicinal_herb_id')->comment('herb: thuốc uống, external_herb: thuốc bó/dùng ngoài, service: dịch vụ nắn chỉnh/xoa bóp');
            $table->string('custom_name', 255)->nullable()->after('item_type')->comment('Tên tự nhập nếu không chọn từ kho');
            $table->boolean('is_secret_formula')->default(false)->after('note')->comment('Công thức gia truyền - không in thành phần');
            $table->boolean('affects_stock')->default(true)->after('is_secret_formula')->comment('Có ảnh hưởng tồn kho không');
            $table->string('usage_area', 255)->nullable()->after('affects_stock')->comment('Vùng điều trị: vai trái, lưng, đầu gối phải...');
            $table->unsignedInteger('sessions')->nullable()->after('usage_area')->comment('Số buổi/lần (cho dịch vụ)');
            $table->text('usage_instruction')->nullable()->after('sessions')->comment('Hướng dẫn sử dụng chi tiết riêng cho item');
        });
    }

    public function down(): void
    {
        Schema::table('prescription_items', function (Blueprint $table) {
            $table->dropColumn([
                'item_type', 'custom_name', 'is_secret_formula',
                'affects_stock', 'usage_area', 'sessions', 'usage_instruction',
            ]);

            // Phục hồi medicinal_herb_id về NOT NULL
            $table->foreignId('medicinal_herb_id')->nullable(false)->change();
        });
    }
};
