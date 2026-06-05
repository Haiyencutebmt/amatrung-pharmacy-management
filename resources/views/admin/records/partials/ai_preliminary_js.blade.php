<script>
(function () {
    'use strict';

    const ASSESS_URL = "{{ route('admin.ai.preliminary-assessment') }}";
    const QUESTIONS_URL = "{{ route('admin.ai.preliminary-assessment.follow-up-questions') }}";
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    let latestLogId = null;
    let pendingOptionToApply = null;

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

    function setButtonBusy(button, isBusy, busyText) {
        if (!button) return;
        if (!button.dataset.normalText) {
            button.dataset.normalText = button.textContent;
        }

        button.disabled = isBusy;
        button.textContent = isBusy ? busyText : button.dataset.normalText;
        button.style.opacity = isBusy ? '0.7' : '1';
        button.style.cursor = isBusy ? 'not-allowed' : 'pointer';
    }

    function getRecordId() {
        return el('btn-ai-preliminary')?.dataset.recordId
            || el('btn-ai-followup-questions')?.dataset.recordId
            || '0';
    }

    function getAdditionalSymptoms() {
        const panelInput = el('ai-preliminary-additional-symptoms');
        if (panelInput) {
            return panelInput.value || '';
        }

        return el('additional_symptoms_inline')?.value || '';
    }

    function requestBody(recordId) {
        return {
            medical_record_id: parseInt(recordId, 10),
            additional_symptoms: getAdditionalSymptoms().trim(),
        };
    }

    function bindAdditionalSymptomsMirror() {
        const panelInput = el('ai-preliminary-additional-symptoms');
        const recordInput = el('additional_symptoms_inline');

        if (!panelInput || !recordInput) return;

        const mirrorValue = (source, target) => {
            if (target.value !== source.value) {
                target.value = source.value;
            }
        };

        panelInput.addEventListener('input', () => mirrorValue(panelInput, recordInput));
        recordInput.addEventListener('input', () => mirrorValue(recordInput, panelInput));
    }

    function showAssessmentLoading() {
        hide('ai-preliminary-status');
        hide('ai-preliminary-error');
        hide('ai-preliminary-result');
        show('ai-preliminary-loading');
    }

    function showAssessmentError(message) {
        hide('ai-preliminary-loading');
        hide('ai-preliminary-result');
        show('ai-preliminary-error');
        el('ai-preliminary-error').textContent = '⚠️ ' + (message || 'AI chưa thể phản hồi. Vui lòng thử lại sau.');
    }

    function showFollowupLoading() {
        hide('ai-followup-placeholder');
        hide('ai-followup-error');
        hide('ai-followup-questions-list');
        show('ai-followup-loading');
    }

    function showFollowupError(message) {
        hide('ai-followup-loading');
        hide('ai-followup-questions-list');
        show('ai-followup-error');
        el('ai-followup-error').textContent = '⚠️ ' + (message || 'AI chưa thể gợi ý câu hỏi. Vui lòng thử lại sau.');
    }

    function renderFollowupQuestions(questions, missingInformation = []) {
        const box = el('ai-followup-questions-list');
        if (!box) return;

        hide('ai-followup-loading');
        hide('ai-followup-error');

        const safeQuestions = Array.isArray(questions) ? questions.filter(Boolean) : [];
        const safeMissing = Array.isArray(missingInformation) ? missingInformation.filter(Boolean) : [];

        if (safeQuestions.length === 0 && safeMissing.length === 0) {
            box.innerHTML = '<div style="border:1px dashed #cbd5e1; border-radius:0.35rem; padding:0.7rem 0.8rem; background:#f8fafc;">AI chưa đưa ra câu hỏi khai thác thêm.</div>';
            show('ai-followup-questions-list');
            return;
        }

        const sections = [];
        if (safeQuestions.length > 0) {
            sections.push(
                '<strong style="color:#1e3a8a;">Câu hỏi nên hỏi thêm:</strong>'
                + '<ul style="margin:0.45rem 0 0 1rem; padding:0;">'
                + safeQuestions.map(q => `<li>${escHtml(q)}</li>`).join('')
                + '</ul>'
            );
        }

        if (safeMissing.length > 0) {
            sections.push(
                '<div style="margin-top:0.7rem;"><strong style="color:#1e3a8a;">Thông tin còn thiếu:</strong>'
                + '<ul style="margin:0.45rem 0 0 1rem; padding:0;">'
                + safeMissing.map(item => `<li>${escHtml(item)}</li>`).join('')
                + '</ul></div>'
            );
        }

        box.innerHTML = sections.join('');
        show('ai-followup-questions-list');
    }

    function compactText(text, maxLength = 220) {
        const normalized = String(text || '').replace(/\s+/g, ' ').trim();
        if (!normalized) return '';

        return normalized.length > maxLength
            ? normalized.slice(0, maxLength).trim() + '...'
            : normalized;
    }

    function normalizeList(value, maxItems = 3) {
        const values = Array.isArray(value) ? value : (value ? [value] : []);

        return values
            .map(item => compactText(item))
            .filter(Boolean)
            .slice(0, maxItems);
    }

    function formatAssessmentNotes(option) {
        const doctorNotes = normalizeList(option.doctor_notes || option.reasoning || option.explanation, 8);
        const cautionFlags = normalizeList(option.caution_flags || option.red_flags || option.caution, 8);
        const blocks = [];

        if (doctorNotes.length > 0) {
            blocks.push('Lưu ý cho bác sĩ:\n' + doctorNotes.map(note => '- ' + note).join('\n'));
        }

        if (cautionFlags.length > 0) {
            blocks.push('Cảnh báo nếu có:\n' + cautionFlags.map(flag => '- ' + flag).join('\n'));
        }

        return blocks.join('\n\n');
    }

    function setTextareaValue(textarea, value) {
        textarea.value = value;
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
        textarea.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function fillAssessmentIntoRecordForm(option) {
        const diagnosisInput = el('diagnosis_inline');
        const doctorNoteInput = el('doctor_note_inline');

        if (diagnosisInput?.readOnly || doctorNoteInput?.readOnly) {
            alert('Bệnh án đã xác nhận chẩn đoán. Nếu cần chỉnh sửa, vui lòng sử dụng nút Sửa bệnh án này.');
            return;
        }

        if (diagnosisInput) {
            setTextareaValue(diagnosisInput, option.title || '');
        }

        if (doctorNoteInput) {
            const aiNotes = formatAssessmentNotes(option);

            if (aiNotes) {
                const currentValue = doctorNoteInput.value.trim();

                if (!currentValue) {
                    setTextareaValue(doctorNoteInput, aiNotes);
                } else {
                    const shouldAppend = window.confirm(
                        'Ô lưu ý cho bác sĩ đang có nội dung. Bấm OK để nối thêm nội dung từ nhận định AI vào cuối, hoặc Cancel để giữ nguyên nội dung lưu ý hiện tại.'
                    );

                    if (shouldAppend) {
                        const separator = '\n\n--- Nội dung từ nhận định AI ---\n';
                        setTextareaValue(doctorNoteInput, currentValue + separator + aiNotes);
                    }
                }
            }
        }

        if (diagnosisInput) {
            diagnosisInput.focus();
            diagnosisInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function renderOptions(options) {
        const container = el('ai-preliminary-options');
        if (!container) return;

        if (!Array.isArray(options) || options.length === 0) {
            container.innerHTML = '<div style="grid-column:1/-1; text-align:center; color:#64748b; background:#fff; border:1px dashed #cbd5e1; border-radius:0.45rem; padding:1rem;">AI chưa đưa ra hướng nhận định cụ thể.</div>';
            return;
        }

        container.innerHTML = options.map((option, index) => {
            const percent = Math.max(0, Math.min(100, parseInt(option.fit_percent || option.confidence_percent || 0, 10)));
            const title = escHtml(option.title || 'Hướng nhận định');
            const doctorNotes = normalizeList(option.doctor_notes || option.reasoning || option.explanation, 4);
            const cautionFlags = normalizeList(option.caution_flags || option.red_flags || option.caution, 4);
            const notes = doctorNotes.length
                ? doctorNotes
                : ['Cần tiếp tục đối chiếu triệu chứng, bệnh nền, dị ứng, thuốc đang dùng và thể trạng trước khi quyết định điều trị.'];
            const notesHtml = '<ul style="margin:0.45rem 0 0 1rem; padding:0;">'
                + notes.map(note => `<li>${escHtml(note)}</li>`).join('')
                + '</ul>';
            const flags = cautionFlags.length
                ? `<div style="margin-top:0.7rem; color:#b91c1c; font-size:0.8rem; line-height:1.45;"><strong>Cảnh báo nếu có:</strong><ul style="margin:0.4rem 0 0 1rem; padding:0;">${cautionFlags.map(flag => `<li>${escHtml(flag)}</li>`).join('')}</ul></div>`
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
                    <div style="color:#475569; font-size:0.82rem; line-height:1.5;">
                        <strong style="color:#1e3a8a;">Lưu ý cho bác sĩ khi kê đơn/thăm khám:</strong>
                        ${notesHtml}
                    </div>
                    ${flags}
                    <div style="display:flex; gap:0.5rem; flex-wrap:wrap; margin-top:0.75rem;">
                        <button type="button" class="btn-apply-ai-diagnosis" data-index="${index}" style="border:none; background:#16a34a; color:#fff; border-radius:0.3rem; padding:0.45rem 0.7rem; font-size:0.78rem; font-weight:750; cursor:pointer;">Áp dụng nhận định tham khảo</button>
                    </div>
                </div>
            `;
        }).join('');

        container.querySelectorAll('.btn-apply-ai-diagnosis').forEach(btn => {
            btn.addEventListener('click', () => applyDiagnosis(options[parseInt(btn.dataset.index, 10)]));
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

        if (Array.isArray(suggestions.follow_up_questions) && suggestions.follow_up_questions.length > 0) {
            renderFollowupQuestions(suggestions.follow_up_questions);
        }

        renderOptions(suggestions.assessment_options || []);
    }

    function showApplyConfirmModal(option) {
        pendingOptionToApply = option;
        const modal = el('ai-confirm-apply-modal');
        const badge = el('ai-confirm-diagnosis-badge');
        if (modal && badge) {
            badge.textContent = option.title;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeApplyConfirmModal() {
        pendingOptionToApply = null;
        const modal = el('ai-confirm-apply-modal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    const btnCancelApply = el('btn-cancel-apply-ai');
    if (btnCancelApply) {
        btnCancelApply.addEventListener('click', closeApplyConfirmModal);
    }

    const btnConfirmApply = el('btn-confirm-apply-ai');
    if (btnConfirmApply) {
        btnConfirmApply.addEventListener('click', function () {
            if (!pendingOptionToApply) return;

            fillAssessmentIntoRecordForm(pendingOptionToApply);
            closeApplyConfirmModal();
        });
    }

    const applyModalEl = el('ai-confirm-apply-modal');
    if (applyModalEl) {
        applyModalEl.addEventListener('click', function(e) {
            if (e.target === applyModalEl) {
                closeApplyConfirmModal();
            }
        });
    }

    async function applyDiagnosis(option) {
        if (!option || !option.title) return;
        showApplyConfirmModal(option);
    }

    bindAdditionalSymptomsMirror();

    const followupBtn = el('btn-ai-followup-questions');
    if (followupBtn) {
        followupBtn.addEventListener('click', async function () {
            const recordId = this.dataset.recordId || getRecordId();
            if (!recordId) return;

            showFollowupLoading();
            setButtonBusy(this, true, 'Đang gợi ý...');

            try {
                const resp = await fetch(QUESTIONS_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(requestBody(recordId)),
                });

                const data = await resp.json();

                if (!resp.ok || data.status === 'error') {
                    showFollowupError(data.message || 'Không thể gọi AI gợi ý câu hỏi.');
                    return;
                }

                if (data.status === 'ai_unavailable') {
                    showFollowupError(data.message || 'Dịch vụ AI gợi ý câu hỏi tạm thời không khả dụng.');
                    return;
                }

                if (data.status !== 'success') {
                    showFollowupError(data.message || 'AI chưa trả về danh sách câu hỏi hợp lệ.');
                    return;
                }

                const suggestions = data.suggestions || {};
                renderFollowupQuestions(suggestions.follow_up_questions || [], suggestions.missing_information || []);
            } catch (err) {
                showFollowupError('Lỗi kết nối mạng: ' + err.message);
            } finally {
                setButtonBusy(this, false);
            }
        });
    }

    const btn = el('btn-ai-preliminary');
    if (btn) {
        btn.addEventListener('click', async function () {
            const recordId = this.dataset.recordId || getRecordId();
            if (!recordId) return;

            showAssessmentLoading();
            setButtonBusy(this, true, 'Đang phân tích...');

            try {
                const resp = await fetch(ASSESS_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(requestBody(recordId)),
                });

                const data = await resp.json();

                if (!resp.ok || data.status === 'error') {
                    showAssessmentError(data.message || 'Không thể gọi AI nhận định sơ bộ.');
                    return;
                }

                if (data.status === 'ai_unavailable') {
                    showAssessmentError(data.message || 'Dịch vụ AI nhận định sơ bộ tạm thời không khả dụng.');
                    return;
                }

                if (data.status !== 'success') {
                    showAssessmentError(data.message || 'AI chưa trả về nhận định hợp lệ.');
                    return;
                }

                renderResult(data);
            } catch (err) {
                showAssessmentError('Lỗi kết nối mạng: ' + err.message);
            } finally {
                setButtonBusy(this, false);
            }
        });
    }
})();
</script>
