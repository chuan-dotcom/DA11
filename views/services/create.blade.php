@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">{{ $title }}</h2>
        <a href="{{ route('admin/services') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    @if(isset($_SESSION['flash']['success']))
        <div class="alert alert-success">{{ $_SESSION['flash']['success'] }}</div>
        @php unset($_SESSION['flash']['success']); @endphp
    @endif
    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin/services/store') }}" method="POST">
                <div class="mb-3">
                    <label class="form-label">Tour du lịch <span class="text-danger">*</span></label>
                    <select name="tour_id" class="form-select" required>
                        <option value="">-- Chọn Tour --</option>
                        @foreach($tours as $tour)
                            <option value="{{ $tour['id'] }}" {{ (isset($_POST['tour_id']) && $_POST['tour_id'] == $tour['id']) ? 'selected' : '' }}>
                                #{{ $tour['id'] }} - {{ $tour['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Loại dịch vụ <span class="text-danger">*</span></label>
                    <div class="row g-2 mb-2">
                        <div class="col-md-auto">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="service_types[]" id="st_thamquan" value="Tham quan"
                                    {{ isset($_POST['service_types']) && is_array($_POST['service_types']) && in_array('Tham quan', $_POST['service_types']) ? 'checked' : '' }}>
                                <label class="form-check-label" for="st_thamquan">Tham quan</label>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="service_types[]" id="st_nhahang" value="Nhà hàng"
                                    {{ isset($_POST['service_types']) && is_array($_POST['service_types']) && in_array('Nhà hàng', $_POST['service_types']) ? 'checked' : '' }}>
                                <label class="form-check-label" for="st_nhahang">Nhà hàng</label>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="service_types[]" id="st_vemaybay" value="Vé máy bay"
                                    {{ isset($_POST['service_types']) && is_array($_POST['service_types']) && in_array('Vé máy bay', $_POST['service_types']) ? 'checked' : '' }}>
                                <label class="form-check-label" for="st_vemaybay">Vé máy bay</label>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="service_types[]" id="st_khachsan" value="Khách sạn"
                                    {{ isset($_POST['service_types']) && is_array($_POST['service_types']) && in_array('Khách sạn', $_POST['service_types']) ? 'checked' : '' }}>
                                <label class="form-check-label" for="st_khachsan">Khách sạn</label>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="service_types[]" id="st_xe" value="Xe"
                                    {{ isset($_POST['service_types']) && is_array($_POST['service_types']) && in_array('Xe', $_POST['service_types']) ? 'checked' : '' }}>
                                <label class="form-check-label" for="st_xe">Xe</label>
                            </div>
                        </div>
                    </div>
                    <input type="text" name="service_types_text" class="form-control" placeholder="Hoặc nhập loại dịch vụ khác (nếu có)" value="{{ isset($_POST['service_types_text']) ? htmlentities($_POST['service_types_text']) : '' }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Nhà cung cấp <span class="text-danger">*</span></label>
                    <input type="text" name="supplier" class="form-control" value="{{ isset($_POST['supplier']) ? htmlentities($_POST['supplier']) : '' }}" placeholder="Ví dụ: Công ty Xe Anh Tài" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                    <input type="number" name="quantity" class="form-control" value="{{ isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1 }}" min="1" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Thời gian bắt đầu</label>
                        <input type="datetime-local" name="start_time" class="form-control" value="{{ isset($_POST['start_time']) ? htmlentities($_POST['start_time']) : '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Thời gian kết thúc</label>
                        <input type="datetime-local" name="end_time" class="form-control" value="{{ isset($_POST['end_time']) ? htmlentities($_POST['end_time']) : '' }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="0" {{ (isset($_POST['status']) && $_POST['status'] == '0') ? 'selected' : '' }}>Chờ</option>
                        <option value="1" {{ (isset($_POST['status']) && $_POST['status'] == '1') ? 'selected' : '' }}>Xác nhận</option>
                        <option value="2" {{ (isset($_POST['status']) && $_POST['status'] == '2') ? 'selected' : '' }}>Hoàn tất</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ghi chú</label>
                    <textarea name="note" class="form-control" rows="4">{{ isset($_POST['note']) ? htmlentities($_POST['note']) : '' }}</textarea>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Lưu dịch vụ</button>
                    <a href="{{ route('admin/services') }}" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
