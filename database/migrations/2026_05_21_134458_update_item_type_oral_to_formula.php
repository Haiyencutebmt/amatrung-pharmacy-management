<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('prescription_items')
            ->where('item_type', 'oral_herb')
            ->update(['item_type' => 'formula_herb']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('prescription_items')
            ->where('item_type', 'formula_herb')
            ->update(['item_type' => 'oral_herb']);
    }
};
