<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TherapyService;
use Illuminate\Support\Facades\DB;

class TherapyServiceController extends Controller
{
    /**
     * Display a listing of the therapy services.
     */
    public function index(Request $request)
    {
        return redirect()->route('admin.treatment-templates.index', ['tab' => 'services']);
    }

    /**
     * Store a newly created therapy service in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:therapy_services,name',
            'default_sessions' => 'required|integer|min:1',
            'default_instruction' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Vui lòng nhập tên dịch vụ.',
            'name.unique' => 'Tên dịch vụ trị liệu này đã tồn tại.',
            'default_sessions.required' => 'Vui lòng nhập số buổi mặc định.',
            'default_sessions.integer' => 'Số buổi phải là số nguyên.',
            'default_sessions.min' => 'Số buổi tối thiểu là 1.',
        ]);

        $service = TherapyService::create($validated);

        session()->put('last_action', [
            'model' => TherapyService::class,
            'type' => 'create',
            'id' => $service->id,
            'redirect_url' => route('admin.treatment-templates.index', ['tab' => 'services']),
        ]);

        return redirect()->route('admin.treatment-templates.index', ['tab' => 'services'])
            ->with('success', 'Đã thêm dịch vụ trị liệu "' . $service->name . '" thành công.');
    }

    /**
     * Update the specified therapy service in storage.
     */
    public function update(Request $request, TherapyService $therapyService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:therapy_services,name,' . $therapyService->id,
            'default_sessions' => 'required|integer|min:1',
            'default_instruction' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Vui lòng nhập tên dịch vụ.',
            'name.unique' => 'Tên dịch vụ trị liệu này đã tồn tại.',
            'default_sessions.required' => 'Vui lòng nhập số buổi mặc định.',
            'default_sessions.integer' => 'Số buổi phải là số nguyên.',
            'default_sessions.min' => 'Số buổi tối thiểu là 1.',
        ]);

        $originalData = $therapyService->getOriginal();
        $therapyService->update($validated);

        session()->put('last_action', [
            'model' => TherapyService::class,
            'type' => 'update',
            'id' => $therapyService->id,
            'original_data' => $originalData,
            'redirect_url' => route('admin.treatment-templates.index', ['tab' => 'services']),
        ]);

        return redirect()->route('admin.treatment-templates.index', ['tab' => 'services'])
            ->with('success', 'Đã cập nhật dịch vụ trị liệu "' . $therapyService->name . '" thành công.');
    }

    /**
     * Remove the specified therapy service from storage.
     */
    public function destroy(TherapyService $therapyService)
    {
        $originalData = [$therapyService->getOriginal()];
        $name = $therapyService->name;
        $therapyService->delete();

        session()->put('last_action', [
            'model' => TherapyService::class,
            'type' => 'delete',
            'original_data' => $originalData,
            'redirect_url' => route('admin.treatment-templates.index', ['tab' => 'services']),
        ]);

        return redirect()->route('admin.treatment-templates.index', ['tab' => 'services'])
            ->with('success', 'Đã xóa dịch vụ trị liệu "' . $name . '" thành công.');
    }
}
