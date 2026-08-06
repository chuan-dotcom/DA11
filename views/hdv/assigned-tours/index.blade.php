@extends('layouts.hdv')

@section('title', 'Tour được phân công')

@section('content')

<div class="mb-3">
    <h4 class="fw-bold text-dark mb-1">Thông tin tour đã được phân bổ</h4>
</div>

<!-- Tour được phân công Table Card (theo định dạng Quản lý đoàn khách) -->
<div class="hdv-card mb-4">
    <div class="d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-folder-fill text-warning fs-5"></i>
        <h5 class="fw-bold mb-0">Tour được phân công ({{ count($assignedTours) }} đoàn)</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hdv table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên đoàn</th>
                    <th>Tour</th>
                    <th class="text-center">Số khách</th>
                    <th class="text-center">Check-in</th>
                    <th>Điểm hẹn</th>
                    <th>Ngày khởi hành</th>
                    <th>Trạng thái</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @if(empty($assignedTours))
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            Chưa có đoàn tour nào được phân công. Vui lòng liên hệ quản trị viên để được phân công cho chuyến khởi hành.
                        </td>
                    </tr>
                @else
                    @foreach($assignedTours as $t)
                        @php
                            $statusMap = [
                                'scheduled' => ['Chờ khởi hành', 'bg-warning text-dark'],
                                'in_progress' => ['Đang diễn ra', 'bg-primary'],
                                'completed' => ['Hoàn tất', 'bg-success'],
                                'cancelled' => ['Đã hủy', 'bg-secondary'],
                            ];
                            $statusInfo = $statusMap[$t['departure_status']] ?? [$t['departure_status'], 'bg-secondary'];
                            $groupName = $t['group_name'] ?: ('Đoàn ' . ($t['tour_name'] ?? 'Tour #' . $t['tour_id']));
                            $assignedPeople = (int)($t['assigned_people'] ?? 0);
                            $maxPeople = (int)($t['max_participants'] ?? 0);
                            $checkedIn = (int)($t['checked_in_guests'] ?? 0);
                            $totalGuests = (int)($t['total_guests'] ?? 0);
                            $roleMap = [
                                'lead_guide' => 'HDV chính',
                                'assistant_guide' => 'HDV phụ',
                                'driver' => 'Lái xe',
                                'other' => 'Nhân sự',
                            ];
                            $roleLabel = $roleMap[$t['hdv_role']] ?? $t['hdv_role'];
                        @endphp
                        <tr class="{{ (int)$selectedDepartureId === (int)$t['departure_id'] ? 'table-primary' : '' }}">
                            <td class="fw-bold text-dark">#{{ $t['departure_id'] }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $groupName }}</div>
                                <div class="text-muted small mt-1">
                                    <i class="bi bi-person-badge me-1"></i>Vai trò:
                                    <span class="badge bg-info-subtle text-dark">{{ $roleLabel }}</span>
                                </div>
                                @if(!empty($t['assignment_notes']))
                                    <div class="text-muted small mt-1"><i class="bi bi-chat-left-text me-1"></i>{{ $t['assignment_notes'] }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $t['tour_name'] }}</div>
                                <div class="text-muted small mt-1">Danh mục: {{ $t['category_name'] ?: 'Chưa phân loại' }}</div>
                                @if($t['total_bookings'] > 0)
                                    <div class="text-muted small mt-1"><i class="bi bi-receipt me-1"></i>{{ (int)$t['total_bookings'] }} booking</div>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info text-dark rounded-pill px-3 py-2">
                                    {{ $assignedPeople }}/{{ $maxPeople ?: '∞' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $checkedIn === $totalGuests && $totalGuests > 0 ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill px-3 py-2">
                                    <i class="bi bi-check2-circle me-1"></i>{{ $checkedIn }}/{{ $totalGuests }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $t['meeting_point'] ?: '— Chưa cập nhật —' }}</div>
                                @if(!empty($t['meeting_time']))
                                    <div class="text-muted small">Giờ: {{ date('H:i', strtotime($t['meeting_time'])) }}</div>
                                @endif
                            </td>
                            <td>
                                <div>Đi: {{ !empty($t['departure_date']) ? date('d/m/Y', strtotime($t['departure_date'])) : '—' }}</div>
                                <div class="text-muted small">Về: {{ !empty($t['return_date']) ? date('d/m/Y', strtotime($t['return_date'])) : '—' }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $statusInfo[1] }} rounded-pill px-3 py-2">{{ $statusInfo[0] }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('hdv/tour-phan-cong?departure_id=' . $t['departure_id']) }}" class="btn btn-sm btn-primary rounded-3 px-3 fw-bold me-1" title="Xem danh sách khách & check-in">
                                    <i class="bi bi-people-fill me-1"></i> Xem đoàn
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Detailed Customer List Section for Selected Tour -->
@if($selectedTour)
    @php
        $checkedInTotal = (int)($selectedTour['checked_in_guests'] ?? 0);
        $guestsTotal = (int)($selectedTour['total_guests'] ?? 0);
        $statusMap = [
            'scheduled' => ['Chờ khởi hành', 'bg-warning text-dark'],
            'in_progress' => ['Đang diễn ra', 'bg-primary'],
            'completed' => ['Hoàn tất', 'bg-success'],
            'cancelled' => ['Đã hủy', 'bg-secondary'],
        ];
        $statusInfo = $statusMap[$selectedTour['departure_status']] ?? [$selectedTour['departure_status'], 'bg-secondary'];
        $groupName = $selectedTour['group_name'] ?: ('Đoàn ' . ($selectedTour['tour_name'] ?? 'Tour #' . $selectedTour['tour_id']));
    @endphp
    <div class="hdv-card border-primary">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <h5 class="fw-bold text-primary mb-1">
                    <i class="bi bi-people-fill me-2"></i> Danh sách khách check-in: {{ $groupName }} (#{{ $selectedTour['departure_id'] }})
                </h5>
                <p class="text-muted small mb-1">
                    Tour: <strong class="text-dark">{{ $selectedTour['tour_name'] }}</strong>
                    · Điểm tập kết: {{ $selectedTour['meeting_point'] ?: 'Chưa cập nhật' }}
                    · Ngày đi: {{ !empty($selectedTour['departure_date']) ? date('d/m/Y', strtotime($selectedTour['departure_date'])) : '—' }}
                </p>
                <p class="mb-0 small">
                    <span class="me-3"><i class="bi bi-info-circle me-1"></i>Trạng thái: <span class="badge {{ $statusInfo[1] }}">{{ $statusInfo[0] }}</span></span>
                    <span class="me-3"><i class="bi bi-receipt me-1"></i>{{ (int)($selectedTour['total_bookings'] ?? 0) }} booking</span>
                    <span class="me-3"><i class="bi bi-people me-1"></i>Số khách: <strong>{{ (int)($selectedTour['assigned_people'] ?? 0) }}/{{ (int)($selectedTour['max_participants'] ?? 0) }}</strong></span>
                    <span><i class="bi bi-check2-circle me-1"></i>Check-in: <strong class="{{ $checkedInTotal === $guestsTotal && $guestsTotal > 0 ? 'text-success' : 'text-warning' }}">{{ $checkedInTotal }}/{{ $guestsTotal }}</strong></span>
                </p>
            </div>
            <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill">{{ count($guests) }} khách</span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">STT</th>
                        <th>Họ và Tên Khách Hàng</th>
                        <th>Giới tính</th>
                        <th>Liên hệ</th>
                        <th>CMND/CCCD</th>
                        <th class="text-center">Trạng thái Check-in</th>
                        <th>Ghi chú / Yêu cầu đặc biệt</th>
                    </tr>
                </thead>
                <tbody>
                    @if(empty($guests))
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Đoàn này chưa có dữ liệu danh sách khách hàng trong bảng <code>booking_guests</code>. Vui lòng liên hệ quản trị viên để thêm khách vào đoàn.
                            </td>
                        </tr>
                    @else
                        @foreach($guests as $idx => $g)
                            <tr>
                                <td class="fw-bold text-center">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $g['full_name'] }}</div>
                                    @if(!empty($g['dob']))
                                        <div class="text-muted small">Ngày sinh: {{ date('d/m/Y', strtotime($g['dob'])) }}</div>
                                    @endif
                                    @if(!empty($g['booking_id']))
                                        <div class="text-muted small mt-1">Booking #{{ (int)$g['booking_id'] }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($g['gender'] === 'male')
                                        <span class="badge bg-info-subtle text-dark"><i class="bi bi-gender-male me-1"></i> Nam</span>
                                    @elseif($g['gender'] === 'female')
                                        <span class="badge bg-danger-subtle text-dark"><i class="bi bi-gender-female me-1"></i> Nữ</span>
                                    @else
                                        <span class="text-muted">Khác</span>
                                    @endif
                                </td>
                                <td>
                                    <div><i class="bi bi-telephone text-primary me-1"></i> {{ $g['phone'] ?: 'N/A' }}</div>
                                    @if(!empty($g['email']))
                                        <div class="text-muted small"><i class="bi bi-envelope me-1"></i> {{ $g['email'] }}</div>
                                    @endif
                                </td>
                                <td>{{ $g['identity_no'] ?: '—' }}</td>
                                <td class="text-center">
                                    <form method="POST" action="{{ route('hdv/guest/check-in') }}" class="d-inline">
                                        <input type="hidden" name="guest_id" value="{{ $g['id'] }}">
                                        <input type="hidden" name="departure_id" value="{{ $selectedDepartureId }}">
                                        @if($g['check_in_status'] == 1)
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" title="Nhấn để đổi trạng thái">
                                                <i class="bi bi-check-circle-fill me-1"></i> Đã Check-in
                                            </button>
                                            @if(!empty($g['checked_in_at']))
                                                <div class="text-muted small mt-1">Lúc: {{ date('H:i d/m', strtotime($g['checked_in_at'])) }}</div>
                                            @endif
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="Nhấn để check-in">
                                                <i class="bi bi-circle me-1"></i> Chưa Check-in
                                            </button>
                                        @endif
                                    </form>
                                </td>
                                <td>
                                    @if(!empty($g['note']))
                                        <span class="badge bg-warning text-dark">{{ $g['note'] }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                    @if(!empty($g['payment_status']) && $g['payment_status'] !== 'unpaid')
                                        <div class="mt-1">
                                            @if($g['payment_status'] === 'paid')
                                                <span class="badge bg-success text-white"><i class="bi bi-cash-stack me-1"></i>Đã TT</span>
                                            @elseif($g['payment_status'] === 'deposit')
                                                <span class="badge bg-info text-white"><i class="bi bi-wallet2 me-1"></i>Đã cọc</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endif

@endsection
