{{--
    ai_js.blade.php
    JavaScript logic cho panel "Gợi ý AI hỗ trợ thầy thuốc".
--}}
<script>
window.aiSuggestionsData = null; // Biến toàn cục để chức năng "Áp dụng vào đơn nháp" có thể đọc

(function () {
    'use strict';

    // ── Config ──────────────────────────────────────────────────
    const AI_SUGGEST_URL    = "{{ route('admin.ai.suggest') }}";
    const CSRF_TOKEN        = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    // ── Helpers ─────────────────────────────────────────────────
    function el(id) { return document.getElementById(id); }
    function show(id) { 
        const e = el(id);
        if (e) {
            // Check if it's the grid box
            if (id === 'ai-result-box') {
                e.style.display = 'grid';
            } else {
                e.style.display = 'block'; 
            }
        }
    }
    function hide(id) { 
        const e = el(id);
        if (e) e.style.display = 'none'; 
    }
    function setText(id, text) {
        const e = el(id);
        if (e) e.textContent = text;
    }
    function setHtml(id, html) {
        const e = el(id);
        if (e) e.innerHTML = html;
    }

    function showLoading() {
        hide('ai-status-box');
        hide('ai-result-box');
        hide('ai-error-box');
        hide('ai-suggestion-disclaimer');
        show('ai-loading');
    }

    function showError(msg) {
        hide('ai-loading');
        hide('ai-result-box');
        hide('ai-suggestion-disclaimer');
        show('ai-error-box');
        setText('ai-error-box', '⚠️ ' + msg);
    }

    function showResult(data) {
        hide('ai-loading');
        hide('ai-status-box');
        hide('ai-error-box');
        show('ai-result-box');
        show('ai-suggestion-disclaimer');

        const suggestions = data.suggestions || {};
        window.aiSuggestionsData = suggestions;

        setText(
            'ai-pre-note-text',
            suggestions.pre_prescription_note || suggestions.reasoning || 'Cần thầy thuốc kiểm tra thêm trước khi áp dụng.'
        );

        const principles = normalizeList(suggestions.treatment_principles);
        setHtml('ai-principles-text', renderSimpleList(
            principles.length ? principles : ['Cần thầy thuốc kiểm tra thêm trước khi áp dụng.']
        ));

        const suggestedItems = normalizeSuggestedItems(suggestions);
        setHtml('ai-draft-items-text', renderSuggestedItems(suggestedItems));

        const safetyItems = normalizeList(suggestions.safety_and_followup);
        if (suggestions.safety_note) safetyItems.push(suggestions.safety_note);
        if (suggestions.follow_up_suggestion) safetyItems.push(suggestions.follow_up_suggestion);

        setHtml('ai-safety-followup-text', renderSimpleList(uniqueList(safetyItems).length
            ? uniqueList(safetyItems)
            : ['Gợi ý AI chỉ mang tính tham khảo. Thầy thuốc cần kiểm tra, chỉnh sửa và xác nhận trước khi lập đơn.']
        ));
    }

    function normalizeList(value) {
        const values = Array.isArray(value) ? value : (value ? [value] : []);
        return values.map(item => String(item || '').trim()).filter(Boolean);
    }

    function uniqueList(values) {
        const seen = new Set();
        return values.filter(value => {
            const key = String(value || '').trim().toLowerCase();
            if (!key || seen.has(key)) return false;
            seen.add(key);
            return true;
        });
    }

    function renderSimpleList(items) {
        return '<ul style="margin:0; padding-left:1rem;">'
            + items.map(item => `<li style="margin-bottom:0.35rem;">${escHtml(item)}</li>`).join('')
            + '</ul>';
    }

    function normalizeSuggestedItems(suggestions) {
        const items = [];

        const rawSuggestedItems = Array.isArray(suggestions.suggested_items) ? suggestions.suggested_items : [];
        rawSuggestedItems.forEach(item => {
            if (typeof item !== 'object' || !item) return;
            items.push({
                type: item.type || 'herb',
                name: item.name || '',
                role: item.role || '',
                draft_dosage: item.draft_dosage || '',
                unit: item.unit || '',
                safety_note: item.safety_note || '',
                inventory_status: item.inventory_status || 'Không rõ tồn kho',
            });
        });

        (suggestions.oral_herbs || []).forEach(item => {
            items.push({
                type: 'herb',
                name: item.herb_name || '',
                role: item.usage_note || item.role || '',
                draft_dosage: item.draft_dosage || 'Thầy thuốc chỉnh liều',
                unit: item.unit || '',
                safety_note: item.safety_note || 'Cần kiểm tra dị ứng, bệnh nền và thuốc đang dùng.',
                inventory_status: item.inventory_status || 'Không rõ tồn kho',
            });
        });

        (suggestions.external_herbs || []).forEach(item => {
            items.push({
                type: 'external_product',
                name: item.custom_name || '',
                role: item.usage_area || item.role || '',
                draft_dosage: item.draft_dosage || 'Thầy thuốc chỉnh số lượng',
                unit: item.unit || '',
                safety_note: item.usage_instruction || item.safety_note || 'Chỉ dùng ngoài theo hướng dẫn.',
                inventory_status: item.inventory_status || 'Không rõ tồn kho',
            });
        });

        (suggestions.therapy_services || []).forEach(item => {
            items.push({
                type: 'service',
                name: item.custom_name || '',
                role: item.usage_area || item.role || '',
                draft_dosage: item.draft_dosage || '1 lần',
                unit: item.unit || 'lần',
                safety_note: item.usage_instruction || item.safety_note || 'Theo dõi đáp ứng sau trị liệu.',
                inventory_status: item.inventory_status || 'Không rõ tồn kho',
            });
        });

        const seen = new Set();
        return items.filter(item => {
            const key = `${item.type}|${String(item.name || '').toLowerCase()}`;
            if (!item.name || seen.has(key)) return false;
            seen.add(key);
            return true;
        });
    }

    function labelForItemType(type) {
        if (type === 'service') return 'Dịch vụ trị liệu';
        if (type === 'external_product') return 'Dùng ngoài';
        return 'Thuốc uống';
    }

    function renderSuggestedItems(items) {
        if (!items.length) {
            return '<div style="color:#94a3b8;">Không có gợi ý đơn nháp phù hợp với hướng điều trị và dữ liệu kho hiện tại.</div>';
        }

        const groups = {
            herb: items.filter(item => item.type === 'herb'),
            external_product: items.filter(item => item.type === 'external_product'),
            service: items.filter(item => item.type === 'service'),
        };

        return Object.entries(groups)
            .filter(([, groupItems]) => groupItems.length > 0)
            .map(([type, groupItems]) => {
                const body = groupItems.map((item, index) => `
                    <div style="border-top:1px dashed #e2e8f0; padding-top:0.55rem; margin-top:0.55rem;">
                        <div style="font-weight:800; color:#1e293b;">${index + 1}. ${escHtml(item.name)}</div>
                        <div><strong>Vai trò:</strong> ${escHtml(item.role || 'Cần thầy thuốc kiểm tra thêm trước khi áp dụng.')}</div>
                        <div><strong>Liều lượng nháp:</strong> ${escHtml(item.draft_dosage || 'Thầy thuốc chỉnh khi lập đơn')} ${escHtml(item.unit || '')}</div>
                        <div><strong>Lưu ý:</strong> ${escHtml(item.safety_note || 'Cần thầy thuốc kiểm tra thêm trước khi áp dụng.')}</div>
                        <div><strong>Tình trạng kho:</strong> ${escHtml(item.inventory_status || 'Không rõ tồn kho')}</div>
                    </div>
                `).join('');

                return `<div style="margin-bottom:0.75rem;"><strong style="color:#1e3a8a;">${labelForItemType(type)}</strong>${body}</div>`;
            })
            .join('');
    }

    // Escape HTML để tránh XSS
    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // ── Sự kiện: Lấy gợi ý ──────────────────────────────────────
    const btnSuggest = el('btn-ai-suggest');
    if (btnSuggest) {
        btnSuggest.addEventListener('click', async function () {
            const recordId = this.dataset.recordId;
            if (!recordId) return;

            showLoading();
            btnSuggest.disabled = true;
            btnSuggest.style.opacity = '0.7';
            btnSuggest.style.cursor = 'not-allowed';

            try {
                const resp = await fetch(AI_SUGGEST_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ medical_record_id: parseInt(recordId) }),
                });

                const data = await resp.json();

                if (!resp.ok) {
                    showError(data.message || 'Lỗi khi gọi AI. Vui lòng thử lại.');
                    return;
                }

                if (data.status === 'error') {
                    showError(data.message || 'Lỗi từ máy chủ AI.');
                    return;
                }

                if (data.status === 'ai_unavailable') {
                    showError(data.message || 'Dịch vụ AI gợi ý tham khảo tạm thời không khả dụng.');
                    return;
                }

                if (data.status === 'diagnosis_required') {
                    showError(data.message || 'Cần có chẩn đoán chính thức trước khi lấy gợi ý điều trị.');
                    return;
                }

                if (data.status === 'referral') {
                    hide('ai-loading');
                    show('ai-status-box');
                    el('ai-status-box').innerHTML =
                        '<div style="color: #d97706; font-weight: 600;">🔁 ' + escHtml(data.message || 'Chuyển viện – không có gợi ý.') + '</div>';
                    return;
                }

                if (data.status !== 'success') {
                    showError(data.message || 'AI chưa trả về gợi ý hợp lệ. Vui lòng thử lại sau.');
                    return;
                }

                showResult(data);

                // Nếu có button Áp dụng gợi ý ở form thì có thể highlight lên để nhắc người dùng
                const applyBtn = document.getElementById('btn-apply-ai');
                if (applyBtn) {
                    applyBtn.classList.add('pulse-animation');
                    setTimeout(() => applyBtn.classList.remove('pulse-animation'), 5000);
                }

            } catch (err) {
                showError('Lỗi kết nối mạng: ' + err.message);
            } finally {
                btnSuggest.disabled = false;
                btnSuggest.style.opacity = '1';
                btnSuggest.style.cursor = 'pointer';
            }
        });
    }

})();
</script>
