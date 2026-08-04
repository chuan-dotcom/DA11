@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h2 class="mb-1">{{ $title }}</h2>
            <div class="text-muted">Booking #{{ $booking['id'] }} - {{ $booking['customer_name'] }}</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin/guest-groups/show/' . $guestGroup['id'] . '?booking_id=' . $booking['id']) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin/guest-groups/booking-guests/store/' . $guestGroup['id'] . '/' . $booking['id']) }}" method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control" required value="{{ isset($_POST['full_name']) ? htmlentities($_POST['full_name']) : '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Giới tính</label>
                        <select name="gender" class="form-select">
                            <option value="">-- Chọn --</option>
                            <option value="male">Nam</option>
                            <option value="female">Nữ</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ngày sinh</label>
                        <input type="date" name="dob" class="form-control" value="{{ isset($_POST['dob']) ? htmlentities($_POST['dob']) : '' }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">SĐT</label>
                        <input type="text" name="phone" class="form-control" value="{{ isset($_POST['phone']) ? htmlentities($_POST['phone']) : '' }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ isset($_POST['email']) ? htmlentities($_POST['email']) : '' }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">CCCD/Passport</label>
                        <input type="text" name="identity_no" class="form-control" value="{{ isset($_POST['identity_no']) ? htmlentities($_POST['identity_no']) : '' }}">
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="address" class="form-control" value="{{ isset($_POST['address']) ? htmlentities($_POST['address']) : '' }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Thanh toán</label>
                        <select name="payment_status" class="form-select">
                            <option value="unpaid">Chưa thanh toán</option>
                            <option value="deposit">Đã đặt cọc</option>
                            <option value="paid">Đã thanh toán</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="note" class="form-control" rows="3">{{ isset($_POST['note']) ? htmlentities($_POST['note']) : '' }}</textarea>
                    </div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Lưu
                    </button>
                    <a href="{{ route('admin/guest-groups/show/' . $guestGroup['id'] . '?booking_id=' . $booking['id']) }}" class="btn btn-secondary">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

