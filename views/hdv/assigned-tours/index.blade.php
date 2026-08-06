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

<!-- Tour được phân công Table Card (đồng bộ cột theo admin staff-assignments) -->
<div class="hdv-card mb-4">
    <div class="d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-folder-fill text-warning fs-5"></i>
        <h5 class="fw-bold mb-0">Tour được phân công ({{ count($assignedTours) }} tour)</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hdv table-hover align-middle">
            <thead>
                <tr>
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
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @if(empty($assignedTours))
                    <tr>
                        <td colspan="12" class="text-center py-5 text-muted">
                            Chưa có tour nào được phân công.
                        </td>
                    </tr>
                @else
                    @foreach($assignedTours as $t)
                        <tr class="{{ (int)$selectedDepartureId === (int)$t['departure_id'] ? 'table-primary' : '' }}">
                            <td class="fw-bold text-dark">#{{ $t['primary_booking_id'] ?: $t['departure_id'] }}</td>
                            <td>
                                <a href="{{ route('hdv/dashboard') . '?tab=chi-tiet&departure_id=' . $t['departure_id'] }}" class="fw-bold text-primary text-decoration-none">
                                    {{ $t['tour_name'] }}
                                </a>
                                @if(!empty($t['group_name']))
                                    <div class="text-muted small">({{ $t['group_name'] }})</div>
                                @endif
                                <div class="text-muted small mt-1">Danh mục: {{ $t['category_name'] ?: 'Chưa phân loại' }}</div>
                            </td>
                            <td>
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
    <div class="hdv-card border-primary mb-4">
        <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-3">
            <div>
                <h5 class="fw-bold text-primary mb-1">
                    <i class="bi bi-people-fill me-2"></i> Danh sách khách hàng: {{ $selectedTour['tour_name'] }} (#{{ $selectedTour['departure_id'] }})
                </h5>
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
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">STT</th>
                        <th>Họ và Tên Khách Hang</th>
                        <th>Giới tính</th>
                        <th>Liên hệ</th>
                        <th>CMND/CCCD</th>
                        <th>Trạng thái Check-in</th>
                        <th>Ghi chú / Yêu cầu đặc biệt</th>
                    </tr>
                </thead>
                <tbody>
                    @if(empty($guests))
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Chưa tìm thấy dữ liệu danh sách khách hàng trong bảng `booking_guests`.
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
                                <td>
                                    <form method="POST" action="{{ route('hdv/guest/check-in') }}" class="d-inline">
                                        <input type="hidden" name="guest_id" value="{{ $g['id'] }}">
                                        <input type="hidden" name="departure_id" value="{{ $selectedDepartureId }}">
                                        @if($g['check_in_status'] == 1)
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" title="Nhấn để đổi trạng thái">
                                                <i class="bi bi-check-circle-fill me-1"></i> Đã Check-in
                                            </button>
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
