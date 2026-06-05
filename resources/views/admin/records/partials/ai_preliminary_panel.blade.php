<div id="ai-preliminary-panel" style="background:#fff; border:1px solid #bfdbfe; border-radius:0.5rem; margin-bottom:1.25rem; overflow:hidden; box-shadow:0 1px 2px rgba(15,23,42,0.04);">
    <div id="ai-panel-header" style="padding:0.8rem 1.25rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; border-bottom:1px solid #dbeafe; background:#eff6ff; cursor:pointer; user-select:none;">
        <div style="display:flex; align-items:center; gap:0.65rem;">
            <div style="width:32px; height:32px; border-radius:0.4rem; background:#dbeafe; color:#2563eb; display:flex; align-items:center; justify-content:center; font-size:1rem;">🧠</div>
            <div>
                <h3 style="margin:0; font-size:0.95rem; font-weight:850; color:#1e3a8a; text-transform:uppercase;">AI hỗ trợ nhận định sơ bộ</h3>
                <p style="margin:0.15rem 0 0; color:#64748b; font-size:0.8rem;">Mức độ phù hợp tham khảo theo thông tin đã nhập, không phải kết luận chẩn đoán.</p>
            </div>
        </div>
        <div style="display:flex; align-items:center; gap:0.35rem; color:#2563eb; font-size:0.82rem; font-weight:750;">
            <span id="ai-panel-toggle-text">Thu gọn</span>
            <span id="ai-panel-toggle-arrow" style="transition: transform 0.2s; font-size: 0.7rem; display: inline-block;">▲</span>
        </div>
    </div>

    <div id="ai-panel-body" style="padding:1rem 1.25rem; background:#f8fafc;">
        <div id="ai-followup-section" style="background:#fff; border:1px solid #dbeafe; border-radius:0.45rem; padding:0.85rem 1rem; margin-bottom:0.85rem;">
            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:0.75rem; margin-bottom:0.65rem;">
                <div>
                    <strong style="display:block; color:#1e3a8a; font-size:0.88rem;">Câu hỏi / thông tin nên khai thác thêm</strong>
                    <p style="margin:0.2rem 0 0; color:#64748b; font-size:0.8rem; line-height:1.45;">Bước hỗ trợ tùy chọn trước khi nhận định sơ bộ.</p>
                </div>
            </div>

            <div id="ai-followup-placeholder" style="color:#64748b; font-size:0.84rem; line-height:1.5; border:1px dashed #cbd5e1; border-radius:0.35rem; padding:0.7rem 0.8rem; background:#f8fafc;">
                Bấm nút để AI gợi ý câu hỏi cần khai thác thêm.
            </div>

            <div id="ai-followup-loading" style="display:none; color:#475569; font-size:0.84rem; font-weight:700; line-height:1.5; border:1px solid #dbeafe; border-radius:0.35rem; padding:0.7rem 0.8rem; background:#eff6ff;">
                Đang gợi ý câu hỏi khai thác thêm...
            </div>

            <div id="ai-followup-error" style="display:none; background:#fef2f2; border:1px solid #fecaca; border-radius:0.35rem; padding:0.7rem 0.8rem; font-size:0.84rem; color:#991b1b; font-weight:700;"></div>

            <div id="ai-followup-questions-list" style="display:none; color:#475569; font-size:0.84rem; line-height:1.55;"></div>
        </div>

        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:0.45rem; padding:0.85rem 1rem; margin-bottom:0.85rem;">
            <label for="ai-preliminary-additional-symptoms" style="display:block; color:#475569; font-size:0.82rem; font-weight:800; margin-bottom:0.4rem;">Triệu chứng / thông tin bổ sung sau khi khai thác thêm</label>
            <textarea id="ai-preliminary-additional-symptoms"
                      rows="4"
                      placeholder="Nhập thêm biểu hiện mới, thời gian khởi phát, mức độ, triệu chứng đi kèm, yếu tố làm nặng/giảm..."
                      style="width:100%; min-height:92px; padding:0.65rem 0.8rem; border:1px solid #cbd5e1; border-radius:0.35rem; font-size:0.86rem; color:#1e293b; font-family:inherit; resize:vertical; box-sizing:border-box;">{{ old('additional_symptoms', $medicalRecord->additional_symptoms) }}</textarea>
        </div>

        <div style="display:flex; align-items:center; gap:0.65rem; flex-wrap:wrap; margin-bottom:0.85rem;">
            <button id="btn-ai-followup-questions"
                    type="button"
                    data-record-id="{{ $medicalRecord->id }}"
                    style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; padding:0.48rem 0.85rem; border-radius:0.35rem; font-size:0.82rem; font-weight:750; cursor:pointer; display:inline-flex; align-items:center; gap:0.4rem;">
                ❔ Gợi ý câu hỏi khai thác thêm
            </button>

            <button id="btn-ai-preliminary"
                    type="button"
                    data-record-id="{{ $medicalRecord->id }}"
                    style="background:#2563eb; color:#fff; border:none; padding:0.5rem 1rem; border-radius:0.35rem; font-size:0.82rem; font-weight:800; cursor:pointer; display:inline-flex; align-items:center; gap:0.4rem;">
                ✨ Phân tích sơ bộ
            </button>
        </div>

        <div style="background:#fff7ed; border:1px solid #fed7aa; color:#9a3412; border-radius:0.35rem; padding:0.65rem 0.85rem; font-size:0.82rem; font-weight:700; line-height:1.45; margin-bottom:0.85rem;">
            Kết quả AI chỉ mang tính hỗ trợ tham khảo. Quyết định chẩn đoán và điều trị thuộc về thầy thuốc.
        </div>

        <div id="ai-preliminary-status" style="text-align:center; color:#64748b; font-size:0.88rem; padding:1rem;">
            Nhấn <strong>“Phân tích sơ bộ”</strong> để AI đọc triệu chứng và gợi ý các hướng nhận định tham khảo.
        </div>

        <div id="ai-preliminary-loading" style="display:none; text-align:center; padding:1.25rem;">
            <div style="display:inline-flex; align-items:center; gap:0.7rem;">
                <svg style="animation:ai-spin 1s linear infinite; width:1.15rem; height:1.15rem; color:#2563eb;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity:0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path style="opacity:0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span style="color:#475569; font-weight:700; font-size:0.88rem;">Đang phân tích thông tin khám...</span>
            </div>
        </div>

        <div id="ai-preliminary-error" style="display:none; background:#fef2f2; border:1px solid #fecaca; border-radius:0.35rem; padding:0.75rem 1rem; font-size:0.85rem; color:#991b1b; font-weight:700;"></div>

        <div id="ai-preliminary-result" style="display:none;">
            <div id="ai-preliminary-summary" style="background:#fff; border:1px solid #e2e8f0; border-radius:0.45rem; padding:0.85rem 1rem; color:#334155; font-size:0.86rem; line-height:1.55; margin-bottom:0.85rem;"></div>
            <div id="ai-preliminary-warning" style="display:none; background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:0.4rem; padding:0.75rem 1rem; font-size:0.85rem; font-weight:700; line-height:1.5; margin-bottom:0.85rem;"></div>
            <div id="ai-preliminary-options" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:0.85rem;"></div>
        </div>
    </div>
</div>

<style>
@keyframes ai-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<!-- Confirm filling an AI reference assessment into the editable record form -->
<div id="ai-confirm-apply-modal" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1rem;">
    <div style="width: 440px; max-width: 100%; background: #f0f9ff; border: 1.5px solid #bae6fd; border-radius: 12px; padding: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); text-align: center; animation: aiModalIn 0.2s ease-out;">
        <div style="width: 48px; height: 48px; margin: 0 auto 0.85rem; border-radius: 999px; display: flex; align-items: center; justify-content: center; background: #e0f2fe; color: #0284c7; font-size: 1.5rem;">
            🧠
        </div>
        <h3 style="margin: 0; color: #0369a1; font-size: 1.15rem; font-weight: 850; text-transform: uppercase; letter-spacing: 0.5px;">Xác nhận nhận định tham khảo</h3>
        
        <p style="margin: 0.75rem 0 0.5rem; color: #334155; font-size: 0.9rem; line-height: 1.5;">
            Bạn có muốn điền nhận định tham khảo này vào form bệnh án không? Tên nhận định sẽ vào ô chẩn đoán, các lưu ý/cảnh báo sẽ vào ô lưu ý cho bác sĩ. Nội dung chưa được lưu cho đến khi thầy thuốc bấm xác nhận.
        </p>

        <!-- Nhận định tham khảo hiển thị trực quan -->
        <div id="ai-confirm-diagnosis-badge" style="display: inline-block; margin: 0.5rem 0 0.75rem; background: #e0f2fe; border: 1px solid #7dd3fc; color: #0369a1; font-weight: 800; font-size: 0.95rem; padding: 0.4rem 1rem; border-radius: 6px; word-break: break-word; text-align: center; max-width: 100%;">
            Nhận định tham khảo mẫu
        </div>

        <div style="background: #e0f2fe; border-left: 4px solid #0284c7; padding: 0.65rem 0.85rem; border-radius: 4px; text-align: left; margin-bottom: 1.25rem;">
            <p style="margin: 0; color: #0369a1; font-size: 0.78rem; font-weight: 700; line-height: 1.4;">
                ⚠️ Lưu ý quan trọng:
            </p>
            <p style="margin: 0.15rem 0 0; color: #0369a1; font-size: 0.78rem; line-height: 1.4;">
                Gợi ý của AI chỉ có tính chất tham khảo. Thầy thuốc vẫn cần tự rà soát và chịu trách nhiệm chuyên môn.
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <button type="button" id="btn-cancel-apply-ai" style="flex: 1; border: 1px solid #cbd5e1; background: #fff; color: #475569; border-radius: 8px; padding: 0.65rem; font-weight: 750; font-size: 0.85rem; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#fff'">
                Hủy bỏ
            </button>
            <button type="button" id="btn-confirm-apply-ai" style="flex: 1; border: none; background: #0284c7; color: white; border-radius: 8px; padding: 0.65rem; font-weight: 750; font-size: 0.85rem; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#0369a1'" onmouseout="this.style.background='#0284c7'">
                Điền lên form
            </button>
        </div>
    </div>
</div>

<style>
@keyframes aiModalIn {
    from { transform: translateY(12px) scale(0.97); opacity: 0; }
    to { transform: translateY(0) scale(1); opacity: 1; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const header = document.getElementById('ai-panel-header');
    const body = document.getElementById('ai-panel-body');
    const toggleText = document.getElementById('ai-panel-toggle-text');
    const toggleArrow = document.getElementById('ai-panel-toggle-arrow');
    const panel = document.getElementById('ai-preliminary-panel');
    
    if (!header || !body) return;

    // Load state
    const savedState = localStorage.getItem('ai_panel_state');
    if (savedState === 'collapsed') {
        body.style.display = 'none';
        panel.style.borderBottomColor = 'transparent';
        if (toggleText) toggleText.textContent = 'Mở rộng';
        if (toggleArrow) toggleArrow.style.transform = 'rotate(180deg)';
    }

    header.addEventListener('click', function() {
        const isCollapsed = body.style.display === 'none';
        if (isCollapsed) {
            body.style.display = 'block';
            panel.style.borderBottomColor = '#bfdbfe';
            if (toggleText) toggleText.textContent = 'Thu gọn';
            if (toggleArrow) toggleArrow.style.transform = 'rotate(0deg)';
            localStorage.setItem('ai_panel_state', 'expanded');
        } else {
            body.style.display = 'none';
            panel.style.borderBottomColor = 'transparent';
            if (toggleText) toggleText.textContent = 'Mở rộng';
            if (toggleArrow) toggleArrow.style.transform = 'rotate(180deg)';
            localStorage.setItem('ai_panel_state', 'collapsed');
        }
    });
});
</script>
