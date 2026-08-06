@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">{{ $title }}</h2>

    @if(isset($_SESSION['flash']['success']))
        <div class="alert alert-success py-2 mb-3">{{ $_SESSION['flash']['success'] }}</div>
        @php unset($_SESSION['flash']['success']); @endphp
    @endif
    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger py-2 mb-3">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex gap-2 mb-3">
                <a href="{{ route('admin/bookings') }}" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại
                </a>
                <a href="{{ route('admin/bookings/edit/' . $booking['id']) }}" class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil me-1"></i> Sửa booking
                </a>
                <a href="{{ route('admin/bookings/delete/' . $booking['id']) }}"
                   class="btn btn-sm btn-danger ms-auto"
                   onclick="return confirm('Bạn có chắc muốn xóa booking này?')">
                    <i class="bi bi-trash me-1"></i> Xóa
                </a>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body">
                            <h5 class="mb-3">
                                <i class="bi bi-person-badge me-2 text-primary"></i> Thông tin khách hàng
                            </h5>
                            <dl class="row mb-0">
                                <dt class="col-sm-4 text-muted">ID Booking</dt>
                                <dd class="col-sm-8 fw-semibold">#{{ $booking['id'] }}</dd>

                                <dt class="col-sm-4 text-muted">Họ tên</dt>
                                <dd class="col-sm-8">{{ $booking['customer_name'] ?? '-' }}</dd>

                                <dt class="col-sm-4 text-muted">Email</dt>
                                <dd class="col-sm-8">{{ $booking['customer_email'] ?? '-' }}</dd>

                                <dt class="col-sm-4 text-muted">Điện thoại</dt>
                                <dd class="col-sm-8">{{ $booking['customer_phone'] ?? '-' }}</dd>

                                <dt class="col-sm-4 text-muted">Ngày đặt</dt>
                                <dd class="col-sm-8">{{ !empty($booking['booking_date']) ? date('d/m/Y', strtotime($booking['booking_date'])) : '-' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body">
                            <h5 class="mb-3">
                                <i class="bi bi-bag-check me-2 text-primary"></i> Thông tin tour
                            </h5>
                            <dl class="row mb-0">
                                <dt class="col-sm-4 text-muted">Tour</dt>
                                <dd class="col-sm-8 fw-semibold">{{ $booking['tour_name'] ?? 'N/A' }}</dd>

                                <dt class="col-sm-4 text-muted">Số người</dt>
                                <dd class="col-sm-8">{{ $booking['num_people'] ?? 0 }} người</dd>

                                <dt class="col-sm-4 text-muted">Tổng tiền</dt>
                                <dd class="col-sm-8 text-primary fw-bold fs-5">{{ !empty($booking['total_price']) ? number_format($booking['total_price'], 0, ',', '.') . ' ₫' : '0 ₫' }}</dd>

                                <dt class="col-sm-4 text-muted">Trạng thái</dt>
                                <dd class="col-sm-8">
                                    @php
                                        $statusInt = (int) ($booking['status'] ?? 0);
                                        $statusMeta = match($statusInt) {
                                            1 => ['text' => 'Đã xác nhận', 'class' => 'bg-success'],
                                            2 => ['text' => 'Đã hủy', 'class' => 'bg-danger'],
                                            default => ['text' => 'Chờ xác nhận', 'class' => 'bg-warning text-dark'],
                                        };
                                    @endphp
                                    <span class="badge {{ $statusMeta['class'] }} fs-6 px-3 py-2">{{ $statusMeta['text'] }}</span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                @if(!empty($booking['note']))
                <div class="col-md-12">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h5 class="mb-2">
                                <i class="bi bi-sticky me-2 text-primary"></i> Ghi chú
                            </h5>
                            <div class="text-muted">{{ nl2br(e($booking['note'])) }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
