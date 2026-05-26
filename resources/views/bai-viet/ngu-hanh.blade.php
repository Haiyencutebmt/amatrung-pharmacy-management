@extends('layouts.guest')
@section('title', $elementName . ' - Y Học Cổ Truyền AmaTrung')

@section('content')
<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8 bg-white shadow-xl rounded-2xl my-10 border border-gray-100">
    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-[#1a5b8f] mb-4">Tìm hiểu về Ngũ Hành: {{ $elementName }}</h1>
        <div class="w-24 h-1 bg-gradient-to-r from-blue-400 to-green-400 mx-auto rounded-full"></div>
    </div>
    
    <div class="flex flex-col md:flex-row gap-8 items-center mb-12">
        <div class="w-full md:w-1/3">
            <img src="{{ asset('images/' . $elementName . '.png') }}" alt="{{ $elementName }}" class="w-full h-auto drop-shadow-2xl hover:scale-105 transition-transform duration-500">
        </div>
        <div class="w-full md:w-2/3">
            <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                <h3 class="text-xl font-bold text-gray-800 mb-3">Tổng quan về {{ $elementName }} trong Y Học Cổ Truyền</h3>
                <p class="text-gray-600 leading-relaxed italic mb-4">
                    Nội dung chi tiết về hành {{ $elementName }} đang được đội ngũ Y Bác sĩ AmaTrung biên soạn và sẽ sớm được cập nhật. Xin quý khách vui lòng quay lại sau!
                </p>
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-6 py-2 bg-[#59A8ED] text-white font-bold rounded-full hover:bg-blue-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Quay về Trang chủ
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
