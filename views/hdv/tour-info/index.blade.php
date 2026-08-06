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

    <!-- Section: Lịch trình hoạt động tour - IMPROVED TIMELINE -->
    <div class="hdv-card mb-4">
        <style>
            .timeline-container {
                position: relative;
                padding: 0;
            }
            
            .timeline-item-improved {
                position: relative;
                padding-left: 60px;
                margin-bottom: 24px;
            }
            
            .timeline-item-improved::before {
                content: '';
                position: absolute;
                left: 18px;
                top: 45px;
                width: 2px;
                height: calc(100% + 24px);
                background-color: #e5e7eb;
            }
            
            .timeline-item-improved:last-child::before {
                display: none;
            }
            
            .timeline-icon {
                position: absolute;
                left: 0;
                top: 0;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 18px;
                z-index: 2;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            }
            
            .timeline-icon.pickup { background: linear-gradient(135deg, #667eea, #764ba2); }
            .timeline-icon.departure { background: linear-gradient(135deg, #f093fb, #f5576c); }
            .timeline-icon.sightseeing { background: linear-gradient(135deg, #4facfe, #00f2fe); }
            .timeline-icon.lunch { background: linear-gradient(135deg, #fa709a, #fee140); }
            .timeline-icon.checkin { background: linear-gradient(135deg, #30cfd0, #330867); }
            .timeline-icon.return { background: linear-gradient(135deg, #a8edea, #fed6e3); }
            .timeline-icon.other { background: linear-gradient(135deg, #ff9a56, #ff6a88); }
            
            .activity-card {
                border: 0;
                border-left: 4px solid #e5e7eb;
                border-radius: 0.5rem;
                transition: all 0.3s ease;
            }
            
            .activity-card:hover {
                box-shadow: 0 8px 16px rgba(0,0,0,0.12);
                border-left-color: #667eea;
            }
            
            .activity-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 12px;
            }
            
            .activity-time {
                font-size: 13px;
                font-weight: 600;
                color: #667eea;
                background: #f0f4ff;
                padding: 4px 12px;
                border-radius: 12px;
                display: inline-block;
            }
            
            .activity-badges {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 12px;
            }
            
            .activity-badge {
                font-size: 12px;
                padding: 4px 10px;
                border-radius: 12px;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }
            
            .form-add-activity {
                background: linear-gradient(135deg, #667eea15, #764ba215);
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 20px;
            }
            
            .form-add-activity .form-label {
                color: #475569;
                font-weight: 600;
                margin-bottom: 8px;
            }
            
            .form-add-activity .form-control,
            .form-add-activity .form-select {
                border: 1px solid #cbd5e1;
                border-radius: 8px;
                font-size: 14px;
            }
            
            .form-add-activity .form-control:focus,
            .form-add-activity .form-select:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            }
            
            .btn-add-activity {
                background: linear-gradient(135deg, #667eea, #764ba2);
                border: 0;
                padding: 8px 20px;
                font-size: 14px;
                font-weight: 600;
                border-radius: 8px;
                color: white;
            }
            
            .btn-add-activity:hover {
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            }
            
            .empty-state-timeline {
                padding: 40px 20px;
                text-align: center;
            }
            
            .empty-state-timeline i {
                font-size: 48px;
                color: #cbd5e1;
                margin-bottom: 12px;
            }
        </style>

        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Lịch trình hoạt động tour</h5>
                        <p class="text-muted small mb-0">Ghi nhận chi tiết quá trình thực hiện chuyến tour</p>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border fw-normal">
                    <i class="bi bi-list-check me-1"></i>{{ count($tourLogs) }} hoạt động
                </span>
                <button class="btn btn-sm btn-add-activity" type="button" data-bs-toggle="collapse" data-bs-target="#add-tour-log" title="Thêm hoạt động mới">
                    <i class="bi bi-plus-lg me-1"></i> Thêm
                </button>
            </div>
        </div>

        <!-- Form thêm hoạt động -->
        <div class="collapse mb-4" id="add-tour-log">
            <form method="POST" action="{{ route('hdv/tour-logs/store') }}" class="form-add-activity">
                <input type="hidden" name="departure_id" value="{{ $selectedDepartureId }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tên hoạt động <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" placeholder="Ví dụ: Đón đoàn tại điểm hẹn, Tham quan bảo tàng, v.v..." required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Thời gian <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" name="log_date" value="{{ date('Y-m-d\\TH:i', strtotime(($currentTourDetail['departure_date'] ?? date('Y-m-d')) . ' ' . ($currentTourDetail['meeting_time'] ?: '06:00:00'))) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Địa điểm</label>
                        <input type="text" class="form-control" name="location" value="{{ $currentTourDetail['meeting_point'] ?? '' }}" placeholder="Nhập địa điểm">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="content" rows="3" placeholder="Mô tả chi tiết hoạt động, những gì đã xảy ra, tình hình đoàn khách, v.v..." required></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Thời tiết</label>
                        <input type="text" class="form-control" name="weather" placeholder="Ví dụ: Nắng đẹp, Có mưa, v.v...">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cảm nhận / Trạng thái</label>
                        <input type="text" class="form-control" name="mood" placeholder="Ví dụ: Đúng kế hoạch, Trễ 30 phút, v.v...">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-add-activity">
                                <i class="bi bi-save me-1"></i> Lưu hoạt động
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Timeline hiển thị -->
        @if(empty($tourLogs))
            <div class="empty-state-timeline">
                <i class="bi bi-inbox"></i>
                <h6 class="text-muted fw-semibold mt-2">Chưa có hoạt động nào</h6>
                <p class="text-muted small">Nhấn "Thêm" để bắt đầu ghi nhận lịch trình của chuyến tour</p>
            </div>
        @else
            <div class="timeline-container">
                @foreach($tourLogs as $index => $log)
                    @php
                        $activityType = 'other';
                        $titleLower = strtolower($log['title']);
                        
                        if (strpos($titleLower, 'đón') !== false) {
                            $activityType = 'pickup';
                        } elseif (strpos($titleLower, 'khởi hành') !== false || strpos($titleLower, 'khởi') !== false) {
                            $activityType = 'departure';
                        } elseif (strpos($titleLower, 'tham quan') !== false || strpos($titleLower, 'thăm') !== false) {
                            $activityType = 'sightseeing';
                        } elseif (strpos($titleLower, 'ăn') !== false || strpos($titleLower, 'cơm') !== false || strpos($titleLower, 'trưa') !== false) {
                            $activityType = 'lunch';
                        } elseif (strpos($titleLower, 'check') !== false || strpos($titleLower, 'khách sạn') !== false) {
                            $activityType = 'checkin';
                        } elseif (strpos($titleLower, 'về') !== false || strpos($titleLower, 'kết thúc') !== false || strpos($titleLower, 'trở về') !== false) {
                            $activityType = 'return';
                        }
                        
                        $iconMap = [
                            'pickup' => 'bi-person-plus-fill',
                            'departure' => 'bi-car-front-fill',
                            'sightseeing' => 'bi-camera-fill',
                            'lunch' => 'bi-cup-hot-fill',
                            'checkin' => 'bi-door-closed',
                            'return' => 'bi-house-fill',
                            'other' => 'bi-star-fill'
                        ];
                    @endphp
                    
                    <div class="timeline-item-improved">
                        <div class="timeline-icon {{ $activityType }}">
                            <i class="bi {{ $iconMap[$activityType] }}"></i>
                        </div>
                        
                        <div class="card activity-card">
                            <div class="card-body p-3">
                                <div class="activity-header">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">{{ $log['title'] }}</h6>
                                        <span class="activity-time">
                                            <i class="bi bi-clock me-1"></i>{{ date('H:i', strtotime($log['log_date'])) }} · {{ date('d/m', strtotime($log['log_date'])) }}
                                        </span>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary rounded-circle" type="button" data-bs-toggle="collapse" data-bs-target="#edit-tour-log-{{ $log['id'] }}" title="Chỉnh sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                </div>
                                
                                <p class="text-dark mb-2 mt-2">{{ $log['content'] }}</p>
                                
                                @if(!empty($log['location']))
                                    <div class="small text-muted mb-2">
                                        <i class="bi bi-geo-alt text-danger me-1"></i>
                                        <strong>{{ $log['location'] }}</strong>
                                    </div>
                                @endif
                                
                                @if(!empty($log['diary_id']))
                                    <div class="mb-2">
                                        <a href="{{ route('hdv/nhat-ky-tour/show/' . $log['diary_id']) }}" class="btn btn-sm btn-outline-info rounded-pill">
                                            <i class="bi bi-journal-text me-1"></i> Xem nhật ký: {{ $log['diary_title'] }}
                                        </a>
                                    </div>
                                @endif
                                
                                <div class="activity-badges">
                                    @if(!empty($log['weather']))
                                        <span class="activity-badge" style="background-color: #e0f2fe; color: #0369a1;">
                                            <i class="bi bi-cloud-sun"></i> {{ $log['weather'] }}
                                        </span>
                                    @endif
                                    @if(!empty($log['mood']))
                                        <span class="activity-badge" style="background-color: #fef3c7; color: #92400e;">
                                            <i class="bi bi-hand-thumbs-up"></i> {{ $log['mood'] }}
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Form chỉnh sửa (collapse) -->
                                <div class="collapse mt-3" id="edit-tour-log-{{ $log['id'] }}">
                                    <form method="POST" action="{{ route('hdv/tour-logs/update/' . $log['id']) }}" class="border-top pt-3">
                                        <input type="hidden" name="departure_id" value="{{ $selectedDepartureId }}">
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <label class="form-label small">Tên hoạt động</label>
                                                <input class="form-control form-control-sm" name="title" value="{{ $log['title'] }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small">Thời gian</label>
                                                <input class="form-control form-control-sm" type="datetime-local" name="log_date" value="{{ date('Y-m-d\\TH:i', strtotime($log['log_date'])) }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small">Địa điểm</label>
                                                <input class="form-control form-control-sm" name="location" value="{{ $log['location'] }}" placeholder="Địa điểm">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small">Nội dung</label>
                                                <textarea class="form-control form-control-sm" name="content" rows="2" required>{{ $log['content'] }}</textarea>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small">Thời tiết</label>
                                                <input class="form-control form-control-sm" name="weather" value="{{ $log['weather'] }}" placeholder="Thời tiết">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small">Cảm nhận</label>
                                                <input class="form-control form-control-sm" name="mood" value="{{ $log['mood'] }}" placeholder="Cảm nhận">
                                            </div>
                                            <div class="col-md-4"></div>
                                            <div class="col-12 d-flex justify-content-end gap-2">
                                                <button type="submit" formaction="{{ route('hdv/tour-logs/delete/' . $log['id']) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa hoạt động này? Không thể hoàn tác!')">
                                                    <i class="bi bi-trash"></i> Xóa
                                                </button>
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-check2"></i> Cập nhật
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
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
