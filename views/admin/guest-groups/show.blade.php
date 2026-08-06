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
            <a href="{{ route('admin/guest-groups/seed-customers/' . $guestGroup['id']) }}"
               class="btn btn-primary"
               onclick="return confirm('Tạo 17 khách hàng mẫu và gắn vào đoàn này?')">
                <i class="bi bi-people"></i> Danh sách khách hàng (17)
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
                    <div class="mb-1">
                        <span class="text-muted">Điểm tập trung:</span>
                        <span class="fw-semibold text-dark">{{ $guestGroup['meeting_point'] ?: 'Chưa cập nhật' }}</span>
                        @if(!empty($guestGroup['meeting_point']))
                            <small class="badge bg-info text-dark ms-2 align-middle" title="Giá trị này sẽ được tự động gắn làm Địa chỉ đón cho tất cả booking trong đoàn">
                                <i class="bi bi-link-45deg me-1"></i>Đã gắn làm địa chỉ đón
                            </small>
                        @endif
                    </div>
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
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">Quản lý khách hàng</h5>
                @if(!empty($assignedBookings) && !empty($selectedBookingId))
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('admin/guest-groups/booking-guests/create/' . $guestGroup['id'] . '/' . $selectedBookingId) }}"
                           class="btn btn-sm btn-success">
                            <i class="bi bi-person-plus"></i> Thêm khách
                        </a>
                        <a href="{{ route('admin/guest-groups/unassign/' . $guestGroup['id'] . '/' . $selectedBookingId) }}"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Gỡ booking này khỏi đoàn?')">
                            Gỡ booking
                        </a>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if(empty($assignedBookings))
                <div class="text-muted">Đoàn này chưa có booking nào để quản lý khách.</div>
            @else
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <form method="GET" action="{{ route('admin/guest-groups/show/' . $guestGroup['id']) }}" class="d-flex gap-2 align-items-center flex-wrap">
                        <div class="fw-semibold">Khách theo booking</div>
                        <select name="booking_id" class="form-select form-select-sm" style="min-width: 260px;" onchange="this.form.submit()">
                            @foreach($assignedBookings as $b)
                                <option value="{{ $b['id'] }}" {{ (int) $selectedBookingId === (int) $b['id'] ? 'selected' : '' }}>
                                    #{{ $b['id'] }} - {{ $b['customer_name'] }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                <div class="d-flex gap-2 align-items-center flex-wrap">
                        <span class="badge bg-primary">
                            {{ (int) ($bookingGuestStats['checked_in_guests'] ?? 0) }}/{{ (int) ($bookingGuestStats['total_guests'] ?? 0) }} khách đã check-in
                        </span>
                        @if(!empty($selectedBooking))
                            <span class="text-muted small">
                                Khởi hành: {{ !empty($selectedBooking['departure_date']) ? date('d/m/Y', strtotime($selectedBooking['departure_date'])) : '-' }}
                            </span>
                        @endif
                    </div>
                    @if(!empty($selectedBooking))
                        @php
                            $pickup = !empty($selectedBooking['pickup_address']) ? $selectedBooking['pickup_address'] : (!empty($selectedBooking['departure_meeting_point']) ? $selectedBooking['departure_meeting_point'] : null);
                        @endphp
                        <div class="mt-3 p-2 rounded bg-light border d-flex gap-3 flex-wrap align-items-center">
                            <div>
                                <div class="small text-muted">Khách đại diện</div>
                                <div class="fw-semibold">{{ $selectedBooking['customer_name'] }}</div>
                            </div>
                            <div>
                                <div class="small text-muted">SĐT</div>
                                <div class="fw-semibold">{{ $selectedBooking['customer_phone'] }}</div>
                            </div>
                            <div class="min-w-0 flex-grow-1">
                                <div class="small text-muted">Địa chỉ đón</div>
                                <div class="fw-semibold d-flex gap-1 align-items-center min-w-0">
                                    <i class="bi bi-geo text-primary"></i>
                                    <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                        {{ $pickup ?: '<span class="text-muted">Chưa có</span>' }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="small text-muted">Số người</div>
                                <div class="fw-semibold">{{ $selectedBooking['num_people'] }} người</div>
                            </div>
                            <div>
                                <div class="small text-muted">Tổng tiền</div>
                                <div class="fw-semibold text-danger">{{ number_format($selectedBooking['total_price']) }}đ</div>
                            </div>
                        </div>
                    @endif
                </div>

                @php
                    $paymentMap = [
                        'unpaid' => ['Chưa thanh toán', 'bg-warning text-dark'],
                        'deposit' => ['Đã đặt cọc', 'bg-info text-dark'],
                        'paid' => ['Đã thanh toán', 'bg-success'],
                    ];
                @endphp

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="70">ID</th>
                                <th>Họ tên</th>
                                <th>Thông tin</th>
                                <th>Địa chỉ</th>
                                <th width="140">Thanh toán</th>
                                <th width="140">Check-in</th>
                                <th width="140">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(empty($bookingGuests))
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Chưa có khách trong booking này.</td>
                                </tr>
                            @else
                                @foreach($bookingGuests as $guest)
                                    @php
                                        $payment = $paymentMap[$guest['payment_status'] ?? 'unpaid'] ?? ['-', 'bg-secondary'];
                                    @endphp
                                    <tr>
                                        <td>{{ $guest['id'] }}</td>
                                        <td class="fw-semibold">{{ $guest['full_name'] }}</td>
                                        <td>
                                            <div class="small text-muted">
                                                {{ !empty($guest['dob']) ? date('Y-m-d', strtotime($guest['dob'])) : '' }}
                                            </div>
                                            <div>{{ $guest['phone'] ?: '-' }}</div>
                                            <div class="small text-muted">{{ $guest['email'] ?: '-' }}</div>
                                            <div class="small text-muted">{{ $guest['identity_no'] ?: '-' }}</div>
                                        </td>
                                        <td>{{ $guest['address'] ?: '-' }}</td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                <span class="badge {{ $payment[1] }} w-100 py-2">{{ $payment[0] }}</span>
                                                @if(($guest['payment_status'] ?? 'unpaid') !== 'paid')
                                                    <a href="{{ route('admin/guest-groups/booking-guests/payment-paid/' . $guestGroup['id'] . '/' . $guest['id']) }}"
                                                       class="btn btn-sm btn-success w-100"
                                                       onclick="return confirm('Đánh dấu Đã thanh toán cho TẤT CẢ khách trong cùng booking này?')">
                                                        <i class="bi bi-check2-circle me-1"></i>Đánh giá Đã thanh toán
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin/guest-groups/booking-guests/payment-unpaid/' . $guestGroup['id'] . '/' . $guest['id']) }}"
                                                       class="btn btn-sm btn-outline-warning w-100"
                                                       onclick="return confirm('Bỏ thanh toán cho TẤT CẢ khách trong cùng booking này?')">
                                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Bỏ thanh toán
                                                    </a>
                                                @endif
                                                <small class="text-muted text-center opacity-75">Đồng bộ toàn bộ booking</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if((int) ($guest['check_in_status'] ?? 0) === 1)
                                                <span class="badge bg-success">Đã check-in</span>
                                                <div class="small text-muted mt-1">
                                                    {{ !empty($guest['checked_in_at']) ? date('d/m/Y H:i', strtotime($guest['checked_in_at'])) : '' }}
                                                </div>
                                            @else
                                                <span class="badge bg-secondary">Chưa check-in</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            @if((int) ($guest['check_in_status'] ?? 0) === 1)
                                                <a href="{{ route('admin/guest-groups/booking-guests/check-in-cancel/' . $guestGroup['id'] . '/' . $guest['id']) }}"
                                                   class="btn btn-sm btn-outline-warning"
                                                   onclick="return confirm('Hủy check-in khách này?')">
                                                    Hủy
                                                </a>
                                            @else
                                                <a href="{{ route('admin/guest-groups/booking-guests/check-in/' . $guestGroup['id'] . '/' . $guest['id']) }}"
                                                   class="btn btn-sm btn-success">
                                                    Check-in
                                                </a>
                                            @endif
                                            <a href="{{ route('admin/guest-groups/booking-guests/edit/' . $guestGroup['id'] . '/' . $guest['id']) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="{{ route('admin/guest-groups/booking-guests/delete/' . $guestGroup['id'] . '/' . $guest['id']) }}"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Xóa khách này?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card group-card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Danh sách khách hàng có thể thêm vào đoàn</h5>
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
                            <th>Địa chỉ đón</th>
                            <th>Ngày booking</th>
                            <th>Tổng tiền</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($availableBookings))
                            <tr>
                                <td colspan="8" class="text-center text-muted">Không còn booking phù hợp để thêm vào đoàn.</td>
                            </tr>
                        @else
                            @foreach($availableBookings as $booking)
                                @php $pickupAv = !empty($booking['pickup_address']) ? $booking['pickup_address'] : null; @endphp
                                <tr>
                                    <td>#{{ $booking['id'] }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $booking['customer_name'] }}</div>
                                        <div class="small text-muted">{{ $booking['customer_email'] }}</div>
                                    </td>
                                    <td>{{ $booking['customer_phone'] }}</td>
                                    <td>{{ $booking['num_people'] }}</td>
                                    <td>
                                        @if(!empty($pickupAv))
                                            <span class="d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-geo text-primary"></i>
                                                <span style="max-width:200px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ htmlentities($pickupAv) }}">{{ $pickupAv }}</span>
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Sẽ lấy từ Điểm tập đoàn</span>
                                        @endif
                                    </td>
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
