@extends('layouts.guest')
@section('title', 'Trang chủ - AmaTrung')

@section('content')
<div class="relative bg-gradient-to-b from-sky-50 via-white to-sky-100 min-h-screen pb-20 overflow-hidden font-sans">
    
    <!-- Hero Section -->
    <div class="max-w-7xl mx-auto px-4 pt-8 md:pt-12 text-center relative z-10">
        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <div class="bg-white p-3 rounded-full shadow-lg border border-sky-100">
                <img src="{{ asset('images/amatrung_logo.png') }}" class="w-20 h-20 md:w-28 md:h-28 object-contain rounded-full" alt="Logo">
            </div>
        </div>
        
        <!-- Title -->
        <h1 class="text-3xl md:text-5xl font-extrabold text-[#1a5b8f] tracking-wide mb-3 drop-shadow-sm">NHÀ THUỐC Y HỌC CỔ TRUYỀN</h1>
        <h2 class="text-4xl md:text-6xl lg:text-7xl font-black text-[#3688D8] uppercase tracking-widest drop-shadow-md mb-8">AMATRUNG</h2>
        
        <!-- Quote -->
        <div class="max-w-3xl mx-auto mb-12 relative px-4">
            <p class="text-lg md:text-xl text-gray-700 font-medium italic leading-relaxed text-center">
                "Thuốc đắng dã tật, sự sống đơm hoa.<br>
                Mỗi vị thuốc đều mang trong mình linh khí của đất trời,<br>
                kết nối hệ tự nhiên với sức khỏe con người."
            </p>
        </div>

        <!-- 5 Elements Characters -->
        <div class="flex justify-center items-end gap-2 sm:gap-4 md:gap-6 mb-16 relative h-[250px] sm:h-[350px] md:h-[450px]">
            <!-- KIM -->
            <a href="{{ url('/bai-viet/ngu-hanh-kim') }}" class="w-[20%] max-w-[200px] transform hover:-translate-y-4 transition-all duration-300 relative z-20 hover:z-50" style="bottom: 10%;">
                <img src="{{ asset('images/Kim.png') }}" alt="Kim" class="w-full h-auto drop-shadow-2xl">
            </a>
            
            <!-- MỘC -->
            <a href="{{ url('/bai-viet/ngu-hanh-moc') }}" class="w-[24%] max-w-[240px] transform hover:-translate-y-4 transition-all duration-300 relative z-30 hover:z-50" style="bottom: 5%;">
                <img src="{{ asset('images/Mộc.png') }}" alt="Mộc" class="w-full h-auto drop-shadow-2xl">
            </a>
            
            <!-- THỦY (Center, Biggest) -->
            <a href="{{ url('/bai-viet/ngu-hanh-thuy') }}" class="w-[30%] max-w-[300px] transform hover:-translate-y-4 transition-all duration-300 relative z-40 hover:z-50" style="bottom: 0;">
                <img src="{{ asset('images/Thủy.png') }}" alt="Thủy" class="w-full h-auto drop-shadow-[0_20px_25px_rgba(59,130,246,0.3)]">
            </a>
            
            <!-- HỎA -->
            <a href="{{ url('/bai-viet/ngu-hanh-hoa') }}" class="w-[24%] max-w-[240px] transform hover:-translate-y-4 transition-all duration-300 relative z-30 hover:z-50" style="bottom: 5%;">
                <img src="{{ asset('images/Hỏa.png') }}" alt="Hỏa" class="w-full h-auto drop-shadow-2xl">
            </a>
            
            <!-- THỔ -->
            <a href="{{ url('/bai-viet/ngu-hanh-tho') }}" class="w-[20%] max-w-[200px] transform hover:-translate-y-4 transition-all duration-300 relative z-20 hover:z-50" style="bottom: 10%;">
                <img src="{{ asset('images/Thổ.png') }}" alt="Thổ" class="w-full h-auto drop-shadow-2xl">
            </a>
        </div>
    </div>

    <!-- Section 2: Features -->
    <div class="max-w-6xl mx-auto px-4 mb-20 relative z-10 mt-8">
        <div class="flex items-center justify-center gap-4 mb-10">
            <div class="h-[2px] bg-green-500 w-12 md:w-32 rounded-full"></div>
            <h3 class="text-xl md:text-3xl font-black text-[#1f4e3c] uppercase tracking-wide text-center">Y Học Cổ Truyền – Gìn giữ sức khỏe từ gốc</h3>
            <div class="h-[2px] bg-green-500 w-12 md:w-32 rounded-full"></div>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
            <!-- Tự nhiên -->
            <div class="bg-white/90 backdrop-blur-md p-6 md:p-8 rounded-[2rem] shadow-lg border-2 border-green-100 text-center hover:-translate-y-2 transition-transform duration-300 group">
                <div class="bg-green-100 w-20 h-20 mx-auto rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-10 h-10 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                </div>
                <h4 class="font-extrabold text-green-800 text-xl mb-3">Tự nhiên</h4>
                <p class="text-sm md:text-base text-gray-600 font-medium">Sử dụng thảo dược quý từ thiên nhiên.</p>
            </div>
            <!-- An toàn -->
            <div class="bg-white/90 backdrop-blur-md p-6 md:p-8 rounded-[2rem] shadow-lg border-2 border-orange-100 text-center hover:-translate-y-2 transition-transform duration-300 group">
                <div class="bg-orange-100 w-20 h-20 mx-auto rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-10 h-10 text-orange-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                </div>
                <h4 class="font-extrabold text-orange-800 text-xl mb-3">An toàn</h4>
                <p class="text-sm md:text-base text-gray-600 font-medium">Lành tính, lành bệnh, hạn chế tác dụng phụ.</p>
            </div>
            <!-- Hiệu quả -->
            <div class="bg-white/90 backdrop-blur-md p-6 md:p-8 rounded-[2rem] shadow-lg border-2 border-red-100 text-center hover:-translate-y-2 transition-transform duration-300 group">
                <div class="bg-red-100 w-20 h-20 mx-auto rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-10 h-10 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>
                </div>
                <h4 class="font-extrabold text-red-800 text-xl mb-3">Hiệu quả</h4>
                <p class="text-sm md:text-base text-gray-600 font-medium">Điều trị từ gốc, bền vững và lâu dài.</p>
            </div>
            <!-- Tận tâm -->
            <div class="bg-white/90 backdrop-blur-md p-6 md:p-8 rounded-[2rem] shadow-lg border-2 border-purple-100 text-center hover:-translate-y-2 transition-transform duration-300 group">
                <div class="bg-purple-100 w-20 h-20 mx-auto rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-10 h-10 text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                </div>
                <h4 class="font-extrabold text-purple-800 text-xl mb-3">Tận tâm</h4>
                <p class="text-sm md:text-base text-gray-600 font-medium">Đồng hành cùng bạn trên hành trình sức khỏe.</p>
            </div>
        </div>
    </div>

    <!-- Section 3: Services & Why Choose Us -->
    <div class="max-w-7xl mx-auto px-4 relative z-10 mt-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">
            <!-- Left: Services -->
            <div class="bg-[#fdfaf3] rounded-[2.5rem] p-8 md:p-10 border-[6px] border-[#ecd2a9] shadow-xl relative mt-8 lg:mt-0">
                <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-[#db9a53] text-white px-8 py-2.5 rounded-full shadow-lg font-black text-xl md:text-2xl uppercase whitespace-nowrap border-[3px] border-[#a06530]">
                    Dịch vụ của chúng tôi
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mt-8">
                    <div class="bg-white rounded-2xl p-5 text-center shadow-sm border border-[#e5d4b5] hover:shadow-lg transition-shadow">
                        <div class="bg-[#f6ebd9] w-14 h-14 mx-auto rounded-full flex items-center justify-center mb-4 text-[#8c5a2c]">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path></svg>
                        </div>
                        <p class="text-[15px] font-bold text-gray-800 leading-tight">Bốc thuốc<br>theo đơn</p>
                    </div>
                    <div class="bg-white rounded-2xl p-5 text-center shadow-sm border border-[#e5d4b5] hover:shadow-lg transition-shadow">
                        <div class="bg-[#e7f3df] w-14 h-14 mx-auto rounded-full flex items-center justify-center mb-4 text-[#4a7a29]">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                        </div>
                        <p class="text-[15px] font-bold text-gray-800 leading-tight">Tư vấn<br>YHCT</p>
                    </div>
                    <div class="bg-white rounded-2xl p-5 text-center shadow-sm border border-[#e5d4b5] hover:shadow-lg transition-shadow">
                        <div class="bg-[#fff6d6] w-14 h-14 mx-auto rounded-full flex items-center justify-center mb-4 text-[#c4980a]">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z"></path></svg>
                        </div>
                        <p class="text-[15px] font-bold text-gray-800 leading-tight">Thảo dược<br>chất lượng</p>
                    </div>
                    <div class="bg-white rounded-2xl p-5 text-center shadow-sm border border-[#e5d4b5] hover:shadow-lg transition-shadow">
                        <div class="bg-[#fce9de] w-14 h-14 mx-auto rounded-full flex items-center justify-center mb-4 text-[#d16113]">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z"></path></svg>
                        </div>
                        <p class="text-[15px] font-bold text-gray-800 leading-tight">Xông - Châm cứu<br>Bấm huyệt</p>
                    </div>
                    <div class="bg-white rounded-2xl p-5 text-center shadow-sm border border-[#e5d4b5] hover:shadow-lg transition-shadow">
                        <div class="bg-[#e1effa] w-14 h-14 mx-auto rounded-full flex items-center justify-center mb-4 text-[#2b71b3]">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                        </div>
                        <p class="text-[15px] font-bold text-gray-800 leading-tight">Dưỡng sinh<br>Phục hồi</p>
                    </div>
                    <div class="bg-white rounded-2xl p-5 text-center shadow-sm border border-[#e5d4b5] hover:shadow-lg transition-shadow">
                        <div class="bg-[#e4f3de] w-14 h-14 mx-auto rounded-full flex items-center justify-center mb-4 text-[#598c37]">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path></svg>
                        </div>
                        <p class="text-[15px] font-bold text-gray-800 leading-tight">Chăm sóc<br>toàn diện</p>
                    </div>
                </div>
            </div>

            <!-- Right: Why Choose Us -->
            <div class="bg-white/95 backdrop-blur-md rounded-[2.5rem] p-8 md:p-10 shadow-xl relative border-[3px] border-[#5698d5] mt-10 lg:mt-0">
                <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-gradient-to-r from-[#4483c6] to-[#60a5fa] text-white px-8 py-2.5 rounded-full shadow-lg font-black text-xl md:text-2xl uppercase whitespace-nowrap border-[3px] border-[#b0d2f5]">
                    Vì sao chọn AMATRUNG ?
                </div>
                <div class="mt-8 space-y-7">
                    <div class="flex items-start gap-4">
                        <div class="bg-[#4483c6] text-white rounded-full p-1.5 mt-1 shrink-0 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="text-gray-800 font-bold text-[17px] leading-snug">Nguồn gốc thảo dược rõ ràng,<br>được chọn lọc kỹ lưỡng</p>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="bg-[#4483c6] text-white rounded-full p-1.5 mt-1 shrink-0 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="text-gray-800 font-bold text-[17px] leading-snug">Kết hợp tinh hoa YHCT<br>với kiến thức hiện đại</p>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="bg-[#4483c6] text-white rounded-full p-1.5 mt-1 shrink-0 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="text-gray-800 font-bold text-[17px] leading-snug">Đội ngũ y dược sĩ giàu kinh nghiệm,<br>tận tâm và chuyên môn cao</p>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="bg-[#4483c6] text-white rounded-full p-1.5 mt-1 shrink-0 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="text-gray-800 font-bold text-[17px] leading-snug">Luôn đặt sức khỏe và sự hài lòng<br>của khách hàng lên hàng đầu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Banner -->
    <div class="max-w-4xl mx-auto px-4 mt-24 relative z-10 text-center pb-8">
        <div class="inline-block bg-[#fffcf5] border-y-[6px] border-[#e8d5b5] py-5 px-10 md:px-20 shadow-2xl relative rounded-[2px]">
            <div class="absolute top-0 left-0 w-6 h-full bg-[#cca673] block border-r-2 border-[#b58f5c] shadow-[inset_2px_0_4px_rgba(0,0,0,0.1)]"></div>
            <div class="absolute top-0 right-0 w-6 h-full bg-[#cca673] block border-l-2 border-[#b58f5c] shadow-[inset_-2px_0_4px_rgba(0,0,0,0.1)]"></div>
            
            <p class="text-xl md:text-2xl text-[#875525] font-extrabold italic mb-2">AMATRUNG – Gìn giữ tinh hoa Y học cổ truyền</p>
            <p class="text-2xl md:text-3xl text-[#d15e12] font-black italic flex items-center justify-center gap-3">
                Vì sức khỏe và hạnh phúc của bạn!
                <svg class="w-8 h-8 text-red-500 drop-shadow-md animate-pulse" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>
            </p>
        </div>
    </div>
</div>
@endsection
