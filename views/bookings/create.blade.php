@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">{{ $title }}</h2>

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
            <form action="{{ route('admin/bookings/store') }}" method="POST">
                <div class="mb-3">
                    <label class="form-label">Tour du lịch <span class="text-danger">*</span></label>
                    <select name="tour_id" class="form-select" required>
                        <option value="">-- Chọn Tour --</option>
                        @foreach($tours as $tour)
                            <option value="{{ $tour['id'] }}">{{ $tour['name'] }} ({{ number_format($tour['price']) }} VNĐ)</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Họ tên khách hàng</label>
                    <input type="text" name="customer_name" class="form-control" value="{{ 
                        isset($_POST['customer_name']) ? htmlentities($_POST['customer_name']) : '' }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="customer_email" class="form-control" value="{{ 
                        isset($_POST['customer_email']) ? htmlentities($_POST['customer_email']) : '' }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="customer_phone" class="form-control" value="{{ 
                        isset($_POST['customer_phone']) ? htmlentities($_POST['customer_phone']) : '' }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Số người</label>
                    <input type="number" name="num_people" class="form-control" value="{{ 
                        isset($_POST['num_people']) ? (int)$_POST['num_people'] : 1 }}" min="1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ngày đặt Tour</label>
                    <input type="date" name="booking_date" class="form-control" value="{{ 
                        isset($_POST['booking_date']) ? htmlentities($_POST['booking_date']) : date('Y-m-d') }}" required>
                </div>

                <div class="mb-3">
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
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
