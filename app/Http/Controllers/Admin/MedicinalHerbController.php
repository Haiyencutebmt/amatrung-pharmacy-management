<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MedicinalHerb;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MedicinalHerbController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:medicinal_herbs.view', only: ['index', 'show', 'exportExcel', 'printList', 'downloadTemplate', 'stockLogs']),
            new Middleware('permission:medicinal_herbs.create', only: ['create', 'store', 'importExcel']),
            new Middleware('permission:medicinal_herbs.edit', only: ['edit', 'update']),
            new Middleware('permission:medicinal_herbs.delete', only: ['destroy', 'bulkDestroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = MedicinalHerb::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('usage_type')) {
            $query->where('usage_type', $request->usage_type);
        }

        if ($request->filled('filter')) {
            $filter = $request->filter;
            if ($filter === 'warning') {
                $query->where(function ($q) {
                    $q->where('stock_quantity', '<=', 0)
                      ->orWhere(function ($sub) {
                          $sub->where('stock_quantity', '>', 0)
                              ->where(function ($inner) {
                                  $inner->where(function ($bocThuoc) {
                                      $bocThuoc->where('category', 'Dược liệu bốc thuốc')
                                               ->where('stock_quantity', '<', 500);
                                  })->orWhere(function ($others) {
                                      $others->where(function ($notBocThuoc) {
                                          $notBocThuoc->where('category', '!=', 'Dược liệu bốc thuốc')
                                                      ->orWhereNull('category');
                                      })->where('stock_quantity', '<=', 10);
                                  });
                              });
                      });
                });
            } elseif ($filter === 'expired') {
                $query->whereNotNull('expiry_date')
                      ->where('expiry_date', '<', now());
            }
        }

        $herbs = $query->orderBy('name')->paginate(20);

        // Tính toán thống kê kho dược liệu
        $totalHerbs = MedicinalHerb::count();
        $outOfStockCount = MedicinalHerb::where('stock_quantity', '<=', 0)->count();
        $warningStockCount = MedicinalHerb::where('stock_quantity', '>', 0)
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('category', 'Dược liệu bốc thuốc')
                        ->where('stock_quantity', '<', 500);
                })->orWhere(function ($sub) {
                    $sub->where(function ($notBocThuoc) {
                        $notBocThuoc->where('category', '!=', 'Dược liệu bốc thuốc')
                                    ->orWhereNull('category');
                    })->where('stock_quantity', '<=', 10);
                });
            })->count();
        $expiredCount = MedicinalHerb::whereNotNull('expiry_date')->where('expiry_date', '<', now())->count();

        return view('admin.medicinal_herbs.index', compact('herbs', 'totalHerbs', 'outOfStockCount', 'warningStockCount', 'expiredCount'));
    }

    public function create()
    {
        return view('admin.medicinal_herbs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'category'       => 'nullable|string|max:255',
            'usage_type'     => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'unit'           => 'nullable|string|max:50',
            'stock_quantity' => 'required|numeric|min:0',
            'expiry_date'    => 'nullable|date',
            'warning_note'   => 'nullable|string|max:255',
            'status'         => 'required|in:active,out_of_stock,expired',
        ]);

        $herb = new MedicinalHerb($validated);
        $herb->stockLogType = 'manual_update';
        $herb->stockLogNote = 'Khởi tạo dược liệu mới (Tồn kho đầu kỳ: ' . floatval($validated['stock_quantity']) . ' ' . ($validated['unit'] ?? 'g') . ')';
        $herb->save();

        session()->put('last_action', [
            'model' => MedicinalHerb::class,
            'type' => 'create',
            'id' => $herb->id,
            'redirect_url' => route('admin.medicinal-herbs.index'),
        ]);

        return redirect()->route('admin.medicinal-herbs.index')->with('success', 'Thêm dược liệu thành công.');
    }

    public function edit(MedicinalHerb $medicinalHerb)
    {
        return view('admin.medicinal_herbs.edit', compact('medicinalHerb'));
    }

    public function update(Request $request, MedicinalHerb $medicinalHerb)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'category'       => 'nullable|string|max:255',
            'usage_type'     => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'unit'           => 'nullable|string|max:50',
            'stock_quantity' => 'required|numeric|min:0',
            'expiry_date'    => 'nullable|date',
            'warning_note'   => 'nullable|string|max:255',
            'status'         => 'required|in:active,out_of_stock,expired',
        ]);

        $originalData = $medicinalHerb->getOriginal();
        $medicinalHerb->stockLogType = 'manual_update';
        $medicinalHerb->update($validated);

        session()->put('last_action', [
            'model' => MedicinalHerb::class,
            'type' => 'update',
            'id' => $medicinalHerb->id,
            'original_data' => $originalData,
            'redirect_url' => route('admin.medicinal-herbs.index'),
        ]);

        return redirect()->route('admin.medicinal-herbs.index')->with('success', 'Cập nhật dược liệu thành công.');
    }

    public function destroy(MedicinalHerb $medicinalHerb)
    {
        $hasPrescription = \DB::table('prescription_items')->where('medicinal_herb_id', $medicinalHerb->id)->exists();
        $hasRetail = \DB::table('retail_order_items')->where('medicinal_herb_id', $medicinalHerb->id)->exists();

        if ($hasPrescription || $hasRetail) {
            return back()->with('error', "Không thể xóa dược liệu \"{$medicinalHerb->name}\" vì đã được sử dụng trong bệnh án/đơn thuốc hoặc đơn bán lẻ.");
        }

        $originalData = [$medicinalHerb->getOriginal()];
        $medicinalHerb->delete();

        session()->put('last_action', [
            'model' => MedicinalHerb::class,
            'type' => 'delete',
            'original_data' => $originalData,
            'redirect_url' => route('admin.medicinal-herbs.index'),
        ]);

        return redirect()->route('admin.medicinal-herbs.index')->with('success', 'Đã xóa dược liệu.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một dược liệu để xóa.');
        }

        $herbs = MedicinalHerb::whereIn('id', $ids)->get();
        $deletedCount = 0;
        $failedNames = [];
        $originalData = [];

        foreach ($herbs as $herb) {
            $hasPrescription = \DB::table('prescription_items')->where('medicinal_herb_id', $herb->id)->exists();
            $hasRetail = \DB::table('retail_order_items')->where('medicinal_herb_id', $herb->id)->exists();

            if ($hasPrescription || $hasRetail) {
                $failedNames[] = $herb->name;
            } else {
                $originalData[] = $herb->getOriginal();
                $herb->delete();
                $deletedCount++;
            }
        }

        if ($deletedCount == 0) {
            return back()->with('error', 'Không thể xóa các dược liệu đã chọn vì tất cả đều đã được sử dụng trong bệnh án/đơn thuốc hoặc đơn bán lẻ.');
        }

        session()->put('last_action', [
            'model' => MedicinalHerb::class,
            'type' => 'delete',
            'original_data' => $originalData,
            'redirect_url' => route('admin.medicinal-herbs.index'),
        ]);

        if (count($failedNames) > 0) {
            $failedStr = implode(', ', $failedNames);
            return redirect()->route('admin.medicinal-herbs.index')->with('success', "Đã xóa {$deletedCount} dược liệu thành công. Không thể xóa các dược liệu sau do đang có giao dịch phát sinh: {$failedStr}.");
        }

        return redirect()->route('admin.medicinal-herbs.index')->with('success', "Đã xóa thành công {$deletedCount} dược liệu đã chọn.");
    }

    public function exportExcel(Request $request)
    {
        $query = MedicinalHerb::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
        }

        if ($request->filled('usage_type')) {
            $query->where('usage_type', $request->usage_type);
        }

        $herbs = $query->orderBy('name')->get();

        $data = [
            'title' => 'DANH SÁCH DƯỢC LIỆU / THẢO DƯỢC KHO',
            'clinic_info' => [
                'name' => 'NHÀ THUỐC AMATRUNG',
                'address' => '54/36 AmaJhao, Phường Tân Lập, Tỉnh Đắk Lắk',
                'phone' => '0983.009.748 - 0918.303.983',
                'mst' => '066070008130',
                'doctor' => 'BS. Y Hiếu Niê'
            ],
            'headers' => ['STT', 'Tên Dược Liệu', 'Phân Loại', 'Cách Dùng', 'Đơn Vị Tính', 'Số Lượng Tồn', 'Hạn Sử Dụng', 'Trạng Thái', 'Ghi Chú Cảnh Báo', 'Mô Tả Chi Tiết'],
            'alignments' => ['center', 'left', 'left', 'center', 'center', 'right', 'center', 'center', 'left', 'left'],
            'rows' => []
        ];

        foreach ($herbs as $index => $h) {
            $statusVi = 'Đang dùng';
            if ($h->status === 'out_of_stock') {
                $statusVi = 'Hết hàng';
            } elseif ($h->status === 'expired') {
                $statusVi = 'Hết hạn';
            }

            $data['rows'][] = [
                $index + 1,
                $h->name,
                $h->category ?? '—',
                $h->usage_type ?? '—',
                $h->unit ?? 'g',
                floatval($h->stock_quantity),
                $h->expiry_date ? $h->expiry_date->format('d/m/Y') : '—',
                $statusVi,
                $h->warning_note ?? '',
                $h->description ?? ''
            ];
        }

        $tempJsonPath = tempnam(sys_get_temp_dir(), 'export_herbs_json_');
        file_put_contents($tempJsonPath, json_encode($data, JSON_UNESCAPED_UNICODE));

        $tempXlsxPath = tempnam(sys_get_temp_dir(), 'export_herbs_xlsx_') . '.xlsx';
        
        $pythonScript = base_path('app/Scripts/export_xlsx.py');
        $cmd = "python " . escapeshellarg($pythonScript) . " " . escapeshellarg($tempJsonPath) . " " . escapeshellarg($tempXlsxPath);
        
        $output = [];
        $resultCode = 0;
        exec($cmd, $output, $resultCode);

        if (file_exists($tempJsonPath)) {
            @unlink($tempJsonPath);
        }

        if ($resultCode === 0 && file_exists($tempXlsxPath)) {
            return response()->download($tempXlsxPath, 'danh_sach_duoc_lieu_' . now()->format('Ymd') . '.xlsx')->deleteFileAfterSend(true);
        } else {
            if (file_exists($tempXlsxPath)) {
                @unlink($tempXlsxPath);
            }
            return back()->with('error', 'Lỗi xuất file Excel: ' . implode("\n", $output));
        }
    }

    public function downloadTemplate()
    {
        $path = public_path('templates/mau_import_duoc_lieu.xlsx');
        if (file_exists($path)) {
            return response()->download($path, 'mau_import_duoc_lieu.xlsx');
        }
        return back()->with('error', 'Không tìm thấy tệp mẫu nhập liệu. Vui lòng liên hệ quản trị viên.');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|max:5120',
        ], [
            'excel_file.required' => 'Vui lòng chọn file để import.',
            'excel_file.max' => 'File không được lớn hơn 5MB.',
        ]);

        $file = $request->file('excel_file');
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, ['csv', 'txt', 'xlsx', 'xls'])) {
            return back()->with('error', 'Định dạng file không hỗ trợ. Vui lòng tải lên file CSV hoặc Excel (.xlsx, .xls).');
        }

        $csvPath = $file->getRealPath();
        $tempCsvPath = null;

        if (in_array($extension, ['xlsx', 'xls'])) {
            $tempXlsxPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'uploaded_herbs_' . uniqid() . '.' . $extension;
            copy($file->getRealPath(), $tempXlsxPath);

            $tempCsvPath = tempnam(sys_get_temp_dir(), 'xlsx_herbs_');
            $pythonScript = base_path('app/Scripts/xlsx2csv.py');
            $cmd = "python " . escapeshellarg($pythonScript) . " " . escapeshellarg($tempXlsxPath) . " " . escapeshellarg($tempCsvPath);
            
            $output = [];
            $resultCode = 0;
            exec($cmd, $output, $resultCode);

            if (file_exists($tempXlsxPath)) {
                @unlink($tempXlsxPath);
            }

            if ($resultCode !== 0) {
                return back()->with('error', 'Lỗi chuyển đổi file Excel: ' . implode("\n", $output));
            }
            $csvPath = $tempCsvPath;
        }

        // Tự động phát hiện dấu phân cách cột (delimiter) của file CSV
        $delimiter = ",";
        if (($handle = fopen($csvPath, "r")) !== FALSE) {
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }
            
            $firstLine = fgets($handle);
            if ($firstLine !== FALSE) {
                // Bỏ qua dòng sep=... nếu có
                if (str_starts_with(trim($firstLine), 'sep=')) {
                    $sepChar = substr(trim($firstLine), 4, 1);
                    if (in_array($sepChar, [',', ';'])) {
                        $delimiter = $sepChar;
                    }
                    $firstLine = fgets($handle);
                }
                
                if ($firstLine !== FALSE) {
                    $commaCount = substr_count($firstLine, ',');
                    $semicolonCount = substr_count($firstLine, ';');
                    if ($semicolonCount > $commaCount) {
                        $delimiter = ';';
                    }
                }
            }
            fclose($handle);
        }

        $rows = [];
        if (($handle = fopen($csvPath, "r")) !== FALSE) {
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }
            
            // Bỏ qua dòng chỉ thị sep=... ở dòng đầu tiên
            $position = ftell($handle);
            $firstLine = fgets($handle);
            if ($firstLine !== FALSE && str_starts_with(trim($firstLine), 'sep=')) {
                // Để yên con trỏ ở dòng tiếp theo
            } else {
                fseek($handle, $position);
            }

            while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
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

        $header = array_map('trim', array_shift($rows));
        
        // Mảng ánh xạ tiêu đề tiếng Việt/tiếng Anh về key DB
        $columnMapping = [
            'tên dược liệu (*)' => 'name',
            'tên dược liệu' => 'name',
            'phân loại' => 'category',
            'cách dùng' => 'usage_type',
            'đơn vị tính' => 'unit',
            'đơn vị' => 'unit',
            'số lượng tồn' => 'stock_quantity',
            'tồn kho' => 'stock_quantity',
            'hạn sử dụng (yyyy-mm-dd)' => 'expiry_date',
            'hạn sử dụng' => 'expiry_date',
            'trạng thái' => 'status',
            'ghi chú cảnh báo' => 'warning_note',
            'mô tả chi tiết' => 'description',
            'mô tả' => 'description',
            
            // Tiếng Anh
            'name' => 'name',
            'category' => 'category',
            'usage_type' => 'usage_type',
            'unit' => 'unit',
            'stock_quantity' => 'stock_quantity',
            'expiry_date' => 'expiry_date',
            'status' => 'status',
            'warning_note' => 'warning_note',
            'description' => 'description'
        ];

        // Chuyển đổi header sang key DB chuẩn
        $englishHeader = [];
        foreach ($header as $col) {
            $colLower = mb_strtolower(trim($col), 'UTF-8');
            if (isset($columnMapping[$colLower])) {
                $englishHeader[] = $columnMapping[$colLower];
            } else {
                $englishHeader[] = $col;
            }
        }

        // Bắt buộc phải có cột Tên dược liệu
        if (!in_array('name', $englishHeader)) {
            return back()->with('error', 'File nhập thiếu cột bắt buộc "Tên Dược Liệu (*)". Vui lòng sử dụng file mẫu để đảm bảo đúng cấu trúc.');
        }

        $imported = 0;
        $errors = [];
        $lineNumber = 1;

        foreach ($rows as $row) {
            $lineNumber++;

            if (count($row) < 1 || empty(trim($row[0] ?? ''))) {
                continue;
            }

            $data = [];
            foreach ($englishHeader as $i => $col) {
                if (!empty($col)) {
                    $data[trim($col)] = trim($row[$i] ?? '');
                }
            }

            // Bỏ qua nếu dòng này trùng lặp header hoặc tiêu đề
            if (isset($data['name']) && (
                $data['name'] === 'name' || 
                str_contains($data['name'], 'Tên dược liệu') ||
                str_contains($data['name'], 'Tên Dược Liệu')
            )) {
                continue;
            }

            if (empty($data['name'])) {
                $errors[] = "Dòng {$lineNumber}: Thiếu tên dược liệu.";
                continue;
            }

            // Chuẩn hóa trạng thái tiếng Việt / tiếng Anh
            $statusRaw = mb_strtolower($data['status'] ?? '', 'UTF-8');
            if (in_array($statusRaw, ['đang sử dụng', 'active', 'đang dùng', 'hoạt động', 'đang hoạt động'])) {
                $status = 'active';
            } elseif (in_array($statusRaw, ['hết hàng', 'out_of_stock'])) {
                $status = 'out_of_stock';
            } else {
                $status = 'expired';
            }

            // Chuẩn hóa hạn sử dụng
            $expiryDate = null;
            if (!empty($data['expiry_date']) && $data['expiry_date'] !== '—') {
                $expiryDate = $data['expiry_date'];
            }

            // Kiểm tra trùng tên dược liệu để cộng dồn tồn kho
            $existingHerb = MedicinalHerb::where('name', $data['name'])->first();

            try {
                if ($existingHerb) {
                    $existingHerb->stockLogType = 'excel_import';
                    $existingHerb->stockLogNote = "Nhập từ Excel (Cộng dồn +" . floatval($data['stock_quantity'] ?? 0) . " " . ($data['unit'] ?: $existingHerb->unit) . ")";
                    $existingHerb->update([
                        'category' => !empty($data['category']) ? $data['category'] : $existingHerb->category,
                        'usage_type' => !empty($data['usage_type']) ? $data['usage_type'] : $existingHerb->usage_type,
                        'unit' => !empty($data['unit']) ? $data['unit'] : $existingHerb->unit,
                        'stock_quantity' => $existingHerb->stock_quantity + floatval($data['stock_quantity'] ?? 0),
                        'expiry_date' => $expiryDate ? $expiryDate : $existingHerb->expiry_date,
                        'status' => $status,
                        'warning_note' => !empty($data['warning_note']) ? $data['warning_note'] : $existingHerb->warning_note,
                        'description' => !empty($data['description']) ? $data['description'] : $existingHerb->description,
                    ]);
                    $imported++;
                } else {
                    $newHerb = new MedicinalHerb();
                    $newHerb->stockLogType = 'excel_import';
                    $newHerb->stockLogNote = "Nhập từ Excel (Tồn kho đầu kỳ: " . floatval($data['stock_quantity'] ?? 0) . " " . ($data['unit'] ?: 'g') . ")";
                    $newHerb->fill([
                        'name' => $data['name'],
                        'category' => !empty($data['category']) ? $data['category'] : 'Dược liệu bốc thuốc',
                        'usage_type' => !empty($data['usage_type']) ? $data['usage_type'] : 'Sắc',
                        'unit' => !empty($data['unit']) ? $data['unit'] : 'g',
                        'stock_quantity' => floatval($data['stock_quantity'] ?? 0),
                        'expiry_date' => $expiryDate ? $expiryDate : null,
                        'status' => $status,
                        'warning_note' => !empty($data['warning_note']) ? $data['warning_note'] : null,
                        'description' => !empty($data['description']) ? $data['description'] : null,
                    ]);
                    $newHerb->save();
                    $imported++;
                }
            } catch (\Exception $e) {
                $errors[] = "Dòng {$lineNumber} ({$data['name']}): " . $e->getMessage();
            }
        }

        $message = "Đã nhập thành công {$imported} dược liệu.";
        if (count($errors) > 0) {
            $message .= ' Có ' . count($errors) . ' dòng lỗi.';
        }

        $redirect = redirect()->route('admin.medicinal-herbs.index')->with('success', $message);
        if (count($errors) > 0) {
            $redirect = $redirect->with('import_errors', $errors);
        }

        return $redirect;
    }

    public function printList(Request $request)
    {
        $query = MedicinalHerb::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
        }

        if ($request->filled('usage_type')) {
            $query->where('usage_type', $request->usage_type);
        }

        $herbs = $query->orderBy('name')->get();
        
        $title = 'DANH SÁCH DƯỢC LIỆU / THẢO DƯỢC KHO';
        
        return view('admin.medicinal_herbs.print-list', compact('herbs', 'title'));
    }

    public function stockLogs(MedicinalHerb $medicinalHerb)
    {
        $logs = \App\Models\MedicinalHerbStockLog::with('user')
            ->where('medicinal_herb_id', $medicinalHerb->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'herb_name' => $medicinalHerb->name,
            'unit' => $medicinalHerb->unit,
            'logs' => $logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'operator' => $log->user ? $log->user->name : 'Hệ thống',
                    'old_quantity' => floatval($log->old_quantity),
                    'new_quantity' => floatval($log->new_quantity),
                    'change_quantity' => floatval($log->change_quantity),
                    'action_type_label' => $log->action_type_label,
                    'note' => $log->note,
                    'details' => $log->details,
                    'time' => $log->created_at->format('H:i d/m/Y'),
                ];
            })
        ]);
    }
}
