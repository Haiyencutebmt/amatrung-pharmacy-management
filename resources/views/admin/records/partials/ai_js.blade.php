{{--
    ai_js.blade.php
    JavaScript logic cho panel "Gợi ý AI hỗ trợ thầy thuốc".

    NGUYÊN TẮC AN TOÀN:
     - Frontend CHỈ gửi medical_record_id.
     - KHÔNG gửi symptoms, diagnosis, patient data.
     - KHÔNG tự điền vào form kê đơn.
     - KHÔNG tự lưu đơn thuốc.
     - Nút tương tác chỉ ghi log (log-status), không apply vào form.
--}}
<script>
(function () {
    'use strict';

    // ── Config ──────────────────────────────────────────────────
    const AI_SUGGEST_URL    = "{{ route('ai.suggest') }}";
    const AI_LOG_STATUS_URL = "{{ route('ai.suggest.log-status') }}";
    const CSRF_TOKEN        = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    let currentLogId = null;

    // ── Helpers ─────────────────────────────────────────────────
    function el(id) { return document.getElementById(id); }
    function show(id) { el(id)?.classList.remove('d-none'); }
    function hide(id) { el(id)?.classList.add('d-none'); }
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

        // Reasoning
        if (suggestions.reasoning) {
            el('ai-reasoning-text').textContent = suggestions.reasoning;
            show('ai-reasoning-section');
        } else {
            hide('ai-reasoning-section');
        }

        // Oral herbs
        const oralHerbs = suggestions.oral_herbs || [];
        if (oralHerbs.length > 0) {
            const rows = oralHerbs.map(h => `
                <div class="d-flex align-items-start gap-2 mb-1 p-2 rounded" style="background:#f8f9fa;font-size:.83rem;">
                    <span>🌿</span>
                    <div>
                        <strong>${escHtml(h.herb_name)}</strong>
                        ${h.usage_note ? `<br><small class="text-muted">${escHtml(h.usage_note)}</small>` : ''}
                    </div>
                </div>`).join('');
            setHtml('ai-oral-list', rows);
            show('ai-oral-section');
        } else {
            hide('ai-oral-section');
        }

        // External herbs
        const extHerbs = suggestions.external_herbs || [];
        if (extHerbs.length > 0) {
            const rows = extHerbs.map(h => `
                <div class="d-flex align-items-start gap-2 mb-1 p-2 rounded" style="background:#fefefe;font-size:.83rem;border:1px solid #eee;">
                    <span>🧴</span>
                    <div>
                        <strong>${escHtml(h.custom_name)}</strong>
                        ${h.usage_area ? `<span class="badge bg-secondary ms-1" style="font-size:.65rem;">${escHtml(h.usage_area)}</span>` : ''}
                        ${h.usage_instruction ? `<br><small class="text-muted">${escHtml(h.usage_instruction)}</small>` : ''}
                    </div>
                </div>`).join('');
            setHtml('ai-external-list', rows);
            show('ai-external-section');
        } else {
            hide('ai-external-section');
        }

        // Therapy services
        const therapies = suggestions.therapy_services || [];
        if (therapies.length > 0) {
            const rows = therapies.map(s => `
                <div class="d-flex align-items-start gap-2 mb-1 p-2 rounded" style="background:#fefefe;font-size:.83rem;border:1px solid #eee;">
                    <span>🏥</span>
                    <div>
                        <strong>${escHtml(s.custom_name)}</strong>
                        ${s.usage_area ? `<span class="badge bg-info ms-1" style="font-size:.65rem;">${escHtml(s.usage_area)}</span>` : ''}
                        ${s.usage_instruction ? `<br><small class="text-muted">${escHtml(s.usage_instruction)}</small>` : ''}
                    </div>
                </div>`).join('');
            setHtml('ai-therapy-list', rows);
            show('ai-therapy-section');
        } else {
            hide('ai-therapy-section');
        }

        // Safety note
        if (suggestions.safety_note) {
            el('ai-safety-text').innerHTML = '⚠️ ' + escHtml(suggestions.safety_note);
            show('ai-safety-section');
        } else {
            hide('ai-safety-section');
        }

        // Follow-up
        if (suggestions.follow_up_suggestion) {
            el('ai-followup-text').textContent = suggestions.follow_up_suggestion;
            show('ai-followup-section');
        } else {
            hide('ai-followup-section');
        }

        // Interaction bar (chỉ hiện nếu có ít nhất 1 gợi ý)
        const hasSuggestions = oralHerbs.length > 0 || extHerbs.length > 0 || therapies.length > 0;
        if (hasSuggestions && currentLogId) {
            show('ai-interaction-bar');
        } else {
            hide('ai-interaction-bar');
        }
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

            currentLogId = null;
            showLoading();
            btnSuggest.disabled = true;

            try {
                const resp = await fetch(AI_SUGGEST_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    // CHỈ gửi medical_record_id, KHÔNG gửi patient data
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
                    showError(data.message || 'Dịch vụ AI tạm thời không khả dụng.');
                    return;
                }

                if (data.status === 'referral') {
                    hide('ai-loading');
                    show('ai-status-box');
                    el('ai-status-box').innerHTML =
                        '<span class="text-warning">🔁 ' + escHtml(data.message || 'Chuyển viện – không có gợi ý.') + '</span>';
                    return;
                }

                currentLogId = data.log_id || null;
                showResult(data);

            } catch (err) {
                showError('Lỗi kết nối mạng: ' + err.message);
            } finally {
                btnSuggest.disabled = false;
            }
        });
    }

    // ── Sự kiện: Ghi nhận tương tác ─────────────────────────────
    // Không tự apply gợi ý vào form – chỉ ghi log
    document.querySelectorAll('.btn-ai-interact').forEach(btn => {
        btn.addEventListener('click', async function () {
            if (!currentLogId) return;

            const status = this.dataset.status;
            document.querySelectorAll('.btn-ai-interact').forEach(b => b.disabled = true);

            try {
                const resp = await fetch(AI_LOG_STATUS_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        log_id: currentLogId,
                        interaction_status: status,
                    }),
                });

                const data = await resp.json();
                const feedbackEl = el('ai-interact-feedback');

                if (resp.ok && data.status === 'success') {
                    feedbackEl.innerHTML = '<span class="text-success">✅ Đã ghi nhận. Cảm ơn thầy thuốc đã phản hồi.</span>';
                } else {
                    feedbackEl.innerHTML = '<span class="text-danger">⚠️ Không thể ghi nhận: ' + escHtml(data.message || '') + '</span>';
                    // Re-enable buttons if failed
                    document.querySelectorAll('.btn-ai-interact').forEach(b => b.disabled = false);
                }
                show('ai-interact-feedback');

            } catch (err) {
                el('ai-interact-feedback').innerHTML =
                    '<span class="text-danger">⚠️ Lỗi mạng khi ghi nhận: ' + escHtml(err.message) + '</span>';
                show('ai-interact-feedback');
                document.querySelectorAll('.btn-ai-interact').forEach(b => b.disabled = false);
            }
        });
    });

})();
</script>
