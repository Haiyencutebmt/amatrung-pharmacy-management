{{--
    ai_js.blade.php
    JavaScript logic cho panel "Gợi ý AI hỗ trợ thầy thuốc".
--}}
<script>
window.aiSuggestionsData = null; // Biến toàn cục để chức năng "Áp dụng gợi ý AI" có thể đọc

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
        show('ai-loading');
    }

    function showError(msg) {
        hide('ai-loading');
        hide('ai-result-box');
        show('ai-error-box');
        setText('ai-error-box', '⚠️ ' + msg);
    }

    function showResult(data) {
        hide('ai-loading');
        hide('ai-status-box');
        hide('ai-error-box');
        show('ai-result-box');

        const suggestions = data.suggestions || {};
        window.aiSuggestionsData = suggestions;

        // Cột 1: Reasoning
        if (suggestions.reasoning) {
            el('ai-reasoning-text').textContent = suggestions.reasoning;
        } else {
            el('ai-reasoning-text').textContent = 'Không có ghi nhận.';
        }

        // Cột 2: Dược liệu và Dịch vụ (Gộp chung hiển thị)
        let herbsHtml = '';
        const oralHerbs = suggestions.oral_herbs || [];
        if (oralHerbs.length > 0) {
            herbsHtml += `<strong>Thuốc uống:</strong><br>`;
            herbsHtml += oralHerbs.map(h => `- ${escHtml(h.herb_name)}`).join('<br>');
        }
        
        const extHerbs = suggestions.external_herbs || [];
        if (extHerbs.length > 0) {
            if (herbsHtml !== '') herbsHtml += '<br><br>';
            herbsHtml += `<strong>Dùng ngoài:</strong><br>`;
            herbsHtml += extHerbs.map(h => `- ${escHtml(h.custom_name)}`).join('<br>');
        }

        const therapies = suggestions.therapy_services || [];
        if (therapies.length > 0) {
            if (herbsHtml !== '') herbsHtml += '<br><br>';
            herbsHtml += `<strong>Dịch vụ trị liệu:</strong><br>`;
            herbsHtml += therapies.map(h => `- ${escHtml(h.custom_name)}`).join('<br>');
        }

        if (herbsHtml === '') {
            herbsHtml = 'Không có gợi ý cụ thể.';
        }
        setHtml('ai-herbs-text', herbsHtml);

        // Cột 3: Follow-up & Safety note
        let followUpHtml = '';
        if (suggestions.safety_note) {
            followUpHtml += `<strong style="color: #dc2626;">Lưu ý an toàn:</strong><br>${escHtml(suggestions.safety_note)}<br><br>`;
        }
        if (suggestions.follow_up_suggestion) {
            followUpHtml += `${escHtml(suggestions.follow_up_suggestion)}`;
        }
        if (followUpHtml === '') followUpHtml = 'Không có ghi nhận.';
        
        setHtml('ai-followup-text', followUpHtml);
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
