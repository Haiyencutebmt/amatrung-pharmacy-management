@extends('layouts.admin')

@section('title', 'Sửa từ điển dược liệu — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="header-title" style="display:flex;align-items:center;gap:1rem;">
    <div style="width:44px;height:44px;background:#ecfdf5;color:#0f766e;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.25rem;border:1px solid #bbf7d0;">🌿</div>
    <div>
        <h1 style="font-size:1.5rem;font-weight:850;color:#1e293b;margin:0;line-height:1.2;">Sửa mục từ điển</h1>
        <p style="margin:0;color:#64748b;font-size:0.85rem;font-weight:500;">{{ $entry->name }}</p>
    </div>
</div>
@endsection

@section('content')
<form action="{{ route('admin.herb-dictionary.update', $entry) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('admin.herb_dictionary._form', ['entry' => $entry])
</form>

@foreach($entry->images as $image)
    <form
        id="delete-herb-image-{{ $image->id }}"
        action="{{ route('admin.herb-dictionary.images.destroy', [$entry, $image]) }}"
        method="POST"
        onsubmit="return confirm('Xóa ảnh này khỏi {{ addslashes($entry->name) }}?');"
        style="display:none;"
    >
        @csrf
        @method('DELETE')
    </form>
@endforeach
@endsection
