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
                @php
                    $departureStatusMap = [
                        'scheduled'   => ['Lên lịch', 'bg-primary'],
                        'in_progress' => ['Đang diễn ra', 'bg-warning text-dark'],
                        'completed'   => ['Hoàn thành', 'bg-success'],
                        'cancelled'   => ['Đã hủy', 'bg-secondary'],
                    ];
                @endphp
                @if(!empty($booking['departure_id']))
                <tr>
                    <th>Chuyến khởi hành</th>
                    <td>
                        <div class="d-flex flex-column gap-2">
                            <div class="d-inline-flex align-items-center flex-wrap gap-2">
                                <i class="bi bi-calendar-week text-primary"></i>
                                <span class="fw-semibold">
                                    {{ !empty($booking['departure_group_name']) ? $booking['departure_group_name'] : ('Đoàn #' . (int)$booking['departure_id']) }}
                                </span>
                                @php
                                    $ds = !empty($booking['departure_status']) ? $departureStatusMap[$booking['departure_status']] ?? null : null;
                                @endphp
                                @if($ds)
                                    <span class="badge {{ $ds[1] }}">{{ $ds[0] }}</span>
                                @endif
                            </div>
                            <div class="small text-muted">
                                @if(!empty($booking['departure_date_info']))
                                    <i class="bi bi-calendar me-1"></i>
                                    Khởi hành: {{ date('d/m/Y', strtotime($booking['departure_date_info'])) }}
                                @endif
                                @if(!empty($booking['departure_return_date']) && (!empty($booking['departure_date_info']) && $booking['departure_return_date'] !== $booking['departure_date_info']))
                                    <span class="mx-2">·</span>
                                    Trở về: {{ date('d/m/Y', strtotime($booking['departure_return_date'])) }}
                                @endif
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin/departures/edit/' . (int)$booking['departure_id']) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Mở chi tiết đoàn
                                </a>
                                <a href="{{ route('admin/guest-groups/show/' . (int)$booking['departure_id']) }}" class="btn btn-sm btn-outline-dark">
                                    <i class="bi bi-people me-1"></i>Danh sách khách đoàn
                                </a>
                                <a href="{{ route('admin/bookings/unassign-departure/' . (int)$booking['id']) }}"
                                   class="btn btn-sm btn-outline-warning"
                                   onclick="return confirm('Gỡ Booking này ra khỏi chuyến khởi hành hiện tại?')">
                                    <i class="bi bi-x-circle me-1"></i>Gỡ khỏi đoàn
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @else
                <tr>
                    <th>Chuyến khởi hành</th>
                    <td>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="text-muted">Chưa gắn vào đoàn nào.</span>
                            <a href="{{ route('admin/guest-groups/show/' . (int)$booking['tour_id']) }}" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-link-45deg me-1"></i>Gắn vào đoàn cùng tour
                            </a>
                        </div>
                    </td>
                </tr>
                @endif
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
