@extends('layouts.admin')

@section('title', 'Thêm từ điển dược liệu — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display:flex;align-items:center;gap:1rem;">
    <div style="width:44px;height:44px;background:#ecfdf5;color:#0f766e;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.25rem;border:1px solid #bbf7d0;">🌿</div>
    <div>
        <h1 style="font-size:1.5rem;font-weight:850;color:#1e293b;margin:0;line-height:1.2;">Thêm mục từ điển</h1>
        <p style="margin:0;color:#64748b;font-size:0.85rem;font-weight:500;">Nhập cây thuốc/vị thuốc nam kèm hình ảnh và lưu ý an toàn</p>
    </div>
</div>
@endsection

@section('content')
<form action="{{ route('admin.herb-dictionary.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('admin.herb_dictionary._form')
</form>
@endsection
