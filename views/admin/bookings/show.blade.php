@extends('layouts.admin')

@section('title', $title)                   

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">{{ $title }}</h2>

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
                @php
                    $pickup = !empty($booking['pickup_address']) ? $booking['pickup_address'] : (!empty($booking['departure_meeting_point']) ? $booking['departure_meeting_point'] : null);
                @endphp
                @if(!empty($pickup))
                <tr>
                    <th>Địa chỉ đón khách hàng</th>
                    <td>
                        <i class="bi bi-geo text-primary me-1"></i>{{ $pickup }}
                        @if(!empty($booking['pickup_address']) && !empty($booking['departure_meeting_point']) && $booking['pickup_address'] !== $booking['departure_meeting_point'])
                            <div class="mt-1"><small class="text-muted">Điểm tập kết đoàn: {{ $booking['departure_meeting_point'] }}</small></div>
                        @endif
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
                @if(!empty($booking['departure_meeting_point']) && empty($booking['pickup_address']))
                <tr>
                    <th>Điểm tập kết (đoàn)</th>
                    <td>{{ $booking['departure_meeting_point'] }}</td>
                </tr>
                @endif
                <tr>
                    <th>Ghi chú</th>
                    <td>{!! !empty($booking['note']) ? nl2br(e($booking['note'])) : 'Không có ghi chú' !!}</td>
                </tr>
                <tr>
                    <th>Ngày tạo</th>
                    <td>{{ $booking['created_at'] }}</td>
                </tr>
            </table>

            <a href="{{ route('admin/bookings/edit/' . $booking['id']) }}" class="btn btn-warning">Sửa</a>
            <a href="{{ route('admin/bookings') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
</div>
@endsection
