@extends('layouts.hdv')

@section('title', 'Thông tin tour')

@section('content')

<!-- Tabs Navigation -->
<ul class="nav hdv-tabs">
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'danh-sach' ? 'active' : '' }}" href="{{ route('hdv/dashboard?tab=danh-sach') }}">
            <i class="bi bi-journal-text me-1"></i> Danh sách tour
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'chi-tiet' ? 'active' : '' }}" href="{{ route('hdv/dashboard?tab=chi-tiet') }}">
            <i class="bi bi-bar-chart-steps me-1"></i> Chi tiết tour
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('hdv/lich-trinh') }}">
            <i class="bi bi-calendar-event me-1"></i> Lịch trình
        </a>
    </li>
</ul>

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
@endphp

@if($activeTab === 'danh-sach')
    <!-- TAB 1: DANH SÁCH TOUR (ĐÃ, ĐANG, SẼ TIẾN HÀNH) -->
    <div class="hdv-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0 text-secondary"><i class="bi bi-list-stars text-info me-2"></i> Danh sách Tour Phân Công theo Trạng Thái</h5>
            <span class="badge bg-info text-dark font-weight-bold">Tổng cộng: {{ count($assignedTours) }} tour</span>
        </div>

        <!-- Sub Tabs or Sections for Đang, Sẽ, Đã -->
        <ul class="nav nav-pills mb-4 gap-2" id="tourPills" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active bg-success text-white fw-bold rounded-pill px-3 py-1 text-sm" id="ongoing-tab" data-bs-toggle="pill" data-bs-target="#ongoing" type="button" role="tab">
                    <i class="bi bi-play-circle-fill me-1"></i> Đang tiến hành ({{ count($ongoingTours) }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link bg-primary text-white fw-bold rounded-pill px-3 py-1 text-sm" id="upcoming-tab" data-bs-toggle="pill" data-bs-target="#upcoming" type="button" role="tab">
                    <i class="bi bi-clock-history me-1"></i> Sẽ tiến hành ({{ count($upcomingTours) }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link bg-secondary text-white fw-bold rounded-pill px-3 py-1 text-sm" id="completed-tab" data-bs-toggle="pill" data-bs-target="#completed" type="button" role="tab">
                    <i class="bi bi-check-circle-fill me-1"></i> Đã tiến hành ({{ count($completedTours) }})
                </button>
            </li>
        </ul>

        <div class="tab-content" id="tourPillsContent">
            <!-- ĐANG TIẾN HÀNH -->
            <div class="tab-pane fade show active" id="ongoing" role="tabpanel">
                @if(empty($ongoingTours))
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-info-circle fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        Hiện tại bạn không có tour nào đang tiến hành.
                    </div>
                @else
                    <div class="row g-3">
                        @foreach($ongoingTours as $t)
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-success shadow-sm rounded-4 overflow-hidden">
                                    <div class="card-header bg-success text-white fw-bold d-flex justify-content-between gap-2 align-items-center">
                                        <span>#{{ $t['departure_id'] }} - {{ $t['tour_name'] }}</span>
                                        <div class="d-flex gap-1 align-items-center">
                                            <span class="badge bg-light text-success">Đang đi</span>
                                            <span class="badge {{ $assignmentBadgeClass($t['assignment_status'] ?? 'assigned') }} border-0">
                                                {{ $assignmentBadgeText($t['assignment_status'] ?? 'assigned') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text mb-1"><i class="bi bi-person-badge text-muted me-1"></i> <strong>Vai trò:</strong> {{ $t['hdv_role'] ? mb_convert_case($t['hdv_role'], MB_CASE_TITLE, 'UTF-8') : 'HDV' }}</p>
                                        <p class="card-text mb-1"><i class="bi bi-tags text-muted me-1"></i> <strong>Danh mục:</strong> {{ $t['category_name'] ?: 'Chưa phân loại' }}</p>
                                        <p class="card-text mb-1"><i class="bi bi-calendar3 text-muted me-1"></i> <strong>Khởi hành:</strong> {{ date('d/m/Y', strtotime($t['departure_date'])) }}</p>
                                        <p class="card-text mb-1"><i class="bi bi-clock text-muted me-1"></i> <strong>Giờ tập trung:</strong> {{ $t['meeting_time'] ?: 'Chưa cập nhật' }}</p>
                                        <p class="card-text mb-1"><i class="bi bi-geo-alt text-muted me-1"></i> <strong>Điểm hẹn:</strong> {{ $t['meeting_point'] ?: 'Chưa cập nhật' }}</p>
                                        <p class="card-text mb-3"><i class="bi bi-truck text-muted me-1"></i> <strong>Xe:</strong> {{ $t['vehicle'] ?: 'Chưa gán' }}</p>
                                        <a href="{{ route('hdv/dashboard') . '?tab=chi-tiet&departure_id=' . $t['departure_id'] }}" class="btn btn-sm btn-success w-100 fw-bold">
                                            <i class="bi bi-eye me-1"></i> Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- SẼ TIẾN HÀNH -->
            <div class="tab-pane fade" id="upcoming" role="tabpanel">
                @if(empty($upcomingTours))
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        Không có tour sắp tới.
                    </div>
                @else
                    <div class="row g-3">
                        @foreach($upcomingTours as $t)
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-primary shadow-sm rounded-4 overflow-hidden">
                                    <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between gap-2 align-items-center">
                                        <span>#{{ $t['departure_id'] }} - {{ $t['tour_name'] }}</span>
                                        <div class="d-flex gap-1 align-items-center">
                                            <span class="badge bg-light text-primary">Sắp khởi hành</span>
                                            <span class="badge {{ $assignmentBadgeClass($t['assignment_status'] ?? 'assigned') }} border-0">
                                                {{ $assignmentBadgeText($t['assignment_status'] ?? 'assigned') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text mb-1"><i class="bi bi-person-badge text-muted me-1"></i> <strong>Vai trò:</strong> {{ $t['hdv_role'] ? mb_convert_case($t['hdv_role'], MB_CASE_TITLE, 'UTF-8') : 'HDV' }}</p>
                                        <p class="card-text mb-1"><i class="bi bi-calendar3 text-muted me-1"></i> <strong>Khởi hành:</strong> {{ date('d/m/Y', strtotime($t['departure_date'])) }}</p>
                                        <p class="card-text mb-1"><i class="bi bi-clock text-muted me-1"></i> <strong>Giờ tập trung:</strong> {{ $t['meeting_time'] ?: 'Chưa cập nhật' }}</p>
                                        <p class="card-text mb-3"><i class="bi bi-geo-alt text-muted me-1"></i> <strong>Điểm hẹn:</strong> {{ $t['meeting_point'] ?: 'Chưa cập nhật' }}</p>
                                        <a href="{{ route('hdv/dashboard') . '?tab=chi-tiet&departure_id=' . $t['departure_id'] }}" class="btn btn-sm btn-primary w-100 fw-bold">
                                            <i class="bi bi-eye me-1"></i> Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- ĐÃ TIẾN HÀNH -->
            <div class="tab-pane fade" id="completed" role="tabpanel">
                @if(empty($completedTours))
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-archive fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        Chưa có lịch sử tour đã hoàn thành.
                    </div>
                @else
                    <div class="row g-3">
                        @foreach($completedTours as $t)
                            @php
                                $isCancelled = ($t['departure_status'] ?? '') === 'cancelled';
                            @endphp
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 {{ $isCancelled ? 'border-danger' : 'border-secondary' }} shadow-sm rounded-4 overflow-hidden">
                                    <div class="card-header {{ $isCancelled ? 'bg-danger' : 'bg-secondary' }} text-white fw-bold d-flex justify-content-between gap-2 align-items-center">
                                        <span>#{{ $t['departure_id'] }} - {{ $t['tour_name'] }}</span>
                                        <div class="d-flex gap-1 align-items-center">
                                            <span class="badge bg-light {{ $isCancelled ? 'text-danger' : 'text-dark' }}">
                                                {{ $isCancelled ? 'Đã hủy' : 'Đã kết thúc' }}
                                            </span>
                                            <span class="badge {{ $assignmentBadgeClass($t['assignment_status'] ?? 'assigned') }} border-0">
                                                {{ $assignmentBadgeText($t['assignment_status'] ?? 'assigned') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text mb-1"><i class="bi bi-person-badge text-muted me-1"></i> <strong>Vai trò:</strong> {{ $t['hdv_role'] ? mb_convert_case($t['hdv_role'], MB_CASE_TITLE, 'UTF-8') : 'HDV' }}</p>
                                        <p class="card-text mb-1"><i class="bi bi-calendar-check text-muted me-1"></i> <strong>Khởi hành:</strong> {{ date('d/m/Y', strtotime($t['departure_date'])) }}</p>
                                        <p class="card-text mb-3"><i class="bi bi-flag text-muted me-1"></i> <strong>Kết thúc:</strong> {{ $t['return_date'] ? date('d/m/Y', strtotime($t['return_date'])) : '-' }}</p>
                                        <a href="{{ route('hdv/dashboard') . '?tab=chi-tiet&departure_id=' . $t['departure_id'] }}" class="btn btn-sm {{ $isCancelled ? 'btn-outline-danger' : 'btn-outline-secondary' }} w-100 fw-bold">
                                            <i class="bi bi-eye me-1"></i> Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@else
    <!-- TAB 2: CHI TIẾT TOUR (MATCHING SCREENSHOT 1) -->

    <!-- Departure selector if multiple assigned tours -->
    @if(count($assignedTours) > 1)
        <div class="card mb-3 border-0 shadow-sm rounded-4">
            <div class="card-body py-2">
                <form method="GET" action="{{ route('hdv/dashboard') }}" class="row align-items-center">
                    <input type="hidden" name="tab" value="chi-tiet">
                    <div class="col-auto">
                        <label class="fw-bold small text-muted"><i class="bi bi-filter me-1"></i> Chọn chuyến khởi hành xem chi tiết:</label>
                    </div>
                    <div class="col-md-6">
                        <select name="departure_id" class="form-select form-select-sm fw-semibold" onchange="this.form.submit()">
                            @foreach($assignedTours as $tour)
                                <option value="{{ $tour['departure_id'] }}" {{ $selectedDepartureId == $tour['departure_id'] ? 'selected' : '' }}>
                                    #{{ $tour['departure_id'] }} - {{ $tour['tour_name'] }} ({{ date('d/m/Y', strtotime($tour['departure_date'])) }}) · {{ $assignmentBadgeText($tour['assignment_status'] ?? 'assigned') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Card Top Banner matching Screenshot 1 -->
    <div class="hdv-card mb-4">
        <h4 class="fw-bold text-dark mb-1">Bảng Điều Khiển Hướng Dẫn Viên</h4>
        <p class="text-muted small mb-1">Quản lý thông tin tour và lịch trình hằng ngày</p>
        <p class="text-muted small mb-3">
            Danh mục tour hiện tại: {{ $currentTourDetail['category_name'] ?? 'Chưa phân loại' }}
            @if(!empty($currentTourDetail))
                · <span class="badge {{ $assignmentBadgeClass($currentTourDetail['assignment_status'] ?? 'assigned') }}">
                    {{ $assignmentBadgeText($currentTourDetail['assignment_status'] ?? 'assigned') }}
                </span>
            @endif
        </p>
        <div class="row g-3">
            <!-- Card 1: Số lượng khách -->
            <div class="col-md-4">
                <div class="stat-pill d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 text-white" style="background-color: #7c3aed;">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Số lượng khách</div>
                        <div class="fs-4 fw-bold text-dark">{{ count($guests) }} người</div>
                        <span class="badge-confirmed"><i class="bi bi-check-circle-fill me-1"></i> ĐÃ XÁC NHẬN</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Nhà xe -->
            <div class="col-md-4">
                <div class="stat-pill d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 text-primary bg-light">
                        <i class="bi bi-truck fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Nhà xe</div>
                        <div class="fs-5 fw-bold text-dark">{{ $currentTourDetail['vehicle'] ?? '—' }}</div>
                        <div class="text-muted small">SL: {{ count($guests) }}</div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Tài xế -->
            <div class="col-md-4">
                <div class="stat-pill d-flex align-items-center gap-3">
                    <div class="rounded-circle p-3 text-dark bg-light">
                        <i class="bi bi-person-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Tài xế</div>
                        <div class="fs-6 fw-bold text-dark">{{ $driverInfo['Hoten'] ?? '—' }}</div>
                        <div class="text-muted small">SĐT: {{ $driverInfo['Lienhe'] ?? '0912 345 678' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($currentTourDetail))
    <div class="hdv-card mb-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-info-circle text-primary fs-5"></i>
            <h5 class="fw-bold mb-0">Thông tin phân bổ từ quản trị</h5>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <ul class="list-group list-group-flush border rounded-3">
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Chuyến khởi hành</span><strong>#{{ $currentTourDetail['departure_id'] }} · {{ $currentTourDetail['tour_name'] }}</strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Vai trò</span><strong>{{ $currentTourDetail['hdv_role'] ? mb_convert_case($currentTourDetail['hdv_role'], MB_CASE_TITLE, 'UTF-8') : 'HDV' }}</strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Trạng thái phân bổ</span><span class="badge {{ $assignmentBadgeClass($currentTourDetail['assignment_status'] ?? 'assigned') }}">{{ $assignmentBadgeText($currentTourDetail['assignment_status'] ?? 'assigned') }}</span></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Trạng thái chuyến đi</span><strong>{{ match($currentTourDetail['departure_status'] ?? '') {
                        'scheduled' => 'Lên lịch',
                        'in_progress' => 'Đang diễn ra',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                        default => '—',
                    } }}</strong></li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="list-group list-group-flush border rounded-3">
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Ngày khởi hành</span><strong>{{ date('d/m/Y', strtotime($currentTourDetail['departure_date'])) }}</strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Ngày trở về</span><strong>{{ $currentTourDetail['return_date'] ? date('d/m/Y', strtotime($currentTourDetail['return_date'])) : '—' }}</strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Giờ tập trung</span><strong>{{ $currentTourDetail['meeting_time'] ?: '—' }}</strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Điểm hẹn</span><strong class="text-end">{{ $currentTourDetail['meeting_point'] ?: '—' }}</strong></li>
                </ul>
            </div>
            @if(!empty($currentTourDetail['assignment_responsibilities']) || !empty($currentTourDetail['assignment_notes']))
            <div class="col-md-12">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        @if(!empty($currentTourDetail['assignment_responsibilities']))
                        <h6 class="mb-2"><i class="bi bi-list-check me-1 text-primary"></i> Trách nhiệm</h6>
                        <p class="mb-3">{{ nl2br(e($currentTourDetail['assignment_responsibilities'])) }}</p>
                        @endif
                        @if(!empty($currentTourDetail['assignment_notes']))
                        <h6 class="mb-2"><i class="bi bi-sticky me-1 text-warning"></i> Ghi chú từ quản trị</h6>
                        <p class="mb-0">{{ nl2br(e($currentTourDetail['assignment_notes'])) }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="hdv-card mb-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-clock-history text-secondary fs-5"></i>
            <h5 class="fw-bold mb-0">Lịch trình hoạt động tour</h5>
            <span class="badge bg-secondary text-white border rounded-pill">{{ count($tourLogs) }} hoạt động</span>
        </div>

        @if(empty($tourLogs))
            <div class="text-center py-5 text-muted">
                <i class="bi bi-info-circle fs-1 d-block mb-2 text-secondary opacity-50"></i>
                Chưa có ghi nhận lịch trình hoạt động cho chuyến này.
            </div>
        @else
            <div class="timeline-list">
                @foreach($tourLogs as $log)
                    <div class="timeline-item mb-4 p-4 rounded-4 border bg-white shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $log['title'] }}</h6>
                                <div class="text-muted small">
                                    {{ date('d/m/Y H:i', strtotime($log['log_date'])) }}
                                    @if(!empty($log['location']))
                                        · {{ $log['location'] }}
                                    @endif
                                </div>
                            </div>
                            <span class="badge bg-primary-subtle text-primary rounded-pill">{{ $log['weather'] ?: 'Thời tiết chưa rõ' }}</span>
                        </div>
                        <p class="text-dark mb-2">{{ $log['content'] }}</p>
                        <div class="d-flex flex-wrap gap-2 text-muted small">
                            @if(!empty($log['mood']))
                                <span class="badge bg-light text-dark border">Tâm trạng: {{ $log['mood'] }}</span>
                            @endif
                            @if(!empty($log['location']))
                                <span class="badge bg-light text-dark border">Địa điểm: {{ $log['location'] }}</span>
                            @endif
                            @if(!empty($log['weather']))
                                <span class="badge bg-light text-dark border">Thời tiết: {{ $log['weather'] }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Section: Danh sách đoàn khách (matching Screenshot 1) -->
    <div class="hdv-card">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-journal-check text-primary fs-5"></i>
            <h5 class="fw-bold mb-0">Danh sách đoàn khách</h5>
            <span class="badge bg-light text-primary border rounded-pill">{{ count($guests) }} KHÁCH</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hdv table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 60px;">STT</th>
                        <th>Họ và tên</th>
                        <th>Thông tin</th>
                        <th>Check-in</th>
                        <th>Ghi chú đặc biệt</th>
                    </tr>
                </thead>
                <tbody>
                    @if(empty($guests))
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                Chưa có khách hàng.
                            </td>
                        </tr>
                    @else
                        @foreach($guests as $index => $g)
                            <tr>
                                <td class="fw-bold text-muted">#{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $g['full_name'] }}</div>
                                    @if(!empty($g['dob']))
                                        <div class="text-muted small"><i class="bi bi-cake2 me-1"></i> {{ date('d/m/Y', strtotime($g['dob'])) }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($g['phone']))
                                        <div><i class="bi bi-telephone me-1 text-primary"></i> {{ $g['phone'] }}</div>
                                    @endif
                                    @if(!empty($g['email']))
                                        <div class="text-muted small"><i class="bi bi-envelope me-1"></i> {{ $g['email'] }}</div>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('hdv/guest/check-in') }}" class="d-inline">
                                        <input type="hidden" name="guest_id" value="{{ $g['id'] }}">
                                        <input type="hidden" name="departure_id" value="{{ $selectedDepartureId }}">
                                        @if($g['check_in_status'] == 1)
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">
                                                <i class="bi bi-check-lg"></i> Đã Check-in
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                <i class="bi bi-circle"></i> Chưa Check-in
                                            </button>
                                        @endif
                                    </form>
                                </td>
                                <td>
                                    @if(!empty($g['note']))
                                        <span class="badge bg-warning-subtle text-dark border border-warning">{{ $g['note'] }}</span>
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
