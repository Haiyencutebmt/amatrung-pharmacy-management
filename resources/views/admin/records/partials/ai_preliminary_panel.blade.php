<div id="ai-preliminary-panel" style="background:#fff; border:1px solid #bfdbfe; border-radius:0.5rem; margin-bottom:1.25rem; overflow:hidden; box-shadow:0 1px 2px rgba(15,23,42,0.04);">
    <div style="padding:0.8rem 1.25rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; border-bottom:1px solid #dbeafe; background:#eff6ff;">
        <div style="display:flex; align-items:center; gap:0.65rem;">
            <div style="width:32px; height:32px; border-radius:0.4rem; background:#dbeafe; color:#2563eb; display:flex; align-items:center; justify-content:center; font-size:1rem;">🧠</div>
            <div>
                <h3 style="margin:0; font-size:0.95rem; font-weight:850; color:#1e3a8a; text-transform:uppercase;">AI hỗ trợ nhận định sơ bộ</h3>
                <p style="margin:0.15rem 0 0; color:#64748b; font-size:0.8rem;">Mức độ phù hợp tham khảo theo thông tin đã nhập, không phải kết luận chẩn đoán.</p>
            </div>
        </div>
        <button id="btn-ai-preliminary"
                type="button"
                data-record-id="{{ $medicalRecord->id }}"
                style="background:#2563eb; color:#fff; border:none; padding:0.45rem 1rem; border-radius:0.35rem; font-size:0.82rem; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:0.4rem;">
            ✨ Phân tích sơ bộ
        </button>
    </div>

    <div style="padding:1rem 1.25rem; background:#f8fafc;">
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
            <div id="ai-preliminary-questions" style="display:none; margin-top:0.9rem; background:#fff; border:1px solid #e2e8f0; border-radius:0.45rem; padding:0.85rem 1rem; color:#475569; font-size:0.84rem; line-height:1.55;"></div>
        </div>
    </div>
</div>

<style>
@keyframes ai-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
