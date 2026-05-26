@extends('layouts.admin')

@section('title', 'Chi tiết Mặt Hàng Kho')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary btn-lg mb-3">
            <i class="fas fa-arrow-left"></i> Quay lại Danh sách
        </a>
        <h1 class="h3 text-gray-800" style="font-size: 28px; font-weight: bold;">
            Chi tiết mặt hàng: <span class="text-primary">{{ $item->name }}</span>
        </h1>
        @if($item->usage_route === 'external')
            <div class="alert alert-danger mt-3" style="font-size: 20px; font-weight: bold;">
                <i class="fas fa-exclamation-triangle"></i> CẢNH BÁO: DÙNG NGOÀI DA - KHÔNG ĐƯỢC UỐNG
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="font-size: 18px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" style="font-size: 18px;">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" style="font-size: 18px;">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Danh sách Lô (Batches) -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary" style="font-size: 20px;">Danh sách Lô hàng</h6>
                    <button class="btn btn-success btn-lg font-weight-bold" data-toggle="modal" data-target="#importBatchModal">
                        <i class="fas fa-plus"></i> Nhập lô mới
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0" style="font-size: 16px;">
                            <thead class="thead-light">
                                <tr>
                                    <th>Mã Lô</th>
                                    <th>Hạn dùng</th>
                                    <th>Số lượng còn</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($item->batches as $batch)
                                <tr>
                                    <td class="font-weight-bold">{{ $batch->batch_number }}</td>
                                    <td>
                                        @if($batch->expiry_date)
                                            {{ $batch->expiry_date->format('d/m/Y') }}
                                        @else
                                            <span class="text-danger font-weight-bold">Chưa rõ</span>
                                        @endif
                                    </td>
                                    <td class="font-weight-bold text-primary" style="font-size: 18px;">
                                        {{ number_format($batch->quantity_remaining, 2) }} {{ $item->unit }}
                                    </td>
                                    <td>
                                        @if($batch->status === 'available')
                                            @if($batch->expiry_date && $batch->expiry_date->diffInDays(now()) <= 30 && $batch->expiry_date >= now()->startOfDay())
                                                <span class="badge badge-warning p-2 text-dark" style="font-size: 14px;">Sắp hết hạn</span>
                                            @else
                                                <span class="badge badge-success p-2" style="font-size: 14px;">Khả dụng</span>
                                            @endif
                                        @elseif($batch->status === 'expired')
                                            <span class="badge badge-danger p-2" style="font-size: 14px;">Hết hạn</span>
                                        @elseif($batch->status === 'unknown_expiry')
                                            <span class="badge badge-warning p-2 text-dark" style="font-size: 14px;">Chưa rõ hạn</span>
                                        @elseif($batch->status === 'blocked')
                                            <span class="badge badge-secondary p-2" style="font-size: 14px;">Đã khóa</span>
                                        @else
                                            <span class="badge badge-info p-2" style="font-size: 14px;">{{ $batch->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($batch->status === 'unknown_expiry' || $batch->status === 'near_expiry' || $batch->status === 'available' || $batch->status === 'expired')
                                            <button class="btn btn-warning btn-sm font-weight-bold" data-toggle="modal" data-target="#updateExpiryModal{{ $batch->id }}">
                                                <i class="fas fa-edit"></i> Cập nhật Hạn
                                            </button>
                                        @endif
                                        
                                        @if($batch->stockMovements->count() > 0)
                                            <form action="{{ route('admin.inventory.batch.toggle', $batch->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn thay đổi trạng thái lô này?');">
                                                @csrf
                                                @method('PATCH')
                                                @if($batch->status === 'blocked')
                                                    <button type="submit" class="btn btn-success btn-sm font-weight-bold"><i class="fas fa-unlock"></i> Mở khóa</button>
                                                @else
                                                    <button type="submit" class="btn btn-secondary btn-sm font-weight-bold"><i class="fas fa-lock"></i> Khóa lô</button>
                                                @endif
                                            </form>
                                        @else
                                            <!-- Should be a delete button if 0 movements, but for safety we use toggle/block in UI as per requirements -->
                                            <form action="{{ route('admin.inventory.batch.toggle', $batch->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn thay đổi trạng thái lô này?');">
                                                @csrf
                                                @method('PATCH')
                                                @if($batch->status === 'blocked')
                                                    <button type="submit" class="btn btn-success btn-sm font-weight-bold"><i class="fas fa-unlock"></i> Mở khóa</button>
                                                @else
                                                    <button type="submit" class="btn btn-secondary btn-sm font-weight-bold"><i class="fas fa-lock"></i> Khóa lô</button>
                                                @endif
                                            </form>
                                        @endif

                                        <!-- Update Expiry Modal -->
                                        <div class="modal fade" id="updateExpiryModal{{ $batch->id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.inventory.batch.update', $batch->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title font-weight-bold">Cập nhật hạn dùng cho Lô: {{ $batch->batch_number }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label style="font-size: 18px;">Hạn sử dụng mới (Ngày/Tháng/Năm) <span class="text-danger">*</span></label>
                                                                <input type="date" name="expiry_date" class="form-control form-control-lg" required value="{{ $batch->expiry_date ? $batch->expiry_date->format('Y-m-d') : '' }}">
                                                                <small class="form-text text-muted mt-2">Hệ thống sẽ tự động cập nhật trạng thái khả dụng hoặc hết hạn dựa trên ngày bạn nhập.</small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">Hủy</button>
                                                            <button type="submit" class="btn btn-primary btn-lg font-weight-bold">Lưu cập nhật</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Chưa có lô hàng nào.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lịch sử giao dịch (Stock Movements) -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary" style="font-size: 20px;">10 Giao dịch gần nhất</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush" style="font-size: 16px;">
                        @php
                            $allMovements = $item->batches->flatMap->stockMovements->sortByDesc('created_at')->take(10);
                        @endphp
                        
                        @forelse($allMovements as $mv)
                            <li class="list-group-item px-0">
                                <div class="d-flex justify-content-between">
                                    <span class="font-weight-bold">Lô: {{ $mv->batch->batch_number }}</span>
                                    <span class="text-muted">{{ $mv->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>
                                        @if($mv->movement_type === 'opening_balance')
                                            <span class="badge badge-info p-1">Số dư đầu kỳ</span>
                                        @elseif($mv->movement_type === 'import')
                                            <span class="badge badge-success p-1">Nhập lô mới</span>
                                        @elseif($mv->movement_type === 'dispense')
                                            <span class="badge badge-danger p-1">Xuất kho (Cấp thuốc)</span>
                                        @else
                                            <span class="badge badge-secondary p-1">{{ $mv->movement_type }}</span>
                                        @endif
                                    </span>
                                    <span class="font-weight-bold {{ $mv->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $mv->quantity > 0 ? '+' : '' }}{{ number_format($mv->quantity, 2) }}
                                    </span>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center">Chưa có giao dịch.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Batch Modal -->
<div class="modal fade" id="importBatchModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.inventory.batch.store', $item->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold">Nhập lô hàng mới: {{ $item->name }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body" style="font-size: 18px;">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Mã lô hàng <span class="text-danger">*</span></label>
                            <input type="text" name="batch_code" class="form-control form-control-lg" required placeholder="Ví dụ: L001-2026">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Số lượng nhập ({{ $item->unit }}) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="quantity" class="form-control form-control-lg" required>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label class="font-weight-bold">Hạn sử dụng <span class="text-danger">*</span></label>
                        <input type="date" name="expiry_date" class="form-control form-control-lg" required>
                    </div>
                    <div class="form-group mt-3">
                        <label class="font-weight-bold">Ghi chú (Tùy chọn)</label>
                        <textarea name="note" class="form-control form-control-lg" rows="2" placeholder="Nguồn nhập, ghi chú bảo quản..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success btn-lg font-weight-bold"><i class="fas fa-check"></i> Xác nhận Nhập Kho</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
