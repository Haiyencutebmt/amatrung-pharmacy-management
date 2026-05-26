<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('herb_dictionary_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 150);
            $table->string('slug', 180)->unique();
            $table->string('scientific_name', 180)->nullable();
            $table->string('other_names', 255)->nullable();
            $table->string('family', 150)->nullable();
            $table->string('plant_part', 150)->nullable();
            $table->string('properties', 255)->nullable()->comment('Tính vị / quy kinh / đặc điểm cơ bản');
            $table->text('basic_info')->nullable();
            $table->text('effects')->nullable();
            $table->text('usage_notes')->nullable();
            $table->text('safety_warning')->nullable();
            $table->string('status', 30)->default('published');
            $table->timestamps();

            $table->index(['status', 'name']);
        });

        Schema::create('herb_dictionary_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->constrained('herb_dictionary_entries')->cascadeOnDelete();
            $table->string('image_path', 500);
            $table->string('caption', 255)->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('herb_dictionary_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('entry_id')->constrained('herb_dictionary_entries')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'entry_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('herb_dictionary_favorites');
        Schema::dropIfExists('herb_dictionary_images');
        Schema::dropIfExists('herb_dictionary_entries');
    }
};
