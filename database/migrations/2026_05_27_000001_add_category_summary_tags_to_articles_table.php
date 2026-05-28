<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('articles', 'summary')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->text('summary')->nullable()->after('title');
            });
        }

        if (!Schema::hasColumn('articles', 'category')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->string('category', 100)->nullable()->after('featured_image');
            });
        }

        if (!Schema::hasColumn('articles', 'tags')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->json('tags')->nullable()->after('category');
            });
        }
    }

    public function down(): void
    {
        $columns = array_filter(['summary', 'category', 'tags'], fn ($column) => Schema::hasColumn('articles', $column));

        if (!empty($columns)) {
            Schema::table('articles', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
