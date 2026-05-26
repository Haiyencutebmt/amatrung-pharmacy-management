@extends('layouts.admin')

@section('title', 'Quản lý Kho Mới')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800" style="font-size: 28px; font-weight: bold;">Danh sách mặt hàng kho</h1>
        <button type="button" class="btn btn-success btn-lg font-weight-bold" data-toggle="modal" data-target="#addItemModal">
            <i class="fas fa-plus-circle"></i> Thêm mặt hàng mới
        </button>
    </div>

    <!-- Bộ lọc -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary" style="font-size: 18px;">Bộ lọc</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.inventory.index') }}" method="GET" class="form-inline">
                <div class="form-group mb-2">
                    <select name="filter" class="form-control form-control-lg" onchange="this.form.submit()" style="font-size: 18px;">
                        <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>Tất cả mặt hàng</option>
                        <option value="available" {{ $filter === 'available' ? 'selected' : '' }}>Còn sử dụng được</option>
                        <option value="near_expiry" {{ $filter === 'near_expiry' ? 'selected' : '' }}>Sắp hết hạn (<= 30 ngày)</option>
                        <option value="expired" {{ $filter === 'expired' ? 'selected' : '' }}>Đã hết hạn</option>
                        <option value="unknown_expiry" {{ $filter === 'unknown_expiry' ? 'selected' : '' }}>Chưa rõ hạn dùng</option>
                        <option value="external_products" {{ $filter === 'external_products' ? 'selected' : '' }}>Thuốc/Chế phẩm dùng ngoài</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0" style="font-size: 16px;">
                    <thead class="thead-light">
                        <tr>
                            <th>Tên mặt hàng</th>
                            <th>Loại mặt hàng</th>
                            <th>Đường dùng</th>
                            <th>Đơn vị</th>
                            <th>Tổng tồn khả dụng</th>
                            <th>Trạng thái/Cảnh báo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                        <tr>
                            <td class="font-weight-bold" style="font-size: 18px;">{{ $item->name }}</td>
                            <td>
                                @if($item->item_type === 'herb')
                                    Dược liệu uống
                                @elseif($item->item_type === 'prepared_product')
                                    Chế phẩm uống
                                @else
                                    Chế phẩm dùng ngoài
                                @endif
                            </td>
                            <td>
                                @if($item->usage_route === 'external')
                                    <span class="badge badge-danger p-2" style="font-size: 14px;">Dùng ngoài da - Không được uống</span>
                                @else
                                    <span class="badge badge-success p-2" style="font-size: 14px;">Uống</span>
                                @endif
                            </td>
                            <td>{{ $item->unit }}</td>
                            <td class="font-weight-bold" style="font-size: 18px; color: #2e59d9;">
                                {{ number_format($item->total_quantity, 2) }}
                            </td>
                            <td>
                                @if($item->computed_status === 'expired')
                                    <span class="badge badge-danger p-2" style="font-size: 14px;">Có lô hết hạn</span>
                                @elseif($item->computed_status === 'unknown_expiry')
                                    <span class="badge badge-warning p-2 text-dark" style="font-size: 14px;">Chưa rõ hạn dùng</span>
                                @elseif($item->computed_status === 'near_expiry')
                                    <span class="badge badge-warning p-2 text-dark" style="font-size: 14px;">Sắp hết hạn</span>
                                @else
                                    <span class="badge badge-success p-2" style="font-size: 14px;">Khả dụng</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.inventory.show', $item->id) }}" class="btn btn-primary btn-lg font-weight-bold">
                                    <i class="fas fa-eye"></i> Chi tiết
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Không có dữ liệu mặt hàng.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $items->appends(['filter' => $filter])->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm mặt hàng mới -->
<div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.inventory.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title font-weight-bold" id="addItemModalLabel" style="font-size: 20px;">
                        <i class="fas fa-plus-circle"></i> Thêm mặt hàng mới
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="font-size: 16px;">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <h6 class="font-weight-bold text-primary mb-3" style="font-size: 18px;">Thông tin mặt hàng</h6>

                    <div class="form-group">
                        <label for="item_name" class="font-weight-bold">Tên mặt hàng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="item_name" name="name" value="{{ old('name') }}" required placeholder="Nhập tên mặt hàng">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="item_type" class="font-weight-bold">Loại mặt hàng <span class="text-danger">*</span></label>
                            <select class="form-control form-control-lg" id="item_type" name="item_type" required>
                                <option value="">-- Chọn loại --</option>
                                <option value="herb" {{ old('item_type') === 'herb' ? 'selected' : '' }}>Dược liệu uống</option>
                                <option value="prepared_product" {{ old('item_type') === 'prepared_product' ? 'selected' : '' }}>Chế phẩm uống</option>
                                <option value="packaged_product" {{ old('item_type') === 'packaged_product' ? 'selected' : '' }}>Chế phẩm dùng ngoài</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="usage_route" class="font-weight-bold">Đường dùng <span class="text-danger">*</span></label>
                            <select class="form-control form-control-lg" id="usage_route" name="usage_route" required>
                                <option value="">-- Chọn đường dùng --</option>
                                <option value="oral" {{ old('usage_route') === 'oral' ? 'selected' : '' }}>Uống</option>
                                <option value="external" {{ old('usage_route') === 'external' ? 'selected' : '' }}>Dùng ngoài</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="item_unit" class="font-weight-bold">Đơn vị tính <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="item_unit" name="unit" value="{{ old('unit') }}" required placeholder="VD: gram, lọ, hộp, chai...">
                    </div>

                    <div class="form-group">
                        <label for="item_description" class="font-weight-bold">Mô tả</label>
                        <textarea class="form-control" id="item_description" name="description" rows="3" placeholder="Mô tả mặt hàng (không bắt buộc)">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group" id="warningGroup" style="display: none;">
                        <label for="item_warning" class="font-weight-bold text-danger">
                            <i class="fas fa-exclamation-triangle"></i> Cảnh báo sử dụng
                        </label>
                        <textarea class="form-control border-danger" id="item_warning" name="warning" rows="2" placeholder="VD: Chỉ dùng ngoài da, không được uống. Tránh tiếp xúc mắt...">{{ old('warning') }}</textarea>
                    </div>

                    <hr>
                    <h6 class="font-weight-bold text-primary mb-3" style="font-size: 18px;">Thông tin lô hàng đầu tiên</h6>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="batch_code" class="font-weight-bold">Mã lô <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="batch_code" name="batch_code" value="{{ old('batch_code') }}" required placeholder="VD: LO-001">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="quantity" class="font-weight-bold">Số lượng nhập <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-lg" id="quantity" name="quantity" value="{{ old('quantity') }}" required min="0.01" step="0.01" placeholder="VD: 100">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="expiry_date" class="font-weight-bold">Hạn sử dụng <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-lg" id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-success btn-lg font-weight-bold">
                        <i class="fas fa-save"></i> Lưu mặt hàng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Hiển thị/ẩn trường cảnh báo theo đường dùng
        function toggleWarningField() {
            var usageRoute = $('#usage_route').val();
            if (usageRoute === 'external') {
                $('#warningGroup').slideDown();
            } else {
                $('#warningGroup').slideUp();
            }
        }

        $('#usage_route').on('change', toggleWarningField);

        // Tự động chọn đường dùng khi chọn loại mặt hàng
        $('#item_type').on('change', function() {
            var itemType = $(this).val();
            if (itemType === 'herb' || itemType === 'prepared_product') {
                $('#usage_route').val('oral');
            } else if (itemType === 'packaged_product') {
                $('#usage_route').val('external');
            }
            toggleWarningField();
        });

        // Hiện lại modal nếu có lỗi validation
        @if($errors->any())
            $('#addItemModal').modal('show');
        @endif

        // Khởi tạo trạng thái ban đầu
        toggleWarningField();
    });
</script>
@endsection
