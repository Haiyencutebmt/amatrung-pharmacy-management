<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->string('record_code', 20)->nullable()->unique()->after('id')
                  ->comment('Mã hồ sơ bệnh án (BA0001, BA0002...)');
        });

        // Gán mã cho các bệnh án cũ đã có
        $records = DB::table('medical_records')->orderBy('id')->get();
        foreach ($records as $record) {
            $code = 'BA' . str_pad($record->id, 4, '0', STR_PAD_LEFT);
            DB::table('medical_records')->where('id', $record->id)->update(['record_code' => $code]);
        }
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn('record_code');
        });
    }
};
