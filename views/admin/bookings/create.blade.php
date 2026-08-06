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
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
