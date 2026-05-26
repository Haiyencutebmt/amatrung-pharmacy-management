<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Quản Trị Viên',
                'email'    => 'admin@amatrung.vn',
                'phone'    => '0901000001',
                'password' => Hash::make('Admin@123'),
                'role'     => 'admin',
                'is_active'=> 1,
            ],
            [
                'name'     => 'Nguyễn Thị Lan',
                'email'    => 'lan.staff@amatrung.vn',
                'phone'    => '0901000002',
                'password' => Hash::make('Staff@123'),
                'role'     => 'staff',
                'is_active'=> 1,
            ],
            [
                'name'     => 'Trần Văn Minh',
                'email'    => 'minh.staff@amatrung.vn',
                'phone'    => '0901000003',
                'password' => Hash::make('Staff@123'),
                'role'     => 'staff',
                'is_active'=> 1,
            ],
            [
                'name'     => 'Lê Thị Hoa',
                'email'    => 'hoa.user@gmail.com',
                'phone'    => '0912345601',
                'password' => Hash::make('User@123'),
                'role'     => 'user',
                'is_active'=> 1,
            ],
            [
                'name'     => 'Phạm Văn Đức',
                'email'    => 'duc.user@gmail.com',
                'phone'    => '0912345602',
                'password' => Hash::make('User@123'),
                'role'     => 'user',
                'is_active'=> 1,
            ],
            [
                'name'     => 'Vũ Thị Mai',
                'email'    => 'mai.user@gmail.com',
                'phone'    => '0912345603',
                'password' => Hash::make('User@123'),
                'role'     => 'user',
                'is_active'=> 1,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('✅ Đã tạo 6 tài khoản: 1 admin, 2 staff, 3 user');
        $this->command->info('   Admin: admin@amatrung.vn / Admin@123');
        $this->command->info('   Staff: lan.staff@amatrung.vn / Staff@123');
    }
}
