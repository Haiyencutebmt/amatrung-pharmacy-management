<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\MedicinalHerb;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Top Metrics
        $patientsCount = Patient::count();
        $visitsToday = MedicalRecord::whereDate('visit_date', today())->count();
        $visitsYesterday = MedicalRecord::whereDate('visit_date', today()->subDay())->count();
        $prescriptionsCount = \App\Models\Prescription::count();

        $chartPeriod = $request->query('chart_period') === 'month' ? 'month' : 'year';
        $selectedMonth = (string) $request->query('chart_month', now()->format('Y-m'));
        $selectedMonth = preg_match('/^\d{4}-\d{2}$/', $selectedMonth) ? $selectedMonth : now()->format('Y-m');

        try {
            $chartMonth = Carbon::createFromFormat('Y-m-d', $selectedMonth . '-01')->startOfMonth();
        } catch (\Throwable) {
            $chartMonth = now()->startOfMonth();
        }

        if ($chartPeriod === 'month') {
            $chartStart = $chartMonth->copy()->startOfMonth();
            $chartEnd = $chartMonth->copy()->endOfMonth();

            $visitsByDay = MedicalRecord::whereBetween('visit_date', [$chartStart->toDateString(), $chartEnd->toDateString()])
                ->get(['visit_date'])
                ->groupBy(fn ($record) => (int) $record->visit_date->format('j'))
                ->map->count();

            $patientsByDay = Patient::whereBetween('created_at', [$chartStart, $chartEnd])
                ->get(['created_at'])
                ->groupBy(fn ($patient) => (int) $patient->created_at->format('j'))
                ->map->count();

            $chartItems = collect(range(1, $chartMonth->daysInMonth))->map(function ($day) use ($visitsByDay, $patientsByDay) {
                return [
                    'label' => str_pad((string) $day, 2, '0', STR_PAD_LEFT),
                    'visits' => (int) ($visitsByDay[$day] ?? 0),
                    'patients' => (int) ($patientsByDay[$day] ?? 0),
                ];
            });
        } else {
            $chartStart = $chartMonth->copy()->startOfYear();
            $chartEnd = $chartMonth->copy()->endOfYear();

            $visitsByMonth = MedicalRecord::whereBetween('visit_date', [$chartStart->toDateString(), $chartEnd->toDateString()])
                ->get(['visit_date'])
                ->groupBy(fn ($record) => (int) $record->visit_date->format('n'))
                ->map->count();

            $patientsByMonth = Patient::whereBetween('created_at', [$chartStart, $chartEnd])
                ->get(['created_at'])
                ->groupBy(fn ($patient) => (int) $patient->created_at->format('n'))
                ->map->count();

            $chartItems = collect(range(1, 12))->map(function ($month) use ($visitsByMonth, $patientsByMonth) {
                return [
                    'label' => 'T' . str_pad((string) $month, 2, '0', STR_PAD_LEFT),
                    'visits' => (int) ($visitsByMonth[$month] ?? 0),
                    'patients' => (int) ($patientsByMonth[$month] ?? 0),
                ];
            });
        }

        $chartMaxValue = max(1, $chartItems->max(fn ($item) => max($item['visits'], $item['patients'])));
        $chartCeiling = max(5, (int) ceil($chartMaxValue / 5) * 5);
        $chartYAxis = collect(range(5, 0))->map(fn ($step) => (int) round($chartCeiling * $step / 5));
        
        // 2. Inventory (Low Stock)
        $allItems = \App\Models\InventoryItem::where('is_active', true)->get();
        // Giả sử 500g là mức tối thiểu
        $lowHerbs = $allItems->filter(fn($i) => $i->item_type == 'herb' && $i->total_available_quantity <= 500);
        $herbsLowCount = $lowHerbs->count();
        $lowHerbsList = $lowHerbs->sortBy('total_available_quantity')->take(4);

        // 3. Appointments Today
        $appointmentsToday = \App\Models\Appointment::with('patient')
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time', 'asc')
            ->get();

        // 4. Recent Activities
        $activities = collect();
        $recentPatients = Patient::latest()->take(3)->get();
        foreach ($recentPatients as $p) {
            $activities->push([
                'type' => 'patient',
                'title' => 'Đăng ký bệnh nhân mới: ' . $p->full_name,
                'time' => $p->created_at,
                'icon' => '👤',
                'bg' => '#eff6ff',
                'color' => '#3b82f6'
            ]);
        }
        $recentRecords = MedicalRecord::with('patient')->latest()->take(3)->get();
        foreach ($recentRecords as $r) {
            $activities->push([
                'type' => 'medical_record',
                'title' => 'Tạo bệnh án mới cho bệnh nhân ' . ($r->patient->full_name ?? 'Ẩn danh'),
                'time' => $r->created_at,
                'icon' => '📋',
                'bg' => '#ecfdf5',
                'color' => '#10b981'
            ]);
        }
        $recentPrescs = \App\Models\Prescription::with('medicalRecord.patient')->latest()->take(3)->get();
        foreach ($recentPrescs as $pr) {
            $activities->push([
                'type' => 'prescription',
                'title' => 'Kê đơn điều trị cho bệnh nhân ' . ($pr->medicalRecord->patient->full_name ?? 'Ẩn danh'),
                'time' => $pr->created_at,
                'icon' => '💊',
                'bg' => '#f5f3ff',
                'color' => '#8b5cf6'
            ]);
        }
        $recentApps = \App\Models\Appointment::with('patient')->latest()->take(3)->get();
        foreach ($recentApps as $a) {
            $activities->push([
                'type' => 'appointment',
                'title' => 'Lịch hẹn mới với bệnh nhân ' . ($a->patient->full_name ?? 'Ẩn danh'),
                'time' => $a->created_at,
                'icon' => '📅',
                'bg' => '#eff6ff',
                'color' => '#3b82f6'
            ]);
        }
        $recentActivities = $activities->sortByDesc('time')->take(4);

        $stats = [
            'patients' => $patientsCount,
            'visits_today' => $visitsToday,
            'visits_yesterday' => $visitsYesterday,
            'prescriptions' => $prescriptionsCount,
            'herbs_low_count' => $herbsLowCount,
        ];

        $growthChart = [
            'period' => $chartPeriod,
            'year' => (int) $chartMonth->year,
            'month' => $chartMonth->format('Y-m'),
            'title' => $chartPeriod === 'month'
                ? 'Tháng ' . $chartMonth->format('m/Y')
                : 'Năm ' . $chartMonth->format('Y'),
            'ceiling' => $chartCeiling,
            'y_axis' => $chartYAxis,
            'items' => $chartItems,
        ];

        return view('admin.dashboard', compact('stats', 'appointmentsToday', 'lowHerbsList', 'recentActivities', 'growthChart'));
    }
}
