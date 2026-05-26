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
        Schema::table('prescription_items', function (Blueprint $table) {
            $table->unsignedBigInteger('packaged_product_id')->nullable()->after('medicinal_herb_id');
            $table->foreign('packaged_product_id')->references('id')->on('packaged_products')->onDelete('set null');
        });

        // Chuyển trạng thái Bó thuốc nam (ID 174) và Lọ rượu thuốc xoa bóp (ID 175) sang inactive trong medicinal_herbs
        DB::table('medicinal_herbs')
            ->whereIn('id', [174, 175])
            ->update(['status' => 'inactive']);

        // Thêm dịch vụ Bó thuốc nam vào therapy_services nếu chưa tồn tại
        $exists = DB::table('therapy_services')->where('name', 'Bó thuốc nam')->exists();
        if (!$exists) {
            DB::table('therapy_services')->insert([
                'name' => 'Bó thuốc nam',
                'default_sessions' => 1,
                'default_instruction' => 'Bó đắp ngoài da tại vị trí đau',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescription_items', function (Blueprint $table) {
            $table->dropForeign(['packaged_product_id']);
            $table->dropColumn('packaged_product_id');
        });

        DB::table('medicinal_herbs')
            ->whereIn('id', [174, 175])
            ->update(['status' => 'active']);

        DB::table('therapy_services')->where('name', 'Bó thuốc nam')->delete();
    }
};
