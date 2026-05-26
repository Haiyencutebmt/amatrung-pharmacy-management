{{--
    ai_panel.blade.php
    Panel "Gợi ý AI hỗ trợ thầy thuốc" – nhúng vào records/show.blade.php và prescriptions/create.blade.php

    Biến cần truyền:
      $medicalRecord  – Model MedicalRecord (có id và treatment_direction)

    Điều kiện hiển thị: Blade @can('use_ai_suggestion') ở trang nhúng.
    Panel này KHÔNG tự kê đơn, KHÔNG tự lưu đơn, KHÔNG tự trừ kho.
--}}
<div id="ai-suggestion-panel" class="card border-0 shadow-sm mt-4" style="border-radius:12px; overflow:hidden;">
    <div class="card-header d-flex align-items-center justify-content-between py-3"
         style="background: linear-gradient(135deg,#1a5276,#2e86c1); border:none;">
        <div class="d-flex align-items-center gap-2">
            <span style="font-size:1.3rem;">🤖</span>
            <div>
                <h6 class="mb-0 text-white fw-semibold" style="font-size:.95rem;">
                    Gợi ý AI hỗ trợ thầy thuốc
                </h6>
                <small class="text-white-50" style="font-size:.72rem;">
                    Chỉ mang tính tham khảo – Mọi quyết định thuộc thẩm quyền thầy thuốc
                </small>
            </div>
        </div>
        @if($medicalRecord->treatment_direction === 'referral')
            <span class="badge bg-warning text-dark" style="font-size:.72rem;">Chuyển viện – không gợi ý</span>
        @else
            <button id="btn-ai-suggest"
                    class="btn btn-sm btn-light fw-semibold"
                    style="font-size:.8rem; border-radius:8px;"
                    data-record-id="{{ $medicalRecord->id }}">
                ✨ Lấy gợi ý
            </button>
        @endif
    </div>

    {{-- Disclaimer cố định --}}
    <div class="px-3 py-2" style="background:#eaf4fb; border-bottom:1px solid #d6eaf8;">
        <small class="text-muted" style="font-size:.72rem;">
            ⚠️ <strong>Lưu ý:</strong> Đây là <strong>gợi ý tham khảo</strong> của AI hỗ trợ thầy thuốc.
            AI không thay thế chẩn đoán lâm sàng. Thầy thuốc chịu trách nhiệm mọi quyết định điều trị.
        </small>
    </div>

    <div class="card-body p-3">
        {{-- Trạng thái --}}
        <div id="ai-status-box" class="text-center text-muted py-3" style="font-size:.85rem;">
            @if($medicalRecord->treatment_direction === 'referral')
                <span class="text-warning">🔁 Bệnh nhân được chỉ định chuyển viện. Không áp dụng gợi ý thuốc.</span>
            @else
                <span class="text-muted">Nhấn <strong>"Lấy gợi ý"</strong> để AI phân tích bệnh án.</span>
            @endif
        </div>

        {{-- Loading --}}
        <div id="ai-loading" class="text-center py-3 d-none">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
            <span class="text-muted" style="font-size:.85rem;">Đang phân tích bệnh án…</span>
        </div>

        {{-- Kết quả --}}
        <div id="ai-result-box" class="d-none">
            {{-- Reasoning --}}
            <div id="ai-reasoning-section" class="mb-3 d-none">
                <p class="fw-semibold text-secondary mb-1" style="font-size:.82rem;">💡 Nhận xét lâm sàng tham khảo:</p>
                <div id="ai-reasoning-text"
                     class="p-2 rounded text-dark"
                     style="background:#f8f9fa; font-size:.83rem; border-left:3px solid #2e86c1; white-space:pre-wrap;"></div>
            </div>

            {{-- Oral Herbs --}}
            <div id="ai-oral-section" class="mb-3 d-none">
                <p class="fw-semibold text-secondary mb-1" style="font-size:.82rem;">🌿 Dược liệu uống tham khảo (có trong kho):</p>
                <div class="alert alert-warning py-1 px-2 mb-2" style="font-size:.72rem; border-radius:6px;">
                    ⚠️ Gợi ý dược liệu bên dưới <strong>chưa có liều lượng</strong>.
                    Thầy thuốc cần xác định liều phù hợp khi lập đơn chính thức.
                </div>
                <div id="ai-oral-list"></div>
            </div>

            {{-- External Herbs --}}
            <div id="ai-external-section" class="mb-3 d-none">
                <p class="fw-semibold text-secondary mb-1" style="font-size:.82rem;">🧴 Sản phẩm dùng ngoài tham khảo:</p>
                <div id="ai-external-list"></div>
            </div>

            {{-- Therapy Services --}}
            <div id="ai-therapy-section" class="mb-3 d-none">
                <p class="fw-semibold text-secondary mb-1" style="font-size:.82rem;">🏥 Dịch vụ trị liệu tham khảo:</p>
                <div id="ai-therapy-list"></div>
            </div>

            {{-- Safety Note --}}
            <div id="ai-safety-section" class="mb-3 d-none">
                <div id="ai-safety-text"
                     class="alert alert-danger py-2 px-2 mb-0"
                     style="font-size:.78rem; border-radius:6px;"></div>
            </div>

            {{-- Follow up --}}
            <div id="ai-followup-section" class="mb-3 d-none">
                <p class="fw-semibold text-secondary mb-1" style="font-size:.82rem;">🔄 Gợi ý theo dõi:</p>
                <div id="ai-followup-text"
                     class="p-2 rounded text-dark"
                     style="background:#f0fff4; font-size:.83rem; border-left:3px solid #27ae60;"></div>
            </div>

            {{-- Interaction status buttons (chỉ để ghi log, không tự apply) --}}
            <div id="ai-interaction-bar" class="d-none pt-2 border-top mt-2">
                <p class="text-muted mb-2" style="font-size:.75rem;">
                    Sau khi xem xét gợi ý, thầy thuốc vui lòng ghi nhận để cải thiện AI:
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-sm btn-outline-success btn-ai-interact"
                            data-status="accepted"
                            style="font-size:.78rem; border-radius:6px;">
                        ✅ Tham khảo và tự lập đơn
                    </button>
                    <button class="btn btn-sm btn-outline-warning btn-ai-interact"
                            data-status="edited"
                            style="font-size:.78rem; border-radius:6px;">
                        ✏️ Có tham khảo một phần
                    </button>
                    <button class="btn btn-sm btn-outline-secondary btn-ai-interact"
                            data-status="ignored"
                            style="font-size:.78rem; border-radius:6px;">
                        🚫 Không dùng gợi ý này
                    </button>
                </div>
                <div id="ai-interact-feedback" class="mt-2 d-none" style="font-size:.75rem;"></div>
            </div>
        </div>

        {{-- Error box --}}
        <div id="ai-error-box" class="d-none alert alert-warning py-2 mb-0" style="font-size:.83rem; border-radius:8px;">
        </div>
    </div>
</div>
