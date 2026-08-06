@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">{{ $title }}</h2>

<<<<<<< HEAD
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th width="250">Mã Booking</th>
                    <td>{{ $booking['id'] }}</td>
                </tr>
                <tr>
                    <th>Tour</th>
                    <td>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span>{{ $booking['tour_name'] }}</span>
                            <a href="{{ route('admin/tours/participants/' . $booking['tour_id']) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-people me-1"></i>Xem danh sách khách tour
                            </a>
                        </div>
                    </td>
                </tr>
                @if(!empty($booking['tour_location']))
                <tr>
                    <th>Địa điểm tour</th>
                    <td><i class="bi bi-geo-alt text-secondary me-1"></i>{{ $booking['tour_location'] }}</td>
                </tr>
                @endif
                <tr>
                    <th>Khách hàng</th>
                    <td>{{ $booking['customer_name'] }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $booking['customer_email'] }}</td>
                </tr>
                <tr>
                    <th>Số điện thoại</th>
                    <td>{{ $booking['customer_phone'] }}</td>
                </tr>
                @if(!empty($booking['pickup_address']))
                <tr>
                    <th>Địa chỉ đón khách hàng</th>
                    <td>
                        <i class="bi bi-geo text-primary me-1"></i>{{ $booking['pickup_address'] }}
                    </td>
                </tr>
                @endif
                <tr>
                    <th>Số người</th>
                    <td>{{ $booking['num_people'] }}</td>
                </tr>
                <tr>
                    <th>Ngày khởi hành</th>
                    <td>{{ $booking['booking_date'] }}</td>
                </tr>
                @php
                    $endDate = null;
                    $start = !empty($booking['booking_date']) ? $booking['booking_date'] : null;
                    $duration = !empty($booking['tour_duration']) ? (int)$booking['tour_duration'] : 0;
                    if ($start && $duration > 0) {
                        $ts = strtotime($start);
                        if ($ts !== false) {
                            $endTs = strtotime('+' . ($duration - 1) . ' days', $ts);
                            $endDate = date('d/m/Y', $endTs);
                        }
                    }
                @endphp
                <tr>
                    <th>Ngày kết thúc</th>
                    <td>
                        @if($endDate)
                            <i class="bi bi-calendar-check text-success me-1"></i>
                            <strong>{{ $endDate }}</strong>
                            <small class="text-muted ms-2">
                                ({{ $duration }} ngày {{ $duration >= 2 ? ($duration - 1) . ' đêm' : '1 ngày' }}
                                @if(!empty($booking['tour_duration'])) · theo thời lượng tour @endif)
                            </small>
                        @else
                            <span class="text-muted">Chưa xác định</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Tổng tiền</th>
                    <td><strong class="text-danger">{{ number_format($booking['total_price']) }} VNĐ</strong></td>
                </tr>
                <tr>
                    <th>Trạng thái</th>
                    <td>
                        @php
                            switch ($booking['status']) {
                                case 0:
                                    echo '<span class="badge bg-warning text-dark">Chờ xác nhận</span>';
                                    break;
                                case 1:
                                    echo '<span class="badge bg-success">Đã xác nhận</span>';
                                    break;
                                case 2:
                                    echo '<span class="badge bg-danger">Đã hủy</span>';
                                    break;
                            }
                        @endphp
                    </td>
                </tr>
                <tr>
                    <th>Ghi chú</th>
                    <td>{!! !empty($booking['note']) ? nl2br(e($booking['note'])) : 'Không có ghi chú' !!}</td>
                </tr>
                <tr>
                    <th>Ngày tạo</th>
                    <td>{{ $booking['created_at'] }}</td>
                </tr>
            </table>
=======
    @if(isset($_SESSION['flash']['success']))
        <div class="alert alert-success py-2 mb-3">{{ $_SESSION['flash']['success'] }}</div>
        @php unset($_SESSION['flash']['success']); @endphp
    @endif
    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger py-2 mb-3">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c

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
