<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Thứ tự quan trọng: users → patients → medicinal_herbs → medical_records (→ prescriptions → prescription_items) → articles
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,           // 1. Tài khoản (admin, staff, user)
            PatientSeeder::class,        // 2. Bệnh nhân
            MedicinalHerbSeeder::class,  // 3. Kho dược liệu (phải trước prescription_items)
            ExternalPrepSeeder::class,   // 3b. Thuốc bó & Rượu thuốc xoa bóp
            TherapyServiceSeeder::class, // 4. Dịch vụ trị liệu
            MedicalRecordSeeder::class,  // 5. Bệnh án + Đơn thuốc + Chi tiết đơn
            ArticleSeeder::class,        // 6. Bài viết

        ]);

        $this->command->info('');
        $this->command->info('🌿 AmaTrung — Seeding hoàn tất!');
        $this->command->info('─────────────────────────────────────');
        $this->command->info('  Admin:  admin@amatrung.vn / Admin@123');
        $this->command->info('  Staff:  lan.staff@amatrung.vn / Staff@123');
        $this->command->info('  User:   hoa.user@gmail.com / User@123');
        $this->command->info('─────────────────────────────────────');
    }
}
