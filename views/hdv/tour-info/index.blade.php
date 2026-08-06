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
                                    <div class="card-header bg-success text-white fw-bold d-flex justify-content-between">
                                        <span>#{{ $t['departure_id'] }} - {{ $t['tour_name'] }}</span>
                                        <span class="badge bg-light text-success">Đang đi</span>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text mb-1"><i class="bi bi-tags text-muted me-1"></i> <strong>Danh mục:</strong> {{ $t['category_name'] ?: 'Chưa phân loại' }}</p>
                                        <p class="card-text mb-1"><i class="bi bi-calendar3 text-muted me-1"></i> <strong>Khởi hành:</strong> {{ date('d/m/Y', strtotime($t['departure_date'])) }}</p>
                                        <p class="card-text mb-1"><i class="bi bi-geo-alt text-muted me-1"></i> <strong>Điểm hẹn:</strong> {{ $t['meeting_point'] ?: 'Chưa cập nhật' }}</p>
                                        <p class="card-text mb-3"><i class="bi bi-truck text-muted me-1"></i> <strong>Xe:</strong> {{ $t['vehicle'] ?: 'Chưa gán' }}</p>
                                        <a href="{{ route('hdv/dashboard?tab=chi-tiet&departure_id=' . $t['departure_id']) }}" class="btn btn-sm btn-success w-100 fw-bold">
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
                                    <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between">
                                        <span>#{{ $t['departure_id'] }} - {{ $t['tour_name'] }}</span>
                                        <span class="badge bg-light text-primary">Sắp khởi hành</span>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text mb-1"><i class="bi bi-calendar3 text-muted me-1"></i> <strong>Khởi hành:</strong> {{ date('d/m/Y', strtotime($t['departure_date'])) }}</p>
                                        <p class="card-text mb-1"><i class="bi bi-clock text-muted me-1"></i> <strong>Giờ tập trung:</strong> {{ $t['meeting_time'] ?: 'Chưa cập nhật' }}</p>
                                        <p class="card-text mb-3"><i class="bi bi-geo-alt text-muted me-1"></i> <strong>Điểm hẹn:</strong> {{ $t['meeting_point'] ?: 'Chưa cập nhật' }}</p>
                                        <a href="{{ route('hdv/dashboard?tab=chi-tiet&departure_id=' . $t['departure_id']) }}" class="btn btn-sm btn-primary w-100 fw-bold">
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
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-secondary shadow-sm rounded-4 overflow-hidden">
                                    <div class="card-header bg-secondary text-white fw-bold d-flex justify-content-between">
                                        <span>#{{ $t['departure_id'] }} - {{ $t['tour_name'] }}</span>
                                        <span class="badge bg-light text-dark">Đã kết thúc</span>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text mb-1"><i class="bi bi-calendar-check text-muted me-1"></i> <strong>Khởi hành:</strong> {{ date('d/m/Y', strtotime($t['departure_date'])) }}</p>
                                        <p class="card-text mb-3"><i class="bi bi-flag text-muted me-1"></i> <strong>Kết thúc:</strong> {{ $t['return_date'] ? date('d/m/Y', strtotime($t['return_date'])) : '-' }}</p>
                                        <a href="{{ route('hdv/dashboard?tab=chi-tiet&departure_id=' . $t['departure_id']) }}" class="btn btn-sm btn-outline-secondary w-100 fw-bold">
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
                                    #{{ $tour['departure_id'] }} - {{ $tour['tour_name'] }} ({{ date('d/m/Y', strtotime($tour['departure_date'])) }})
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
        <p class="text-muted small mb-3">Danh mục tour hiện tại: {{ $currentTourDetail['category_name'] ?? 'Chưa phân loại' }}</p>
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
                        <div class="text-warning small" style="font-size: 0.8rem;">★ 4.8/5 (120 đánh giá)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($importantAlerts))
        <div class="hdv-card mb-4 border-warning">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                <h5 class="fw-bold mb-0">Cảnh báo quan trọng</h5>
                <span class="badge bg-warning text-dark rounded-pill">{{ count($importantAlerts) }}</span>
            </div>
            <div class="row g-3">
                @foreach($importantAlerts as $alert)
                    <div class="col-12 col-lg-6">
                        <div class="d-flex gap-3 h-100 rounded-4 border p-3 bg-{{ $alert['type'] }}-subtle">
                            <div class="fs-4 text-{{ $alert['type'] }}"><i class="bi {{ $alert['icon'] }}"></i></div>
                            <div>
                                <div class="fw-bold text-dark">{{ $alert['title'] }}</div>
                                <div class="small text-muted mt-1">{{ $alert['message'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="hdv-card mb-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-clock-history text-secondary fs-5"></i>
            <h5 class="fw-bold mb-0">Lịch trình hoạt động tour</h5>
            <span class="badge bg-secondary text-white border rounded-pill">{{ count($tourLogs) }} hoạt động</span>
            <button class="btn btn-sm btn-primary ms-auto rounded-pill px-3" type="button" data-bs-toggle="collapse" data-bs-target="#add-tour-log">
                <i class="bi bi-plus-lg me-1"></i> Thêm hoạt động
            </button>
        </div>

        <div class="collapse mb-4" id="add-tour-log">
            <form method="POST" action="{{ route('hdv/tour-logs/store') }}" class="border rounded-4 p-3 bg-light">
                <input type="hidden" name="departure_id" value="{{ $selectedDepartureId }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Tên hoạt động <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" placeholder="Ví dụ: Đón đoàn tại điểm hẹn" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Thời gian <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" name="log_date" value="{{ date('Y-m-d\\TH:i', strtotime(($currentTourDetail['departure_date'] ?? date('Y-m-d')) . ' ' . ($currentTourDetail['meeting_time'] ?: '06:00:00'))) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Địa điểm</label>
                        <input type="text" class="form-control" name="location" value="{{ $currentTourDetail['meeting_point'] ?? '' }}" placeholder="Địa điểm diễn ra">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nội dung <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="content" rows="2" placeholder="Mô tả chi tiết hoạt động" required></textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Thời tiết</label>
                        <input type="text" class="form-control" name="weather" placeholder="Ví dụ: Nắng nhẹ">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Trạng thái / cảm nhận</label>
                        <input type="text" class="form-control" name="mood" placeholder="Ví dụ: Đúng kế hoạch">
                    </div>
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-light" data-bs-toggle="collapse" data-bs-target="#add-tour-log">Hủy</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Lưu hoạt động</button>
                    </div>
                </div>
            </form>
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
                            <div class="d-flex gap-2">
                                @if(!empty($log['weather']))
                                    <span class="badge bg-primary-subtle text-primary rounded-pill">{{ $log['weather'] }}</span>
                                @endif
                                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#edit-tour-log-{{ $log['id'] }}" title="Cập nhật hoạt động"><i class="bi bi-pencil"></i></button>
                            </div>
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
                        <div class="collapse mt-3" id="edit-tour-log-{{ $log['id'] }}">
                            <form method="POST" action="{{ route('hdv/tour-logs/update/' . $log['id']) }}" class="border-top pt-3">
                                <input type="hidden" name="departure_id" value="{{ $selectedDepartureId }}">
                                <div class="row g-2">
                                    <div class="col-md-6"><input class="form-control form-control-sm" name="title" value="{{ $log['title'] }}" required></div>
                                    <div class="col-md-3"><input class="form-control form-control-sm" type="datetime-local" name="log_date" value="{{ date('Y-m-d\\TH:i', strtotime($log['log_date'])) }}" required></div>
                                    <div class="col-md-3"><input class="form-control form-control-sm" name="location" value="{{ $log['location'] }}" placeholder="Địa điểm"></div>
                                    <div class="col-md-6"><textarea class="form-control form-control-sm" name="content" rows="2" required>{{ $log['content'] }}</textarea></div>
                                    <div class="col-md-3"><input class="form-control form-control-sm" name="weather" value="{{ $log['weather'] }}" placeholder="Thời tiết"></div>
                                    <div class="col-md-3"><input class="form-control form-control-sm" name="mood" value="{{ $log['mood'] }}" placeholder="Trạng thái / cảm nhận"></div>
                                    <div class="col-12 d-flex justify-content-end gap-2">
                                        <button type="submit" formaction="{{ route('hdv/tour-logs/delete/' . $log['id']) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa hoạt động này?')"><i class="bi bi-trash"></i> Xóa</button>
                                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-save"></i> Cập nhật</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Section: Danh sách đoàn khách (matching Screenshot 1) -->
    <div class="hdv-card">
        @php
            $checkedInGuests = count(array_filter($guests, function ($guest) {
                return (int) ($guest['check_in_status'] ?? 0) === 1;
            }));
            $guestsWithNotes = count(array_filter($guests, function ($guest) {
                return !empty(trim((string) ($guest['note'] ?? '')));
            }));
        @endphp
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-journal-check text-primary fs-5"></i>
            <h5 class="fw-bold mb-0">Danh sách đoàn khách</h5>
            <span class="badge bg-light text-primary border rounded-pill">{{ count($guests) }} KHÁCH</span>
        </div>

        <div class="row g-2 mb-3">
            <div class="col-12 col-lg-6">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="search" id="guest-search" class="form-control" placeholder="Tìm theo tên, số điện thoại hoặc email…" autocomplete="off">
                </div>
            </div>
            <div class="col-12 col-lg-6 d-flex flex-wrap gap-2 align-items-center">
                <button type="button" class="btn btn-sm btn-primary guest-filter active" data-filter="all">Tất cả <span class="badge bg-white text-primary ms-1">{{ count($guests) }}</span></button>
                <button type="button" class="btn btn-sm btn-outline-secondary guest-filter" data-filter="pending">Chưa check-in <span class="badge bg-secondary ms-1">{{ count($guests) - $checkedInGuests }}</span></button>
                <button type="button" class="btn btn-sm btn-outline-success guest-filter" data-filter="checked-in">Đã check-in <span class="badge bg-success ms-1">{{ $checkedInGuests }}</span></button>
                @if($guestsWithNotes > 0)
                    <button type="button" class="btn btn-sm btn-outline-warning guest-filter" data-filter="has-note"><i class="bi bi-exclamation-circle me-1"></i>Có lưu ý <span class="badge bg-warning text-dark ms-1">{{ $guestsWithNotes }}</span></button>
                @endif
                <span id="guest-filter-result" class="small text-muted ms-lg-auto"></span>
            </div>
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
                            <tr class="guest-row" data-checkin="{{ (int) $g['check_in_status'] }}" data-has-note="{{ !empty(trim((string) ($g['note'] ?? ''))) ? '1' : '0' }}" data-search="{{ $g['full_name'] }} {{ $g['phone'] ?? '' }} {{ $g['email'] ?? '' }}">
                                <td class="fw-bold text-muted">#{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $g['full_name'] }}</div>
                                    @if(!empty($g['dob']))
                                        <div class="text-muted small"><i class="bi bi-cake2 me-1"></i> {{ date('d/m/Y', strtotime($g['dob'])) }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($g['phone']))
                                        <div>
                                            <i class="bi bi-telephone me-1 text-primary"></i>
                                            <a class="text-decoration-none fw-semibold" href="tel:{{ preg_replace('/[^0-9+]/', '', $g['phone']) }}" title="Gọi {{ $g['full_name'] }}">{{ $g['phone'] }}</a>
                                        </div>
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
                                        <div class="d-flex align-items-start gap-1 text-warning-emphasis">
                                            <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                                            <span class="badge bg-warning-subtle text-dark border border-warning text-wrap text-start">{{ $g['note'] }}</span>
                                        </div>
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
        <div id="guest-empty-filter" class="text-center text-muted py-4 d-none">
            <i class="bi bi-search fs-3 d-block mb-2"></i>
            Không tìm thấy khách phù hợp.
        </div>
    </div>

    <script>
        (function () {
            const searchInput = document.getElementById('guest-search');
            const filterButtons = document.querySelectorAll('.guest-filter');
            const rows = document.querySelectorAll('.guest-row');
            const emptyState = document.getElementById('guest-empty-filter');
            const result = document.getElementById('guest-filter-result');
            let activeFilter = 'all';

            function applyGuestFilter() {
                const keyword = (searchInput.value || '').trim().toLocaleLowerCase('vi-VN');
                let visible = 0;

                rows.forEach(function (row) {
                    const searchable = (row.dataset.search || '').toLocaleLowerCase('vi-VN');
                    const matchesKeyword = !keyword || searchable.includes(keyword);
                    const isCheckedIn = row.dataset.checkin === '1';
                    const matchesFilter = activeFilter === 'all'
                        || (activeFilter === 'pending' && !isCheckedIn)
                        || (activeFilter === 'checked-in' && isCheckedIn)
                        || (activeFilter === 'has-note' && row.dataset.hasNote === '1');
                    const shouldShow = matchesKeyword && matchesFilter;

                    row.classList.toggle('d-none', !shouldShow);
                    if (shouldShow) visible++;
                });

                emptyState.classList.toggle('d-none', visible !== 0);
                result.textContent = 'Hiển thị ' + visible + '/' + rows.length + ' khách';
            }

            searchInput.addEventListener('input', applyGuestFilter);
            filterButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    activeFilter = button.dataset.filter;
                    filterButtons.forEach(function (item) {
                        item.classList.remove('active', 'btn-primary', 'btn-outline-primary', 'btn-outline-secondary', 'btn-outline-success', 'btn-outline-warning');
                        if (item !== button) {
                            item.classList.add(item.dataset.filter === 'has-note' ? 'btn-outline-warning' : (item.dataset.filter === 'checked-in' ? 'btn-outline-success' : 'btn-outline-secondary'));
                        }
                    });
                    button.classList.add('active', 'btn-primary');
                    button.classList.remove('btn-outline-primary', 'btn-outline-secondary', 'btn-outline-success', 'btn-outline-warning');
                    applyGuestFilter();
                });
            });

            applyGuestFilter();
        })();
    </script>
@endif

@endsection
