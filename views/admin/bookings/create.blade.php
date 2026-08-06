@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">{{ $title }}</h2>

    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger py-2 mb-3">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin/bookings/store') }}" method="POST">
<<<<<<< HEAD
                <div class="mb-3 mt-2">
                    <label class="form-label">Tour du lịch <span class="text-danger">*</span></label>
                    <select name="tour_id" id="tour_id" class="form-select" required>
                        <option value="">-- Chọn Tour --</option>
                        @foreach($tours as $tour)
                            <option value="{{ $tour['id'] }}" data-price="{{ $tour['price'] ?? 0 }}" {{ (isset($preTourId) && (int)$preTourId === (int)$tour['id']) ? 'selected' : '' }}>
                                {{ $tour['name'] }} ({{ number_format($tour['price']) }} VNĐ)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Họ tên khách hàng</label>
                    <input type="text" name="customer_name" class="form-control" value="{{ 
                        isset($_POST['customer_name']) ? htmlentities($_POST['customer_name']) : '' }}" required>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="form-label">Email</label>
                            <input type="email" name="customer_email" id="customer_email" class="form-control" value="{{ 
                                isset($_POST['customer_email']) ? htmlentities($_POST['customer_email']) : '' }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="customer_phone" id="customer_phone" class="form-control" value="{{ 
                                isset($_POST['customer_phone']) ? htmlentities($_POST['customer_phone']) : '' }}" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label">Địa chỉ đón khách hàng</label>
                    <input type="text" name="pickup_address" id="pickup_address" class="form-control" value="{{ 
                        isset($_POST['pickup_address']) ? htmlentities($_POST['pickup_address']) : '' }}" placeholder="Ví dụ: Số 123 đường Nguyễn Huệ, Q.1, TP.HCM">
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="form-label">Số người</label>
                            <input type="number" name="num_people" id="num_people" class="form-control" value="{{ 
                                isset($_POST['num_people']) ? (int)$_POST['num_people'] : 1 }}" min="1" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="form-label">Ngày đặt Tour</label>
                            <input type="date" name="booking_date" id="booking_date" class="form-control" value="{{ 
                                isset($_POST['booking_date']) ? htmlentities($_POST['booking_date']) : date('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="0">Chờ xác nhận</option>
                        <option value="1">Đã xác nhận</option>
                        <option value="2">Đã hủy</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ghi chú</label>
                    <textarea name="note" class="form-control" rows="4">{{ isset($_POST['note']) ? htmlentities($_POST['note']) : '' }}</textarea>
                </div>

                <div class="alert alert-info">
                    <strong>Lưu ý:</strong> Tổng tiền sẽ được hệ thống tự động tính bằng <strong>Giá Tour × Số người</strong>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Lưu Booking</button>
                    <a href="{{ route('admin/bookings') }}" class="btn btn-secondary">Quay lại</a>
=======
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tour_id" class="form-label">Tour <span class="text-danger">*</span></label>
                            <select class="form-select" id="tour_id" name="tour_id" required>
                                <option value="">-- Chọn tour --</option>
                                @foreach($tours as $t)
                                    <option value="{{ $t['id'] }}" {{ old('tour_id') == $t['id'] ? 'selected' : '' }}>
                                        {{ $t['name'] }} ({{ number_format($t['price'], 0, ',', '.') }} ₫)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Họ tên khách hàng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Ví dụ: Nguyễn Văn A" required value="{{ old('customer_name') }}">
                        </div>
                        <div class="mb-3">
                            <label for="customer_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="customer_email" name="customer_email" placeholder="example@gmail.com" required value="{{ old('customer_email') }}">
                        </div>
                        <div class="mb-3">
                            <label for="customer_phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customer_phone" name="customer_phone" placeholder="09xx xxx xxx" required value="{{ old('customer_phone') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="num_people" class="form-label">Số người <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="num_people" name="num_people" min="1" value="{{ old('num_people', 1) }}">
                        </div>
                        <div class="mb-3">
                            <label for="booking_date" class="form-label">Ngày đặt <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="booking_date" name="booking_date" required value="{{ old('booking_date', date('Y-m-d')) }}">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Chờ xác nhận</option>
                                <option value="1" {{ old('status') === '1' ? 'selected' : '' }}>Đã xác nhận</option>
                                <option value="2" {{ old('status') === '2' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="note" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="note" name="note" rows="3" placeholder="Yêu cầu đặc biệt (nếu có)">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin/bookings') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Quay lại
                    </a>
                    <button type="reset" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Nhập lại
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Lưu Booking
                    </button>
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
