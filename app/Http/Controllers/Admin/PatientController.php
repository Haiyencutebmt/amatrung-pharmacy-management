<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PatientController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:patients.view', only: ['index', 'show', 'printList', 'exportExcel']),
            new Middleware('permission:patients.create', only: ['create', 'store', 'legacyCreate', 'legacyStore', 'csvImportForm', 'csvImportProcess']),
            new Middleware('permission:patients.edit', only: ['edit', 'update']),
            new Middleware('permission:patients.delete', only: ['destroy', 'bulkDestroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Patient::query();

        // Thống kê
        $totalPatients = Patient::count();
        $legacyPatientsCount = Patient::where('is_legacy_data', true)->count();
        $newPatientsCount = Patient::where('is_legacy_data', false)->count();
        $newPatientsThisMonth = Patient::where('is_legacy_data', false)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Tính % tăng trưởng (giả định so với tháng trước hoặc mặc định 12% như hình)
        $growthRate = 12; 

        $upcomingAppointmentsCount = \App\Models\Appointment::whereBetween('appointment_date', [
            now()->toDateString(), 
            now()->addDays(7)->toDateString()
        ])->count();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('patient_code', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "{$search}%")
                  ->orWhere('full_name', 'like', "% {$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('guardian_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'oldest': $query->orderBy('id', 'asc'); break;
            case 'name_asc': $query->orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) ASC"); break;
            case 'name_desc': $query->orderByRaw("SUBSTRING_INDEX(full_name, ' ', -1) DESC"); break;
            default: $query->orderByDesc('id'); break;
        }

        $patients = $query->paginate(10);

        return view('admin.patients.index', compact(
            'patients', 
            'totalPatients', 
            'newPatientsCount',
            'legacyPatientsCount',
            'newPatientsThisMonth', 
            'upcomingAppointmentsCount',
            'growthRate'
        ));
    }

    public function create()
    {
        return view('admin.patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'      => 'required|string|max:255',
            'phone'          => 'required_without:guardian_phone|nullable|string|max:20',
            'date_of_birth'  => 'required|date',
            'gender'         => 'required|in:male,female,other',
            'address'        => 'required|string',
            'is_guardian_phone'=> 'nullable|boolean',
            'guardian_name'  => 'required_if:is_guardian_phone,1|nullable|string|max:255',
            'guardian_phone' => 'required_without:phone|nullable|string|max:20',
            'relationship'   => 'required_if:is_guardian_phone,1|nullable|string|max:255',
            'note'           => 'nullable|string',
        ]);

        // Cảnh báo mềm nếu trùng SĐT (ở đây ta chỉ check log hoặc view có thể tự check bằng ajax, nhưng yêu cầu không chặn lưu)
        $validated['patient_code'] = Patient::generateCode();

        $patient = Patient::create($validated);

        session()->put('last_action', [
            'type' => 'create',
            'patient_id' => $patient->id,
            'description' => "Thêm bệnh nhân {$patient->full_name}"
        ]);

        return redirect()->route('admin.patients.show', $patient)->with('success', 'Thêm bệnh nhân thành công.');
    }

    public function show(Patient $patient)
    {
        $patient->load(['medicalRecords.staff', 'medicalRecords.prescriptions.items.medicinalHerb']);
        return view('admin.patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('admin.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'full_name'      => 'required|string|max:255',
            'phone'          => 'required_without:guardian_phone|nullable|string|max:20',
            'date_of_birth'  => 'required|date',
            'gender'         => 'required|in:male,female,other',
            'address'        => 'required|string',
            'is_guardian_phone'=> 'nullable|boolean',
            'guardian_name'  => 'required_if:is_guardian_phone,1|nullable|string|max:255',
            'guardian_phone' => 'required_without:phone|nullable|string|max:20',
            'relationship'   => 'required_if:is_guardian_phone,1|nullable|string|max:255',
            'note'           => 'nullable|string',
            'legacy_date'    => 'nullable|date',
            'legacy_note'    => 'nullable|string',
        ]);

        $originalData = $patient->only([
            'full_name', 'phone', 'date_of_birth', 'gender', 'address',
            'is_guardian_phone', 'guardian_name', 'guardian_phone',
            'relationship', 'note', 'legacy_date', 'legacy_note'
        ]);

        $patient->update($validated);

        session()->put('last_action', [
            'type' => 'update',
            'patient_id' => $patient->id,
            'original_data' => $originalData,
            'description' => "Cập nhật bệnh nhân {$patient->full_name}"
        ]);

        return redirect()->route('admin.patients.show', $patient)->with('success', 'Cập nhật bệnh nhân thành công.');
    }

    public function destroy(Patient $patient)
    {
        // Kiểm tra xem bệnh nhân có bệnh án không
        if ($patient->medicalRecords()->count() > 0) {
            return back()->with('error', 'Không thể xóa bệnh nhân đã có bệnh án. Vui lòng xóa các bệnh án liên quan trước.');
        }

        $patientData = $patient->toArray();

        $patient->delete();

        session()->put('last_action', [
            'type' => 'delete',
            'patients_data' => [$patientData],
            'description' => "Xóa bệnh nhân {$patient->full_name}"
        ]);

        return redirect()->route('admin.patients.index')->with('success', 'Đã xóa bệnh nhân.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một bệnh nhân để xóa.');
        }

        if (count($ids) > 5) {
            return back()->with('error', 'Mỗi lần chỉ có thể xóa tối đa 5 bệnh nhân.');
        }

        $patients = Patient::whereIn('id', $ids)->get();
        $deletedCount = 0;
        $failedCount = 0;
        $deletedPatientsData = [];

        /** @var \App\Models\Patient $patient */
        foreach ($patients as $patient) {
            if ($patient->medicalRecords()->count() > 0) {
                $failedCount++;
                continue;
            }
            $deletedPatientsData[] = $patient->toArray();
            $patient->delete();
            $deletedCount++;
        }

        if ($deletedCount > 0) {
            session()->put('last_action', [
                'type' => 'delete',
                'patients_data' => $deletedPatientsData,
                'description' => "Xóa hàng loạt {$deletedCount} bệnh nhân"
            ]);
        }

        if ($failedCount > 0) {
            if ($deletedCount > 0) {
                return redirect()->route('admin.patients.index')->with('success', "Đã xóa thành công {$deletedCount} bệnh nhân. Có {$failedCount} bệnh nhân không thể xóa vì đã có bệnh án.");
            }
            return redirect()->route('admin.patients.index')->with('error', "Không thể xóa vì các bệnh nhân đã chọn đều có bệnh án liên quan.");
        }

        return redirect()->route('admin.patients.index')->with('success', "Đã xóa thành công {$deletedCount} bệnh nhân.");
    }

    // ── Giai đoạn 1: Nhập dữ liệu từ hồ sơ giấy ──────────────────

    /**
     * Hiển thị form nhập dữ liệu từ hồ sơ giấy
     */
    public function legacyCreate()
    {
        return view('admin.patients.legacy-create');
    }

    /**
     * Lưu bệnh nhân từ hồ sơ giấy
     */
    public function legacyStore(Request $request)
    {
        $validated = $request->validate([
            'full_name'        => 'required|string|max:255',
            'phone'            => 'required_without:guardian_phone|nullable|string|max:20',
            'date_of_birth'    => 'nullable|date',
            'gender'           => 'nullable|in:male,female,other',
            'address'          => 'nullable|string',
            'is_guardian_phone'=> 'nullable|boolean',
            'guardian_name'    => 'required_if:is_guardian_phone,1|nullable|string|max:255',
            'guardian_phone'   => 'required_without:phone|nullable|string|max:20',
            'relationship'     => 'required_if:is_guardian_phone,1|nullable|string|max:255',
            'note'             => 'nullable|string',
            'legacy_note'      => 'nullable|string',
            'legacy_date'      => 'nullable|date',
        ], [
            'full_name.required' => 'Vui lòng nhập họ và tên bệnh nhân.',
            'phone.required_without' => 'Vui lòng nhập ít nhất một số điện thoại liên hệ (bệnh nhân hoặc người giám hộ).',
            'guardian_phone.required_without' => 'Vui lòng nhập ít nhất một số điện thoại liên hệ (bệnh nhân hoặc người giám hộ).',
            'guardian_name.required_if' => 'Vui lòng nhập họ tên người giám hộ khi dùng SĐT giám hộ.',
            'relationship.required_if' => 'Vui lòng nhập quan hệ với bệnh nhân khi dùng SĐT giám hộ.',
        ]);

        // Xử lý force_save (bỏ qua cảnh báo trùng)
        $forceSave = $request->boolean('force_save');

        // Kiểm tra trùng theo tổ hợp: họ tên + ngày sinh + giới tính
        if (!$forceSave) {
            $duplicateQuery = Patient::where('full_name', $validated['full_name']);
            
            if (!empty($validated['date_of_birth'])) {
                $duplicateQuery->where('date_of_birth', $validated['date_of_birth']);
            }
            if (!empty($validated['gender'])) {
                $duplicateQuery->where('gender', $validated['gender']);
            }

            $duplicates = $duplicateQuery->get();

            if ($duplicates->count() > 0) {
                return back()->withInput()->with('duplicate_warning', true)->with('duplicates', $duplicates);
            }
        }

        // Gán các trường legacy
        $validated['patient_code'] = Patient::generateCode();
        $validated['is_legacy_data'] = true;
        $validated['legacy_source'] = 'paper_record';
        $validated['imported_at'] = now();
        $validated['imported_by'] = auth()->id();

        $patient = Patient::create($validated);

        session()->put('last_action', [
            'type' => 'create',
            'patient_id' => $patient->id,
            'description' => "Nhập bệnh nhân {$patient->full_name} từ hồ sơ giấy"
        ]);

        return redirect()->route('admin.patients.show', $patient)->with('success', 'Đã nhập thành công dữ liệu bệnh nhân từ hồ sơ giấy.');
    }

    /**
     * API: Kiểm tra trùng SĐT (trả về JSON cho AJAX)
     */
    public function checkDuplicate(Request $request)
    {
        $phone = $request->input('phone');
        $fullName = $request->input('full_name');
        $dateOfBirth = $request->input('date_of_birth');
        $gender = $request->input('gender');
        $isGuardian = filter_var($request->input('is_guardian_phone', false), FILTER_VALIDATE_BOOLEAN);

        $warnings = [];

        // Kiểm tra trùng SĐT (bỏ qua nếu đánh dấu là SĐT người giám hộ)
        if ($phone && !$isGuardian) {
            $phoneMatches = Patient::where(function ($q) use ($phone) {
                $q->where('phone', $phone)->orWhere('guardian_phone', $phone);
            })->get(['id', 'patient_code', 'full_name', 'phone', 'guardian_phone']);

            if ($phoneMatches->count() > 0) {
                $warnings[] = [
                    'type' => 'phone',
                    'message' => "Số điện thoại này đã tồn tại trong hệ thống. Vui lòng kiểm tra lại hoặc chọn 'SĐT người giám hộ' nếu dùng chung.",
                    'patients' => $phoneMatches->map(fn($p) => [
                        'code' => $p->patient_code,
                        'name' => $p->full_name,
                    ])->toArray(),
                ];
            }
        }

        // Kiểm tra trùng tổ hợp: họ tên + ngày sinh + giới tính
        if ($fullName) {
            $nameQuery = Patient::where('full_name', $fullName);
            if ($dateOfBirth) $nameQuery->where('date_of_birth', $dateOfBirth);
            if ($gender) $nameQuery->where('gender', $gender);
            $nameMatches = $nameQuery->get(['id', 'patient_code', 'full_name']);

            if ($nameMatches->count() > 0) {
                $warnings[] = [
                    'type' => 'identity',
                    'message' => "Bệnh nhân \"{$fullName}\" có thể đã tồn tại trong hệ thống.",
                    'patients' => $nameMatches->map(fn($p) => [
                        'code' => $p->patient_code,
                        'name' => $p->full_name,
                    ])->toArray(),
                ];
            }
        }

        return response()->json(['warnings' => $warnings]);
    }

    // ── Giai đoạn 2: Import CSV/Excel ──────────────────────────────

    /**
     * Hiển thị form import CSV
     */
    public function csvImportForm()
    {
        return view('admin.patients.csv-import');
    }

    /**
     * Xử lý import CSV
     */
    public function csvImportProcess(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|max:5120',
        ], [
            'csv_file.required' => 'Vui lòng chọn file để import.',
            'csv_file.max' => 'File không được lớn hơn 5MB.',
        ]);

        $file = $request->file('csv_file');
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, ['csv', 'txt', 'xlsx', 'xls'])) {
            return back()->with('error', 'Định dạng file không hỗ trợ. Vui lòng tải lên file CSV hoặc Excel (.xlsx, .xls).');
        }

        $csvPath = $file->getRealPath();
        $tempCsvPath = null;

        if (in_array($extension, ['xlsx', 'xls'])) {
            // openpyxl needs the correct file extension, so copy uploaded tmp file to a .xlsx temp file
            $tempXlsxPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'uploaded_' . uniqid() . '.' . $extension;
            copy($file->getRealPath(), $tempXlsxPath);

            // Convert Excel to CSV using Python helper script
            $tempCsvPath = tempnam(sys_get_temp_dir(), 'xlsx_import_');
            $pythonScript = base_path('app/Scripts/xlsx2csv.py');
            $cmd = "python " . escapeshellarg($pythonScript) . " " . escapeshellarg($tempXlsxPath) . " " . escapeshellarg($tempCsvPath);
            
            $output = [];
            $resultCode = 0;
            exec($cmd, $output, $resultCode);

            // Clean up Excel temp file
            if (file_exists($tempXlsxPath)) {
                @unlink($tempXlsxPath);
            }

            if ($resultCode !== 0) {
                return back()->with('error', 'Lỗi chuyển đổi file Excel: ' . implode("\n", $output));
            }
            $csvPath = $tempCsvPath;
        }

        // Read CSV
        $rows = [];
        if (($handle = fopen($csvPath, "r")) !== FALSE) {
            // Check BOM for UTF-8
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $rows[] = $data;
            }
            fclose($handle);
        }

        if ($tempCsvPath && file_exists($tempCsvPath)) {
            @unlink($tempCsvPath);
        }

        if (empty($rows)) {
            return back()->with('error', 'File không có dữ liệu.');
        }

        // Lấy header
        $header = array_map('trim', array_shift($rows));
        
        $expectedColumns = ['full_name', 'date_of_birth', 'gender', 'phone', 'address', 'guardian_name', 'guardian_phone', 'relationship', 'legacy_note'];
        
        // Validate header
        $missingCols = array_diff($expectedColumns, $header);
        if (count($missingCols) > 0) {
            return back()->with('error', 'File thiếu các cột bắt buộc: ' . implode(', ', $missingCols) . '. Vui lòng tải file mẫu và thử lại.');
        }

        $imported = 0;
        $errors = [];
        $skippedRows = [];
        $lineNumber = 1;

        foreach ($rows as $row) {
            $lineNumber++;

            // Skip dòng trống
            if (count($row) < 1 || empty(trim($row[0] ?? ''))) {
                continue;
            }

            // Map header to values
            $data = [];
            foreach ($header as $i => $col) {
                $data[trim($col)] = trim($row[$i] ?? '');
            }

            // Skip dòng tiêu đề tiếng Việt nếu người dùng giữ lại
            if (isset($data['full_name']) && (
                $data['full_name'] === 'Họ và tên *' || 
                str_contains($data['full_name'], 'Họ và tên')
            )) {
                continue;
            }

            // Validate tối thiểu: phải có họ tên
            if (empty($data['full_name'])) {
                $errors[] = "Dòng {$lineNumber}: Thiếu họ và tên.";
                continue;
            }

            // Validate: phải có ít nhất 1 SĐT
            if (empty($data['phone']) && empty($data['guardian_phone'])) {
                $errors[] = "Dòng {$lineNumber} ({$data['full_name']}): Thiếu số điện thoại liên hệ.";
                continue;
            }

            // Kiểm tra trùng lặp bệnh nhân (Phương án 1: Skip)
            $isDuplicate = false;
            $dupReason = '';
            
            if (!empty($data['date_of_birth'])) {
                $dupDob = Patient::where('full_name', $data['full_name'])
                                 ->where('date_of_birth', $data['date_of_birth'])
                                 ->exists();
                if ($dupDob) {
                    $isDuplicate = true;
                    $dupReason = 'trùng Họ tên và Ngày sinh (' . $data['date_of_birth'] . ')';
                }
            }
            
            if (!$isDuplicate && !empty($data['phone'])) {
                $dupPhone = Patient::where('full_name', $data['full_name'])
                                   ->where('phone', $data['phone'])
                                   ->exists();
                if ($dupPhone) {
                    $isDuplicate = true;
                    $dupReason = 'trùng Họ tên và Số điện thoại (' . $data['phone'] . ')';
                }
            }

            if (!$isDuplicate && !empty($data['guardian_phone'])) {
                $dupGPhone = Patient::where('full_name', $data['full_name'])
                                    ->where('guardian_phone', $data['guardian_phone'])
                                    ->exists();
                if ($dupGPhone) {
                    $isDuplicate = true;
                    $dupReason = 'trùng Họ tên và SĐT người giám hộ (' . $data['guardian_phone'] . ')';
                }
            }

            if ($isDuplicate) {
                $skippedRows[] = "Dòng {$lineNumber} ({$data['full_name']}): Bỏ qua do {$dupReason}.";
                continue;
            }

            // Chuẩn hóa gender
            $gender = strtolower($data['gender'] ?? '');
            if (in_array($gender, ['nam', 'male', 'm'])) $gender = 'male';
            elseif (in_array($gender, ['nữ', 'nu', 'female', 'f'])) $gender = 'female';
            elseif (!empty($gender)) $gender = 'other';
            else $gender = null;

            try {
                Patient::create([
                    'patient_code' => Patient::generateCode(),
                    'full_name' => $data['full_name'],
                    'date_of_birth' => !empty($data['date_of_birth']) ? $data['date_of_birth'] : null,
                    'gender' => $gender,
                    'phone' => !empty($data['phone']) ? $data['phone'] : null,
                    'address' => !empty($data['address']) ? $data['address'] : null,
                    'guardian_name' => !empty($data['guardian_name']) ? $data['guardian_name'] : null,
                    'guardian_phone' => !empty($data['guardian_phone']) ? $data['guardian_phone'] : null,
                    'relationship' => !empty($data['relationship']) ? $data['relationship'] : null,
                    'legacy_note' => !empty($data['legacy_note']) ? $data['legacy_note'] : null,
                    'is_legacy_data' => true,
                    'legacy_source' => 'csv_import',
                    'imported_at' => now(),
                    'imported_by' => auth()->id(),
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Dòng {$lineNumber} ({$data['full_name']}): " . $e->getMessage();
            }
        }

        $message = "Đã import thành công {$imported} bệnh nhân.";
        if (count($skippedRows) > 0) {
            $message .= ' Đã tự động bỏ qua ' . count($skippedRows) . ' dòng trùng lặp.';
        }
        if (count($errors) > 0) {
            $message .= ' Có ' . count($errors) . ' dòng lỗi.';
        }

        $redirect = redirect()->route('admin.patients.index')->with('success', $message);
        
        if (count($skippedRows) > 0) {
            $redirect = $redirect->with('import_warnings', $skippedRows);
        }
        if (count($errors) > 0) {
            $redirect = $redirect->with('import_errors', $errors);
        }

        return $redirect;
    }

    /**
     * Tải file CSV mẫu
     */
    public function downloadCsvTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="mau_import_benh_nhan.csv"',
        ];

        $columns = ['full_name', 'date_of_birth', 'gender', 'phone', 'address', 'guardian_name', 'guardian_phone', 'relationship', 'legacy_note'];
        
        $exampleRows = [
            ['Nguyễn Văn An', '1990-05-15', 'Nam', '0912345678', '123 Lê Lợi, TP.BMT', '', '', '', 'Bệnh nhân cũ từ sổ 2024'],
            ['Trần Thị Bé', '2020-03-10', 'Nữ', '', '456 Trần Phú, Buôn Ma Thuột', 'Trần Văn Lớn', '0987654321', 'Bố', 'Trẻ em, dùng SĐT của bố'],
        ];

        $callback = function() use ($columns, $exampleRows) {
            $file = fopen('php://output', 'w');
            // BOM for UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $columns);
            foreach ($exampleRows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * In danh sách bệnh nhân theo bộ lọc thời gian
     */
    public function printList(Request $request)
    {
        $range = $request->input('range', 'all');
        $query = Patient::query();

        if ($range === 'month') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        } elseif ($range === 'today') {
            $query->whereDate('created_at', now()->toDateString());
        }

        $patients = $query->orderByDesc('id')->get();
        
        $title = 'DANH SÁCH BỆNH NHÂN';
        if ($range === 'month') {
            $title .= ' - THÁNG NÀY (' . now()->format('m/Y') . ')';
        } elseif ($range === 'today') {
            $title .= ' - HÔM NAY (' . now()->format('d/m/Y') . ')';
        }

        return view('admin.patients.print-list', compact('patients', 'title', 'range'));
    }

    /**
     * Xuất Excel (CSV UTF-8) danh sách bệnh nhân theo bộ lọc thời gian
     */
    public function exportExcel(Request $request)
    {
        $range = $request->input('range', 'all');
        $query = Patient::query();

        if ($range === 'month') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        } elseif ($range === 'today') {
            $query->whereDate('created_at', now()->toDateString());
        }

        $patients = $query->orderByDesc('id')->get();

        $data = [
            'title' => 'DANH SÁCH BỆNH NHÂN',
            'clinic_info' => [
                'name' => 'NHÀ THUỐC AMATRUNG',
                'address' => '54/36 AmaJhao, Phường Tân Lập, Tỉnh Đắk Lắk',
                'phone' => '0983.009.748 - 0918.303.983',
                'mst' => '066070008130',
                'doctor' => 'BS. Y Hiếu Niê'
            ],
            'headers' => ['STT', 'Mã BN', 'Họ và tên', 'Ngày sinh', 'Giới tính', 'Số điện thoại', 'Địa chỉ', 'Họ tên người giám hộ', 'SĐT người giám hộ', 'Mối quan hệ', 'Ghi chú y tế', 'Ngày đăng ký'],
            'alignments' => ['center', 'center', 'left', 'center', 'center', 'center', 'left', 'left', 'center', 'left', 'left', 'center'],
            'rows' => []
        ];

        foreach ($patients as $index => $p) {
            $data['rows'][] = [
                $index + 1,
                $p->patient_code,
                $p->full_name,
                $p->date_of_birth ? $p->date_of_birth->format('d/m/Y') : '—',
                $p->gender_label,
                $p->phone ?? '—',
                $p->address ?? '—',
                $p->guardian_name ?? '—',
                $p->guardian_phone ?? '—',
                $p->relationship ?? '—',
                $p->note ?? '—',
                $p->created_at->format('d/m/Y H:i')
            ];
        }

        $tempJsonPath = tempnam(sys_get_temp_dir(), 'export_patients_json_');
        file_put_contents($tempJsonPath, json_encode($data, JSON_UNESCAPED_UNICODE));

        $tempXlsxPath = tempnam(sys_get_temp_dir(), 'export_patients_xlsx_') . '.xlsx';
        
        $pythonScript = base_path('app/Scripts/export_xlsx.py');
        $cmd = "python " . escapeshellarg($pythonScript) . " " . escapeshellarg($tempJsonPath) . " " . escapeshellarg($tempXlsxPath);
        
        $output = [];
        $resultCode = 0;
        exec($cmd, $output, $resultCode);

        if (file_exists($tempJsonPath)) {
            @unlink($tempJsonPath);
        }

        if ($resultCode === 0 && file_exists($tempXlsxPath)) {
            return response()->download($tempXlsxPath, 'danh_sach_benh_nhan_' . $range . '_' . now()->format('Ymd') . '.xlsx')->deleteFileAfterSend(true);
        } else {
            if (file_exists($tempXlsxPath)) {
                @unlink($tempXlsxPath);
            }
            return back()->with('error', 'Lỗi xuất file Excel: ' . implode("\n", $output));
        }
    }


}
