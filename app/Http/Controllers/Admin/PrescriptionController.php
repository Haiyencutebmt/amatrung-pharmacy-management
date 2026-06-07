<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\MedicalRecord;
use App\Models\InventoryItem;
use App\Models\Appointment;
use App\Services\PrescriptionService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Exception;

class PrescriptionController extends Controller implements HasMiddleware
{
    protected PrescriptionService $prescriptionService;

    public function __construct(PrescriptionService $prescriptionService)
    {
        $this->prescriptionService = $prescriptionService;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:prescriptions.view', only: ['index', 'show', 'print']),
            new Middleware('permission:prescriptions.create', only: ['create', 'store']),
            new Middleware('permission:prescriptions.delete', only: ['destroy']),
            new Middleware('permission:dispense_prescriptions', only: ['dispense']),
        ];
    }

    public function index(Request $request)
    {
        $query = Prescription::with(['medicalRecord.patient', 'staff', 'items']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('medicalRecord.patient', function($pq) use ($search) {
                $pq->where('full_name', 'like', "%{$search}%")
                   ->orWhere('patient_code', 'like', "%{$search}%")
                   ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $prescriptions = $query->orderByDesc('created_at')->paginate(20);

        return view('admin.prescriptions.index', compact('prescriptions'));
    }

    public function create(Request $request, ?MedicalRecord $medicalRecord = null)
    {
        if (!$medicalRecord || !$medicalRecord->id) {
            $medical_record_id = $request->input('medical_record_id');
            if (!$medical_record_id) {
                return redirect()->route('admin.medical-records.index')
                    ->with('error', 'Vui lòng chọn một bệnh án để tạo đơn điều trị.');
            }
            $medicalRecord = MedicalRecord::findOrFail($medical_record_id);
        }

        if ($medicalRecord->treatment_direction === 'referral') {
            return redirect()->route('admin.medical-records.show', $medicalRecord)
                ->with('error', 'Ca khám đã được chỉ định chuyển tuyến, không thể kê đơn.');
        }

        $inventoryItems = InventoryItem::where('is_active', true)->orderBy('name')->get();
        $herbs = $inventoryItems->where('item_type', 'herb');
        $externalProducts = $inventoryItems->filter(function ($item) {
            return $item->usage_route === 'external'
                || in_array($item->item_type, ['packaged_product', 'external_product'], true);
        });
        $therapyServices = \App\Models\TherapyService::where('status', 'active')->orderBy('name')->get();

        return view('admin.prescriptions.create', compact('medicalRecord', 'herbs', 'externalProducts', 'therapyServices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'medical_record_id'    => 'required|exists:medical_records,id',
            'treatment_type'       => 'nullable|string|max:50',
            'prescription_name'    => 'nullable|string|max:255',
            'note'                 => 'nullable|string',
            'num_of_doses'         => 'nullable|integer|min:1',
            'usage_instruction'    => 'nullable|string',
            'course_days'          => 'nullable|integer|min:1',
            'follow_up_date'       => 'nullable|date|after_or_equal:today',
            'items'                => 'required|array|min:1',
            'items.*.item_type'    => 'required|in:herb,packaged_product,external_product,therapy_service',
            'items.*.inventory_item_id' => 'nullable|exists:inventory_items,id',
            'items.*.custom_name'  => 'nullable|string|max:255',
            'items.*.quantity_per_dose' => 'nullable|numeric|min:0',
            'items.*.unit'         => 'nullable|string|max:50',
            'items.*.dosage'       => 'nullable|string',
            'items.*.note'         => 'nullable|string',
            'items.*.sessions'     => 'nullable|integer|min:1',
        ], [
            'items.required'            => 'Đơn điều trị phải có ít nhất một hạng mục.',
            'follow_up_date.date'       => 'Ngày hẹn tái khám không đúng định dạng.',
            'follow_up_date.after_or_equal' => 'Ngày hẹn tái khám phải là ngày hôm nay hoặc sau ngày hôm nay.',
        ]);

        try {
            // Map form inputs to database columns
            if ($request->has('prescription_name')) {
                $validated['public_instruction'] = $validated['note'] ?? null;
                $validated['note'] = $validated['prescription_name'] ?? null;
            }

            $prescription = $this->prescriptionService->createPrescription($validated, auth()->id());

            // Create appointment if needed
            if (!empty($validated['follow_up_date'])) {
                $record = MedicalRecord::with('patient')->find($validated['medical_record_id']);
                if ($record && $record->patient) {
                    Appointment::create([
                        'patient_id'       => $record->patient_id,
                        'appointment_date' => $validated['follow_up_date'],
                        'appointment_time' => '08:00',
                        'reason'           => 'Tái khám theo đơn điều trị #' . $prescription->id . ' (BA: ' . $record->record_code . ')',
                        'status'           => 'confirmed',
                        'notes'            => 'Tự động tạo từ quy trình kê đơn điều trị.',
                    ]);
                }
            }

            return redirect()->route('admin.prescriptions.show', $prescription)
                ->with('success', 'Đã tạo đơn điều trị thành công. Vui lòng xác nhận xuất thuốc.');

        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function dispense(Prescription $prescription)
    {
        try {
            $this->prescriptionService->dispensePrescription($prescription, auth()->id());
            return back()->with('success', 'Đã xuất thuốc và trừ tồn kho (FEFO) thành công.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Prescription $prescription)
    {
        try {
            $this->prescriptionService->cancelPrescription($prescription);
            return redirect()->route('admin.medical-records.show', $prescription->medical_record_id)
                ->with('success', 'Đã hủy đơn thuốc thành công.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(Request $request, Prescription $prescription)
    {
        $prescription->load(['medicalRecord.patient', 'staff', 'items.inventoryItem']);
        $printType = $request->query('type') === 'internal' ? 'internal' : 'patient';

        if ($request->ajax()) {
            return view('admin.prescriptions.modal_content', compact('prescription', 'printType'));
        }
        return view('admin.prescriptions.show', compact('prescription', 'printType'));
    }

    public function print(Request $request, Prescription $prescription)
    {
        $prescription->load(['medicalRecord.patient', 'staff', 'items.inventoryItem']);
        $printType = $request->query('type') === 'internal' ? 'internal' : 'patient';

        return view('admin.prescriptions.print', compact('prescription', 'printType'));
    }
}
