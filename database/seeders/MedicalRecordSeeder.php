<?php

namespace Database\Seeders;

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\MedicinalHerb;
use App\Models\User;
use Illuminate\Database\Seeder;

class MedicalRecordSeeder extends Seeder
{
    public function run(): void
    {
        $staff = User::where('role', 'staff')->first();

        if (!$staff) {
            $this->command->warn('⚠️  Chưa có staff, bỏ qua MedicalRecordSeeder.');
            return;
        }

        $patients = Patient::take(4)->get();

        if ($patients->count() < 2) {
            $this->command->warn('⚠️  Chưa đủ bệnh nhân, bỏ qua MedicalRecordSeeder.');
            return;
        }

        // Bệnh án 1: bệnh nhân đầu tiên
        $record1 = MedicalRecord::create([
            'patient_id'     => $patients[0]->id,
            'staff_id'       => $staff->id,
            'visit_date'     => now()->subDays(14)->format('Y-m-d'),
            'symptoms'       => 'Mệt mỏi, chán ăn, hơi thở ngắn, da vàng nhạt, ngủ kém.',
            'diagnosis'      => 'Khí huyết lưỡng hư, tỳ vị hư nhược.',
            'treatment_plan' => 'Bổ khí dưỡng huyết, kiện tỳ ích vị. Dùng bài Bát Trân Thang gia giảm. Nghỉ ngơi hợp lý, ăn đủ chất.',
        ]);

        // Đơn thuốc cho bệnh án 1
        $prescription1 = Prescription::create([
            'medical_record_id' => $record1->id,
            'staff_id'          => $staff->id,
            'note'              => 'Sắc uống ngày 1 thang, chia 3 lần. Uống ấm sau bữa ăn. Kiêng đồ lạnh, bia rượu.',
        ]);

        $hoangKy    = MedicinalHerb::where('name', 'Hoàng Kỳ')->first();
        $duongQuy   = MedicinalHerb::where('name', 'Đương Quy')->first();
        $bachTruat  = MedicinalHerb::where('name', 'Bạch Truật')->first();
        $camThao    = MedicinalHerb::where('name', 'Cam Thảo')->first();
        $phucLinh   = MedicinalHerb::where('name', 'Phục Linh')->first();

        if ($hoangKy && $duongQuy && $bachTruat && $camThao && $phucLinh) {
            PrescriptionItem::insert([
                ['prescription_id' => $prescription1->id, 'medicinal_herb_id' => $hoangKy->id,   'quantity' => 20, 'unit' => 'g', 'dosage' => '20g/ngày', 'note' => null, 'created_at' => now(), 'updated_at' => now()],
                ['prescription_id' => $prescription1->id, 'medicinal_herb_id' => $duongQuy->id,  'quantity' => 15, 'unit' => 'g', 'dosage' => '15g/ngày', 'note' => null, 'created_at' => now(), 'updated_at' => now()],
                ['prescription_id' => $prescription1->id, 'medicinal_herb_id' => $bachTruat->id, 'quantity' => 12, 'unit' => 'g', 'dosage' => '12g/ngày', 'note' => null, 'created_at' => now(), 'updated_at' => now()],
                ['prescription_id' => $prescription1->id, 'medicinal_herb_id' => $phucLinh->id,  'quantity' => 12, 'unit' => 'g', 'dosage' => '12g/ngày', 'note' => null, 'created_at' => now(), 'updated_at' => now()],
                ['prescription_id' => $prescription1->id, 'medicinal_herb_id' => $camThao->id,   'quantity' => 6,  'unit' => 'g', 'dosage' => '6g/ngày',  'note' => 'Điều hòa các vị', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Bệnh án 2: bệnh nhân thứ 2
        $record2 = MedicalRecord::create([
            'patient_id'     => $patients[1]->id,
            'staff_id'       => $staff->id,
            'visit_date'     => now()->subDays(5)->format('Y-m-d'),
            'symptoms'       => 'Đau lưng âm ỉ, mỏi gối, tiểu đêm 2–3 lần, hay hoa mắt chóng mặt.',
            'diagnosis'      => 'Thận âm dương lưỡng hư, thiên về thận âm hư.',
            'treatment_plan' => 'Bổ thận âm, nạp khí quy nguyên. Dùng Lục Vị Địa Hoàng Hoàn. Hạn chế thức khuya.',
        ]);

        // Đơn thuốc cho bệnh án 2 (dùng chế phẩm)
        $lvcDh = MedicinalHerb::where('name', 'Lục Vị Địa Hoàng Hoàn')->first();
        $thucDia = MedicinalHerb::where('name', 'Thục Địa')->first();

        $prescription2 = Prescription::create([
            'medical_record_id' => $record2->id,
            'staff_id'          => $staff->id,
            'note'              => 'Uống Lục Vị Địa Hoàng Hoàn theo hướng dẫn hộp. Kết hợp nghỉ ngơi sớm trước 23h.',
        ]);

        if ($lvcDh) {
            PrescriptionItem::create([
                'prescription_id'  => $prescription2->id,
                'medicinal_herb_id'=> $lvcDh->id,
                'quantity'         => 1,
                'unit'             => 'hộp',
                'dosage'           => '8 viên/ngày, chia 2 lần',
                'note'             => 'Dùng 1 liệu trình 30 ngày, tái khám sau khi hết',
            ]);
        }

        $this->command->info('✅ Đã tạo 2 bệnh án và 2 đơn thuốc mẫu');
    }
}
