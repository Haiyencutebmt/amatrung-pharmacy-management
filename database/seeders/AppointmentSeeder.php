<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = \App\Models\Patient::all();

        if ($patients->isEmpty()) {
            return;
        }

        $now = now();
        $year = $now->year;
        $month = $now->month;

        for ($i = 0; $i < 20; $i++) {
            $patient = $patients->random();
            $day = rand(1, 31);
            
            // Handle invalid dates like Feb 30th etc (though May has 31)
            try {
                $date = \Carbon\Carbon::create($year, $month, $day);
            } catch (\Exception $e) {
                continue;
            }

            \App\Models\Appointment::create([
                'patient_id' => $patient->id,
                'appointment_date' => $date->toDateString(),
                'appointment_time' => rand(8, 17) . ':' . (rand(0, 1) ? '00' : '30'),
                'reason' => 'Tái khám định kỳ',
                'status' => 'confirmed',
                'notes' => 'Bệnh nhân cần mang theo kết quả xét nghiệm cũ.',
            ]);
        }
    }
}
