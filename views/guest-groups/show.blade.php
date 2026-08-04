@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
    .group-card {
        border: 1px solid #edf0f4;
        border-radius: 14px;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.05);                
    }

    .group-stat {
        border-radius: 12px;
        border: 1px solid #edf0f4;
        background: #fff;
        padding: 1rem 1.1rem;
        height: 100%;
    }

    .group-stat .value {
        font-size: 1.6rem;
        font-weight: 700;
    }
</style>

<div class="container mt-4">
    @php
        $statusMap = [
            'scheduled' => ['Chờ khởi hành', 'bg-warning text-dark'],
            'in_progress' => ['Đang diễn ra', 'bg-primary'],
            'completed' => ['Hoàn tất', 'bg-success'],
            'cancelled' => ['Đã hủy', 'bg-secondary'],
        ];
        $statusInfo = $statusMap[$guestGroup['status']] ?? [$guestGroup['status'], 'bg-secondary'];
        $groupName = $guestGroup['group_name'] ?: ('Đoàn ' . ($guestGroup['tour_name'] ?? 'Tour #' . $guestGroup['tour_id']));
    @endphp

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h2 class="mb-1">{{ $groupName }}</h2>
            <div class="text-muted">{{ $guestGroup['tour_name'] ?? '-' }}</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin/guest-groups') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Danh sách đoàn
            </a>
            <a href="{{ route('admin/guest-groups/print/' . $guestGroup['id']) }}" class="btn btn-outline-dark" target="_blank">
                <i class="bi bi-printer"></i> In danh sách
            </a>
            <a href="{{ route('admin/departures/edit/' . $guestGroup['id']) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Sửa đoàn
            </a>
        </div>
    </div>

    @if(isset($_SESSION['flash']['success']))
        <div class="alert alert-success">{{ $_SESSION['flash']['success'] }}</div>
        @php unset($_SESSION['flash']['success']); @endphp
    @endif
    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="card group-card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="mb-2">
                        <span class="badge {{ $statusInfo[1] }}">{{ $statusInfo[0] }}</span>
                    </div>
                    <div class="text-muted mb-1">Khởi hành: {{ !empty($guestGroup['departure_date']) ? date('d/m/Y', strtotime($guestGroup['departure_date'])) : '-' }}</div>
                    <div class="text-muted mb-1">Điểm tập trung: {{ $guestGroup['meeting_point'] ?: 'Chưa cập nhật' }}</div>
                    <div class="text-muted">Giờ tập trung: {{ $guestGroup['meeting_time'] ?: 'Chưa cập nhật' }}</div>
                </div>
                <div class="text-end">
                    <div class="small text-muted">Sức chứa đoàn</div>
                    <div class="fs-4 fw-bold">{{ (int) ($stats['total_people'] ?? 0) }}/{{ (int) ($guestGroup['max_participants'] ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="group-stat">
                <div class="text-muted small">Booking trong đoàn</div>
                <div class="value">{{ (int) ($stats['total_bookings'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="group-stat">
                <div class="text-muted small">Tổng khách</div>
                <div class="value text-primary">{{ (int) ($stats['total_people'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="group-stat">
                <div class="text-muted small">Đã check-in</div>
                <div class="value text-success">{{ (int) ($stats['checked_in_people'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="group-stat">
                <div class="text-muted small">Chưa check-in</div>
                <div class="value text-warning">{{ (int) ($stats['pending_check_in_people'] ?? 0) }}</div>
            </div>
        </div>
    </div>

    <div class="card group-card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Danh sách khách đoàn</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tên khách</th>
                            <th>Tour</th>
                            <th>Ngày đi</th>
                            <th>Số người</th>
                            <th>Email</th>
                            <th>SĐT</th>
                            <th>Thanh toán</th>
                            <th>Trạng thái</th>
                            <th>Ghi chú</th>
                            <th>Check-in</th>
                            <th>Chức năng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($assignedBookings))
                            <tr>
                                <td colspan="12" class="text-center text-muted">Đoàn này chưa có khách.</td>
                            </tr>
                        @else
                            @foreach($assignedBookings as $booking)
                                <tr>
                                    <td>#{{ $booking['id'] }}</td>
                                    <td>{{ $booking['customer_name'] }}</td>
                                    <td>{{ $booking['tour_name'] ?? '-' }}</td>
                                    <td>{{ !empty($booking['booking_date']) ? date('Y-m-d', strtotime($booking['booking_date'])) : '-' }}</td>
                                    <td>{{ $booking['num_people'] }}</td>
                                    <td>{{ $booking['customer_email'] }}</td>
                                    <td>{{ $booking['customer_phone'] }}</td>
                                    <td>{{ number_format($booking['total_price']) }}đ</td>
                                    <td>
                                        @if((int) $booking['status'] === 1)
                                            <span class="badge bg-success">Đã xác nhận</span>
                                        @elseif((int) $booking['status'] === 0)
                                            <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                                        @else
                                            <span class="badge bg-danger">Đã hủy</span>
                                        @endif
                                    </td>
                                    <td>{{ $booking['note'] ?: '-' }}</td>
                                    <td>
                                        @if((int) ($booking['check_in_status'] ?? 0) === 1)
                                            <span class="badge bg-success">Đã check-in</span>
                                            <div class="small text-muted mt-1">
                                                {{ !empty($booking['checked_in_at']) ? date('d/m/Y H:i', strtotime($booking['checked_in_at'])) : '' }}
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">Chưa check-in</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        @if((int) ($booking['check_in_status'] ?? 0) === 1)
                                            <a href="{{ route('admin/guest-groups/check-in-cancel/' . $guestGroup['id'] . '/' . $booking['id']) }}"
                                               class="btn btn-sm btn-outline-warning"
                                               onclick="return confirm('Bạn có chắc muốn hủy check-in khách này?')">
                                                Hủy check-in
                                            </a>
                                        @else
                                            <a href="{{ route('admin/guest-groups/check-in/' . $guestGroup['id'] . '/' . $booking['id']) }}"
                                               class="btn btn-sm btn-success">
                                                Check-in
                                            </a>
                                        @endif
                                        <a href="{{ route('admin/guest-groups/unassign/' . $guestGroup['id'] . '/' . $booking['id']) }}"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Bạn có chắc muốn xóa khách này khỏi đoàn?')">
                                            Xóa
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card group-card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Booking có thể thêm vào đoàn</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Khách đại diện</th>
                            <th>Số điện thoại</th>
                            <th>Số người</th>
                            <th>Ngày booking</th>
                            <th>Tổng tiền</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($availableBookings))
                            <tr>
                                <td colspan="7" class="text-center text-muted">Không còn booking phù hợp để thêm vào đoàn.</td>
                            </tr>
                        @else
                            @foreach($availableBookings as $booking)
                                <tr>
                                    <td>#{{ $booking['id'] }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $booking['customer_name'] }}</div>
                                        <div class="small text-muted">{{ $booking['customer_email'] }}</div>
                                    </td>
                                    <td>{{ $booking['customer_phone'] }}</td>
                                    <td>{{ $booking['num_people'] }}</td>
                                    <td>{{ !empty($booking['booking_date']) ? date('Y-m-d', strtotime($booking['booking_date'])) : '-' }}</td>
                                    <td>{{ number_format($booking['total_price']) }}đ</td>
                                    <td>
                                        <a href="{{ route('admin/guest-groups/assign/' . $guestGroup['id'] . '/' . $booking['id']) }}"
                                           class="btn btn-sm btn-primary">
                                            Thêm vào đoàn
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
