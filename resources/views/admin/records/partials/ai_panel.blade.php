{{--
    ai_panel.blade.php
    Panel "Gợi ý AI hỗ trợ thầy thuốc" – nhúng vào prescriptions/create.blade.php
--}}
<div id="ai-suggestion-panel" style="background: #fff; border: 1px solid #e0e7ff; border-radius: 0.5rem; margin-bottom: 1.5rem; overflow: hidden;">
    
    {{-- Header --}}
    <div style="padding: 0.75rem 1.25rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e0e7ff;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div style="color: #3b82f6; font-size: 1.1rem; display: flex; align-items: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8V4H8"/><rect width="16" height="12" x="4" y="8" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M15 13v2"/><path d="M9 13v2"/></svg>
            </div>
            <h3 style="margin: 0; font-size: 0.9rem; font-weight: 800; color: #1e3a8a; text-transform: uppercase;">
                Gợi ý AI hỗ trợ thầy thuốc
            </h3>
            <span style="color: #94a3b8; font-size: 0.8rem; margin-left: 0.5rem;">Tham khảo, không thay thế quyết định chuyên môn</span>
        </div>

        @if($medicalRecord->treatment_direction === 'referral')
            <span style="background: #fef3c7; color: #92400e; padding: 0.3rem 0.75rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 700;">
                Chuyển viện – không gợi ý
            </span>
        @else
            <button id="btn-ai-suggest"
                    type="button"
                    data-record-id="{{ $medicalRecord->id }}"
                    style="background: #2563eb; color: #fff; border: none; padding: 0.4rem 1rem; border-radius: 0.25rem; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.4rem; transition: background 0.2s;"
                    onmouseover="this.style.background='#1d4ed8'"
                    onmouseout="this.style.background='#2563eb'">
                ✨ Lấy gợi ý mới
            </button>
        @endif
    </div>

    {{-- Body --}}
    <div style="padding: 1rem 1.25rem; background: #f8fafc;">

        {{-- Status box (hiện mặc định) --}}
        <div id="ai-status-box" style="text-align: center; padding: 1rem; color: #64748b; font-size: 0.88rem;">
            @if($medicalRecord->treatment_direction === 'referral')
                <span>🔁 Bệnh nhân được chỉ định chuyển viện. Không áp dụng gợi ý thuốc.</span>
            @else
                <span>Nhấn <strong>"✨ Lấy gợi ý mới"</strong> để AI phân tích bệnh án và đề xuất.</span>
            @endif
        </div>

        {{-- Loading (ẩn mặc định) --}}
        <div id="ai-loading" style="display: none; text-align: center; padding: 1.5rem;">
            <div style="display: inline-flex; align-items: center; gap: 0.75rem;">
                <svg style="animation: ai-spin 1s linear infinite; width: 1.25rem; height: 1.25rem; color: #2563eb;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span style="color: #475569; font-weight: 600; font-size: 0.88rem;">Đang phân tích bệnh án…</span>
            </div>
        </div>

        {{-- Error box --}}
        <div id="ai-error-box" style="display: none; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.25rem; padding: 0.75rem 1rem; font-size: 0.85rem; color: #991b1b; font-weight: 600;"></div>

        {{-- Kết quả (3 cột grid) --}}
        <div id="ai-result-box" style="display: none; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
            
            {{-- Cột 1: Nhận xét lâm sàng --}}
            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <div style="background: #eff6ff; color: #3b82f6; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 0.25rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </div>
                    <span style="font-weight: 700; color: #1e293b; font-size: 0.9rem;">Nhận xét lâm sàng</span>
                </div>
                <div id="ai-reasoning-text" style="font-size: 0.82rem; color: #475569; line-height: 1.5;"></div>
            </div>

            {{-- Cột 2: Gợi ý dược liệu --}}
            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <div style="background: #f0fdf4; color: #22c55e; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 0.25rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                    </div>
                    <span style="font-weight: 700; color: #1e293b; font-size: 0.9rem;">Gợi ý dược liệu / Dịch vụ</span>
                </div>
                <div id="ai-herbs-text" style="font-size: 0.82rem; color: #475569; line-height: 1.5;"></div>
            </div>

            {{-- Cột 3: Gợi ý theo dõi --}}
            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <div style="background: #faf5ff; color: #a855f7; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 0.25rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                    </div>
                    <span style="font-weight: 700; color: #1e293b; font-size: 0.9rem;">Gợi ý theo dõi</span>
                </div>
                <div id="ai-followup-text" style="font-size: 0.82rem; color: #475569; line-height: 1.5;"></div>
            </div>

        </div>
    </div>
</div>

<style>
@keyframes ai-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
