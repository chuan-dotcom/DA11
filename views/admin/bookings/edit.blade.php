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
            <form action="{{ route('admin/bookings/update/' . $booking['id']) }}" method="POST">
                <div class="mb-3">
                    <label class="form-label">Tour</label>
                    <select name="tour_id" class="form-select" required>
                        @foreach($tours as $tour)
                            @php
                                $selected = '';
                                if(isset($_POST['tour_id'])) {
                                    $selected = $_POST['tour_id'] == $tour['id'] ? 'selected' : '';
                                } else {
                                    $selected = $tour['id'] == $booking['tour_id'] ? 'selected' : '';
                                }
                            @endphp
                            <option value="{{ $tour['id'] }}" {{ $selected }}>{{ $tour['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Họ tên khách hàng</label>
                    <input type="text" name="customer_name" class="form-control" required value="{{ isset($_POST['customer_name']) ? htmlentities($_POST['customer_name']) : $booking['customer_name'] }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="customer_email" class="form-control" required value="{{ isset($_POST['customer_email']) ? htmlentities($_POST['customer_email']) : $booking['customer_email'] }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="customer_phone" class="form-control" required value="{{ isset($_POST['customer_phone']) ? htmlentities($_POST['customer_phone']) : $booking['customer_phone'] }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Số người</label>
                    <input type="number" name="num_people" class="form-control" min="1" required value="{{ isset($_POST['num_people']) ? (int)$_POST['num_people'] : $booking['num_people'] }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Tổng tiền hiện tại</label>
                    <input type="text" class="form-control" disabled value="{{ number_format($booking['total_price']) }} VNĐ">
                </div>

                <div class="mb-3">
                    <label class="form-label">Ngày khởi hành</label>
                    <input type="date" name="booking_date" class="form-control" required value="{{ isset($_POST['booking_date']) ? htmlentities($_POST['booking_date']) : $booking['booking_date'] }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        @php $s = isset($_POST['status']) ? (int)$_POST['status'] : (int)$booking['status']; @endphp
                        <option value="0" {{ $s === 0 ? 'selected' : '' }}>Chờ xác nhận</option>
                        <option value="1" {{ $s === 1 ? 'selected' : '' }}>Đã xác nhận</option>
                        <option value="2" {{ $s === 2 ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ghi chú</label>
                    <textarea name="note" rows="4" class="form-control">{{ isset($_POST['note']) ? htmlentities($_POST['note']) : $booking['note'] }}</textarea>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-warning">Cập nhật</button>
                    <a href="{{ route('admin/bookings') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
