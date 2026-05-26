@extends('layouts.admin')

@section('title', 'Viết Bài Mới — AmaTrung')
@section('page-title', 'Viết Bài Mới')

@section('content')
<div class="max-w-[1200px] mx-auto px-2 md:px-4 mt-2">
    
    <!-- Header banner -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-800 tracking-wide">Viết Bài Viết Mới</h1>
            <p class="text-sm text-slate-500 mt-1">Chia sẻ kiến thức y khoa cổ truyền bổ ích đến với cộng đồng bệnh nhân.</p>
        </div>
        <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary flex items-center gap-2 shadow-sm rounded-2xl hover:bg-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span class="font-bold text-sm">Quay lại danh sách</span>
        </a>
    </div>

    <!-- Main Form -->
    <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Column: Main Editor (8 Columns) -->
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white rounded-[2rem] p-6 md:p-8 shadow-[0_10px_30px_rgba(0,0,0,0.02)] border border-slate-100">
                    
                    <!-- Title Input -->
                    <div class="form-group mb-6">
                        <label for="title" class="block text-sm font-bold text-slate-700 mb-2">Tiêu đề bài viết *</label>
                        <input type="text" name="title" id="title" class="w-full px-5 py-3.5 rounded-2xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition-all shadow-sm text-lg font-bold text-slate-800 placeholder-slate-300" value="{{ old('title') }}" required placeholder="Nhập tiêu đề bài viết...">
                        @error('title')
                            <p class="text-red-500 text-xs mt-1.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Editor Content -->
                    <div class="form-group">
                        <label for="content" class="block text-sm font-bold text-slate-700 mb-2">Nội dung bài viết *</label>
                        <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                            <textarea name="content" id="content" class="w-full outline-none p-4" rows="15" placeholder="Nhập nội dung bài viết...">{{ old('content') }}</textarea>
                        </div>
                        <p class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Mẹo: Soạn thảo văn bản và định dạng trực tiếp bằng công cụ viết chuyên nghiệp.
                        </p>
                        @error('content')
                            <p class="text-red-500 text-xs mt-1.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    
                </div>
            </div>

            <!-- Right Column: Settings & Upload Sidebar (4 Columns) -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- Card 1: Publish Settings -->
                <div class="bg-white rounded-[2rem] p-6 shadow-[0_10px_30px_rgba(0,0,0,0.02)] border border-slate-100">
                    <h3 class="font-extrabold text-slate-800 text-base mb-4 pb-3 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        Trạng thái hiển thị
                    </h3>

                    <div class="flex items-center gap-3 py-1">
                        <!-- Custom iOS toggle switch -->
                        <label class="relative inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="is_published" id="is_published" value="1" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                            <span class="ml-3 text-sm font-bold text-slate-600">Xuất bản bài viết</span>
                        </label>
                    </div>
                    <p class="text-xs text-slate-400 mt-2 leading-relaxed">Khi xuất bản, bài viết sẽ được hiển thị ngay lập tức trên trang chủ và kho thư viện y khoa.</p>

                    <div class="flex gap-3 mt-6 pt-4 border-t border-slate-100">
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary w-1/2 rounded-xl text-center py-2.5 font-bold">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary w-1/2 rounded-xl py-2.5 font-bold shadow-md shadow-blue-500/10">Đăng bài</button>
                    </div>
                </div>

                <!-- Card 2: Featured Image upload zone -->
                <div class="bg-white rounded-[2rem] p-6 shadow-[0_10px_30px_rgba(0,0,0,0.02)] border border-slate-100">
                    <h3 class="font-extrabold text-slate-800 text-base mb-4 pb-3 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Hình ảnh minh họa
                    </h3>

                    <div class="form-group">
                        <!-- Image Preview Box -->
                        <div class="mb-2">
                            <div id="image-preview-placeholder" class="w-full h-48 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-center p-4 cursor-pointer hover:bg-blue-50/50 hover:border-blue-400 transition-colors group" onclick="document.getElementById('featured_image').click()">
                                <svg class="w-12 h-12 text-slate-400 group-hover:text-blue-500 transition-colors mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-sm font-bold text-slate-500 group-hover:text-blue-600 transition-colors">Tải ảnh lên</span>
                                <span class="text-xs text-slate-400 mt-1 leading-relaxed">Hỗ trợ JPG, PNG, WEBP (Tối đa 2MB)</span>
                            </div>
                            
                            <!-- Image preview container -->
                            <div id="image-preview-container" class="relative w-full h-48 rounded-2xl overflow-hidden shadow-inner border border-slate-100 hidden group">
                                <img id="image-preview" src="#" alt="Preview" class="w-full h-full object-cover">
                                <!-- Hover Actions overlay -->
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center gap-3">
                                    <button type="button" class="bg-white/95 text-slate-800 p-2.5 rounded-full shadow hover:bg-white hover:scale-105 transition-all" onclick="document.getElementById('featured_image').click()" title="Thay đổi ảnh">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <button type="button" class="bg-red-500/95 text-white p-2.5 rounded-full shadow hover:bg-red-600 hover:scale-105 transition-all" onclick="removePreviewImage()" title="Xóa ảnh">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Real Input (hidden) -->
                        <input type="file" name="featured_image" id="featured_image" class="hidden" accept="image/*" onchange="previewFile(this)">
                        @error('featured_image')
                            <p class="text-red-500 text-xs mt-1.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

        </div>
    </form>
</div>

<!-- TinyMCE Rich Text Editor Library -->
<script src="https://cdn.tiny.cloud/1/s72x9nbmbstwud1bmsiwiirfj0zr4xfgqdddah0ip2j4exjk/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<style>
    .tox-notifications-container { display: none !important; }
    .tox-promotion { display: none !important; }
    .tox-tinymce { border-radius: 1rem !important; border-color: transparent !important; }
</style>
<script>
  tinymce.init({
    selector: 'textarea#content',
    promotion: false,
    branding: false,
    plugins: 'image link media table lists fontfamily fontsize colorpicker textcolor',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline forecolor backcolor | link image | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat',
    height: 600,
    image_title: true,
    automatic_uploads: true,
    file_picker_types: 'image',
    file_picker_callback: function (cb, value, meta) {
      var input = document.createElement('input');
      input.setAttribute('type', 'file');
      input.setAttribute('accept', 'image/*');
      input.onchange = function () {
        var file = this.files[0];
        var reader = new FileReader();
        reader.onload = function () {
          var id = 'blobid' + (new Date()).getTime();
          var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
          var base64 = reader.result.split(',')[1];
          var blobInfo = blobCache.create(id, file, base64);
          blobCache.add(blobInfo);
          cb(blobInfo.blobUri(), { title: file.name });
        };
        reader.readAsDataURL(file);
      };
      input.click();
    },
    setup: function (editor) {
      editor.on('init', function (e) {
        if (editor.getContent() === '') {
            editor.setContent('<p><br></p><p><br></p><p><em>Nguồn: Phòng khám YHCT AmaTrung</em></p>');
        }
      });
    }
  });

  // Client-side image upload preview
  function previewFile(input) {
      const file = input.files[0];
      if (file) {
          // Check file size (max 2MB)
          if (file.size > 2 * 1024 * 1024) {
              alert('Kích thước ảnh quá lớn! Vui lòng chọn ảnh nhỏ hơn 2MB.');
              input.value = '';
              return;
          }
          const reader = new FileReader();
          reader.onload = function(e) {
              document.getElementById('image-preview').src = e.target.result;
              document.getElementById('image-preview-placeholder').classList.add('hidden');
              document.getElementById('image-preview-container').classList.remove('hidden');
          }
          reader.readAsDataURL(file);
      }
  }

  function removePreviewImage() {
      document.getElementById('featured_image').value = '';
      document.getElementById('image-preview').src = '#';
      document.getElementById('image-preview-container').classList.add('hidden');
      document.getElementById('image-preview-placeholder').classList.remove('hidden');
  }
</script>
@endsection
