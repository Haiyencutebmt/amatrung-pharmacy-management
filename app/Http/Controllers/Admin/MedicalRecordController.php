<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordAttachment;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MedicalRecordController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:medical_records.view', only: ['index', 'show', 'print', 'downloadXray']),
            new Middleware('permission:medical_records.create', only: ['create', 'store', 'legacyCreate', 'legacyStore']),
            new Middleware('permission:medical_records.edit', only: ['edit', 'update']),
            new Middleware('permission:medical_records.delete', only: ['destroy']),
            new Middleware('permission:upload_medical_record_attachments', only: ['uploadAttachments']),
            new Middleware('permission:view_medical_record_attachments', only: ['downloadAttachment']),
        ];
    }

    public function index(Request $request)
    {
        $query = MedicalRecord::with(['patient', 'staff'])->withCount('prescriptions');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('patient', function($pq) use ($search) {
                    $pq->where('full_name', 'like', "%{$search}%")
                       ->orWhere('patient_code', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhere('diagnosis', 'like', "%{$search}%")
                ->orWhere('record_code', 'like', "%{$search}%");
            });
        }

        $dateFilter = $request->input('date');
        
        // Mặc định hiển thị hôm nay nếu không có bộ lọc nào được chọn
        if ($dateFilter === null && !$request->filled('search') && !$request->filled('status') && !$request->filled('legacy') && !$request->has('page')) {
            $dateFilter = 'today';
        }

        if ($dateFilter) {
            if ($dateFilter === 'today') {
                $query->whereDate('visit_date', date('Y-m-d'));
            } elseif ($dateFilter === 'this_week') {
                $query->whereBetween('visit_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()]);
            } elseif ($dateFilter === 'this_month') {
                $query->whereMonth('visit_date', date('m'))->whereYear('visit_date', date('Y'));
            } elseif ($dateFilter !== 'all') {
                $query->whereDate('visit_date', $dateFilter);
            }
        }

        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->doesntHave('prescriptions');
            } elseif ($request->status === 'prescribed') {
                $query->has('prescriptions');
            }
        }

        if ($request->filled('legacy') && $request->legacy == '1') {
            $query->where('is_legacy_data', true);
        }

        $records = $query->orderByDesc('visit_date')->orderByDesc('id')->paginate(20);

        // Thống kê cơ bản
        $totalRecords = MedicalRecord::count();
        $recordsThisMonth = MedicalRecord::whereMonth('visit_date', date('m'))
                                                    ->whereYear('visit_date', date('Y'))
                                                    ->count();
        $recordsToday = MedicalRecord::whereDate('visit_date', date('Y-m-d'))->count();

        // Danh sách bệnh nhân cho modal tạo bệnh án
        $patients = Patient::orderBy('full_name')->get();

        return view('admin.records.index', compact('records', 'totalRecords', 'recordsThisMonth', 'recordsToday', 'patients'));
    }

    public function create(Request $request)
    {
        $patient_id = $request->input('patient_id');
        $patients = Patient::orderBy('full_name')->get();
        return view('admin.records.create', compact('patients', 'patient_id'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'     => 'required|exists:patients,id',
            'visit_date'     => 'required|date',
            'weight'         => 'nullable|numeric|min:0|max:500',
            'height'         => 'nullable|numeric|min:0|max:300',
            'symptoms'       => 'required|string',
            'diagnosis'      => 'required|string',
            'treatment_plan' => 'nullable|string',
            'doctor_note'    => 'nullable|string',
            // Khám xương khớp
            'case_type'        => 'nullable|in:normal,general,musculoskeletal,combined,both',
            'injury_type'      => 'nullable|string|max:100',
            'injury_location'  => 'nullable|string|max:255',
            'injury_cause'     => 'nullable|string',
            'clinical_signs'   => 'nullable|string',
            'palpation_result' => 'nullable|string',
            'pain_level'       => 'nullable|integer|min:0|max:10',
            'xray_image'       => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:5120',
            'xray_note'        => 'nullable|string',
            'treatment_direction' => 'required|in:oral_only,external_only,combined,referral',
            'referral_reason'  => 'nullable|string|required_if:treatment_direction,referral',
            'allergies'        => 'nullable|string',
            'underlying_diseases' => 'nullable|string',
            'current_medications' => 'nullable|string',
        ], [
            'patient_id.required' => 'Vui lòng chọn bệnh nhân.',
            'visit_date.required' => 'Vui lòng chọn ngày khám.',
            'symptoms.required'   => 'Vui lòng nhập triệu chứng.',
            'diagnosis.required'  => 'Vui lòng nhập chẩn đoán.',
        ]);

        $validated['staff_id'] = auth()->id();
        $validated['case_type'] = $this->normalizeCaseType($validated['case_type'] ?? null);

        // Upload ảnh phim nếu có
        // Upload ảnh/phim private
        if ($request->hasFile('xray_image')) {
            $validated['xray_file_path'] = $request->file('xray_image')->store('medical-records-private');
            unset($validated['xray_image']);
        } else {
            unset($validated['xray_image']);
        }

        $record = MedicalRecord::create($validated);

        session()->put('last_action', [
            'model' => MedicalRecord::class,
            'type' => 'create',
            'id' => $record->id,
            'redirect_url' => route('admin.medical-records.index'),
        ]);

        return redirect()->route('admin.medical-records.show', $record)->with('success', 'Đã tạo bệnh án thành công.');
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $medicalRecord->load(['patient', 'staff', 'prescriptions.items.medicinalHerb', 'prescriptions.items.packagedProduct', 'prescriptions.staff', 'attachments']);
        
        $herbs = \App\Models\MedicinalHerb::where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->where(function($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', now());
            })
            ->orderBy('name')
            ->get();

        // Tải thuốc dùng ngoài/Trà thảo mộc đang hoạt động và còn tồn kho
        $packagedProducts = \App\Models\PackagedProduct::where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->where(function($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>', now());
            })
            ->orderBy('name')
            ->get();

        // Nạp bài thuốc mẫu động từ cơ sở dữ liệu
        $samplePrescriptions = \App\Models\SamplePrescription::with('items.medicinalHerb')->get();
        if ($samplePrescriptions->count() === 0) {
            try {
                // Tự động seed dữ liệu bài thuốc mẫu ban đầu nếu CSDL trống
                $sampleController = new \App\Http\Controllers\Admin\SamplePrescriptionController();
                $sampleController->index(request());
                $samplePrescriptions = \App\Models\SamplePrescription::with('items.medicinalHerb')->get();
            } catch (\Exception $e) {
                // Bỏ qua nếu có lỗi
            }
        }

        // Tải các dịch vụ trị liệu đang hoạt động
        $therapyServices = \App\Models\TherapyService::where('status', 'active')->orderBy('name')->get();

        return view('admin.records.show', compact(
            'medicalRecord', 
            'herbs', 
            'packagedProducts', 
            'samplePrescriptions', 
            'therapyServices'
        ));
    }


    public function print(MedicalRecord $medicalRecord)
    {
        $medicalRecord->load(['patient', 'staff']);
        return view('admin.records.print', compact('medicalRecord'));
    }

    public function edit(MedicalRecord $medicalRecord)
    {
        return redirect()->route('admin.medical-records.show', $medicalRecord)->with('open_edit_modal', true);
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validated = $request->validate([
            'patient_id'     => 'required|exists:patients,id',
            'visit_date'     => 'required|date',
            'symptoms'       => 'required|string',
            'diagnosis'      => 'required|string',
            'treatment_plan' => 'nullable|string',
            'doctor_note'    => 'nullable|string',
            // Khám xương khớp
            'case_type'        => 'nullable|in:normal,general,musculoskeletal,combined,both',
            'injury_type'      => 'nullable|string|max:100',
            'injury_location'  => 'nullable|string|max:255',
            'injury_cause'     => 'nullable|string',
            'clinical_signs'   => 'nullable|string',
            'palpation_result' => 'nullable|string',
            'pain_level'       => 'nullable|integer|min:0|max:10',
            'xray_image'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'xray_note'        => 'nullable|string',
            'treatment_direction' => 'required|in:oral_only,external_only,combined,referral',
            'referral_reason'  => 'nullable|string|required_if:treatment_direction,referral',
            'allergies'        => 'nullable|string',
            'underlying_diseases' => 'nullable|string',
            'current_medications' => 'nullable|string',
        ]);

        $validated['case_type'] = $this->normalizeCaseType($validated['case_type'] ?? null);

        // Upload ảnh phim mới: xóa ảnh cũ nếu tồn tại
        // Upload ảnh/phim private mới
        if ($request->hasFile('xray_image')) {
            if ($medicalRecord->xray_file_path && Storage::exists($medicalRecord->xray_file_path)) {
                Storage::delete($medicalRecord->xray_file_path);
            }
            $validated['xray_file_path'] = $request->file('xray_image')->store('medical-records-private');
            unset($validated['xray_image']);
        } else {
            unset($validated['xray_image']);
        }

        $originalData = $medicalRecord->getOriginal();
        $medicalRecord->update($validated);

        session()->put('last_action', [
            'model' => MedicalRecord::class,
            'type' => 'update',
            'id' => $medicalRecord->id,
            'original_data' => $originalData,
            'redirect_url' => route('admin.medical-records.show', $medicalRecord),
        ]);

        return redirect()->route('admin.medical-records.show', $medicalRecord)->with('success', 'Đã cập nhật bệnh án thành công.');
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        // Kiểm tra xem có đơn thuốc không
        if ($medicalRecord->prescriptions()->count() > 0) {
            return back()->with('error', 'Không thể xóa bệnh án đã có đơn điều trị. Vui lòng xóa đơn điều trị trước.');
        }

        // Xóa ảnh phim nếu có
        if ($medicalRecord->xray_image && Storage::disk('public')->exists($medicalRecord->xray_image)) {
            Storage::disk('public')->delete($medicalRecord->xray_image);
        }

        $originalData = [$medicalRecord->getOriginal()];
        $patient_id = $medicalRecord->patient_id;
        $medicalRecord->delete();

        session()->put('last_action', [
            'model' => MedicalRecord::class,
            'type' => 'delete',
            'original_data' => $originalData,
            'redirect_url' => route('admin.patients.show', $patient_id),
        ]);

        return redirect()->route('admin.patients.show', $patient_id)->with('success', 'Đã xóa bệnh án.');
    }

    private function normalizeCaseType(?string $caseType): string
    {
        return match ($caseType) {
            'general', null, '' => MedicalRecord::CASE_NORMAL,
            'both' => MedicalRecord::CASE_COMBINED,
            default => $caseType,
        };
    }

    public function downloadXray(MedicalRecord $medicalRecord)
    {
        if (!$medicalRecord->xray_file_path || !Storage::exists($medicalRecord->xray_file_path)) {
            abort(404, 'File không tồn tại');
        }
        
        return Storage::download($medicalRecord->xray_file_path);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một bệnh án để xóa.');
        }

        $records = MedicalRecord::whereIn('id', $ids)->get();
        $deletedCount = 0;
        /** @var \App\Models\MedicalRecord $record */
        foreach ($records as $record) {
            if ($record->prescriptions()->count() == 0) {
                if ($record->xray_image && Storage::disk('public')->exists($record->xray_image)) {
                    Storage::disk('public')->delete($record->xray_image);
                }
                $record->delete();
                $deletedCount++;
            }
        }

        if ($deletedCount == 0) {
            return back()->with('error', 'Không thể xóa các bệnh án đã chọn vì tất cả đều đã có đơn điều trị.');
        } elseif ($deletedCount < count($ids)) {
            return back()->with('success', "Đã xóa {$deletedCount} bệnh án. Các bệnh án còn lại không thể xóa do đã có đơn điều trị.");
        }

        return back()->with('success', "Đã xóa {$deletedCount} bệnh án thành công.");
    }

    // ── Nhập bệnh án cũ từ hồ sơ giấy ──────────────────────────

    /**
     * Form nhập bệnh án cũ cho bệnh nhân
     */
    public function legacyCreate(Request $request)
    {
        $patient_id = $request->input('patient_id');
        if (!$patient_id) {
            return redirect()->route('admin.patients.index')->with('error', 'Vui lòng chọn bệnh nhân để nhập bệnh án cũ.');
        }

        $patient = Patient::findOrFail($patient_id);
        
        // Lấy danh sách dược liệu cho phần đơn thuốc cũ (lấy tất cả, kể cả hết hàng vì chỉ ghi nhận lịch sử)
        $herbs = \App\Models\MedicinalHerb::orderBy('name')->get();
        
        return view('admin.records.legacy-create', compact('patient', 'herbs'));
    }

    /**
     * Lưu bệnh án cũ (có thể kèm đơn thuốc cũ)
     */
    public function legacyStore(Request $request)
    {
        $validated = $request->validate([
            'patient_id'     => 'required|exists:patients,id',
            'visit_date'     => 'required|date',
            'symptoms'       => 'required|string',
            'diagnosis'      => 'required|string',
            'treatment_plan' => 'nullable|string',
            'doctor_note'    => 'nullable|string',
            'legacy_note'    => 'nullable|string',
            // Đơn thuốc cũ (tuỳ chọn)
            'has_prescription'     => 'nullable|boolean',
            'prescription_note'    => 'nullable|string',
            'prescription_legacy_note' => 'nullable|string',
            'items'                => 'nullable|array',
            'items.*.herb_id'      => 'nullable|exists:medicinal_herbs,id',
            'items.*.herb_name'    => 'nullable|string|max:255',
            'items.*.quantity'     => 'nullable|numeric|min:0.01',
            'items.*.unit'         => 'nullable|string|max:50',
            'items.*.dosage'       => 'nullable|string',
            'items.*.note'         => 'nullable|string',
        ], [
            'visit_date.required' => 'Vui lòng nhập ngày khám cũ.',
            'symptoms.required'   => 'Vui lòng nhập tình trạng bệnh / lý do khám.',
            'diagnosis.required'  => 'Vui lòng nhập chẩn đoán.',
        ]);

        try {
            $result = DB::transaction(function () use ($validated, $request) {
                // 1. Tạo bệnh án cũ
                $record = MedicalRecord::create([
                    'patient_id'     => $validated['patient_id'],
                    'staff_id'       => auth()->id(),
                    'visit_date'     => $validated['visit_date'],
                    'symptoms'       => $validated['symptoms'],
                    'diagnosis'      => $validated['diagnosis'],
                    'treatment_plan' => $validated['treatment_plan'] ?? null,
                    'doctor_note'    => $validated['doctor_note'] ?? null,
                    'is_legacy_data' => true,
                    'legacy_source'  => 'paper_record',
                    'legacy_note'    => $validated['legacy_note'] ?? null,
                    'imported_at'    => now(),
                    'imported_by'    => auth()->id(),
                ]);

                // 2. Nếu có đơn thuốc cũ
                if ($request->boolean('has_prescription') && !empty($validated['items'])) {
                    $prescription = Prescription::create([
                        'medical_record_id' => $record->id,
                        'staff_id'          => auth()->id(),
                        'note'              => $validated['prescription_note'] ?? null,
                        'is_legacy_data'    => true,
                        'legacy_source'     => 'paper_record',
                        'legacy_note'       => $validated['prescription_legacy_note'] ?? null,
                        'affect_stock'      => false, // Không trừ tồn kho
                    ]);

                    foreach ($validated['items'] as $item) {
                        // Bỏ qua dòng trống
                        if (empty($item['herb_id']) && empty($item['herb_name'])) continue;
                        if (empty($item['quantity'])) continue;

                        PrescriptionItem::create([
                            'prescription_id'   => $prescription->id,
                            'medicinal_herb_id' => $item['herb_id'] ?? null,
                            'item_type'         => 'herb',
                            'custom_name'       => empty($item['herb_id']) ? ($item['herb_name'] ?? null) : null,
                            'quantity'          => $item['quantity'],
                            'unit'              => $item['unit'] ?? 'g',
                            'dosage'            => $item['dosage'] ?? null,
                            'note'              => $item['note'] ?? null,
                            'affects_stock'     => false,
                        ]);
                        // KHÔNG trừ tồn kho vì đây là đơn thuốc cũ
                    }
                }

                return $record;
            });

            return redirect()->route('admin.medical-records.show', $result)
                ->with('success', 'Đã nhập thành công bệnh án cũ từ hồ sơ giấy.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi khi lưu bệnh án: ' . $e->getMessage());
        }
    }

    // ── File đính kèm bệnh án ──────────────────────────────────

    /**
     * Upload nhiều file đính kèm cho bệnh án.
     */
    public function uploadAttachments(Request $request, MedicalRecord $medicalRecord)
    {
        $this->authorize('upload_medical_record_attachments');

        $request->validate([
            'attachments' => 'required|array|min:1',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
            'descriptions' => 'nullable|array',
            'descriptions.*' => 'nullable|string|max:255',
        ]);

        foreach ($request->file('attachments') as $index => $file) {
            $path = $file->store('medical-records-private', 'local');
            MedicalRecordAttachment::create([
                'medical_record_id' => $medicalRecord->id,
                'uploaded_by' => auth()->id(),
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'description' => $request->input('descriptions.' . $index),
            ]);
        }

        return back()->with('success', 'Đã tải lên file đính kèm thành công.');
    }

    /**
     * Download file đính kèm bệnh án.
     */
    public function downloadAttachment(MedicalRecordAttachment $attachment)
    {
        $this->authorize('view_medical_record_attachments');

        if (!Storage::disk('local')->exists($attachment->file_path)) {
            abort(404, 'File không tồn tại');
        }

        return Storage::disk('local')->download($attachment->file_path, $attachment->file_name);
    }
}
