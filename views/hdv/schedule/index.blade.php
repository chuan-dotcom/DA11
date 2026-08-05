@extends('layouts.hdv')

@section('title', 'Lịch trình tour')

@section('content')

<div class="hdv-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-calendar3-range text-primary me-2"></i> Lịch Trình Khởi Hành Tour</h4>
            <p class="text-muted small mb-0">Danh sách các tour được phân công công tác cho {{ $activeHdv['Hoten'] ?? 'Hướng dẫn viên' }} theo thời gian (từ ngày nào đến ngày nào)</p>
        </div>
        <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill">{{ count($schedules) }} Chuyến công tác</span>
    </div>

    <!-- Timeline / Schedule List -->
    @if(empty($schedules))
        <div class="text-center py-5 text-muted">
            <i class="bi bi-calendar-x fs-1 d-block mb-3 opacity-50"></i>
            Chưa có lịch trình tour nào được ghi nhận cho hướng dẫn viên này.
        </div>
    @else
        <div class="row g-4">
            @foreach($schedules as $s)
                @php
                    $startDate = strtotime($s['departure_date']);
                    $endDate = $s['return_date'] ? strtotime($s['return_date']) : $startDate;
                    $today = strtotime(date('Y-m-d'));

                    $isPast = $endDate < $today;
                    $isCurrent = ($startDate <= $today && $endDate >= $today);
                    $isFuture = $startDate > $today;
                @endphp

                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden {{ $isCurrent ? 'border-start border-success border-5' : '' }}">
                        <div class="card-body p-4">
                            <div class="row align-items-center g-3">
                                <!-- Date Range Box -->
                                <div class="col-md-3 border-end">
                                    <div class="text-center p-3 rounded-3 {{ $isCurrent ? 'bg-success text-white' : ($isFuture ? 'bg-primary-subtle text-primary' : 'bg-light text-muted') }}">
                                        <div class="small text-uppercase fw-bold opacity-75">Từ ngày - Đến ngày</div>
                                        <div class="fs-5 fw-bold my-1">
                                            {{ date('d/m/Y', $startDate) }}
                                        </div>
                                        <div class="small fw-semibold">đến {{ date('d/m/Y', $endDate) }}</div>

                                        <div class="mt-2">
                                            @if($isCurrent)
                                                <span class="badge bg-white text-success fw-bold">ĐANG TIẾN HÀNH</span>
                                            @elseif($isFuture)
                                                <span class="badge bg-primary text-white fw-bold">SẼ TIẾN HÀNH</span>
                                            @else
                                                <span class="badge bg-secondary text-white fw-bold">ĐÃ HOÀN THÀNH</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Tour Details -->
                                <div class="col-md-6">
                                    <h5 class="fw-bold text-dark mb-2">
                                        <a href="{{ route('hdv/dashboard?tab=chi-tiet&departure_id=' . $s['departure_id']) }}" class="text-dark text-decoration-none hover-primary">
                                            {{ $s['tour_name'] }}
                                        </a>
                                    </h5>

                                    <div class="d-flex flex-wrap gap-3 text-muted small mb-3">
                                        <div><i class="bi bi-tags me-1 text-secondary"></i> <strong>Danh mục:</strong> {{ $s['category_name'] ?: 'Chưa phân loại' }}</div>
                                        <div><i class="bi bi-geo-alt me-1 text-danger"></i> <strong>Điểm hẹn:</strong> {{ $s['meeting_point'] ?: 'Chưa cập nhật' }}</div>
                                        <div><i class="bi bi-clock me-1 text-warning"></i> <strong>Giờ đón:</strong> {{ $s['meeting_time'] ?: 'Cập nhật sau' }}</div>
                                        <div><i class="bi bi-truck me-1 text-info"></i> <strong>Phương tiện:</strong> {{ $s['vehicle'] ?: 'Chưa xếp xe' }}</div>
                                    </div>

                                    @if(!empty($s['description']))
                                        <p class="text-muted small mb-0 text-truncate" style="max-width: 500px;">
                                            {{ $s['description'] }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Actions & Guest Count -->
                                <div class="col-md-3 text-md-end">
                                    <div class="mb-3">
                                        <span class="fs-4 fw-bold text-primary">{{ $s['total_guests'] }}</span>
                                        <span class="text-muted small">khách tham gia</span>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('hdv/tour-phan-cong?departure_id=' . $s['departure_id']) }}" class="btn btn-sm btn-primary rounded-pill fw-bold">
                                            <i class="bi bi-people me-1"></i> Xem danh sách khách
                                        </a>
                                        <a href="{{ route('hdv/dashboard?tab=chi-tiet&departure_id=' . $s['departure_id']) }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                                            <i class="bi bi-info-circle me-1"></i> Chi tiết tour
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection
