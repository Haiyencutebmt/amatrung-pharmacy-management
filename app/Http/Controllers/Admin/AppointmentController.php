<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $date = Carbon::create($year, $month, 1);
        
        $appointments = Appointment::with('patient')
            ->whereYear('appointment_date', $year)
            ->whereMonth('appointment_date', $month)
            ->get()
            ->groupBy(function($item) {
                return $item->appointment_date->format('Y-m-d');
            });

        return view('admin.appointments.index', compact('appointments', 'date'));
    }

    public function getPatientsByDate($date)
    {
        $appointments = Appointment::with('patient')
            ->whereDate('appointment_date', $date)
            ->orderBy('appointment_time')
            ->get();

        return response()->json($appointments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Appointment::create($validated);

        return back()->with('success', 'Lịch hẹn đã được tạo thành công.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return back()->with('success', 'Lịch hẹn đã được xóa.');
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $appointment->update([
            'status' => $validated['status']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái lịch hẹn thành công.',
            'appointment' => $appointment->load('patient')
        ]);
    }

    public function dayView($date)
    {
        try {
            $parsedDate = \Carbon\Carbon::parse($date);
        } catch (\Exception $e) {
            abort(404, 'Ngày không hợp lệ');
        }

        $appointments = Appointment::with(['patient.medicalRecords'])
            ->whereDate('appointment_date', $parsedDate->toDateString())
            ->orderBy('appointment_time')
            ->get();

        return view('admin.appointments.day_view', compact('appointments', 'parsedDate'));
    }
}
