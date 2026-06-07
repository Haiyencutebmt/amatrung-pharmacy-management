@extends('layouts.admin')

@section('title', 'Soạn Bài Viết Mới — AmaTrung')
@section('page-title', '')

@section('header-left')
    <div class="article-compose-header" style="margin-bottom: 0;">
        <div class="article-compose-mark" style="width: 48px; height: 48px;">
            <svg width="32" height="32" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                <path d="M24 42V16" stroke="#22c55e" stroke-width="3" stroke-linecap="round"/>
                <path d="M24 24C14 22 10 15 9 8c8 0 14 4 15 16Z" fill="#dcfce7" stroke="#22c55e" stroke-width="2"/>
                <path d="M25 28c10-1 15-7 16-15-8 0-14 4-16 15Z" fill="#eff6ff" stroke="#3b82f6" stroke-width="2"/>
                <path d="M24 36c-8-1-12-6-13-12 7 0 12 4 13 12Z" fill="#ecfeff" stroke="#06b6d4" stroke-width="2"/>
            </svg>
        </div>
        <div>
            <h1 style="font-size: 1.5rem; margin: 0; font-weight: 900; line-height: 1.1;">Soạn bài viết mới</h1>
            <p style="margin: 0.2rem 0 0; font-size: 0.85rem; color: #64748b; font-weight: 600;">Chia sẻ kiến thức y học cổ truyền bổ ích đến với cộng đồng bệnh nhân.</p>
        </div>
    </div>
@endsection

@section('content')
<div class="article-compose-page" style="padding-top: 0;">

    @if($errors->any())
        <div class="article-form-alert">
            @foreach($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" id="articleForm">
        @csrf
        <div class="article-compose-grid">
            <section class="article-editor-card">
                <div class="article-field">
                    <label for="title">Tiêu đề bài viết <span>*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required maxlength="120" placeholder="Nhập tiêu đề bài viết...">
                    <div class="article-counter"><span id="titleCount">0</span>/120</div>
                </div>

                <div class="article-field">
                    <label for="summary">Tóm tắt ngắn</label>
                    <textarea name="summary" id="summary" maxlength="200" rows="4" placeholder="Nhập tóm tắt ngắn gọn về nội dung bài viết (không bắt buộc)...">{{ old('summary') }}</textarea>
                    <div class="article-counter"><span id="summaryCount">0</span>/200</div>
                </div>

                <div class="article-field">
                    <label for="content">Nội dung bài viết <span>*</span></label>
                    <div class="article-editor-shell">
                        <textarea name="content" id="content" rows="18" placeholder="Bắt đầu viết nội dung bài viết tại đây...">{{ old('content') }}</textarea>
                    </div>
                    <div class="article-word-count"><span id="wordCount">0</span> từ</div>
                </div>

                <p class="article-tip">
                    <span>ⓘ</span> Mẹo: Bạn có thể kéo thả hình ảnh, bảng biểu hoặc định dạng văn bản theo ý muốn.
                </p>
            </section>

            <aside class="article-side-panel">
                <section class="article-side-card">
                    <div class="article-card-title">
                        <span class="article-card-icon">↗</span>
                        <h2>Xuất bản</h2>
                    </div>

                    <label class="article-toggle-row" for="is_published">
                        <span>
                            <strong>Trạng thái hiển thị</strong>
                            <small>Khi bật, bài viết sẽ được hiển thị ngay trên trang chủ và kho thư viện y khoa.</small>
                        </span>
                        <input type="checkbox" name="is_published" id="is_published" value="1" checked>
                    </label>

                    <div class="article-status-row">
                        <span>Trạng thái hiện tại</span>
                        <strong id="publishStatusLabel">Sẵn sàng xuất bản</strong>
                    </div>

                    <div class="article-publish-actions">
                        <button type="submit" class="article-btn secondary" id="saveDraftButton">▣ Lưu nháp</button>
                        <button type="submit" class="article-btn primary" id="publishButton">Đăng bài</button>
                    </div>
                </section>

                <section class="article-side-card">
                    <div class="article-card-title">
                        <span class="article-card-icon">▧</span>
                        <h2>Hình ảnh minh họa</h2>
                    </div>

                    <div class="article-upload-zone" id="uploadZone">
                        <div id="image-preview-placeholder">
                            <svg width="64" height="64" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                                <rect x="10" y="12" width="44" height="40" rx="8" fill="#eff6ff"/>
                                <path d="M18 44l10-12 8 8 6-7 8 11H18Z" fill="#93c5fd"/>
                                <circle cx="43" cy="24" r="5" fill="#bfdbfe"/>
                            </svg>
                            <strong>Kéo & thả ảnh vào đây</strong>
                            <span>hoặc</span>
                            <button type="button" class="article-upload-button" onclick="document.getElementById('featured_image').click()">Chọn ảnh từ máy tính</button>
                        </div>
                        <div id="image-preview-container" hidden>
                            <img id="image-preview" src="#" alt="Ảnh minh họa">
                            <button type="button" onclick="removePreviewImage()">Xóa ảnh</button>
                        </div>
                    </div>
                    <input type="file" name="featured_image" id="featured_image" hidden accept="image/*" onchange="previewFile(this)">
                    <p class="article-upload-note">Định dạng: JPG, PNG, WEBP. Dung lượng tối đa 5MB.</p>
                </section>

                <section class="article-side-card">
                    <div class="article-card-title">
                        <span class="article-card-icon">□</span>
                        <h2>Danh mục & Thẻ</h2>
                    </div>

                    <div class="article-field compact">
                        <label for="category">Danh mục <span>*</span></label>
                        <select name="category" id="category" required>
                            <option value="">Chọn danh mục phù hợp...</option>
                            @foreach($articleCategories as $value => $label)
                                <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="article-field compact">
                        <label for="tagInput">Thẻ (tag)</label>
                        <input type="hidden" name="tags" id="tags" value="{{ old('tags') }}">
                        <input type="text" id="tagInput" placeholder="Nhập thẻ và nhấn Enter...">
                        <div class="article-tags" id="tagList"></div>
                        <small>Thêm các thẻ để giúp bài viết dễ tìm kiếm hơn.</small>
                    </div>
                </section>
            </aside>
        </div>
    </form>
</div>

<script src="https://cdn.tiny.cloud/1/s72x9nbmbstwud1bmsiwiirfj0zr4xfgqdddah0ip2j4exjk/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<style>
.article-compose-page {
    color: #1e293b;
    padding: 0.25rem 0 3rem;
}

.article-compose-header {
    align-items: center;
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.article-compose-mark {
    align-items: center;
    background: #f8fafc;
    border-radius: 1rem;
    display: flex;
    height: 64px;
    justify-content: center;
    width: 64px;
}

.article-compose-header h1 {
    font-size: 2rem;
    font-weight: 900;
    letter-spacing: 0;
    line-height: 1.1;
    margin: 0;
}

.article-compose-header p {
    color: #64748b;
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0.45rem 0 0;
}

.article-form-alert {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 0.75rem;
    color: #b91c1c;
    font-weight: 700;
    margin-bottom: 1rem;
    padding: 0.9rem 1rem;
}

.article-compose-grid {
    display: grid;
    gap: 1.6rem;
    grid-template-columns: minmax(0, 2fr) minmax(340px, 0.95fr);
}

.article-editor-card,
.article-side-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.04);
}

.article-editor-card {
    padding: 1.45rem;
}

.article-side-panel {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.article-side-card {
    padding: 1.1rem;
}

.article-field {
    margin-bottom: 1.35rem;
}

.article-field.compact {
    margin-bottom: 1rem;
}

.article-field label {
    color: #334155;
    display: block;
    font-size: 0.9rem;
    font-weight: 850;
    margin-bottom: 0.55rem;
}

.article-field label span {
    color: #ef4444;
}

.article-field input,
.article-field textarea,
.article-field select {
    background: #fff;
    border: 1px solid #cbd5e1;
    border-radius: 0.7rem;
    color: #1e293b;
    font-size: 0.95rem;
    outline: none;
    padding: 0.85rem 1rem;
    width: 100%;
}

.article-field textarea {
    resize: vertical;
}

.article-field input:focus,
.article-field textarea:focus,
.article-field select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px #dbeafe;
}

.article-counter,
.article-word-count {
    color: #94a3b8;
    font-size: 0.78rem;
    font-weight: 700;
    margin-top: 0.35rem;
    text-align: right;
}

.article-editor-shell {
    border: 1px solid #cbd5e1;
    border-radius: 0.85rem;
    overflow: hidden;
}

.article-tip {
    align-items: center;
    color: #64748b;
    display: flex;
    font-size: 0.8rem;
    font-weight: 600;
    gap: 0.4rem;
    margin: 1rem 0 0;
}

.article-tip span {
    color: #3b82f6;
}

.article-card-title {
    align-items: center;
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.article-card-title h2 {
    font-size: 1rem;
    font-weight: 850;
    margin: 0;
}

.article-card-icon {
    align-items: center;
    background: #eff6ff;
    border-radius: 0.65rem;
    color: #2563eb;
    display: inline-flex;
    font-weight: 900;
    height: 34px;
    justify-content: center;
    width: 34px;
}

.article-toggle-row {
    align-items: center;
    display: flex;
    justify-content: space-between;
    gap: 1rem;
}

.article-toggle-row strong,
.article-status-row span {
    color: #334155;
    display: block;
    font-size: 0.85rem;
    font-weight: 850;
}

.article-toggle-row small {
    color: #64748b;
    display: block;
    font-size: 0.78rem;
    line-height: 1.45;
    margin-top: 0.4rem;
}

.article-toggle-row input {
    accent-color: #2563eb;
    height: 22px;
    width: 42px;
}

.article-status-row {
    margin-top: 1rem;
}

.article-status-row strong {
    background: #dcfce7;
    border-radius: 999px;
    color: #16a34a;
    display: inline-flex;
    font-size: 0.78rem;
    font-weight: 850;
    margin-top: 0.45rem;
    padding: 0.35rem 0.65rem;
}

.article-publish-actions {
    display: grid;
    gap: 0.75rem;
    grid-template-columns: 1fr 1fr;
    margin-top: 1.2rem;
}

.article-btn {
    border-radius: 0.65rem;
    cursor: pointer;
    font-weight: 850;
    padding: 0.8rem 1rem;
}

.article-btn.primary {
    background: #2563eb;
    border: 1px solid #2563eb;
    color: #fff;
}

.article-btn.secondary {
    background: #fff;
    border: 1px solid #93c5fd;
    color: #2563eb;
}

.article-upload-zone {
    align-items: center;
    border: 1.5px dashed #bfdbfe;
    border-radius: 0.85rem;
    display: flex;
    justify-content: center;
    min-height: 170px;
    overflow: hidden;
    padding: 1rem;
    text-align: center;
}

.article-upload-zone.is-dragover {
    background: #eff6ff;
    border-color: #2563eb;
}

#image-preview-placeholder {
    align-items: center;
    color: #64748b;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

#image-preview-placeholder strong {
    color: #475569;
    font-size: 0.9rem;
}

.article-upload-button {
    background: #fff;
    border: 1px solid #bfdbfe;
    border-radius: 0.45rem;
    color: #2563eb;
    cursor: pointer;
    font-size: 0.8rem;
    font-weight: 850;
    margin-top: 0.35rem;
    padding: 0.45rem 0.75rem;
}

#image-preview-container {
    position: relative;
    width: 100%;
}

#image-preview-container img {
    border-radius: 0.7rem;
    display: block;
    height: 170px;
    object-fit: cover;
    width: 100%;
}

#image-preview-container button {
    background: #ef4444;
    border: 0;
    border-radius: 0.45rem;
    bottom: 0.75rem;
    color: #fff;
    cursor: pointer;
    font-size: 0.78rem;
    font-weight: 850;
    padding: 0.45rem 0.75rem;
    position: absolute;
    right: 0.75rem;
}

.article-upload-note,
.article-field small {
    color: #64748b;
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 0.55rem;
}

.article-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    margin-top: 0.65rem;
}

.article-tags span {
    align-items: center;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 999px;
    color: #1d4ed8;
    display: inline-flex;
    font-size: 0.78rem;
    font-weight: 800;
    gap: 0.35rem;
    padding: 0.35rem 0.6rem;
}

.article-tags button {
    background: transparent;
    border: 0;
    color: #1d4ed8;
    cursor: pointer;
    font-weight: 900;
    padding: 0;
}

.tox-notifications-container,
.tox-promotion {
    display: none !important;
}

.tox-tinymce {
    border: 0 !important;
    border-radius: 0 !important;
}

@media (max-width: 1100px) {
    .article-compose-grid {
        grid-template-columns: 1fr;
    }
}
</style>
<script type="application/json" id="initial-tags-data">{!! json_encode(old('tags', ''), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
<script>
const initialTagsElement = document.getElementById('initial-tags-data');
const initialTags = initialTagsElement ? JSON.parse(initialTagsElement.textContent || '""') : '';
let articleTags = initialTags ? initialTags.split(',').map(tag => tag.trim()).filter(Boolean) : [];

tinymce.init({
    selector: 'textarea#content',
    promotion: false,
    branding: false,
    menubar: false,
    plugins: 'image link table lists code',
    toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | blockquote | link image table code | more',
    height: 360,
    placeholder: 'Bắt đầu viết nội dung bài viết tại đây...',
    setup: function (editor) {
        editor.on('keyup change input init', updateWordCount);
    },
    image_title: true,
    automatic_uploads: true,
    file_picker_types: 'image',
    file_picker_callback: function (cb) {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.onchange = function () {
            const file = this.files[0];
            const reader = new FileReader();
            reader.onload = function () {
                const id = 'blobid' + (new Date()).getTime();
                const blobCache = tinymce.activeEditor.editorUpload.blobCache;
                const base64 = reader.result.split(',')[1];
                const blobInfo = blobCache.create(id, file, base64);
                blobCache.add(blobInfo);
                cb(blobInfo.blobUri(), { title: file.name });
            };
            reader.readAsDataURL(file);
        };
        input.click();
    }
});

function updateTextCounters() {
    document.getElementById('titleCount').textContent = document.getElementById('title').value.length;
    document.getElementById('summaryCount').textContent = document.getElementById('summary').value.length;
}

function updateWordCount() {
    const text = tinymce.get('content') ? tinymce.get('content').getContent({ format: 'text' }) : document.getElementById('content').value;
    const words = text.trim() ? text.trim().split(/\s+/).length : 0;
    document.getElementById('wordCount').textContent = words;
}

function syncTags() {
    document.getElementById('tags').value = articleTags.join(', ');
    const tagList = document.getElementById('tagList');
    tagList.innerHTML = '';

    articleTags.forEach((tag, index) => {
        const item = document.createElement('span');
        item.textContent = tag;
        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.textContent = '×';
        removeButton.onclick = function () {
            articleTags.splice(index, 1);
            syncTags();
        };
        item.appendChild(removeButton);
        tagList.appendChild(item);
    });
}

function addTagFromInput() {
    const input = document.getElementById('tagInput');
    const value = input.value.trim();

    if (value && !articleTags.includes(value) && articleTags.length < 12) {
        articleTags.push(value);
        syncTags();
    }

    input.value = '';
}

function setPublishState(isPublished) {
    document.getElementById('is_published').checked = isPublished;
}

function updatePublishLabel() {
    const label = document.getElementById('publishStatusLabel');
    const isPublished = document.getElementById('is_published').checked;
    label.textContent = isPublished ? 'Sẵn sàng xuất bản' : 'Đang lưu nháp';
    label.style.background = isPublished ? '#dcfce7' : '#f1f5f9';
    label.style.color = isPublished ? '#16a34a' : '#64748b';
}

function previewSelectedFile(file) {
    if (!file) return;

    if (file.size > 5 * 1024 * 1024) {
        alert('Kích thước ảnh quá lớn! Vui lòng chọn ảnh nhỏ hơn 5MB.');
        document.getElementById('featured_image').value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function (event) {
        document.getElementById('image-preview').src = event.target.result;
        document.getElementById('image-preview-placeholder').hidden = true;
        document.getElementById('image-preview-container').hidden = false;
    };
    reader.readAsDataURL(file);
}

function previewFile(input) {
    previewSelectedFile(input.files[0]);
}

function removePreviewImage() {
    document.getElementById('featured_image').value = '';
    document.getElementById('image-preview').src = '#';
    document.getElementById('image-preview-placeholder').hidden = false;
    document.getElementById('image-preview-container').hidden = true;
}

document.addEventListener('DOMContentLoaded', function () {
    const title = document.getElementById('title');
    const summary = document.getElementById('summary');
    const tagInput = document.getElementById('tagInput');
    const publishToggle = document.getElementById('is_published');
    const uploadZone = document.getElementById('uploadZone');
    const imageInput = document.getElementById('featured_image');

    title.addEventListener('input', updateTextCounters);
    summary.addEventListener('input', updateTextCounters);
    publishToggle.addEventListener('change', updatePublishLabel);

    document.getElementById('saveDraftButton').addEventListener('click', function () {
        setPublishState(false);
        syncTags();
    });

    document.getElementById('publishButton').addEventListener('click', function () {
        setPublishState(true);
        syncTags();
    });

    tagInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            addTagFromInput();
        }
    });

    document.getElementById('articleForm').addEventListener('submit', function (event) {
        if (tinymce.get('content')) {
            tinymce.get('content').save();
        }
        const contentVal = document.getElementById('content').value.trim();
        if (!contentVal) {
            alert('Vui lòng nhập nội dung bài viết.');
            event.preventDefault();
            return false;
        }
        syncTags();
    });

    uploadZone.addEventListener('click', function (event) {
        if (event.target.closest('button')) return;
        imageInput.click();
    });

    uploadZone.addEventListener('dragover', function (event) {
        event.preventDefault();
        uploadZone.classList.add('is-dragover');
    });

    uploadZone.addEventListener('dragleave', function () {
        uploadZone.classList.remove('is-dragover');
    });

    uploadZone.addEventListener('drop', function (event) {
        event.preventDefault();
        uploadZone.classList.remove('is-dragover');
        const file = event.dataTransfer.files[0];
        if (!file) return;

        const transfer = new DataTransfer();
        transfer.items.add(file);
        imageInput.files = transfer.files;
        previewSelectedFile(file);
    });

    syncTags();
    updateTextCounters();
    updatePublishLabel();
});
</script>
@endsection
