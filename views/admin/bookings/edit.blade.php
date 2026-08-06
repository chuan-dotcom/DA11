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
            <form action="{{ route('admin/bookings/update/' . $booking['id']) }}" method="POST">
<<<<<<< HEAD
                <div class="mb-3 mt-2">
                    <label class="form-label">Tour</label>
                    <select name="tour_id" id="tour_id" class="form-select" required>
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

                <div class="mb-3 mt-3">
                    <label class="form-label">Họ tên khách hàng</label>
                    <input type="text" name="customer_name" class="form-control" required value="{{ isset($_POST['customer_name']) ? htmlentities($_POST['customer_name']) : $booking['customer_name'] }}">
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="form-label">Email</label>
                            <input type="email" name="customer_email" class="form-control" required value="{{ isset($_POST['customer_email']) ? htmlentities($_POST['customer_email']) : $booking['customer_email'] }}">
=======
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tour_id" class="form-label">Tour <span class="text-danger">*</span></label>
                            <select class="form-select" id="tour_id" name="tour_id" required>
                                <option value="">-- Chọn tour --</option>
                                @foreach($tours as $t)
                                    @php $selTourId = (int) old('tour_id', $booking['tour_id'] ?? 0); @endphp
                                    <option value="{{ $t['id'] }}" {{ $selTourId === (int) $t['id'] ? 'selected' : '' }}>
                                        {{ $t['name'] }} ({{ number_format($t['price'], 0, ',', '.') }} ₫)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Họ tên khách hàng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" required value="{{ old('customer_name', $booking['customer_name'] ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label for="customer_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="customer_email" name="customer_email" required value="{{ old('customer_email', $booking['customer_email'] ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label for="customer_phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customer_phone" name="customer_phone" required value="{{ old('customer_phone', $booking['customer_phone'] ?? '') }}">
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="num_people" class="form-label">Số người <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="num_people" name="num_people" min="1" value="{{ old('num_people', $booking['num_people'] ?? 1) }}">
                        </div>
                        <div class="mb-3">
                            <label for="booking_date" class="form-label">Ngày đặt <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="booking_date" name="booking_date" required value="{{ old('booking_date', $booking['booking_date'] ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                @php $selStatus = (string) old('status', $booking['status'] ?? '0'); @endphp
                                <option value="0" {{ $selStatus === '0' ? 'selected' : '' }}>Chờ xác nhận</option>
                                <option value="1" {{ $selStatus === '1' ? 'selected' : '' }}>Đã xác nhận</option>
                                <option value="2" {{ $selStatus === '2' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="note" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="note" name="note" rows="3">{{ old('note', $booking['note'] ?? '') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tổng tiền (tự tính)</label>
                            <input type="text" class="form-control bg-light" value="{{ !empty($booking['total_price']) ? number_format($booking['total_price'], 0, ',', '.') . ' ₫' : '0 ₫' }}" disabled>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin/bookings') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
