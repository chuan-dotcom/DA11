@extends('layouts.hdv')

@section('title', 'Tour được phân công')

@section('content')

@php
    $assignmentBadgeClass = function (string $s): string {
        return match($s) {
            'confirmed' => 'bg-success',
            'assigned'  => 'bg-primary',
            'pending'   => 'bg-warning text-dark',
            default     => 'bg-secondary',
        };
    };
    $assignmentBadgeText = function (string $s): string {
        return match($s) {
            'confirmed' => 'Đã xác nhận',
            'assigned'  => 'Đã phân bổ',
            'pending'   => 'Chờ xác nhận',
            default     => mb_convert_case($s, MB_CASE_TITLE, 'UTF-8'),
        };
    };
    $roleBadgeClass = function (string $r): string {
        $r = mb_strtolower($r);
        if (str_contains($r, 'hdv') || str_contains($r, 'chính') || str_contains($r, 'hướng dẫn')) {
            return 'bg-primary';
        }
        if (str_contains($r, 'lái') || str_contains($r, 'xe') || str_contains($r, 'tài')) {
            return 'bg-warning text-dark';
        }
        if (str_contains($r, 'nhiếp') || str_contains($r, 'ảnh')) {
            return 'bg-success';
        }
        if (str_contains($r, 'phục') || str_contains($r, 'vụ')) {
            return 'bg-info text-dark';
        }
        return 'bg-secondary';
    };
@endphp

<div class="mb-3">
    <h4 class="fw-bold text-dark mb-1">Thông tin tour đã được phân bổ</h4>
</div>

<<<<<<< HEAD
<!-- Tour được phân công Table Card (theo định dạng Quản lý đoàn khách) -->
=======
<!-- Tour được phân công Table Card (đồng bộ cột theo admin staff-assignments) -->
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
<div class="hdv-card mb-4">
    <div class="d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-folder-fill text-warning fs-5"></i>
        <h5 class="fw-bold mb-0">Tour được phân công ({{ count($assignedTours) }} đoàn)</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hdv table-hover align-middle">
            <thead>
                <tr>
<<<<<<< HEAD
                    <th>ID</th>
                    <th>Tên đoàn</th>
                    <th>Tour</th>
                    <th class="text-center">Số khách</th>
                    <th class="text-center">Check-in</th>
                    <th>Điểm hẹn</th>
                    <th>Ngày khởi hành</th>
                    <th>Trạng thái</th>
=======
                    <th>Mã</th>
                    <th>Tên Tour</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Khách hàng</th>
                    <th>Điểm hẹn</th>
                    <th>Giờ bắt đầu</th>
                    <th>Ngày đi</th>
                    <th>Ngày kết thúc</th>
                    <th>Ngày phân bổ</th>
                    <th>Ghi chú</th>
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @if(empty($assignedTours))
                    <tr>
<<<<<<< HEAD
                        <td colspan="9" class="text-center py-5 text-muted">
                            Chưa có đoàn tour nào được phân công. Vui lòng liên hệ quản trị viên để được phân công cho chuyến khởi hành.
=======
                        <td colspan="12" class="text-center py-5 text-muted">
                            Chưa có tour nào được phân công.
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
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
<<<<<<< HEAD
                                <div class="fw-semibold text-dark">{{ $groupName }}</div>
                                <div class="text-muted small mt-1">
                                    <i class="bi bi-person-badge me-1"></i>Vai trò:
                                    <span class="badge bg-info-subtle text-dark">{{ $roleLabel }}</span>
                                </div>
                                @if(!empty($t['assignment_notes']))
                                    <div class="text-muted small mt-1"><i class="bi bi-chat-left-text me-1"></i>{{ $t['assignment_notes'] }}</div>
=======
                                <a href="{{ route('hdv/dashboard') . '?tab=chi-tiet&departure_id=' . $t['departure_id'] }}" class="fw-bold text-primary text-decoration-none">
                                    {{ $t['tour_name'] }}
                                </a>
                                @if(!empty($t['group_name']))
                                    <div class="text-muted small">({{ $t['group_name'] }})</div>
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
                                @endif
                            </td>
                            <td>
<<<<<<< HEAD
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
=======
                                <span class="badge {{ $roleBadgeClass($t['hdv_role'] ?? 'HDV') }}">
                                    {{ $t['hdv_role'] ? mb_convert_case($t['hdv_role'], MB_CASE_TITLE, 'UTF-8') : 'HDV' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $assignmentBadgeClass($t['assignment_status'] ?? 'assigned') }}">
                                    {{ $assignmentBadgeText($t['assignment_status'] ?? 'assigned') }}
                                </span>
                                @php
                                    $depStatus = $t['departure_status'] ?? '';
                                    $depBadge = match($depStatus) {
                                        'in_progress' => ['text' => 'Đang diễn ra', 'class' => 'bg-success'],
                                        'completed'   => ['text' => 'Hoàn thành', 'class' => 'bg-secondary'],
                                        'cancelled'   => ['text' => 'Đã hủy', 'class' => 'bg-danger'],
                                        default       => ['text' => 'Lên lịch', 'class' => 'bg-primary'],
                                    };
                                @endphp
                                <div class="mt-1">
                                    <span class="badge {{ $depBadge['class'] }} opacity-75">{{ $depBadge['text'] }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $t['primary_customer_name'] ?: 'Khách đoàn' }}</div>
                                <div class="text-muted small">{{ $t['total_guests'] }} người</div>
                            </td>
                            <td>{{ $t['meeting_point'] ?: '—' }}</td>
                            <td>{{ $t['meeting_time'] ?: '—' }}</td>
                            <td>{{ !empty($t['departure_date']) ? date('d/m/Y', strtotime($t['departure_date'])) : '—' }}</td>
                            <td>{{ !empty($t['return_date']) ? date('d/m/Y', strtotime($t['return_date'])) : '—' }}</td>
                            <td>{{ !empty($t['assigned_at']) ? date('d/m/Y H:i', strtotime($t['assigned_at'])) : '—' }}</td>
                            <td>{{ $t['assignment_notes'] ?: '—' }}</td>
                            <td class="text-center text-nowrap">
                                <a href="{{ route('hdv/tour-phan-cong') . '?departure_id=' . $t['departure_id'] }}" class="btn btn-sm btn-primary rounded-3 px-3 fw-bold me-1">
                                    <i class="bi bi-people me-1"></i> Khách
                                </a>
                                <a href="{{ route('hdv/dashboard') . '?tab=chi-tiet&departure_id=' . $t['departure_id'] }}" class="btn btn-sm btn-outline-primary rounded-3 px-3 fw-bold">
                                    <i class="bi bi-eye me-1"></i> Chi tiết
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
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
<<<<<<< HEAD
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
=======
    <div class="hdv-card border-primary mb-4">
        <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-3">
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
            <div>
                <h5 class="fw-bold text-primary mb-1">
                    <i class="bi bi-people-fill me-2"></i> Danh sách khách check-in: {{ $groupName }} (#{{ $selectedTour['departure_id'] }})
                </h5>
<<<<<<< HEAD
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
=======
                <p class="text-muted small mb-0">Ngày đi: {{ date('d/m/Y', strtotime($selectedTour['departure_date'])) }} | Điểm hẹn: {{ $selectedTour['meeting_point'] ?: 'Chưa ghi chú' }} | Giờ: {{ $selectedTour['meeting_time'] ?: '—' }}</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <span class="badge {{ $roleBadgeClass($selectedTour['hdv_role'] ?? 'HDV') }}">
                    {{ $selectedTour['hdv_role'] ? mb_convert_case($selectedTour['hdv_role'], MB_CASE_TITLE, 'UTF-8') : 'HDV' }}
                </span>
                <span class="badge {{ $assignmentBadgeClass($selectedTour['assignment_status'] ?? 'assigned') }}">
                    {{ $assignmentBadgeText($selectedTour['assignment_status'] ?? 'assigned') }}
                </span>
                <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill">{{ count($guests) }} Khách</span>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="card border-0 bg-light">
                    <div class="card-body py-2">
                        <h6 class="small fw-bold text-muted mb-2"><i class="bi bi-info-circle me-1"></i> Thông tin phân bổ</h6>
                        <ul class="list-unstyled mb-0 small">
                            <li><strong>Vai trò:</strong> {{ $selectedTour['hdv_role'] ? mb_convert_case($selectedTour['hdv_role'], MB_CASE_TITLE, 'UTF-8') : 'HDV' }}</li>
                            <li><strong>Ngày phân bổ:</strong> {{ !empty($selectedTour['assigned_at']) ? date('d/m/Y H:i', strtotime($selectedTour['assigned_at'])) : '—' }}</li>
                            <li><strong>Cập nhật lần cuối:</strong> {{ !empty($selectedTour['updated_at']) ? date('d/m/Y H:i', strtotime($selectedTour['updated_at'])) : '—' }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 bg-light">
                    <div class="card-body py-2">
                        <h6 class="small fw-bold text-muted mb-2"><i class="bi bi-sticky me-1 text-warning"></i> Trách nhiệm & Ghi chú từ quản trị</h6>
                        <div class="small mb-0">
                            @if(!empty($selectedTour['assignment_responsibilities']))
                                <p class="mb-1"><strong>Trách nhiệm:</strong> {{ nl2br(e($selectedTour['assignment_responsibilities'])) }}</p>
                            @endif
                            @if(!empty($selectedTour['assignment_notes']))
                                <p class="mb-0"><strong>Ghi chú:</strong> {{ nl2br(e($selectedTour['assignment_notes'])) }}</p>
                            @endif
                            @if(empty($selectedTour['assignment_responsibilities']) && empty($selectedTour['assignment_notes']))
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
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
