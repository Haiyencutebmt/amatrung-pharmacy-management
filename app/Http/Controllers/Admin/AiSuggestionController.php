<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiSuggestionLog;
use App\Models\MedicalRecord;
use App\Services\AiClinicalSuggestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * AiSuggestionController
 *
 * Endpoint "Gợi ý AI hỗ trợ thầy thuốc".
 *
 * NGUYÊN TẮC AN TOÀN:
 *  - Frontend CHỈ gửi medical_record_id.
 *  - Controller tự load bệnh án và kiểm tra quyền truy cập.
 *  - Không nhận symptoms/diagnosis/patient data từ request.
 *  - Ghi log đầy đủ vào ai_suggestion_logs (payload đã ẩn danh).
 */
class AiSuggestionController extends Controller
{
    protected AiClinicalSuggestionService $aiService;

    public function __construct(AiClinicalSuggestionService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * POST /admin/api/ai-suggest
     * Body: { medical_record_id: int }
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggest(Request $request)
    {
        // 1. Validate: chỉ nhận medical_record_id
        $validated = $request->validate([
            'medical_record_id' => 'required|integer|exists:medical_records,id',
        ], [
            'medical_record_id.required' => 'Thiếu ID bệnh án.',
            'medical_record_id.integer'  => 'ID bệnh án không hợp lệ.',
            'medical_record_id.exists'   => 'Bệnh án không tồn tại.',
        ]);

        // 2. Load bệnh án kèm patient (eager load)
        $record = MedicalRecord::with('patient')->find($validated['medical_record_id']);

        if (!$record) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bệnh án không tồn tại.',
            ], 404);
        }

        // 3. Kiểm tra quyền: chỉ cho phép staff phụ trách hoặc admin xem bệnh án này
        // (Policy check - nếu staff_id tồn tại, chỉ staff đó hoặc admin mới gọi được)
        $user = Auth::user();
        $isAdmin = $user->hasRole(['admin', 'owner']);
        $isAssignedStaff = $record->staff_id && $record->staff_id === $user->id;

        if (!$isAdmin && !$isAssignedStaff) {
            Log::warning("[AiSuggestionController] User #{$user->id} cố gọi AI cho bệnh án #{$record->id} không được phân công.");
            return response()->json([
                'status'  => 'error',
                'message' => 'Bạn không có quyền truy cập bệnh án này.',
            ], 403);
        }

        // 4. Gọi service sinh gợi ý
        try {
            $result = $this->aiService->suggestFromRecord($record);
        } catch (\Exception $e) {
            Log::error("[AiSuggestionController] Exception: " . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Lỗi hệ thống khi xử lý gợi ý AI.',
            ], 500);
        }

        // 5. Ghi vào ai_suggestion_logs (payload đã ẩn danh từ contextBuilder)
        $logEntry = null;
        try {
            $logEntry = AiSuggestionLog::create([
                'user_id'           => $user->id,
                'medical_record_id' => $record->id,
                'payload'           => $result['payload_sent'] ?? [],
                'response'          => $result['suggestions'] ?? [],
                'status'            => ($result['status'] === 'success') ? 'generated' : 'failed',
                'error_message'     => ($result['status'] === 'ai_unavailable')
                                         ? ($result['message'] ?? null)
                                         : null,
            ]);
        } catch (\Exception $e) {
            // Log lỗi nhưng không dừng request
            Log::error("[AiSuggestionController] Không thể ghi ai_suggestion_logs: " . $e->getMessage());
        }

        // 6. Trả về JSON cho frontend (không bao gồm payload_sent - chỉ để log)
        return response()->json([
            'status'      => $result['status'],
            'log_id'      => $logEntry?->id,
            'suggestions' => $result['suggestions'] ?? [],
            'disclaimer'  => $result['disclaimer'] ?? $this->aiService->getDisclaimer(),
            'message'     => $result['message'] ?? null,
        ]);
    }

    /**
     * POST /admin/api/ai-suggest/log-status
     * Body: { log_id: int, interaction_status: 'referenced'|'not_used' }
     *
     * Cập nhật trạng thái tương tác của bác sĩ với gợi ý AI.
     * Không tự lưu đơn thuốc - chỉ cập nhật log.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLogStatus(Request $request)
    {
        $validated = $request->validate([
            'log_id'             => 'required|integer|exists:ai_suggestion_logs,id',
            'interaction_status' => 'required|in:referenced,not_used',
        ], [
            'log_id.required'             => 'Thiếu ID log.',
            'log_id.exists'               => 'Log không tồn tại.',
            'interaction_status.required' => 'Thiếu trạng thái tương tác.',
            'interaction_status.in'       => 'Trạng thái không hợp lệ. Chấp nhận: referenced, not_used.',
        ]);

        $log = AiSuggestionLog::find($validated['log_id']);

        if (!$log) {
            return response()->json(['status' => 'error', 'message' => 'Log không tồn tại.'], 404);
        }

        // Kiểm tra quyền: chỉ người tạo log hoặc admin mới được cập nhật
        $user    = Auth::user();
        $isAdmin = $user->hasRole(['admin', 'owner']);
        if (!$isAdmin && $log->user_id !== $user->id) {
            return response()->json(['status' => 'error', 'message' => 'Không có quyền cập nhật log này.'], 403);
        }

        $log->update(['status' => $validated['interaction_status']]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Đã cập nhật trạng thái tương tác.',
            'log_id'  => $log->id,
        ]);
    }
}
