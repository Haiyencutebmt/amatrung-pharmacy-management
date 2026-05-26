<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HerbDictionaryEntry;
use App\Models\HerbDictionaryImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class HerbDictionaryController extends Controller implements HasMiddleware
{
    private const DEFAULT_SAFETY_WARNING = 'Không nên tự ý sử dụng thuốc nam hoặc phối hợp nhiều vị thuốc khi chưa được thầy thuốc thăm khám. Vui lòng khám trực tiếp để biết tình trạng phù hợp trước khi sử dụng.';

    public static function middleware(): array
    {
        return [
            new Middleware('permission:herb_dictionary.manage'),
        ];
    }

    public function index(Request $request)
    {
        $query = HerbDictionaryEntry::with('images')->withCount('favorites');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('scientific_name', 'like', "%{$search}%")
                    ->orWhere('other_names', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['published', 'draft'], true)) {
            $query->where('status', $request->status);
        }

        $entries = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.herb_dictionary.index', compact('entries'));
    }

    public function create()
    {
        return view('admin.herb_dictionary.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateEntry($request);
        $validated['created_by'] = auth()->id();
        $validated['slug'] = $this->uniqueSlug($validated['name']);
        $validated['status'] = $request->input('status', 'published');

        $images = $request->file('images', []);
        if (count($images) === 0) {
            return back()->withInput()->with('error', 'Mỗi mục từ điển cần có ít nhất 1 hình ảnh.');
        }

        $entry = HerbDictionaryEntry::create($validated);
        $this->storeImages($entry, $images);

        return redirect()->route('admin.herb-dictionary.index')->with('success', 'Đã thêm mục từ điển thuốc nam.');
    }

    public function edit(HerbDictionaryEntry $herbDictionary)
    {
        $herbDictionary->load('images');

        return view('admin.herb_dictionary.edit', ['entry' => $herbDictionary]);
    }

    public function update(Request $request, HerbDictionaryEntry $herbDictionary)
    {
        $validated = $this->validateEntry($request, $herbDictionary);

        if ($herbDictionary->name !== $validated['name']) {
            $validated['slug'] = $this->uniqueSlug($validated['name'], $herbDictionary->id);
        }

        $validated['status'] = $request->input('status', 'published');
        $herbDictionary->update($validated);

        $images = $request->file('images', []);
        $currentImageCount = $herbDictionary->images()->count();

        if ($currentImageCount + count($images) > 5) {
            return back()->withInput()->with('error', 'Mỗi mục từ điển chỉ được tối đa 5 hình ảnh.');
        }

        if ($images) {
            $this->storeImages($herbDictionary, $images, $currentImageCount);
        }

        return redirect()->route('admin.herb-dictionary.index')->with('success', 'Đã cập nhật mục từ điển thuốc nam.');
    }

    public function storeImage(Request $request, HerbDictionaryEntry $herbDictionary)
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'image.required' => 'Vui lòng chọn hình ảnh cần thêm.',
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.mimes' => 'Ảnh chỉ hỗ trợ JPG, PNG hoặc WEBP.',
            'image.max' => 'Mỗi ảnh tối đa 4MB.',
        ]);

        $currentImageCount = $herbDictionary->images()->count();
        if ($currentImageCount >= 5) {
            $message = 'Mỗi mục từ điển chỉ được tối đa 5 hình ảnh.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $path = $validated['image']->store('herb-dictionary', 'public');
        $image = HerbDictionaryImage::create([
            'entry_id' => $herbDictionary->id,
            'image_path' => $path,
            'sort_order' => $currentImageCount,
        ]);

        $message = "Đã thêm hình ảnh cho {$herbDictionary->name}.";

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'image' => [
                    'id' => $image->id,
                    'url' => $image->url,
                    'alt' => $herbDictionary->name,
                ],
            ], 201);
        }

        return back()->with('success', $message);
    }

    public function destroyImage(HerbDictionaryEntry $herbDictionary, HerbDictionaryImage $image)
    {
        if ((int) $image->entry_id !== (int) $herbDictionary->id) {
            abort(404);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        $herbDictionary->images()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->values()
            ->each(function (HerbDictionaryImage $remainingImage, int $index) {
                if ((int) $remainingImage->sort_order !== $index) {
                    $remainingImage->update(['sort_order' => $index]);
                }
            });

        return back()->with('success', "Đã xóa hình ảnh khỏi {$herbDictionary->name}.");
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:herb_dictionary_entries,id'],
        ], [
            'ids.required' => 'Vui lòng chọn ít nhất một mục từ điển để xóa.',
            'ids.min' => 'Vui lòng chọn ít nhất một mục từ điển để xóa.',
        ]);

        $entries = HerbDictionaryEntry::with('images')
            ->whereIn('id', $validated['ids'])
            ->get();

        if ($entries->isEmpty()) {
            return back()->with('error', 'Không tìm thấy mục từ điển cần xóa.');
        }

        $imagePaths = $entries
            ->flatMap(fn (HerbDictionaryEntry $entry) => $entry->images->pluck('image_path'))
            ->filter()
            ->values()
            ->all();

        if (!empty($imagePaths)) {
            Storage::disk('public')->delete($imagePaths);
        }

        $deletedCount = $entries->count();
        $entries->each->delete();

        return back()->with('success', "Đã xóa {$deletedCount} mục từ điển đã chọn.");
    }

    public function downloadTemplate()
    {
        $path = public_path('templates/mau_import_tu_dien_thuoc_nam.xlsx');

        if (!file_exists($path)) {
            return back()->with('error', 'Không tìm thấy file mẫu Excel từ điển thuốc nam. Vui lòng liên hệ quản trị viên.');
        }

        return response()->download($path, 'mau_import_tu_dien_thuoc_nam.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'dictionary_file' => ['required', 'file', 'max:8192'],
        ], [
            'dictionary_file.required' => 'Vui lòng chọn file Excel hoặc CSV để nhập từ điển.',
            'dictionary_file.max' => 'File nhập tối đa 8MB.',
        ]);

        $file = $request->file('dictionary_file');
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, ['xlsx', 'csv', 'txt'], true)) {
            return back()->with('error', 'Định dạng file chưa hỗ trợ. Vui lòng dùng file Excel .xlsx hoặc CSV.');
        }

        try {
            $rows = $this->readImportRows($file, $extension);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        if (empty($rows)) {
            return back()->with('error', 'File nhập không có dữ liệu.');
        }

        [$header, $rows] = $this->extractImportHeaderAndDataRows($rows);
        $mappedHeader = [];

        foreach ($header as $index => $columnName) {
            $field = $this->mapImportColumn((string) $columnName);
            if ($field) {
                $mappedHeader[$index] = $field;
            }
        }

        if (!in_array('name', $mappedHeader, true)) {
            return back()->with('error', 'File nhập thiếu cột bắt buộc "Tên thuốc nam *". Vui lòng tải file mẫu và thử lại.');
        }

        $created = 0;
        $updated = 0;
        $errors = [];

        foreach ($rows as $rowIndex => $row) {
            $lineNumber = $rowIndex + 2;

            if ($this->isEmptyImportRow($row)) {
                continue;
            }

            $data = [];
            foreach ($mappedHeader as $index => $field) {
                $value = $this->cleanImportValue($row[$index] ?? '');
                if ($value !== '') {
                    $data[$field] = $value;
                }
            }

            if ($this->isRepeatedHeaderRow($data)) {
                continue;
            }

            if (empty($data['name'])) {
                $errors[] = "Dòng {$lineNumber}: Thiếu tên thuốc nam.";
                continue;
            }

            try {
                $slug = Str::slug($data['name']);
                $entry = HerbDictionaryEntry::where('name', $data['name'])
                    ->orWhere('slug', $slug)
                    ->first();

                if ($entry) {
                    $payload = $this->buildImportPayload($data, $entry);
                    $entry->update($payload);
                    $updated++;
                } else {
                    $payload = $this->buildImportPayload($data);
                    $payload['created_by'] = auth()->id();
                    $payload['slug'] = $this->uniqueSlug($payload['name']);
                    HerbDictionaryEntry::create($payload);
                    $created++;
                }
            } catch (\Throwable $e) {
                $errors[] = "Dòng {$lineNumber} ({$data['name']}): {$e->getMessage()}";
            }
        }

        if ($created === 0 && $updated === 0) {
            $message = 'Chưa nhập được mục từ điển nào.';
            return back()->with('error', $message)->with('import_errors', $errors);
        }

        $message = "Đã nhập {$created} mục mới";
        if ($updated > 0) {
            $message .= ", cập nhật {$updated} mục đã có";
        }
        $message .= '. Ảnh không được nhập từ file, có thể bổ sung bằng dấu + ở cột Hình ảnh hoặc trong nút Sửa.';

        $redirect = redirect()->route('admin.herb-dictionary.index')->with('success', $message);
        if (!empty($errors)) {
            $redirect = $redirect->with('import_errors', $errors);
        }

        return $redirect;
    }

    private function validateEntry(Request $request, ?HerbDictionaryEntry $entry = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'scientific_name' => ['nullable', 'string', 'max:180'],
            'other_names' => ['nullable', 'string', 'max:255'],
            'family' => ['nullable', 'string', 'max:150'],
            'plant_part' => ['nullable', 'string', 'max:150'],
            'properties' => ['nullable', 'string', 'max:255'],
            'basic_info' => ['required', 'string'],
            'effects' => ['required', 'string'],
            'usage_notes' => ['nullable', 'string'],
            'safety_warning' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['published', 'draft'])],
            'images' => [$entry ? 'nullable' : 'required', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'images.required' => 'Mỗi mục từ điển cần có ít nhất 1 hình ảnh.',
            'images.max' => 'Mỗi mục từ điển chỉ được tối đa 5 hình ảnh.',
            'images.*.image' => 'Tệp tải lên phải là hình ảnh.',
            'images.*.mimes' => 'Ảnh chỉ hỗ trợ JPG, PNG hoặc WEBP.',
            'images.*.max' => 'Mỗi ảnh tối đa 4MB.',
        ]);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (HerbDictionaryEntry::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    private function storeImages(HerbDictionaryEntry $entry, array $images, int $startOrder = 0): void
    {
        foreach (array_values($images) as $index => $image) {
            $path = $image->store('herb-dictionary', 'public');
            HerbDictionaryImage::create([
                'entry_id' => $entry->id,
                'image_path' => $path,
                'sort_order' => $startOrder + $index,
            ]);
        }
    }

    private function readImportRows(UploadedFile $file, string $extension): array
    {
        $csvPath = $file->getRealPath();
        $tempCsvPath = null;

        if ($extension === 'xlsx') {
            $tempXlsxPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'uploaded_herb_dictionary_' . uniqid() . '.xlsx';
            copy($file->getRealPath(), $tempXlsxPath);

            $tempCsvPath = tempnam(sys_get_temp_dir(), 'herb_dictionary_csv_');
            $pythonScript = base_path('app/Scripts/xlsx2csv.py');
            $cmd = 'python ' . escapeshellarg($pythonScript) . ' ' . escapeshellarg($tempXlsxPath) . ' ' . escapeshellarg($tempCsvPath);

            $output = [];
            $resultCode = 0;
            exec($cmd, $output, $resultCode);

            if (file_exists($tempXlsxPath)) {
                @unlink($tempXlsxPath);
            }

            if ($resultCode !== 0) {
                if ($tempCsvPath && file_exists($tempCsvPath)) {
                    @unlink($tempCsvPath);
                }

                throw new \RuntimeException('Lỗi đọc file Excel: ' . implode("\n", $output));
            }

            $csvPath = $tempCsvPath;
        }

        $rows = $this->readCsvRows($csvPath);

        if ($tempCsvPath && file_exists($tempCsvPath)) {
            @unlink($tempCsvPath);
        }

        return $rows;
    }

    private function readCsvRows(string $csvPath): array
    {
        $delimiter = $this->detectCsvDelimiter($csvPath);
        $rows = [];

        if (($handle = fopen($csvPath, 'r')) === false) {
            return $rows;
        }

        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $position = ftell($handle);
        $firstLine = fgets($handle);
        if ($firstLine === false || !str_starts_with(mb_strtolower(trim($firstLine), 'UTF-8'), 'sep=')) {
            fseek($handle, $position);
        }

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (!empty($data)) {
                $rows[] = $data;
            }
        }

        fclose($handle);

        return $rows;
    }

    private function extractImportHeaderAndDataRows(array $rows): array
    {
        foreach ($rows as $index => $row) {
            $mappedColumns = array_filter(array_map(
                fn ($columnName) => $this->mapImportColumn((string) $columnName),
                $row
            ));

            if (in_array('name', $mappedColumns, true) && count($mappedColumns) >= 2) {
                return [$row, array_slice($rows, $index + 1)];
            }
        }

        return [array_shift($rows) ?? [], $rows];
    }

    private function detectCsvDelimiter(string $csvPath): string
    {
        $delimiter = ',';

        if (($handle = fopen($csvPath, 'r')) === false) {
            return $delimiter;
        }

        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $firstLine = fgets($handle);
        if ($firstLine !== false && str_starts_with(mb_strtolower(trim($firstLine), 'UTF-8'), 'sep=')) {
            $sepChar = substr(trim($firstLine), 4, 1);
            if (in_array($sepChar, [',', ';', "\t"], true)) {
                fclose($handle);
                return $sepChar;
            }

            $firstLine = fgets($handle);
        }

        if ($firstLine !== false) {
            $commaCount = substr_count($firstLine, ',');
            $semicolonCount = substr_count($firstLine, ';');
            $tabCount = substr_count($firstLine, "\t");

            if ($semicolonCount > $commaCount && $semicolonCount >= $tabCount) {
                $delimiter = ';';
            } elseif ($tabCount > $commaCount && $tabCount > $semicolonCount) {
                $delimiter = "\t";
            }
        }

        fclose($handle);

        return $delimiter;
    }

    private function mapImportColumn(string $columnName): ?string
    {
        $aliases = [
            'ten thuoc nam' => 'name',
            'ten thuoc nam' => 'name',
            'ten cay thuoc' => 'name',
            'ten vi thuoc' => 'name',
            'name' => 'name',
            'scientific name' => 'scientific_name',
            'ten khoa hoc' => 'scientific_name',
            'other names' => 'other_names',
            'ten goi khac' => 'other_names',
            'family' => 'family',
            'ho thuc vat' => 'family',
            'ho' => 'family',
            'plant part' => 'plant_part',
            'bo phan dung' => 'plant_part',
            'properties' => 'properties',
            'tinh vi dac diem' => 'properties',
            'tinh vi' => 'properties',
            'dac diem' => 'properties',
            'basic info' => 'basic_info',
            'thong tin co ban' => 'basic_info',
            'mo ta' => 'basic_info',
            'effects' => 'effects',
            'tac dung' => 'effects',
            'cong dung' => 'effects',
            'usage notes' => 'usage_notes',
            'luu y khi su dung' => 'usage_notes',
            'luu y' => 'usage_notes',
            'cach dung' => 'usage_notes',
            'safety warning' => 'safety_warning',
            'khuyen cao an toan' => 'safety_warning',
            'canh bao an toan' => 'safety_warning',
            'canh bao' => 'safety_warning',
            'status' => 'status',
            'trang thai' => 'status',
        ];

        $key = $this->normalizeImportText($columnName);
        $key = trim(str_replace('*', '', $key));

        return $aliases[$key] ?? null;
    }

    private function buildImportPayload(array $data, ?HerbDictionaryEntry $entry = null): array
    {
        $payload = [
            'name' => $data['name'],
        ];

        foreach (['scientific_name', 'other_names', 'family', 'plant_part', 'properties', 'basic_info', 'effects', 'usage_notes', 'safety_warning'] as $field) {
            if (array_key_exists($field, $data)) {
                $payload[$field] = $data[$field];
            }
        }

        if (array_key_exists('status', $data)) {
            $payload['status'] = $this->normalizeImportStatus($data['status']);
        } elseif (!$entry) {
            $payload['status'] = 'published';
        }

        if (!$entry) {
            $payload['basic_info'] = $payload['basic_info'] ?? 'Đang cập nhật thông tin cơ bản.';
            $payload['effects'] = $payload['effects'] ?? 'Đang cập nhật tác dụng tham khảo, cần bổ sung trước khi sử dụng làm tài liệu tra cứu.';
            $payload['safety_warning'] = $payload['safety_warning'] ?? self::DEFAULT_SAFETY_WARNING;
        }

        return $payload;
    }

    private function normalizeImportStatus(string $status): string
    {
        $normalized = $this->normalizeImportText($status);

        if (in_array($normalized, ['draft', 'ban nhap', 'nhap', 'chua xuat ban'], true)) {
            return 'draft';
        }

        return 'published';
    }

    private function cleanImportValue(mixed $value): string
    {
        $value = trim((string) $value);

        if (str_starts_with($value, "\xEF\xBB\xBF")) {
            $value = substr($value, 3);
        }

        return trim($value);
    }

    private function normalizeImportText(string $value): string
    {
        $value = $this->cleanImportValue($value);
        $value = mb_strtolower(Str::ascii($value), 'UTF-8');
        $value = preg_replace('/[^a-z0-9*]+/u', ' ', $value) ?? '';

        return trim(preg_replace('/\s+/', ' ', $value) ?? '');
    }

    private function isEmptyImportRow(array $row): bool
    {
        foreach ($row as $value) {
            if ($this->cleanImportValue($value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function isRepeatedHeaderRow(array $data): bool
    {
        if (!isset($data['name'])) {
            return false;
        }

        return in_array($this->normalizeImportText($data['name']), [
            'name',
            'ten thuoc nam',
            'ten cay thuoc',
            'ten vi thuoc',
        ], true);
    }
}
