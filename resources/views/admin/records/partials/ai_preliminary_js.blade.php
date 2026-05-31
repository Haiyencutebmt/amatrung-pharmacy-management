<script>
(function () {
    'use strict';

    const ASSESS_URL = "{{ route('admin.ai.preliminary-assessment') }}";
    const APPLY_URL = "{{ route('admin.ai.preliminary-assessment.apply-diagnosis') }}";
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    let latestLogId = null;

    function el(id) { return document.getElementById(id); }
    function show(id, display = 'block') { const node = el(id); if (node) node.style.display = display; }
    function hide(id) { const node = el(id); if (node) node.style.display = 'none'; }
    function escHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function setBusy(isBusy) {
        const btn = el('btn-ai-preliminary');
        if (!btn) return;
        btn.disabled = isBusy;
        btn.style.opacity = isBusy ? '0.7' : '1';
        btn.style.cursor = isBusy ? 'not-allowed' : 'pointer';
    }

    function showLoading() {
        hide('ai-preliminary-status');
        hide('ai-preliminary-error');
        hide('ai-preliminary-result');
        show('ai-preliminary-loading');
    }

    function showError(message) {
        hide('ai-preliminary-loading');
        hide('ai-preliminary-result');
        show('ai-preliminary-error');
        el('ai-preliminary-error').textContent = '⚠️ ' + (message || 'AI chưa thể phản hồi. Vui lòng thử lại sau.');
    }

    function renderQuestions(questions) {
        const box = el('ai-preliminary-questions');
        if (!box) return;

        if (!Array.isArray(questions) || questions.length === 0) {
            hide('ai-preliminary-questions');
            box.innerHTML = '';
            return;
        }

        box.innerHTML = '<strong style="color:#1e3a8a;">Câu hỏi/thông tin nên khai thác thêm:</strong><ul style="margin:0.45rem 0 0 1rem; padding:0;">'
            + questions.map(q => `<li>${escHtml(q)}</li>`).join('')
            + '</ul>';
        show('ai-preliminary-questions');
    }

    function renderOptions(options) {
        const container = el('ai-preliminary-options');
        if (!container) return;

        if (!Array.isArray(options) || options.length === 0) {
            container.innerHTML = '<div style="grid-column:1/-1; text-align:center; color:#64748b; background:#fff; border:1px dashed #cbd5e1; border-radius:0.45rem; padding:1rem;">AI chưa đưa ra hướng nhận định cụ thể.</div>';
            return;
        }

        container.innerHTML = options.map((option, index) => {
            const percent = Math.max(0, Math.min(100, parseInt(option.fit_percent || 0, 10)));
            const title = escHtml(option.title || 'Hướng nhận định');
            const reasoning = escHtml(option.reasoning || 'Chưa có lý do cụ thể.');
            const advice = escHtml(option.advice_draft || '');
            const flags = Array.isArray(option.red_flags) && option.red_flags.length
                ? `<div style="margin-top:0.65rem; color:#b91c1c; font-size:0.78rem; line-height:1.45;"><strong>Cảnh báo cần lưu ý:</strong><br>${option.red_flags.map(escHtml).join('<br>')}</div>`
                : '';

            return `
                <div class="ai-assessment-card" style="background:#fff; border:1px solid #dbeafe; border-radius:0.5rem; padding:0.95rem; box-shadow:0 1px 2px rgba(15,23,42,0.04);">
                    <div style="display:flex; justify-content:space-between; gap:0.75rem; align-items:flex-start; margin-bottom:0.7rem;">
                        <div style="font-weight:850; color:#0f172a; font-size:0.92rem; line-height:1.35;">${title}</div>
                        <div style="background:#eff6ff; border:1px solid #bfdbfe; color:#1d4ed8; border-radius:999px; padding:0.2rem 0.5rem; font-weight:850; font-size:0.78rem; flex-shrink:0;">${percent}%</div>
                    </div>
                    <div style="height:8px; background:#e2e8f0; border-radius:999px; overflow:hidden; margin-bottom:0.75rem;">
                        <div style="width:${percent}%; height:100%; background:#2563eb; border-radius:999px;"></div>
                    </div>
                    <div style="color:#475569; font-size:0.82rem; line-height:1.5;">${reasoning}</div>
                    ${flags}
                    ${advice ? `<div style="margin-top:0.7rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:0.35rem; padding:0.65rem; color:#334155; font-size:0.8rem; line-height:1.45;"><strong>Lời dặn tham khảo:</strong><br>${advice}</div>` : ''}
                    <div style="display:flex; gap:0.5rem; flex-wrap:wrap; margin-top:0.75rem;">
                        <button type="button" class="btn-apply-ai-diagnosis" data-index="${index}" style="border:none; background:#16a34a; color:#fff; border-radius:0.3rem; padding:0.45rem 0.7rem; font-size:0.78rem; font-weight:750; cursor:pointer;">Áp dụng chẩn đoán</button>
                        ${advice ? `<button type="button" class="btn-copy-ai-advice" data-index="${index}" style="border:1px solid #bfdbfe; background:#eff6ff; color:#1d4ed8; border-radius:0.3rem; padding:0.45rem 0.7rem; font-size:0.78rem; font-weight:750; cursor:pointer;">Sao chép lời dặn</button>` : ''}
                    </div>
                </div>
            `;
        }).join('');

        container.querySelectorAll('.btn-apply-ai-diagnosis').forEach(btn => {
            btn.addEventListener('click', () => applyDiagnosis(options[parseInt(btn.dataset.index, 10)]));
        });

        container.querySelectorAll('.btn-copy-ai-advice').forEach(btn => {
            btn.addEventListener('click', () => {
                const option = options[parseInt(btn.dataset.index, 10)] || {};
                copyText(option.advice_draft || '', btn);
            });
        });
    }

    function renderResult(data) {
        hide('ai-preliminary-loading');
        hide('ai-preliminary-status');
        hide('ai-preliminary-error');
        show('ai-preliminary-result');

        latestLogId = data.log_id || null;
        const suggestions = data.suggestions || {};

        el('ai-preliminary-summary').innerHTML = suggestions.clinical_summary
            ? `<strong style="color:#1e3a8a;">Tóm tắt nhận định:</strong> ${escHtml(suggestions.clinical_summary)}`
            : '<strong style="color:#1e3a8a;">Tóm tắt nhận định:</strong> Chưa có tóm tắt.';

        const warningBox = el('ai-preliminary-warning');
        if (suggestions.urgent_warning) {
            warningBox.textContent = 'Cảnh báo: ' + suggestions.urgent_warning;
            show('ai-preliminary-warning');
        } else {
            hide('ai-preliminary-warning');
        }

        renderOptions(suggestions.assessment_options || []);
        renderQuestions(suggestions.follow_up_questions || []);
    }

    async function applyDiagnosis(option) {
        if (!option || !option.title) return;

        const confirmed = window.confirm('Áp dụng nhận định này làm chẩn đoán cho bệnh án? Thầy thuốc vẫn cần tự rà soát và chịu trách nhiệm chuyên môn.');
        if (!confirmed) return;

        try {
            const resp = await fetch(APPLY_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    medical_record_id: parseInt(el('btn-ai-preliminary')?.dataset.recordId || '0', 10),
                    diagnosis: option.title,
                    log_id: latestLogId,
                }),
            });

            const data = await resp.json();
            if (!resp.ok || data.status !== 'success') {
                alert(data.message || 'Không thể áp dụng chẩn đoán.');
                return;
            }

            window.location.reload();
        } catch (err) {
            alert('Lỗi kết nối khi áp dụng chẩn đoán: ' + err.message);
        }
    }

    async function copyText(text, button) {
        if (!text) return;

        try {
            await navigator.clipboard.writeText(text);
        } catch (err) {
            const temp = document.createElement('textarea');
            temp.value = text;
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);
        }

        const oldText = button.textContent;
        button.textContent = 'Đã sao chép';
        setTimeout(() => button.textContent = oldText, 1400);
    }

    const btn = el('btn-ai-preliminary');
    if (btn) {
        btn.addEventListener('click', async function () {
            const recordId = this.dataset.recordId;
            if (!recordId) return;

            showLoading();
            setBusy(true);

            try {
                const resp = await fetch(ASSESS_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ medical_record_id: parseInt(recordId, 10) }),
                });

                const data = await resp.json();

                if (!resp.ok || data.status === 'error') {
                    showError(data.message || 'Không thể gọi AI nhận định sơ bộ.');
                    return;
                }

                if (data.status === 'ai_unavailable') {
                    showError(data.message || 'Dịch vụ AI nhận định sơ bộ tạm thời không khả dụng.');
                    return;
                }

                if (data.status !== 'success') {
                    showError(data.message || 'AI chưa trả về nhận định hợp lệ.');
                    return;
                }

                renderResult(data);
            } catch (err) {
                showError('Lỗi kết nối mạng: ' + err.message);
            } finally {
                setBusy(false);
            }
        });
    }
})();
</script>
