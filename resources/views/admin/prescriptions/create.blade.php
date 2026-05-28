@extends('layouts.admin')

@section('title', 'Kê Đơn Điều Trị — AmaTrung')
@section('page-title', '')

@section('header-left')
<div class="rx-header" style="margin-bottom: 0.5rem;">
    <h1 style="font-size: 1.5rem; font-weight: 850; color: #1e293b; margin: 0 0 0.2rem 0; line-height: 1.2;">Kê Đơn Điều Trị</h1>
    <p style="margin: 0; color: #64748b; font-size: 0.85rem; font-weight: 500;">
        Bệnh án &gt; Kê đơn điều trị &gt; <span style="color: #3b82f6;">{{ $medicalRecord->record_code }}</span>
    </p>
</div>
@endsection

@section('header-right')
<div style="display: flex; gap: 0.75rem; align-items: center;">
    <a href="{{ route('admin.medical-records.show', $medicalRecord) }}" style="color: #475569; text-decoration: none; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; background: #fff; padding: 0.5rem 1rem; border-radius: 2rem; border: 1px solid #e2e8f0; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'">
        ← Quay lại bệnh án
    </a>
    <button style="width: 36px; height: 36px; border-radius: 50%; border: 1px solid #e2e8f0; background: #fff; color: #475569; display: flex; align-items: center; justify-content: center; position: relative; cursor: pointer;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
        <span style="position: absolute; top: -4px; right: -4px; width: 16px; height: 16px; background: #ef4444; color: #fff; font-size: 0.6rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">3</span>
    </button>
</div>
@endsection

@section('content')
<div class="rx-create-container" style="padding-bottom: 3rem; max-width: 100%; overflow-x: hidden;">

    @if($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; padding: 1rem 1.5rem; margin-bottom: 1.5rem;">
            <ul style="margin: 0; padding-left: 1.5rem; color: #dc2626; font-size: 0.9rem;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- TÓM TẮT BỆNH ÁN --}}
    <div style="border: 1px solid #bfdbfe; border-radius: 0.5rem; padding: 1rem 1.5rem; margin-bottom: 1.5rem; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div style="color: #3b82f6; font-size: 0.8rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; text-transform: uppercase;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
            TÓM TẮT BỆNH ÁN
        </div>
        <div style="display: grid; grid-template-columns: 2fr 3fr 1.5fr 1.5fr; gap: 1.5rem;">
            {{-- Bệnh nhân --}}
            <div style="display: flex; gap: 1rem; border-right: 1px solid #e2e8f0; padding-right: 1rem;">
                <div style="width: 48px; height: 48px; background: #dbeafe; color: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                    👤
                </div>
                <div>
                    <div style="font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 0.4rem; font-size: 0.95rem;">
                        {{ $medicalRecord->patient->full_name }} 
                        <span style="color: #ec4899; font-size: 0.8rem;">{{ $medicalRecord->patient->gender == 'Nữ' ? '♀' : '♂' }}</span>
                    </div>
                    <div style="color: #64748b; font-size: 0.8rem; margin-top: 0.2rem;">{{ $medicalRecord->patient->age }} tuổi ({{ $medicalRecord->patient->date_of_birth ? \Carbon\Carbon::parse($medicalRecord->patient->date_of_birth)->format('d/m/Y') : 'Không rõ' }})</div>
                    <div style="color: #64748b; font-size: 0.8rem; margin-top: 0.1rem;">Mã bệnh án: {{ $medicalRecord->record_code }}</div>
                    <div style="color: #64748b; font-size: 0.8rem; margin-top: 0.1rem;">SĐT: {{ $medicalRecord->patient->phone ?? 'Không có' }}</div>
                </div>
            </div>
            
            {{-- Chẩn đoán --}}
            <div style="border-right: 1px solid #e2e8f0; padding-right: 1rem;">
                <div style="font-weight: 800; color: #475569; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 0.4rem;">Chẩn đoán</div>
                <div style="color: #334155; font-size: 0.85rem; line-height: 1.5;">{{ $medicalRecord->diagnosis }}</div>
            </div>

            {{-- Định hướng --}}
            <div style="border-right: 1px solid #e2e8f0; padding-right: 1rem;">
                <div style="font-weight: 800; color: #475569; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 0.4rem;">Định hướng điều trị</div>
                <div style="margin-top: 0.3rem;">
                    @if($medicalRecord->treatment_direction === 'oral_only')
                        <span style="background: #eef2ff; color: #4f46e5; padding: 0.2rem 0.6rem; border-radius: 2rem; font-size: 0.75rem; font-weight: 700; border: 1px solid #c7d2fe;">Chỉ Thuốc Uống</span>
                    @elseif($medicalRecord->treatment_direction === 'external_only')
                        <span style="background: #fef2f2; color: #dc2626; padding: 0.2rem 0.6rem; border-radius: 2rem; font-size: 0.75rem; font-weight: 700; border: 1px solid #fecaca;">Chỉ Dùng Ngoài</span>
                    @else
                        <span style="background: #f0fdf4; color: #16a34a; padding: 0.2rem 0.6rem; border-radius: 2rem; font-size: 0.75rem; font-weight: 700; border: 1px solid #bbf7d0;">Kết hợp</span>
                    @endif
                </div>
            </div>

            {{-- Dị ứng --}}
            <div>
                <div style="font-weight: 800; color: #475569; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 0.4rem;">Dị ứng</div>
                @if($medicalRecord->allergies)
                    <div style="color: #dc2626; font-size: 0.85rem; font-weight: 700;">{{ $medicalRecord->allergies }}</div>
                @else
                    <div style="color: #16a34a; font-size: 0.85rem; font-weight: 700;">Không ghi nhận dị ứng</div>
                @endif
            </div>
        </div>
    </div>

    {{-- GỢI Ý AI HỖ TRỢ THẦY THUỐC --}}
    @can('use_ai_suggestion')
    @include('admin.records.partials.ai_panel', ['medicalRecord' => $medicalRecord])
    @include('admin.records.partials.ai_js')
    @endcan

    <form action="{{ route('admin.prescriptions.store') }}" method="POST" id="prescriptionForm" onsubmit="prepareFormSubmission(event)">
        @csrf
        <input type="hidden" name="medical_record_id" value="{{ $medicalRecord->id }}">
        
        {{-- Vùng chứa ẩn để append các input trước khi submit --}}
        <div id="hidden-inputs-container"></div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start;">
            
            {{-- CỘT TRÁI: DANH MỤC VỊ THUỐC (CATALOG) --}}
            <div>
                <h4 style="font-size: 1rem; font-weight: 800; color: #3b82f6; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                    1. KÊ THUỐC THANG (UỐNG)
                </h4>
                
                <div style="border: 1px solid #e0e7ff; border-radius: 0.5rem; background: #fff; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <div style="background: #eff6ff; padding: 0.75rem 1rem; border-bottom: 1px solid #e0e7ff; font-weight: 800; color: #3b82f6; font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
                        DANH MỤC VỊ THUỐC
                    </div>
                    
                    <div style="padding: 1rem;">
                        {{-- Search --}}
                        <div style="margin-bottom: 1rem;">
                            <div style="position: relative;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <input type="text" id="herb-search" placeholder="Tìm vị thuốc..." style="width: 100%; padding: 0.5rem 0.5rem 0.5rem 2rem; border: 1px solid #e2e8f0; border-radius: 0.25rem; font-size: 0.85rem;" oninput="filterCatalog()">
                            </div>
                        </div>

                        {{-- Filters (Removed as requested) --}}

                        {{-- Catalog Table --}}
                        <div style="overflow-x: auto; min-height: 180px;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.8rem;">
                                <thead>
                                    <tr style="border-bottom: 2px solid #e2e8f0; color: #475569; font-weight: 800; text-transform: uppercase;">
                                        <th style="text-align: left; padding: 0.5rem;">VỊ THUỐC</th>
                                        <th style="text-align: left; padding: 0.5rem;">NHÓM / TÊN KHÁC</th>
                                        <th style="text-align: center; padding: 0.5rem;">TỒN KHO</th>
                                        <th style="text-align: center; padding: 0.5rem;">ĐƠN VỊ</th>
                                        <th style="text-align: center; padding: 0.5rem;">LIỀU T.KHẢO</th>
                                        <th style="text-align: center; padding: 0.5rem;">THAO TÁC</th>
                                    </tr>
                                </thead>
                                <tbody id="catalog-body">
                                    <!-- Rendered via JS -->
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination Mock --}}
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; color: #64748b; font-size: 0.75rem;">
                            <div id="catalog-info">Hiển thị 1 - 5 của ... vị thuốc</div>
                            <div style="display: flex; gap: 0.2rem;" id="catalog-pagination">
                                <!-- Rendered via JS -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI: ĐƠN THUỐC ĐANG KÊ (CART) --}}
            <div>
                <div style="border: 1px solid #e0e7ff; border-radius: 0.5rem; background: #fff; overflow: hidden; margin-top: 2.1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <div style="background: #eff6ff; padding: 0.75rem 1rem; border-bottom: 1px solid #e0e7ff; font-weight: 800; color: #3b82f6; font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        ĐƠN THUỐC ĐANG KÊ
                    </div>
                    
                    <div style="padding: 1rem;">
                        <div style="margin-bottom: 1rem;">
                            <label style="font-size: 0.75rem; font-weight: 800; color: #475569; display: block; margin-bottom: 0.3rem;">Tên bài thuốc (Hiển thị thay vì liệt kê từng vị)</label>
                            <input type="text" name="prescription_name" value="{{ old('prescription_name') }}" placeholder="Ví dụ: Bát trân thang gia giảm..." style="width: 100%; border: 1px solid #cbd5e1; border-radius: 0.25rem; padding: 0.5rem; font-size: 0.8rem; outline: none;">
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; margin-bottom: 1.5rem;">
                            <div>
                                <label style="font-size: 0.75rem; font-weight: 800; color: #475569; display: block; margin-bottom: 0.3rem;">Số thang thuốc</label>
                                <div style="display: flex; border: 1px solid #cbd5e1; border-radius: 0.25rem; overflow: hidden;">
                                    <button type="button" style="background: #f8fafc; border: none; border-right: 1px solid #cbd5e1; padding: 0 0.75rem; font-weight: bold; color: #475569; cursor: pointer;" onclick="changeDoses(-1)">-</button>
                                    <input type="number" name="num_of_doses" id="num_of_doses" value="{{ old('num_of_doses', 7) }}" style="flex: 1; border: none; padding: 0.5rem; text-align: center; font-weight: 700; outline: none;" min="1" oninput="renderCart()">
                                    <button type="button" style="background: #f8fafc; border: none; border-left: 1px solid #cbd5e1; padding: 0 0.75rem; font-weight: bold; color: #475569; cursor: pointer;" onclick="changeDoses(1)">+</button>
                                </div>
                            </div>
                            <div>
                                <label style="font-size: 0.75rem; font-weight: 800; color: #475569; display: block; margin-bottom: 0.3rem;">Cách sắc / cách uống</label>
                                <input type="text" name="usage_instruction" value="{{ old('usage_instruction', 'Sắc với 1.5 lít nước, còn 600ml, chia 2 lần uống trong ngày') }}" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 0.25rem; padding: 0.5rem; font-size: 0.8rem; outline: none;">
                            </div>
                        </div>

                        <div style="overflow-x: auto; margin-bottom: 1rem; min-height: 140px;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.8rem;">
                                <thead>
                                    <tr style="border-bottom: 2px solid #e2e8f0; color: #475569; font-weight: 800; font-size: 0.7rem; text-transform: uppercase;">
                                        <th style="text-align: center; padding: 0.5rem; width: 40px;">STT</th>
                                        <th style="text-align: left; padding: 0.5rem;">VỊ THUỐC</th>
                                        <th style="text-align: center; padding: 0.5rem; width: 80px;">LIỀU LƯỢNG (g)</th>
                                        <th style="text-align: center; padding: 0.5rem; width: 80px;">SỐ LƯỢNG (g)</th>
                                        <th style="text-align: center; padding: 0.5rem; width: 60px;">ĐƠN VỊ</th>
                                        <th style="text-align: left; padding: 0.5rem;">GHI CHÚ</th>
                                        <th style="text-align: center; padding: 0.5rem; width: 40px;">XÓA</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-body">
                                    <!-- Rendered via JS -->
                                </tbody>
                            </table>
                            <div id="cart-empty" style="text-align: center; color: #94a3b8; font-size: 0.8rem; padding: 2rem 0; display: none;">
                                Đơn thuốc chưa có vị nào.<br>Thêm vị thuốc từ danh mục bên trái.
                            </div>
                        </div>
                        
                        <div style="text-align: center; margin-bottom: 1rem;">
                            <span style="color: #3b82f6; font-size: 0.8rem; font-weight: 600; cursor: pointer;">+ Thêm vị thuốc vào đơn</span>
                        </div>

                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.5rem; padding: 1rem; display: flex; align-items: center; justify-content: flex-start; gap: 3rem; margin-bottom: 1rem;">
                            <div>
                                <div style="font-size: 0.75rem; color: #166534; font-weight: 700; margin-bottom: 0.2rem;">Tổng số vị thuốc</div>
                                <div style="font-size: 1.25rem; font-weight: 800; color: #166534; display: flex; align-items: center; gap: 0.5rem;">
                                    <span id="summary-count">0</span>
                                </div>
                            </div>
                            <div>
                                <div style="font-size: 0.75rem; color: #166534; font-weight: 700; margin-bottom: 0.2rem;">Ước tính lượng xuất kho (g)</div>
                                <div style="font-size: 1.25rem; font-weight: 800; color: #166534;" id="summary-weight">0 g</div>
                            </div>
                        </div>

                        <div>
                            <button type="button" id="btn-apply-ai" style="width: 100%; background: #e0f2fe; color: #0369a1; border: none; padding: 0.75rem; border-radius: 0.25rem; font-weight: 700; font-size: 0.85rem; cursor: pointer; transition: 0.2s;" onclick="applyAI()" onmouseover="this.style.background='#bae6fd'" onmouseout="this.style.background='#e0f2fe'">
                                ✨ Áp dụng gợi ý AI
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MIDDLE SECTION: EXTERNAL PRODUCTS & SERVICES --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 2rem;">
            
            {{-- THUỐC / CHẾ PHẨM DÙNG NGOÀI --}}
            <div>
                <h4 style="font-size: 1rem; font-weight: 800; color: #3b82f6; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"></path></svg>
                    2. THUỐC / CHẾ PHẨM DÙNG NGOÀI
                </h4>

                <div style="border: 1px solid #e0e7ff; border-radius: 0.75rem; background: #fff; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <div id="external-products-container" style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                        {{-- JS Render External Products --}}
                    </div>
                    <div id="external-products-summary" style="margin-top: 1rem; font-size: 0.8rem; color: #1d4ed8; font-weight: 700;">
                        Chưa chọn chế phẩm dùng ngoài.
                    </div>
                </div>
            </div>

            {{-- DỊCH VỤ TRỊ LIỆU --}}
            <div>
                <h4 style="font-size: 1rem; font-weight: 800; color: #3b82f6; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    3. DỊCH VỤ TRỊ LIỆU (TÙY CHỌN)
                </h4>
                
                <div style="margin-bottom: 1rem;">
                    <div style="position: relative;">
                        <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </span>
                        <input type="text" id="service-search" placeholder="Tìm dịch vụ trị liệu..." style="width: 100%; padding: 0.5rem 0.5rem 0.5rem 2.25rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.85rem; color: #334155; outline: none; transition: 0.2s;" oninput="handleServiceSearch(this.value)" onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 2px #eff6ff'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;" id="services-container">
                    {{-- JS Render Services --}}
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;" id="service-pagination">
                    <span style="font-size: 0.8rem; color: #64748b;" id="service-page-info"></span>
                    <div style="display: flex; gap: 0.5rem;" id="service-page-controls">
                    </div>
                </div>

                <div style="margin-top: 1rem; font-size: 0.75rem; color: #3b82f6; display: flex; align-items: center; gap: 0.4rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    Lưu ý: Thông tin gợi ý từ AI chỉ mang tính tham khảo, thầy thuốc chịu trách nhiệm quyết định cuối cùng.
                </div>
            </div>
        </div>

        {{-- LỜI DẶN BÁC SĨ --}}
        <div style="margin-top: 2rem;">
            <h4 style="font-size: 1rem; font-weight: 800; color: #3b82f6; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                4. LỜI DẶN BÁC SĨ
            </h4>
            <div style="border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1.25rem; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="font-weight: 800; font-size: 0.85rem; color: #475569; display: block; margin-bottom: 0.3rem;">Lời dặn chung</label>
                        <textarea name="note" placeholder="Nhập lời dặn cho bệnh nhân..." style="width: 100%; height: 80px; border: 1px solid #cbd5e1; border-radius: 0.25rem; padding: 0.75rem; font-size: 0.85rem; resize: vertical; outline: none;"></textarea>
                    </div>
                    <div>
                        <label style="font-weight: 800; font-size: 0.85rem; color: #475569; display: block; margin-bottom: 0.3rem;">Ghi chú mức tiến triển</label>
                        <textarea name="progress_note" placeholder="Nhập ghi chú mức độ tiến triển của bệnh nhân..." style="width: 100%; height: 80px; border: 1px solid #cbd5e1; border-radius: 0.25rem; padding: 0.75rem; font-size: 0.85rem; resize: vertical; outline: none;"></textarea>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <label style="font-weight: 800; font-size: 0.85rem; color: #475569;">Hẹn tái khám</label>
                    <input type="date" name="follow_up_date" style="border: 1px solid #cbd5e1; border-radius: 0.25rem; padding: 0.4rem 0.6rem; font-size: 0.85rem; color: #475569; outline: none;">
                    
                    <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; color: #64748b; margin-left: 1rem; flex: 1;">
                        <span style="font-weight: 800; color: #475569; white-space: nowrap;">Lưu ý tái khám:</span>
                        <input type="text" name="follow_up_note" placeholder="Nhập lưu ý..." style="border: 1px solid #cbd5e1; border-radius: 0.25rem; padding: 0.4rem 0.6rem; font-size: 0.85rem; color: #475569; outline: none; width: 100%;">
                    </label>

                    <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; color: #64748b; cursor: pointer; margin-left: auto;">
                        <input type="checkbox" name="remind_follow_up" style="width: 16px; height: 16px; accent-color: #3b82f6;"> Nhắc lịch tái khám cho bệnh nhân
                    </label>
                </div>
            </div>
        </div>

        {{-- NÚT LƯU ĐƠN --}}
        <div style="margin-top: 2rem;">
            <button type="submit" style="width: 100%; background: #2563eb; color: #fff; border: none; padding: 1rem; border-radius: 0.5rem; font-weight: 800; font-size: 1.1rem; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2), 0 2px 4px -1px rgba(37, 99, 235, 0.1);" onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                Lưu đơn & tạo phiếu cấp thuốc
            </button>
        </div>

    </form>
</div>

<script>
    const herbsData = @json($herbs->values());
    const extProducts = @json($externalProducts->values());
    const externalProductsData = extProducts.filter(p => !p.usage_route || p.usage_route === 'external');
    let cartItems = [];
    let selectedExternalProducts = [];
    
    // Pagination & Search
    let currentSearch = '';
    const itemsPerPage = 4;
    let currentPage = 1;

    // Helpers
    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
    const colors = ['#f87171', '#fb923c', '#fbbf24', '#4ade80', '#2dd4bf', '#38bdf8', '#818cf8', '#a78bfa', '#e879f9', '#f43f5e'];
    function getColorForLetter(letter) {
        const code = letter.charCodeAt(0) || 0;
        return colors[code % colors.length];
    }

    // Catalog functions
    function filterCatalog() {
        currentSearch = document.getElementById('herb-search').value.toLowerCase();
        currentPage = 1;
        renderCatalog();
    }
    function resetFilters() {
        document.getElementById('herb-search').value = '';
        currentSearch = '';
        currentPage = 1;
        renderCatalog();
    }

    function renderCatalog() {
        const tbody = document.getElementById('catalog-body');
        
        let filtered = herbsData.filter(h => h.name.toLowerCase().includes(currentSearch));
        
        const totalItems = filtered.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;
        if (currentPage > totalPages) currentPage = totalPages;
        
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageItems = filtered.slice(start, end);

        let html = '';
        pageItems.forEach(h => {
            const firstLetter = h.name.charAt(0).toUpperCase();
            const iconColor = getColorForLetter(firstLetter);
            
            let stockBadge = '';
            if (h.total_available_quantity > 50) stockBadge = `<span style="color: #16a34a; font-weight: 700;">Còn hàng</span>`;
            else if (h.total_available_quantity > 0) stockBadge = `<span style="color: #d97706; font-weight: 700;">Sắp hết</span>`;
            else stockBadge = `<span style="color: #dc2626; font-weight: 700;">Hết hàng</span>`;

            // Check if already in cart
            const inCart = cartItems.find(i => i.id === h.id);
            const btnHtml = inCart 
                ? `<button type="button" style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #64748b; padding: 0.3rem 0.6rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 700; cursor: not-allowed;">Đã thêm</button>`
                : `<button type="button" onclick="addToCart(${h.id})" style="background: #fff; border: 1px solid #bfdbfe; color: #3b82f6; padding: 0.3rem 0.6rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 700; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='#fff'">+ Thêm</button>`;

            html += `
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 0.6rem 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: ${iconColor}; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem; flex-shrink: 0;">${firstLetter}</div>
                        <div>
                            <div style="font-weight: 800; color: #1e293b; font-size: 0.85rem;">${escHtml(h.name)}</div>
                            <div style="color: #64748b; font-size: 0.7rem; font-style: italic;">Glycyrrhiza uralensis</div>
                        </div>
                    </div>
                </td>
                <td style="padding: 0.6rem 0.5rem; color: #475569; font-size: 0.8rem; font-weight: 600;">Bổ ích / Điều hòa</td>
                <td style="text-align: center; padding: 0.6rem 0.5rem; font-size: 0.75rem;">${stockBadge}</td>
                <td style="text-align: center; padding: 0.6rem 0.5rem; color: #475569; font-weight: 600; font-size: 0.8rem;">${escHtml(h.unit)}</td>
                <td style="text-align: center; padding: 0.6rem 0.5rem; color: #475569; font-weight: 600; font-size: 0.8rem;">2-6</td>
                <td style="text-align: center; padding: 0.6rem 0.5rem;">${btnHtml}</td>
            </tr>`;
        });
        
        if (pageItems.length === 0) {
            html = `<tr><td colspan="6" style="text-align: center; padding: 2rem; color: #94a3b8;">Không tìm thấy vị thuốc phù hợp.</td></tr>`;
        }
        
        tbody.innerHTML = html;

        // Render Pagination Info
        const info = document.getElementById('catalog-info');
        if (totalItems > 0) {
            info.innerHTML = `Hiển thị ${start + 1} – ${Math.min(end, totalItems)} của ${totalItems} vị thuốc`;
        } else {
            info.innerHTML = `Hiển thị 0 vị thuốc`;
        }

        // Render Pagination Buttons
        const pag = document.getElementById('catalog-pagination');
        let pagHtml = '';
        if (totalPages > 1) {
            pagHtml += `<button type="button" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} style="border: none; background: transparent; color: ${currentPage === 1 ? '#cbd5e1' : '#3b82f6'}; cursor: ${currentPage === 1 ? 'default' : 'pointer'}; padding: 0.2rem 0.4rem; font-weight: bold;">&lt;</button>`;
            
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    if (i === currentPage) {
                        pagHtml += `<button type="button" style="border: none; background: #e0e7ff; color: #3b82f6; border-radius: 0.25rem; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.75rem;">${i}</button>`;
                    } else {
                        pagHtml += `<button type="button" onclick="goToPage(${i})" style="border: none; background: transparent; color: #64748b; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.75rem;" onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='#64748b'">${i}</button>`;
                    }
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    pagHtml += `<span style="color: #94a3b8; padding: 0 0.2rem;">...</span>`;
                }
            }
            
            pagHtml += `<button type="button" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} style="border: none; background: transparent; color: ${currentPage === totalPages ? '#cbd5e1' : '#3b82f6'}; cursor: ${currentPage === totalPages ? 'default' : 'pointer'}; padding: 0.2rem 0.4rem; font-weight: bold;">&gt;</button>`;
        }
        pag.innerHTML = pagHtml;
    }

    function goToPage(p) {
        currentPage = p;
        renderCatalog();
    }

    // Cart Functions
    function changeDoses(val) {
        const input = document.getElementById('num_of_doses');
        let current = parseInt(input.value) || 1;
        current += val;
        if (current < 1) current = 1;
        input.value = current;
        renderCart();
    }

    function addToCart(id) {
        if (cartItems.find(i => i.id === id)) return;
        const herb = herbsData.find(h => h.id === id);
        if (!herb) return;

        cartItems.push({
            id: herb.id,
            name: herb.name,
            unit: herb.unit,
            dose: 10,
            note: ''
        });
        renderCart();
        renderCatalog(); // Update button state
    }

    function removeFromCart(index) {
        cartItems.splice(index, 1);
        renderCart();
        renderCatalog(); // Update button state
    }

    function updateCartItem(index, field, value) {
        if (cartItems[index]) {
            if (field === 'dose') {
                cartItems[index][field] = parseFloat(value) || 0;
                renderCart(); // re-render to update total per item and global total
            } else {
                cartItems[index][field] = value;
            }
        }
    }

    function renderCart() {
        const tbody = document.getElementById('cart-body');
        const emptyDiv = document.getElementById('cart-empty');
        const numDoses = parseFloat(document.getElementById('num_of_doses').value) || 1;
        
        let html = '';
        let totalWeight = 0;

        if (cartItems.length === 0) {
            tbody.innerHTML = '';
            emptyDiv.style.display = 'block';
        } else {
            emptyDiv.style.display = 'none';
            cartItems.forEach((item, index) => {
                const rowTotal = item.dose * numDoses;
                totalWeight += rowTotal;

                html += `
                <tr style="border-bottom: 1px dashed #e2e8f0;">
                    <td style="text-align: center; padding: 0.75rem 0.5rem; color: #64748b; font-weight: 700;">${index + 1}</td>
                    <td style="padding: 0.75rem 0.5rem; font-weight: 800; color: #334155;">${escHtml(item.name)}</td>
                    <td style="text-align: center; padding: 0.75rem 0.5rem;">
                        <input type="number" step="0.1" value="${item.dose}" onchange="updateCartItem(${index}, 'dose', this.value)" style="width: 100%; text-align: center; border: 1px solid #cbd5e1; border-radius: 0.25rem; padding: 0.3rem; font-size: 0.85rem; font-weight: 700; color: #0f172a; outline: none;">
                    </td>
                    <td style="text-align: center; padding: 0.75rem 0.5rem; color: #475569; font-weight: 700; font-size: 0.9rem;">${rowTotal}</td>
                    <td style="text-align: center; padding: 0.75rem 0.5rem; color: #64748b; font-weight: 600;">${item.unit}</td>
                    <td style="padding: 0.75rem 0.5rem;">
                        <input type="text" value="${escHtml(item.note)}" onchange="updateCartItem(${index}, 'note', this.value)" style="width: 100%; border: none; background: transparent; font-size: 0.8rem; color: #475569; outline: none;" placeholder="Ghi chú">
                    </td>
                    <td style="text-align: center; padding: 0.75rem 0.5rem;">
                        <button type="button" onclick="removeFromCart(${index})" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 0.2rem;" title="Xóa">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                        </button>
                    </td>
                </tr>`;
            });
            tbody.innerHTML = html;
        }

        document.getElementById('summary-count').innerText = cartItems.length;
        document.getElementById('summary-weight').innerText = totalWeight.toFixed(1) + ' g';
    }

    // External Products Functions
    function getExternalProductStockBadge(product) {
        const availableQuantity = Number(product.total_available_quantity || 0);

        if (availableQuantity > 0) {
            return `<span style="color: #1d4ed8; font-weight: 800;">Còn ${availableQuantity.toLocaleString('vi-VN')} ${escHtml(product.unit || '')}</span>`;
        }

        return `<span style="color: #dc2626; font-weight: 800;">Hết hàng</span>`;
    }

    function toggleExternalProduct(id) {
        const existingIndex = selectedExternalProducts.findIndex(item => item.id === id);

        if (existingIndex >= 0) {
            selectedExternalProducts.splice(existingIndex, 1);
        } else {
            const product = externalProductsData.find(item => item.id === id);
            if (!product) return;

            selectedExternalProducts.push({
                id: product.id,
                name: product.name,
                item_type: product.item_type || 'packaged_product',
                unit: product.unit || 'đơn vị',
                quantity: 1,
                dosage: 'Dùng ngoài theo hướng dẫn của thầy thuốc. Không được uống.',
            });
        }

        renderExternalProducts();
    }

    function updateExternalProductQuantity(id, value) {
        const selected = selectedExternalProducts.find(item => item.id === id);
        if (!selected) return;

        const quantity = parseFloat(value);
        selected.quantity = quantity > 0 ? quantity : 1;
        renderExternalProducts();
    }

    function renderExternalProducts() {
        const container = document.getElementById('external-products-container');
        const summary = document.getElementById('external-products-summary');
        if (!container) return;

        if (externalProductsData.length === 0) {
            container.innerHTML = `<div style="grid-column: 1 / -1; color: #94a3b8; text-align: center; padding: 1.25rem; font-size: 0.85rem; border: 1px dashed #cbd5e1; border-radius: 0.5rem;">Chưa có thuốc/chế phẩm dùng ngoài trong kho.</div>`;
            if (summary) summary.innerText = 'Chưa chọn chế phẩm dùng ngoài.';
            return;
        }

        let html = '';
        externalProductsData.forEach(product => {
            const selected = selectedExternalProducts.find(item => item.id === product.id);
            const checked = selected ? 'checked' : '';
            const desc = product.default_instruction || product.therapeutic_effect || product.description || 'Dùng ngoài theo chỉ định';
            const borderColor = selected ? '#3b82f6' : '#e2e8f0';
            const bgColor = selected ? '#eff6ff' : '#fff';

            html += `
            <div style="border: 1px solid ${borderColor}; border-radius: 0.6rem; padding: 0.85rem; background: ${bgColor}; transition: 0.2s; min-height: 116px;">
                <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
                    <div style="width: 36px; height: 36px; border-radius: 0.5rem; background: #dbeafe; color: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">🧴</div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; gap: 0.5rem; align-items: flex-start;">
                            <div style="font-weight: 850; color: #1e293b; font-size: 0.86rem;">${escHtml(product.name)}</div>
                            <input type="checkbox" onchange="toggleExternalProduct(${product.id})" ${checked} style="width: 17px; height: 17px; accent-color: #3b82f6; cursor: pointer; flex-shrink: 0;">
                        </div>
                        <div style="color: #64748b; font-size: 0.74rem; margin-top: 0.2rem; line-height: 1.35;">${escHtml(desc)}</div>
                        <div style="font-size: 0.72rem; margin-top: 0.35rem;">${getExternalProductStockBadge(product)}</div>
                    </div>
                </div>
                ${selected ? `
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px dashed #bfdbfe;">
                    <label style="font-size: 0.74rem; font-weight: 800; color: #1e40af; white-space: nowrap;">Số lượng</label>
                    <input type="number" min="0.1" step="0.1" value="${selected.quantity}" onchange="updateExternalProductQuantity(${product.id}, this.value)" style="width: 88px; border: 1px solid #93c5fd; border-radius: 0.35rem; padding: 0.35rem 0.45rem; font-weight: 800; color: #1e3a8a; outline: none;">
                    <span style="font-size: 0.78rem; color: #1e40af; font-weight: 800;">${escHtml(selected.unit)}</span>
                </div>` : ''}
            </div>`;
        });

        container.innerHTML = html;

        if (summary) {
            const totalSelected = selectedExternalProducts.length;
            summary.innerText = totalSelected > 0
                ? `Đã chọn ${totalSelected} chế phẩm dùng ngoài.`
                : 'Chưa chọn chế phẩm dùng ngoài.';
        }
    }

    // Services Variables
    let currentServicePage = 1;
    const SERVICES_PER_PAGE = 4;
    const therapyServicesData = @json($therapyServices->values());
    let filteredServicesData = therapyServicesData.map(ts => ({
        name: ts.name,
        desc: ts.default_instruction || "Dịch vụ trị liệu chuyên môn"
    }));
    let selectedServices = [];

    function handleServiceSearch(query) {
        query = query.toLowerCase();
        filteredServicesData = therapyServicesData.map(ts => ({
            name: ts.name,
            desc: ts.default_instruction || "Dịch vụ trị liệu chuyên môn"
        })).filter(s => s.name.toLowerCase().includes(query) || s.desc.toLowerCase().includes(query));
        currentServicePage = 1;
        renderServices();
    }

    function changeServicePage(page) {
        currentServicePage = page;
        renderServices();
    }

    function toggleService(name) {
        if (selectedServices.includes(name)) {
            selectedServices = selectedServices.filter(n => n !== name);
        } else {
            selectedServices.push(name);
        }
        renderServices();
    }

    // Services Render
    function renderServices() {
        const container = document.getElementById('services-container');
        
        const totalItems = filteredServicesData.length;
        const totalPages = Math.ceil(totalItems / SERVICES_PER_PAGE);
        
        if (currentServicePage > totalPages && totalPages > 0) currentServicePage = totalPages;
        if (totalPages === 0) currentServicePage = 1;
        
        const start = (currentServicePage - 1) * SERVICES_PER_PAGE;
        const end = start + SERVICES_PER_PAGE;
        const pageItems = filteredServicesData.slice(start, end);

        let html = '';
        pageItems.forEach((s, idx) => {
            const isChecked = selectedServices.includes(s.name) ? 'checked' : '';
            html += `
            <label style="display: flex; align-items: flex-start; gap: 0.75rem; border: 1px solid ${isChecked ? '#3b82f6' : '#e2e8f0'}; border-radius: 0.5rem; padding: 0.75rem; cursor: pointer; transition: 0.2s; background: ${isChecked ? '#eff6ff' : '#fff'};" onmouseover="this.style.borderColor='#3b82f6'" onmouseout="this.style.borderColor='${isChecked ? '#3b82f6' : '#e2e8f0'}'">
                <input type="checkbox" onchange="toggleService('${escHtml(s.name)}')" ${isChecked} style="margin-top: 0.2rem; width: 16px; height: 16px; accent-color: #3b82f6;">
                <div>
                    <div style="font-weight: 800; color: #1e3a8a; font-size: 0.85rem;">${s.name}</div>
                    <div style="color: #64748b; font-size: 0.75rem; margin-top: 0.1rem;">${s.desc}</div>
                </div>
            </label>`;
        });
        
        if (pageItems.length === 0) {
            html = `<div style="grid-column: span 2; text-align: center; color: #94a3b8; padding: 1rem; font-size: 0.85rem;">Không tìm thấy dịch vụ nào.</div>`;
        }
        
        container.innerHTML = html;
        renderServicePagination(totalItems, totalPages);
    }

    function renderServicePagination(totalItems, totalPages) {
        document.getElementById('service-page-info').innerText = totalItems > 0 
            ? `Hiển thị ${((currentServicePage - 1) * SERVICES_PER_PAGE) + 1} - ${Math.min(currentServicePage * SERVICES_PER_PAGE, totalItems)} của ${totalItems}`
            : 'Không có dữ liệu';
            
        let controls = '';
        if (totalPages > 1) {
            controls += `<button type="button" ${currentServicePage === 1 ? 'disabled' : ''} onclick="changeServicePage(${currentServicePage - 1})" style="border: 1px solid #e2e8f0; background: #fff; border-radius: 0.25rem; padding: 0.2rem 0.5rem; cursor: ${currentServicePage === 1 ? 'not-allowed' : 'pointer'}; opacity: ${currentServicePage === 1 ? '0.5' : '1'}">&lt;</button>`;
            
            for(let i=1; i<=totalPages; i++) {
                if (i === currentServicePage) {
                    controls += `<button type="button" style="border: none; background: #3b82f6; color: #fff; border-radius: 0.25rem; padding: 0.2rem 0.5rem; font-weight: 700;">${i}</button>`;
                } else {
                    controls += `<button type="button" onclick="changeServicePage(${i})" style="border: 1px solid #e2e8f0; background: #fff; border-radius: 0.25rem; padding: 0.2rem 0.5rem; cursor: pointer;">${i}</button>`;
                }
            }
            
            controls += `<button type="button" ${currentServicePage === totalPages ? 'disabled' : ''} onclick="changeServicePage(${currentServicePage + 1})" style="border: 1px solid #e2e8f0; background: #fff; border-radius: 0.25rem; padding: 0.2rem 0.5rem; cursor: ${currentServicePage === totalPages ? 'not-allowed' : 'pointer'}; opacity: ${currentServicePage === totalPages ? '0.5' : '1'}">&gt;</button>`;
        }
        document.getElementById('service-page-controls').innerHTML = controls;
    }

    // AI Integration
    function applyAI() {
        if (!window.aiSuggestionsData) {
            window.showToastMessage('Chưa có dữ liệu AI. Vui lòng lấy gợi ý AI trước.', 'warning');
            return;
        }
        
        const aiHerbs = window.aiSuggestionsData.oral_herbs || [];
        if (aiHerbs.length === 0) {
            window.showToastMessage('AI không gợi ý vị thuốc uống nào.', 'warning');
            return;
        }

        let addedCount = 0;
        aiHerbs.forEach(aiH => {
            // Find in herbsData
            const herb = herbsData.find(h => h.name.toLowerCase() === aiH.herb_name.toLowerCase());
            if (herb && !cartItems.find(i => i.id === herb.id)) {
                cartItems.push({
                    id: herb.id,
                    name: herb.name,
                    unit: herb.unit,
                    dose: 10, // AI doesn't give dose, default to 10
                    note: aiH.usage_note || ''
                });
                addedCount++;
            }
        });

        if (addedCount > 0) {
            renderCart();
            renderCatalog();
            window.showToastMessage(`Đã thêm ${addedCount} vị thuốc theo gợi ý AI.`, 'success');
        } else {
            window.showToastMessage('Các vị thuốc AI gợi ý đã có trong đơn hoặc không tìm thấy trong kho.', 'warning');
        }
    }

    // Prepare Form on Submit
    function prepareFormSubmission(e) {
        if (cartItems.length === 0) {
            const hasExternalProducts = selectedExternalProducts.length > 0;
            const hasServices = selectedServices.length > 0;
            if (!hasExternalProducts && !hasServices) {
                e.preventDefault();
                window.showToastMessage('Vui lòng kê ít nhất một vị thuốc, chế phẩm dùng ngoài hoặc chọn một dịch vụ!', 'error');
                return false;
            }
        }

        const container = document.getElementById('hidden-inputs-container');
        container.innerHTML = ''; // clear

        let globalIndex = 0;
        
        // Append herbs
        cartItems.forEach(item => {
            container.innerHTML += `
                <input type="hidden" name="items[${globalIndex}][item_type]" value="herb">
                <input type="hidden" name="items[${globalIndex}][inventory_item_id]" value="${item.id}">
                <input type="hidden" name="items[${globalIndex}][quantity_per_dose]" value="${item.dose}">
                <input type="hidden" name="items[${globalIndex}][unit]" value="${item.unit}">
                <input type="hidden" name="items[${globalIndex}][note]" value="${escHtml(item.note)}">
            `;
            globalIndex++;
        });

        // Append external products
        selectedExternalProducts.forEach(item => {
            container.innerHTML += `
                <input type="hidden" name="items[${globalIndex}][item_type]" value="${escHtml(item.item_type || 'packaged_product')}">
                <input type="hidden" name="items[${globalIndex}][inventory_item_id]" value="${item.id}">
                <input type="hidden" name="items[${globalIndex}][quantity_per_dose]" value="${item.quantity}">
                <input type="hidden" name="items[${globalIndex}][unit]" value="${escHtml(item.unit)}">
                <input type="hidden" name="items[${globalIndex}][dosage]" value="${escHtml(item.dosage)}">
            `;
            globalIndex++;
        });

        // Append services
        selectedServices.forEach(name => {
            container.innerHTML += `
                <input type="hidden" name="items[${globalIndex}][item_type]" value="therapy_service">
                <input type="hidden" name="items[${globalIndex}][custom_name]" value="${escHtml(name)}">
                <input type="hidden" name="items[${globalIndex}][sessions]" value="1">
            `;
            globalIndex++;
        });
    }

    // Init
    document.addEventListener("DOMContentLoaded", function() {
        renderCatalog();
        renderCart();
        renderExternalProducts();
        renderServices();
    });

</script>
@endsection
