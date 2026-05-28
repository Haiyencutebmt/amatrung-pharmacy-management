@extends('layouts.admin')

@section('title', 'Dashboard — AmaTrung')

@section('header-left')
    <h1 style="margin: 0; font-size: 1.75rem; font-weight: 800; color: #1e3a5f;">Hệ Thống Quản Trị</h1>
@endsection

@section('content')
<style>
    .dashboard-container {
        font-family: 'Inter', sans-serif;
        color: #1e293b;
    }
    
    .metric-card {
        background: #fff;
        border-radius: 1rem;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border: 1px solid #f1f5f9;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
    }
    
    .icon-box {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .metric-info h4 {
        margin: 0;
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
    }
    .metric-info .value {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1e3a8a;
        margin: 0.25rem 0;
        line-height: 1;
    }
    .metric-info .sub-text {
        font-size: 0.75rem;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .trend-up {
        color: #10b981;
    }
    .trend-down {
        color: #ef4444;
    }
    .trend-warning {
        color: #f59e0b;
    }
    
    .dashboard-section {
        background: #fff;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid #f1f5f9;
        margin-bottom: 1.5rem;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    .section-subtitle {
        font-size: 0.8rem;
        color: #94a3b8;
        margin-top: 0.2rem;
    }
    
    .link-all {
        font-size: 0.85rem;
        color: #3b82f6;
        text-decoration: none;
        font-weight: 600;
    }
    .link-all:hover {
        text-decoration: underline;
    }
    
    .quick-action-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.25rem 1rem;
        min-height: 150px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        text-align: center;
        text-decoration: none;
        transition: all 0.2s;
        position: relative;
    }
    .quick-action-card:hover {
        background: #fff;
        border-color: #cbd5e1;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .quick-action-card > .icon-box {
        width: 48px !important;
        height: 48px !important;
        flex-shrink: 0;
    }
    .quick-action-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.25rem;
        min-height: 48px;
    }
    .quick-action-title {
        font-weight: 700;
        font-size: 0.9rem;
        color: #1e293b;
        line-height: 1.35;
    }
    .quick-action-desc {
        font-size: 0.75rem;
        color: #64748b;
        line-height: 1.45;
    }
    .quick-action-card .arrow {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .appointment-item, .activity-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .appointment-item:last-child, .activity-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .appointment-time {
        font-weight: 700;
        color: #3b82f6;
        font-size: 0.95rem;
        min-width: 50px;
    }
    
    .table-custom {
        width: 100%;
        border-collapse: collapse;
    }
    .table-custom th {
        text-align: left;
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 600;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .table-custom td {
        padding: 1rem 0;
        font-size: 0.9rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-custom tr:last-child td {
        border-bottom: none;
    }
</style>

<div class="dashboard-container">
    {{-- 1. Top Metrics --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
        
        <div class="metric-card">
            <div class="icon-box" style="background: #eff6ff; color: #3b82f6;">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="metric-info">
                <h4>Bệnh nhân</h4>
                <div class="value">{{ number_format($stats['patients']) }}</div>
                <div class="sub-text">Tổng số bệnh nhân <svg class="trend-up" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></div>
            </div>
        </div>
        
        <div class="metric-card">
            <div class="icon-box" style="background: #ecfdf5; color: #10b981;">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 2a2 2 0 0 0-2 2v5H4a2 2 0 0 0-2 2v2c0 1.1.9 2 2 2h5v5c0 1.1.9 2 2 2h2a2 2 0 0 0 2-2v-5h5a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2h-5V4a2 2 0 0 0-2-2h-2z" stroke="none" fill="currentColor" opacity="0.2"/><path d="M4.8 2.3A10.3 10.3 0 1 0 21.7 19.2"/><path d="M14 2h7v7"/><path d="M11 13 21 3"/></svg>
            </div>
            <div class="metric-info">
                <h4>Lượt khám hôm nay</h4>
                <div class="value">{{ number_format($stats['visits_today']) }}</div>
                <div class="sub-text">
                    @php
                        $diff = $stats['visits_today'] - $stats['visits_yesterday'];
                    @endphp
                    So với hôm qua 
                    @if($diff >= 0)
                        <svg class="trend-up" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                    @else
                        <svg class="trend-down" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="metric-card">
            <div class="icon-box" style="background: #f5f3ff; color: #8b5cf6;">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M9 14h6"/><path d="M9 10h6"/><path d="M9 18h6"/></svg>
            </div>
            <div class="metric-info">
                <h4>Đơn điều trị</h4>
                <div class="value">{{ number_format($stats['prescriptions']) }}</div>
                <div class="sub-text">Tổng đơn đã tạo <svg class="trend-up" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></div>
            </div>
        </div>

        <div class="metric-card">
            <div class="icon-box" style="background: #fff7ed; color: #ea580c;">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="M12 8v4"/><path d="M12 16h.01"/><path d="M8 12c0 4.4 3.6 8 8 8"/></svg>
            </div>
            <div class="metric-info">
                <h4>Dược liệu sắp hết</h4>
                <div class="value">{{ number_format($stats['herbs_low_count']) }}</div>
                <div class="sub-text trend-down">Cần bổ sung sớm <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
            </div>
        </div>
        
    </div>

    {{-- Main Grid --}}
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        
        {{-- LEFT COLUMN --}}
        <div>
            {{-- Biểu đồ tăng trưởng --}}
            <div class="dashboard-section" style="padding-bottom: 0;">
                <div class="section-header">
                    <div>
                        <h3 class="section-title">Biểu đồ tăng trưởng</h3>
                        <p class="section-subtitle">Thống kê lượt khám & bệnh nhân mới</p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; justify-content: flex-end;">
                        <span style="border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0.4rem 0.75rem; font-size: 0.8rem; font-weight: 700; color: #475569; display: flex; align-items: center; gap: 0.5rem; background: #fff;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            {{ $growthChart['title'] }}
                        </span>
                        <a href="{{ route('admin.dashboard', ['chart_period' => 'year', 'chart_month' => $growthChart['month']]) }}"
                           style="text-decoration: none; border: 1px solid {{ $growthChart['period'] === 'year' ? '#3b82f6' : '#e2e8f0' }}; border-radius: 0.5rem; padding: 0.4rem 0.75rem; font-size: 0.8rem; font-weight: 800; color: {{ $growthChart['period'] === 'year' ? '#fff' : '#475569' }}; background: {{ $growthChart['period'] === 'year' ? '#3b82f6' : '#fff' }};">
                            Theo năm
                        </a>
                        <a href="{{ route('admin.dashboard', ['chart_period' => 'month', 'chart_month' => $growthChart['month']]) }}"
                           style="text-decoration: none; border: 1px solid {{ $growthChart['period'] === 'month' ? '#3b82f6' : '#e2e8f0' }}; border-radius: 0.5rem; padding: 0.4rem 0.75rem; font-size: 0.8rem; font-weight: 800; color: {{ $growthChart['period'] === 'month' ? '#fff' : '#475569' }}; background: {{ $growthChart['period'] === 'month' ? '#3b82f6' : '#fff' }};">
                            Theo tháng
                        </a>
                        <form action="{{ route('admin.dashboard') }}" method="GET" style="margin: 0;">
                            <input type="hidden" name="chart_period" value="month">
                            <input type="month" name="chart_month" value="{{ $growthChart['month'] }}" onchange="this.form.submit()" style="height: 34px; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0 0.55rem; font-size: 0.8rem; font-weight: 700; color: #475569; outline: none; background: #fff;">
                        </form>
                    </div>
                </div>

                @php
                    $chartWidth = 800;
                    $chartHeight = 200;
                    $chartItems = collect($growthChart['items'])->values();
                    $chartCeiling = max(1, (int) $growthChart['ceiling']);
                    $chartStepX = $chartItems->count() > 1 ? $chartWidth / ($chartItems->count() - 1) : 0;
                    $chartPoint = function ($value, $index) use ($chartHeight, $chartStepX, $chartCeiling) {
                        $x = round($chartStepX * $index, 2);
                        $y = round($chartHeight - (((int) $value / $chartCeiling) * $chartHeight), 2);

                        return $x . ',' . $y;
                    };
                    $visitPoints = $chartItems->map(fn ($item, $index) => $chartPoint($item['visits'], $index))->implode(' ');
                    $patientPoints = $chartItems->map(fn ($item, $index) => $chartPoint($item['patients'], $index))->implode(' ');
                    $buildSmoothPath = function ($items, $key) use ($chartPoint, $chartHeight) {
                        $points = $items->map(function ($item, $index) use ($chartPoint, $key) {
                            [$x, $y] = explode(',', $chartPoint($item[$key], $index));

                            return ['x' => (float) $x, 'y' => (float) $y];
                        })->values();

                        if ($points->isEmpty()) {
                            return '';
                        }

                        $formatPoint = fn ($x, $y) => round($x, 2) . ',' . round(max(0, min($chartHeight, $y)), 2);
                        $path = 'M ' . $formatPoint($points[0]['x'], $points[0]['y']);

                        for ($index = 0; $index < $points->count() - 1; $index++) {
                            $previous = $points[max(0, $index - 1)];
                            $current = $points[$index];
                            $next = $points[$index + 1];
                            $afterNext = $points[min($points->count() - 1, $index + 2)];

                            $control1X = $current['x'] + (($next['x'] - $previous['x']) / 6);
                            $control1Y = $current['y'] + (($next['y'] - $previous['y']) / 6);
                            $control2X = $next['x'] - (($afterNext['x'] - $current['x']) / 6);
                            $control2Y = $next['y'] - (($afterNext['y'] - $current['y']) / 6);

                            $path .= ' C '
                                . $formatPoint($control1X, $control1Y) . ' '
                                . $formatPoint($control2X, $control2Y) . ' '
                                . $formatPoint($next['x'], $next['y']);
                        }

                        return $path;
                    };
                    $visitPath = $buildSmoothPath($chartItems, 'visits');
                    $patientPath = $buildSmoothPath($chartItems, 'patients');
                    $visitAreaPath = $visitPath . ' L ' . $chartWidth . ',' . $chartHeight . ' L 0,' . $chartHeight . ' Z';
                @endphp

                {{-- Chart Area --}}
                <div style="position: relative; height: 250px; display: flex; flex-direction: column;">
                    <!-- Y Axis Labels -->
                    <div style="position: absolute; left: 0; top: 0; bottom: 40px; display: flex; flex-direction: column; justify-content: space-between; font-size: 0.75rem; color: #94a3b8; align-items: flex-end; width: 30px;">
                        @foreach($growthChart['y_axis'] as $label)
                            <span>{{ number_format($label) }}</span>
                        @endforeach
                    </div>
                    <!-- Chart Graphic -->
                    <div style="flex-grow: 1; margin-left: 40px; margin-bottom: 40px; position: relative; border-bottom: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0;">
                        <!-- Grid lines -->
                        <div style="position: absolute; width: 100%; height: 20%; border-top: 1px dashed #f1f5f9; top: 20%;"></div>
                        <div style="position: absolute; width: 100%; height: 20%; border-top: 1px dashed #f1f5f9; top: 40%;"></div>
                        <div style="position: absolute; width: 100%; height: 20%; border-top: 1px dashed #f1f5f9; top: 60%;"></div>
                        <div style="position: absolute; width: 100%; height: 20%; border-top: 1px dashed #f1f5f9; top: 80%;"></div>
                        
                        <svg viewBox="0 0 800 200" style="width: 100%; height: 100%;" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="gradBlue" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" style="stop-color:rgba(59, 130, 246, 0.3);stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:rgba(59, 130, 246, 0);stop-opacity:1" />
                                </linearGradient>
                            </defs>
                            <path d="{{ $visitAreaPath }}" fill="url(#gradBlue)" />
                            <path d="{{ $visitPath }}" fill="none" stroke="#3b82f6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="{{ $patientPath }}" fill="none" stroke="#10b981" stroke-width="2.5" stroke-dasharray="6,4" stroke-linecap="round" stroke-linejoin="round" />

                            @foreach($chartItems as $index => $item)
                                @php
                                    $pointLabel = $growthChart['period'] === 'month' ? 'Ngày ' . $item['label'] : $item['label'];
                                    [$visitX, $visitY] = explode(',', $chartPoint($item['visits'], $index));
                                    [$patientX, $patientY] = explode(',', $chartPoint($item['patients'], $index));
                                @endphp
                                <circle cx="{{ $visitX }}" cy="{{ $visitY }}" r="3.5" fill="#3b82f6">
                                    <title>{{ $pointLabel }} - Lượt khám: {{ number_format($item['visits']) }}</title>
                                </circle>
                                <circle cx="{{ $patientX }}" cy="{{ $patientY }}" r="3.5" fill="#10b981">
                                    <title>{{ $pointLabel }} - Bệnh nhân mới: {{ number_format($item['patients']) }}</title>
                                </circle>
                            @endforeach
                        </svg>

                        <!-- X Axis Labels -->
                        <div style="position: absolute; left: 0; right: 0; bottom: -25px; display: grid; grid-template-columns: repeat({{ $chartItems->count() }}, minmax(0, 1fr)); font-size: 0.72rem; color: #94a3b8; padding: 0 10px;">
                            @foreach($chartItems as $item)
                                @php
                                    $hideMonthlyLabel = $growthChart['period'] === 'month'
                                        && !($loop->first || $loop->last || $loop->iteration % 5 === 0);
                                @endphp
                                <span style="text-align: center; {{ $hideMonthlyLabel ? 'visibility: hidden;' : '' }}">{{ $item['label'] }}</span>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Legend -->
                    <div style="display: flex; gap: 1.5rem; font-size: 0.75rem; font-weight: 600; color: #64748b; margin-left: 40px;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6;"></span> Lượt khám
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span> Bệnh nhân mới
                        </div>
                    </div>
                </div>
            </div>

            {{-- Thao tác nhanh --}}
            <div style="margin-bottom: 1.5rem;">
                <h3 class="section-title" style="margin-bottom: 1rem;">Thao tác nhanh</h3>
                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem;">
                    
                    <a href="{{ route('admin.patients.index', ['open_create' => 1]) }}" class="quick-action-card">
                        <div class="icon-box" style="background: #eff6ff; color: #3b82f6; width: 40px; height: 40px; flex-shrink: 0;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                        </div>
                        <div class="quick-action-content">
                            <div class="quick-action-title">Thêm bệnh nhân</div>
                            <div class="quick-action-desc">Đăng ký bệnh nhân mới</div>
                        </div>
                        <div class="arrow" style="background: #eff6ff; color: #3b82f6;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </div>
                    </a>

                    <a href="{{ route('admin.inventory.index') }}" class="quick-action-card">
                        <div class="icon-box" style="background: #fff7ed; color: #ea580c; width: 40px; height: 40px; flex-shrink: 0;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.2 7.8l-7.7 7.7-4-4-5.7 5.7"/><path d="M15 7h6v6"/></svg>
                        </div>
                        <div class="quick-action-content">
                            <div class="quick-action-title">Quản lý dược liệu</div>
                            <div class="quick-action-desc">Quản lý kho dược liệu</div>
                        </div>
                        <div class="arrow" style="background: #fff7ed; color: #ea580c;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </div>
                    </a>

                </div>
            </div>

            {{-- Dược liệu cần chú ý --}}
            <div class="dashboard-section">
                <div class="section-header">
                    <h3 class="section-title">Dược liệu cần chú ý</h3>
                    <a href="{{ route('admin.inventory.index') }}" class="link-all">Xem tất cả</a>
                </div>
                
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Dược liệu</th>
                            <th>Tồn kho</th>
                            <th>Tối thiểu</th>
                            <th style="text-align: right;">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowHerbsList as $herb)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 32px; height: 32px; background: #fff7ed; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                            🌿
                                        </div>
                                        <span style="font-weight: 600; color: #334155;">{{ $herb->name }}</span>
                                    </div>
                                </td>
                                <td>{{ number_format($herb->total_available_quantity) }}{{ $herb->unit ?? 'g' }}</td>
                                <td>500{{ $herb->unit ?? 'g' }}</td>
                                <td style="text-align: right;">
                                    <span style="display: inline-block; padding: 0.25rem 0.75rem; background: #fff7ed; color: #ea580c; border-radius: 999px; font-size: 0.75rem; font-weight: 700;">
                                        Sắp hết
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: #94a3b8; padding: 2rem 0;">Không có dược liệu nào sắp hết.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
        </div>

        {{-- RIGHT COLUMN --}}
        <div>
            {{-- Lịch hẹn hôm nay --}}
            <div class="dashboard-section">
                <div class="section-header">
                    <h3 class="section-title">Lịch hẹn hôm nay</h3>
                    <a href="{{ route('admin.appointments.index') }}" class="link-all">Xem tất cả</a>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    @forelse($appointmentsToday as $appt)
                        @php
                            $badgeColor = '#3b82f6';
                            $badgeBg = '#eff6ff';
                            if ($appt->status == 'pending') {
                                $badgeColor = '#ea580c';
                                $badgeBg = '#fff7ed';
                            } elseif ($appt->status == 'completed') {
                                $badgeColor = '#10b981';
                                $badgeBg = '#ecfdf5';
                            }
                        @endphp
                        <div class="appointment-item">
                            <div class="appointment-time">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('H:i') }}</div>
                            <div style="flex-grow: 1;">
                                <div style="font-weight: 700; font-size: 0.9rem; color: #1e293b;">{{ $appt->patient->full_name ?? 'Ẩn danh' }}</div>
                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">{{ $appt->reason }}</div>
                            </div>
                            <div>
                                <span style="display: inline-block; padding: 0.25rem 0.6rem; background: {{ $badgeBg }}; color: {{ $badgeColor }}; border-radius: 999px; font-size: 0.7rem; font-weight: 700; white-space: nowrap;">
                                    {{ $appt->status_label }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; color: #94a3b8; padding: 2rem 0; font-size: 0.85rem;">Không có lịch hẹn nào hôm nay.</div>
                    @endforelse
                </div>
            </div>

            {{-- Hoạt động gần đây --}}
            <div class="dashboard-section">
                <div class="section-header">
                    <h3 class="section-title">Hoạt động gần đây</h3>
                    <a href="#" class="link-all">Xem tất cả</a>
                </div>
                
                <div style="display: flex; flex-direction: column;">
                    @forelse($recentActivities as $activity)
                        <div class="activity-item">
                            <div class="icon-box" style="background: {{ $activity['bg'] }}; color: {{ $activity['color'] }}; width: 32px; height: 32px; font-size: 0.9rem; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                {{ $activity['icon'] }}
                            </div>
                            <div style="flex-grow: 1; min-width: 0;">
                                <div style="font-size: 0.85rem; color: #334155; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $activity['title'] }}
                                </div>
                            </div>
                            <div style="font-size: 0.7rem; color: #94a3b8; white-space: nowrap; text-align: right;">
                                {{ \Carbon\Carbon::parse($activity['time'])->format('H:i, d/m/Y') }}
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; color: #94a3b8; padding: 2rem 0; font-size: 0.85rem;">Chưa có hoạt động nào.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
