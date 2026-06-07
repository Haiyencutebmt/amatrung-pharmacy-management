@extends('layouts.guest')

@section('title', 'Về Thầy thuốc Y Hiếu Niê (AmaTrung) — Y Học Cổ Truyền')

@section('content')
<div class="relative bg-gradient-to-b from-sky-50 via-white to-sky-100 min-h-screen pb-20 overflow-hidden font-sans">
    
    <!-- Decorative Floating Leaf/Flower (Abstract CSS shapes & gradients) -->
    <div class="absolute top-20 left-10 w-72 h-72 bg-sky-200/30 rounded-full blur-3xl pointer-events-none z-0"></div>
    <div class="absolute bottom-40 right-10 w-96 h-96 bg-green-100/40 rounded-full blur-3xl pointer-events-none z-0"></div>

    <!-- Hero Title Section -->
    <div class="max-w-[1400px] mx-auto px-4 mt-8 md:mt-12 relative z-10">
        <div class="bg-gradient-to-r from-[#1a5b8f] to-[#4292d6] rounded-[2.5rem] py-12 md:py-16 px-6 md:px-12 text-center md:text-left shadow-lg border border-sky-100 text-white relative overflow-hidden">
            <!-- Decorative background pattern overlay -->
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>
            
            <div class="max-w-4xl relative z-10 flex flex-col md:flex-row items-center gap-6 md:gap-8">
                <div class="bg-white/10 backdrop-blur-sm p-4 rounded-full border border-white/20">
                    <img src="{{ asset('images/amatrung_logo.png') }}" class="w-16 h-16 md:w-24 md:h-24 object-contain rounded-full" alt="Logo">
                </div>
                <div>
                    <!-- Badge -->
                    <span class="inline-block bg-white/20 backdrop-blur-sm px-4 py-1.5 rounded-full text-xs md:text-sm font-bold uppercase tracking-wider mb-3 border border-white/20">
                        GIỚI THIỆU THẦY THUỐC & THƯƠNG HIỆU
                    </span>
                    <h1 class="text-3xl md:text-5xl font-black tracking-tight mb-3">Thầy thuốc Y Hiếu Niê & Tên gọi AmaTrung</h1>
                    <p class="text-sky-100 text-base md:text-lg max-w-2xl font-medium leading-relaxed">
                        Khám phá nguồn gốc tên gọi đậm đà bản sắc Tây Nguyên và hành trình mang thảo dược cứu giúp người bệnh của nhà y học cổ truyền dân tộc Ê-Đê.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="max-w-[1400px] mx-auto px-4 mt-6 relative z-10">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-[#1a5b8f] hover:text-[#2978c4] font-bold transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại Trang chủ
        </a>
    </div>

    <!-- Main Content Layout -->
    <div class="max-w-[1400px] mx-auto px-4 mt-6 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-12">
            
            <!-- Left Side: Profile Card (Column span 4) -->
            <div class="lg:col-span-4 flex flex-col gap-6">
                
                <!-- Doctor Profile Card -->
                <div class="bg-white rounded-[2.5rem] p-6 shadow-[0_10px_35px_rgba(0,0,0,0.03)] border border-sky-100 flex flex-col items-center text-center">
                    
                    <!-- Doctor Image Container with elegant borders -->
                    <div class="relative group w-full max-w-[280px] aspect-[4/5] rounded-[2rem] overflow-hidden shadow-[0_15px_30px_rgba(0,0,0,0.1)] border-[6px] border-white ring-2 ring-sky-100">
                        <img src="{{ asset('images/doctor.JPG') }}" alt="Thầy thuốc Y Hiếu Niê" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-[#1a5b8f]/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    
                    <!-- Name & Subtitle -->
                    <h2 class="mt-6 text-2xl font-black text-[#1a5b8f] tracking-wide">Y HIẾU NIÊ</h2>
                    <span class="mt-1 bg-sky-50 text-[#1a5b8f] font-bold text-xs px-3.5 py-1.5 rounded-full border border-sky-100 uppercase tracking-wider">
                        Thầy thuốc Y học cổ truyền
                    </span>
                    
                    <!-- Divider -->
                    <div class="w-24 h-1 bg-gradient-to-r from-sky-200 via-sky-400 to-sky-200 my-6 rounded-full"></div>
                    
                    <!-- Quick Specs -->
                    <div class="w-full text-left space-y-4 px-2">
                        <div class="flex items-start gap-3">
                            <div class="bg-sky-50 text-[#1a5b8f] p-2 rounded-xl border border-sky-100 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Dân tộc & Quê quán</h4>
                                <p class="text-sm font-bold text-slate-700">Người Ê-Đê - Buôn Ma Thuột, Đắk Lắk</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="bg-sky-50 text-[#1a5b8f] p-2 rounded-xl border border-sky-100 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Chuyên môn</h4>
                                <p class="text-sm font-bold text-slate-700">Y sĩ đa khoa, Y Học Cổ Truyền Tây Nguyên, Vật lý trị liệu</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="bg-sky-50 text-[#1a5b8f] p-2 rounded-xl border border-sky-100 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kinh nghiệm công tác</h4>
                                <p class="text-sm font-bold text-slate-700">Từng công tác tại Bệnh viện Đa khoa Cao Nguyên</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="bg-sky-50 text-[#1a5b8f] p-2 rounded-xl border border-sky-100 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Địa chỉ thăm khám</h4>
                                <p class="text-sm font-bold text-slate-700">54/36 Amajhao, Phường Tân Lập, Buôn Ma Thuột, Đắk Lắk</p>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="w-full h-px bg-slate-100 my-6"></div>

                    <!-- Philosophy Quote Box -->
                    <div class="bg-sky-50/50 rounded-2xl p-4 border border-sky-100 w-full text-center">
                        <p class="text-[#1a5b8f] font-bold italic text-sm leading-relaxed">
                            "Mỗi cây thuốc Nam nơi đất đỏ bazan đều mang trong mình sức sống mãnh liệt của đại ngàn, sẵn sàng che chở và chữa lành cho sức khỏe con người."
                        </p>
                    </div>

                </div>

                <!-- Contact & Information Card -->
                <div class="bg-white rounded-[2.5rem] p-6 shadow-[0_10px_35px_rgba(0,0,0,0.03)] border border-sky-100">
                    <h3 class="font-bold text-lg text-slate-800 mb-4 flex items-center gap-2">
                        <span class="bg-[#1a5b8f]/10 text-[#1a5b8f] p-1.5 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </span>
                        Thông tin liên hệ
                    </h3>
                    
                    <div class="space-y-3.5">
                        <div class="flex items-center gap-3">
                            <div class="text-slate-400 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <a href="tel:0912345678" class="text-sm font-bold text-slate-600 hover:text-[#1a5b8f] transition-colors">0912 345 678</a>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-slate-400 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <span class="text-sm font-bold text-slate-600">contact@amatrung.vn</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-slate-400 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <span class="text-sm font-bold text-slate-600">8:00 – 17:30 Hằng ngày</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Side: Stories & Background Details (Column span 8) -->
            <div class="lg:col-span-8 flex flex-col gap-8">
                
                <!-- Section 1: The Origin of the Name AmaTrung -->
                <div class="bg-white rounded-[2.5rem] p-6 md:p-10 shadow-[0_10px_35px_rgba(0,0,0,0.03)] border border-sky-100 relative overflow-hidden group">
                    <!-- Accent color block on left border -->
                    <div class="absolute left-0 top-0 bottom-0 w-2.5 bg-gradient-to-b from-[#1a5b8f] to-[#4292d6]"></div>
                    
                    <h2 class="text-2xl md:text-3xl font-black text-[#1a5b8f] mb-6 flex items-center gap-3">
                        <span class="text-3xl">🌱</span> Nguồn gốc tên gọi "AmaTrung"
                    </h2>
                    
                    <div class="prose prose-slate max-w-none text-slate-600 text-sm md:text-base leading-relaxed space-y-4">
                        <p class="font-medium text-slate-800">
                            Thương hiệu <strong class="text-[#1a5b8f] font-extrabold">AmaTrung</strong> không phải là một danh xưng ngẫu nhiên, mà nó bắt nguồn sâu sắc từ nét văn hóa đặc trưng lâu đời của đồng bào dân tộc Ê-Đê tại vùng đất Tây Nguyên đầy nắng gió.
                        </p>
                        
                        <p>
                            Trong tiếng Ê-Đê, <strong class="text-[#1a5b8f]">"Ama"</strong> có nghĩa là <strong class="font-bold">Bố</strong> hoặc <strong class="font-bold">Cha</strong>. Theo phong tục mẫu hệ và cách xưng hô truyền thống của người Ê-Đê, khi một người đàn ông lập gia đình và có đứa con đầu lòng, buôn làng sẽ không gọi ông bằng tên khai sinh nữa. Thay vào đó, họ sẽ gọi ông bằng từ **"Ama"** kết hợp với tên của người con cả.
                        </p>

                        <!-- Highlighted Explanation Card -->
                        <div class="bg-sky-50 rounded-2xl p-5 border border-sky-100 my-6 flex flex-col md:flex-row items-center gap-5">
                            <span class="text-5xl shrink-0">🏡</span>
                            <div>
                                <p class="text-[#1a5b8f] font-black text-lg mb-1">Ama + Trung = Cha của Trung</p>
                                <p class="text-slate-600 text-sm">
                                    Con trai lớn của Thầy thuốc <strong class="text-slate-800 font-bold">Y Hiếu Niê</strong> tên là <strong class="text-slate-800 font-bold">Trung</strong>. Do đó, theo tập tục xưng hô thân thương trong buôn làng, bà con luôn gọi ông là <strong class="text-[#1a5b8f] font-bold">Ama Trung</strong>.
                                </p>
                            </div>
                        </div>

                        <p>
                            Cách đặt tên này thể hiện nét đẹp văn hóa sâu sắc, coi trọng tình mẫu tử, tình phụ tử và gia đình của người Tây Nguyên. Việc lấy tên đứa con đầu lòng làm định danh cho cha mẹ tượng trưng cho sự gắn kết máu thịt, niềm tự hào và trách nhiệm lớn lao của đấng sinh thành đối với thế hệ tương lai và đối với buôn làng.
                        </p>
                        
                        <p>
                            Nhà thuốc quyết định lấy tên gọi <strong class="text-[#1a5b8f] font-extrabold">AmaTrung</strong> làm thương hiệu với mong muốn mang lại sự thân thiện, gìn giữ bản sắc văn hóa vùng cao, đồng thời gửi gắm cam kết trị bệnh cứu người bằng cả tấm lòng, sự bao dung và trách nhiệm như một người cha chăm sóc sức khỏe cho gia đình lớn của mình.
                        </p>
                    </div>
                </div>

                <!-- Section 2: Journey & Professional Values -->
                <div class="bg-white rounded-[2.5rem] p-6 md:p-10 shadow-[0_10px_35px_rgba(0,0,0,0.03)] border border-sky-100 relative overflow-hidden group">
                    <div class="absolute left-0 top-0 bottom-0 w-2.5 bg-gradient-to-b from-[#5ca8e6] to-[#a1c6ed]"></div>

                    <h2 class="text-2xl md:text-3xl font-black text-[#1a5b8f] mb-6 flex items-center gap-3">
                        <span class="text-3xl">🩺</span> Hành trình y nghiệp & Sứ mệnh cứu người
                    </h2>

                    <div class="prose prose-slate max-w-none text-slate-600 text-sm md:text-base leading-relaxed space-y-4">
                        <p>
                            Thầy thuốc <strong class="text-slate-800 font-bold">Y Hiếu Niê</strong> sinh ra và lớn lên giữa đại ngàn Tây Nguyên, nơi được thiên nhiên ban tặng hệ sinh thái dược liệu vô cùng phong phú. Từ nhỏ, ông đã được tiếp xúc với những bài thuốc Nam truyền thống của đồng bào Ê-Đê, chứng kiến cách người xưa dùng lá cây rừng để chữa trị các vết thương, bệnh tật.
                        </p>
                        
                        <p>
                            Nung nấu ý chí mang y thuật phục vụ bà con, ông đã không ngừng học tập, nghiên cứu cả về các bài thuốc dân gian bản địa lẫn lý luận Y Học Cổ Truyền bài bản (bao gồm lý thuyết âm dương ngũ hành, mạch chẩn học). Ông nhận ra rằng:
                        </p>

                        <!-- Core Principles Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 my-6">
                            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 hover:shadow-sm transition-shadow">
                                <h4 class="font-bold text-[#1a5b8f] flex items-center gap-2 mb-2">
                                    <span class="text-emerald-500">✓</span> Thảo dược bản địa chất lượng
                                </h4>
                                <p class="text-xs md:text-sm text-slate-500">
                                    Sử dụng dược liệu tự nhiên, an toàn, được thu hái trực tiếp từ núi rừng Đắk Lắk và các vùng Tây Nguyên nhằm đảm bảo dược tính tự nhiên cao nhất.
                                </p>
                            </div>

                            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 hover:shadow-sm transition-shadow">
                                <h4 class="font-bold text-[#1a5b8f] flex items-center gap-2 mb-2">
                                    <span class="text-emerald-500">✓</span> Cá nhân hóa từng phác đồ
                                </h4>
                                <p class="text-xs md:text-sm text-slate-500">
                                    Không áp dụng một bài thuốc chung cho mọi người. Tùy thuộc vào thể trạng (chỉ số BMI), nhịp mạch và biểu hiện lâm sàng, thuốc sẽ được gia giảm vị cho khớp với từng cơ địa.
                                </p>
                            </div>
                        </div>

                        <p>
                            Tại phòng khám **AmaTrung**, Thầy thuốc Y Hiếu Niê trực tiếp bắt mạch, chẩn bệnh và đưa ra phương án điều trị tối ưu nhất. Với tấm lòng y đức cao cả, ông luôn coi sức khỏe của người bệnh là phần thưởng quý giá nhất của cuộc đời thầy thuốc.
                        </p>
                    </div>
                </div>

                <!-- Section 3: Google Map and Address Info -->
                <div class="bg-[#f0f7ff] rounded-[2.5rem] p-6 md:p-8 border border-sky-100 shadow-sm flex flex-col md:flex-row gap-6 md:gap-8 items-stretch">
                    <!-- Map Frame -->
                    <div class="w-full md:w-1/2 rounded-[2rem] overflow-hidden shadow-md border border-white min-h-[250px]">
                        <iframe src="https://maps.google.com/maps?q=Nh%C3%A0%20s%C3%A0n%20th%C3%A2n%20y%C3%AAu,%2054/36%20Ama%20Jhao,%20Ph%C6%B0%E1%BB%9Dng%20T%C3%A2n%20L%E1%BA%ADp,%20Bu%C3%B4n%20Ma%20Thu%E1%BB%99t,%20%C4%90%E1%BA%AFk%20L%E1%BA%AFk&t=&z=15&ie=UTF8&iwloc=&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" class="w-full h-full object-cover"></iframe>
                    </div>

                    <!-- Directions & Address Info -->
                    <div class="w-full md:w-1/2 flex flex-col justify-center text-left">
                        <span class="text-xs font-bold text-[#1a5b8f] uppercase tracking-wider mb-2">Địa Chỉ Phòng Khám</span>
                        <h3 class="text-xl md:text-2xl font-black text-[#1a5b8f] mb-4">Phòng Khám Y Học Cổ Truyền AmaTrung</h3>
                        
                        <p class="text-slate-600 text-sm leading-relaxed mb-4">
                            Tọa lạc tại địa chỉ <a href="https://maps.app.goo.gl/A7pamE9PfwgbvNLj7" target="_blank" rel="noopener noreferrer" class="text-[#1a5b8f] hover:underline font-bold">54/36 Amajhao, Phường Tân Lập, TP. Buôn Ma Thuột, Tỉnh Đắk Lắk</a>. 
                        </p>
                        
                        <p class="text-slate-500 text-xs italic leading-relaxed">
                            Bà con có thể liên hệ trực tiếp qua số điện thoại <strong class="text-[#1a5b8f]">0912 345 678</strong> trước khi đến để được chuẩn bị và sắp xếp lịch bắt mạch thuận tiện nhất.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>
@endsection
