<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patients = [
            ['full_name' => 'Nguyễn Văn An',    'phone' => '0911111101', 'date_of_birth' => '1955-03-12', 'gender' => 'male',   'address' => '12 Lê Lợi, TP. Đà Lạt', 'note' => 'Tiểu đường type 2, cần lưu ý'],
            ['full_name' => 'Trần Thị Bình',     'phone' => '0911111102', 'date_of_birth' => '1962-07-25', 'gender' => 'female', 'address' => '45 Nguyễn Trãi, TP. Đà Lạt', 'guardian_name' => 'Trần Văn Tâm', 'guardian_phone' => '0922222201', 'relationship' => 'con trai'],
            ['full_name' => 'Lê Văn Cường',      'phone' => '0911111103', 'date_of_birth' => '1948-11-05', 'gender' => 'male',   'address' => '78 Trần Phú, TP. Đà Lạt', 'note' => 'Huyết áp cao, dị ứng nhân sâm'],
            ['full_name' => 'Phạm Thị Dung',     'phone' => '0911111104', 'date_of_birth' => '1970-02-14', 'gender' => 'female', 'address' => '23 Hoàng Văn Thụ, TP. Đà Lạt'],
            ['full_name' => 'Hoàng Văn Em',      'phone' => '0911111105', 'date_of_birth' => '1938-09-30', 'gender' => 'male',   'address' => '56 Phan Đình Phùng, TP. Đà Lạt', 'guardian_name' => 'Hoàng Thị Lan', 'guardian_phone' => '0922222202', 'relationship' => 'con gái'],
            ['full_name' => 'Đặng Thị Phượng',  'phone' => '0911111106', 'date_of_birth' => '1965-06-18', 'gender' => 'female', 'address' => '34 Lý Tự Trọng, TP. Đà Lạt'],
            ['full_name' => 'Bùi Văn Giang',     'phone' => '0911111107', 'date_of_birth' => '1952-12-22', 'gender' => 'male',   'address' => '67 Hai Bà Trưng, TP. Đà Lạt', 'note' => 'Đau lưng mãn tính'],
            ['full_name' => 'Ngô Thị Hạnh',      'phone' => '0911111108', 'date_of_birth' => '1975-04-08', 'gender' => 'female', 'address' => '89 Trần Hưng Đạo, TP. Đà Lạt'],
            ['full_name' => 'Đinh Văn Inh',      'phone' => '0911111109', 'date_of_birth' => '1943-08-16', 'gender' => 'male',   'address' => '11 Võ Thị Sáu, TP. Đà Lạt', 'guardian_name' => 'Đinh Thị Ngọc', 'guardian_phone' => '0922222203', 'relationship' => 'con gái'],
            ['full_name' => 'Lương Thị Kim',     'phone' => '0911111110', 'date_of_birth' => '1968-01-27', 'gender' => 'female', 'address' => '22 Nguyễn Du, TP. Đà Lạt'],
        ];

        foreach ($patients as $data) {
            $data['patient_code'] = Patient::generateCode();
            Patient::create($data);
        }

        $this->command->info('✅ Đã tạo 10 bệnh nhân mẫu (BN0001 ~ BN0010)');
    }
}
