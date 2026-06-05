<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            if (!Schema::hasColumn('medical_records', 'diagnosis_confirmed_at')) {
                $table->timestamp('diagnosis_confirmed_at')->nullable();
            }

            if (!Schema::hasColumn('medical_records', 'diagnosis_confirmed_by')) {
                $table->unsignedBigInteger('diagnosis_confirmed_by')->nullable();
            }
        });

        DB::table('medical_records')
            ->whereNotNull('diagnosis')
            ->whereRaw('TRIM(diagnosis) <> ?', [''])
            ->whereRaw('TRIM(diagnosis) <> ?', ['Chưa chẩn đoán'])
            ->whereNull('diagnosis_confirmed_at')
            ->update([
                'diagnosis_confirmed_at' => now(),
                'diagnosis_confirmed_by' => DB::raw('staff_id'),
            ]);
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            if (Schema::hasColumn('medical_records', 'diagnosis_confirmed_by')) {
                $table->dropColumn('diagnosis_confirmed_by');
            }

            if (Schema::hasColumn('medical_records', 'diagnosis_confirmed_at')) {
                $table->dropColumn('diagnosis_confirmed_at');
            }
        });
    }
};
