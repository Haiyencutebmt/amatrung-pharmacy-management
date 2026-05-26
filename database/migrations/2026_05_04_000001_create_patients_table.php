<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_code', 20)->unique()->comment('Mã bệnh nhân, VD: BN0001');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('full_name', 100);
            $table->string('phone', 15)->nullable()->comment('Số liên lạc, không unique');
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('address')->nullable();
            $table->string('guardian_name', 100)->nullable()->comment('Tên người thân/giám hộ');
            $table->string('guardian_phone', 15)->nullable();
            $table->string('relationship', 50)->nullable()->comment('VD: con, vợ, chồng');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
