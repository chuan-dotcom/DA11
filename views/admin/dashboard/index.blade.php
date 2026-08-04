@extends('layouts.admin')

@section('title', $title)
                      
@section('content')
<style>
    .stat-card {
        border-radius: 10px;
        border: 1px solid #e8e8e8;
        background: #fff;
    }
    .stat-card .icon-box {
        width: 44px; height: 44px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; flex-shrink: 0;
    }
    .stat-card .stat-label  { font-size: 0.77rem; color: #888; margin-bottom: 1px; }
    .stat-card .stat-value  { font-size: 1.3rem; font-weight: 700; color: #1a1a1a; line-height: 1.2; }
    .stat-card .stat-sub    { font-size: 0.7rem; color: #bbb; margin-top: 1px; }
    .section-card {
        border: 1px solid #e8e8e8;
        border-radius: 10px;
        background: #fff;
        padding: 1rem 1.2rem;
        margin-bottom: 1.25rem;
    }
    .section-title { font-weight: 600; font-size: 0.93rem; color: #1a1a1a; }
    .table thead th { font-size: 0.81rem; font-weight: 600; color: #555; white-space: nowrap; }
    .table tbody td { font-size: 0.82rem; vertical-align: middle; }
    .revenue-total  { color: #16a34a; font-weight: 700; font-size: 0.9rem; }
</style>

<div class="container-fluid px-4 py-3">

    {{-- ======== HEADER ======== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">Báo cáo - Thống Kê</h5>
        <form method="GET" action="{{ route('admin/dashboard') }}" class="d-flex align-items-center gap-2">
            <span class="text-muted" style="font-size:.82rem;">Lọc tháng:</span>
            <select name="month" class="form-select form-select-sm" style="width:115px;">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                @endfor
            </select>
            <select name="year" class="form-select form-select-sm" style="width:85px;">
                @for($y = 2024; $y <= 2026; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="btn btn-sm btn-primary px-3">Xem</button>
        </form>
    </div>

    {{-- ======== 4 CARD TỔNG QUAN ======== --}}
    {{--
        Card 1: Tổng khách hàng          → từ bảng bookings (distinct email)
        Card 2: Tour đang mở (chờ xử lý) → bookings.status = 0
        Card 3: Doanh thu                → SUM bookings.total_price WHERE status=1
        Card 4: Hoàn thành / Tổng booking → bookings (kèm link)
    --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card p-3 d-flex align-items-center gap-3">
                <div class="icon-box" style="background:#f0f4ff;">
                    <i class="bi bi-people-fill" style="color:#4a6cf7;"></i>
                </div>
                <div>
                    <div class="stat-label">Tổng khách hàng</div>
                    <div class="stat-value">{{ $totalCustomers }}</div>
                    <div class="stat-sub">{{ $totalBookings }} lượt booking</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card p-3 d-flex align-items-center gap-3">
                <div class="icon-box" style="background:#fff7e6;">
                    <i class="bi bi-airplane-fill" style="color:#f59e0b;"></i>
                </div>
                <div>
                    <div class="stat-label">Tour đang mở</div>
                    <div class="stat-value">{{ $totalPendingBookings }}</div>
                    <div class="stat-sub">{{ $totalActiveTours }}/{{ $totalTours }} tour hiển thị</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card p-3 d-flex align-items-center gap-3">
                <div class="icon-box" style="background:#f0fdf4;">
                    <i class="bi bi-graph-up-arrow" style="color:#16a34a;"></i>
                </div>
                <div>
                    <div class="stat-label">Doanh thu</div>
                    <div class="stat-value text-success">{{ number_format($totalRevenue) }}đ</div>
                    <div class="stat-sub">Từ {{ $totalCompletedBookings }} booking hoàn thành</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card p-3 d-flex align-items-center gap-3">
                <div class="icon-box" style="background:#fdf4ff;">
                    <i class="bi bi-clock-history" style="color:#9333ea;"></i>
                </div>
                <div>
                    <div class="stat-label">Hoàn thành / Tổng booking</div>
                    <div class="stat-value">{{ $totalCompletedBookings }} / {{ $totalBookings }}</div>
                    <div class="stat-sub">
                        <a href="{{ route('admin/tours') }}" class="text-decoration-none" style="color:#9333ea;">
                            Đi đến quản lý booking
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======== HÀNG 2 BIỂU ĐỒ ======== --}}
    <div class="row g-3 mb-3">

        {{-- Biểu đồ line: Số booking theo ngày --}}
        <div class="col-md-8">
            <div class="section-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="section-title">Số tour được đặt</span>
                    <span class="text-muted" style="font-size:.8rem;">Tháng {{ $month }}/{{ $year }}</span>
                </div>
                <canvas id="chartBookings" height="100"></canvas>
            </div>
        </div>

        {{-- Biểu đồ donut: Số tour theo danh mục (từ bảng tours + tour_categories) --}}
        <div class="col-md-4">
            <div class="section-card h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="section-title">Tour theo danh mục</span>
                    <span class="text-muted" style="font-size:.8rem;">{{ $totalTours }} tour · {{ $totalCategories }} danh mục</span>
                </div>
                @if(empty($catChartCounts) || array_sum($catChartCounts) == 0)
                    <div class="text-muted text-center my-auto py-4" style="font-size:.85rem;">
                        Chưa có tour nào được phân loại.
                    </div>
                @else
                    <canvas id="chartCategory" style="max-height:210px;"></canvas>
                    <ul class="list-unstyled mt-3 mb-0" style="font-size:.8rem;">
                        @foreach($catChartLabels as $i => $label)
                            <li class="d-flex justify-content-between py-1 border-bottom">
                                <span>{{ $label }}</span>
                                <span class="fw-bold">{{ $catChartCounts[$i] }} tour</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- ======== BIỂU ĐỒ DOANH THU THEO NGÀY ======== --}}
    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="section-title">Doanh thu theo thời gian</span>
            <span class="text-muted" style="font-size:.8rem;">Tháng {{ $month }}/{{ $year }}</span>
        </div>
        <div class="mb-2">
            <span class="revenue-total">Tổng doanh thu: {{ number_format($totalRevenue) }}đ</span>
        </div>
        <canvas id="chartRevenue" height="80"></canvas>
    </div>

    {{-- ======== BẢNG TOUR ĐÃ HOÀN THÀNH ======== --}}
    {{--
        Lấy từ bookings JOIN tours JOIN tour_categories (status booking = 1)
        Nhóm theo tour, tính tổng booking + doanh thu + ngày cuối
    --}}
    <div class="section-card">
        <div class="section-title mb-3">Tour đã hoàn thành</div>
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tên tour</th>
                        <th>Danh mục</th>
                        <th class="text-center">Số booking</th>
                        <th>Doanh thu</th>
                        <th>Ngày booking cuối</th>
                        <th>Sự cố xảy ra</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $groupedCompleted = [];
                        foreach ($completedBookings as $bk) {
                            $tid = $bk['tour_id'];
                            if (!isset($groupedCompleted[$tid])) {
                                $groupedCompleted[$tid] = [
                                    'tour_id'       => $tid,
                                    'tour_name'     => $bk['tour_name'],
                                    'category_name' => $bk['category_name'] ?? 'Chưa phân loại',
                                    'count'         => 0,
                                    'revenue'       => 0,
                                    'last_date'     => $bk['booking_date'],
                                ];
                            }
                            $groupedCompleted[$tid]['count']++;
                            $groupedCompleted[$tid]['revenue'] += (int) $bk['total_price'];
                            if ($bk['booking_date'] > $groupedCompleted[$tid]['last_date']) {
                                $groupedCompleted[$tid]['last_date'] = $bk['booking_date'];
                            }
                        }
                    @endphp

                    @if(empty($groupedCompleted))
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">Chưa có booking nào hoàn thành.</td>
                        </tr>
                    @else
                        @foreach($groupedCompleted as $row)
                            <tr>
                                <td class="fw-semibold">
                                    <a href="{{ route('admin/tours/show/' . $row['tour_id']) }}" class="text-decoration-none text-dark">
                                        {{ $row['tour_name'] }}
                                    </a>
                                </td>
                                <td>{{ $row['category_name'] }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info text-dark">{{ $row['count'] }}</span>
                                </td>
                                <td class="fw-bold text-success">{{ number_format($row['revenue']) }}đ</td>
                                <td>{{ date('d/m/Y', strtotime($row['last_date'])) }}</td>
                                <td><span class="badge bg-success">Không có</span></td>
                                <td>
                                    <a href="{{ route('admin/tours/show/' . $row['tour_id']) }}"
                                       class="btn btn-sm btn-outline-primary py-0 px-2">Xem</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- ======== BẢNG QUẢN LÝ DANH SÁCH TOUR ======== --}}
    {{--
        Lấy từ tours JOIN tour_categories + LEFT JOIN bookings
        Hiển thị: tên tour, danh mục, thời gian, giá, số booking (đã hoàn thành), doanh thu, trạng thái tour
    --}}
    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="section-title">Quản lý danh sách Tour</span>
            <a href="{{ route('admin/tours/create') }}" class="btn btn-sm btn-success">+ Thêm tour</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tên tour</th>
                        <th>Danh mục</th>
                        <th>Thời gian</th>
                        <th>Giá tour</th>
                        <th class="text-center">Số booking</th>
                        <th>Doanh thu</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @if(empty($revenueByTour))
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">Chưa có tour nào.</td>
                        </tr>
                    @else
                        @foreach($revenueByTour as $t)
                            <tr>
                                <td class="fw-semibold">
                                    <a href="{{ route('admin/tours/show/' . ($t['tour_id'] ?? '')) }}" class="text-decoration-none text-dark">
                                        {{ $t['tour_name'] ?? '—' }}
                                    </a>
                                </td>
                                <td>{{ $t['category_name'] ?? '—' }}</td>
                                <td>{{ $t['duration'] ?? '—' }}</td>
                                <td>{{ number_format((float)($t['unit_price'] ?? 0)) }}đ</td>
                                <td class="text-center">
                                    @if(($t['booking_count'] ?? 0) > 0)
                                        <span class="badge bg-primary">{{ $t['booking_count'] }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td>
                                    @if(($t['revenue'] ?? 0) > 0)
                                        <span class="text-success fw-bold">{{ number_format((float)($t['revenue'] ?? 0)) }}đ</span>
                                    @else
                                        <span class="text-muted">0đ</span>
                                    @endif
                                </td>
                                <td>
                                    @if(($t['status'] ?? 0) == 1)
                                        <span class="badge bg-success">Hiển thị</span>
                                    @else
                                        <span class="badge bg-secondary">Ẩn</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin/tours/show/' . ($t['tour_id'] ?? '')) }}"
                                       class="btn btn-sm btn-outline-info py-0 px-2" title="Xem">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin/tours/edit/' . ($t['tour_id'] ?? '')) }}"
                                       class="btn btn-sm btn-outline-warning py-0 px-2" title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('admin/tours/delete/' . ($t['tour_id'] ?? '')) }}"
                                       class="btn btn-sm btn-outline-danger py-0 px-2"
                                       onclick="return confirm('Xóa tour này sẽ xóa luôn các booking liên quan. Tiếp tục?')"
                                       title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-2 text-muted" style="font-size:.78rem;">
            Tổng: <strong>{{ $totalTours }}</strong> tour &nbsp;|&nbsp;
            Hiển thị: <strong class="text-success">{{ $totalActiveTours }}</strong> &nbsp;|&nbsp;
            Đang ẩn: <strong class="text-secondary">{{ $totalTours - $totalActiveTours }}</strong> &nbsp;|&nbsp;
            Danh mục: <strong>{{ $totalCategories }}</strong>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ---- Biểu đồ line: số booking theo ngày ----
    new Chart(document.getElementById('chartBookings').getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Số booking',
                data: {!! json_encode($chartBookings) !!},
                borderColor: '#4a6cf7',
                backgroundColor: 'rgba(74, 108, 247, 0.10)',
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#4a6cf7',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    // ---- Biểu đồ donut: tour theo danh mục ----
    @if(!empty($catChartCounts) && array_sum($catChartCounts) > 0)
    new Chart(document.getElementById('chartCategory').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($catChartLabels, JSON_UNESCAPED_UNICODE) !!},
            datasets: [{
                data: {!! json_encode($catChartCounts) !!},
                backgroundColor: ['#4a6cf7','#f59e0b','#16a34a','#9333ea','#ef4444','#06b6d4'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } }
            },
            cutout: '60%'
        }
    });
    @endif

    // ---- Biểu đồ bar: doanh thu theo ngày ----
    new Chart(document.getElementById('chartRevenue').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Doanh thu (đ)',
                data: {!! json_encode($chartRevenue) !!},
                backgroundColor: 'rgba(134, 239, 172, 0.75)',
                borderColor: '#16a34a',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(v) {
                            if (v >= 1000000) return (v/1000000).toFixed(0) + 'M';
                            if (v >= 1000)    return (v/1000).toFixed(0) + 'K';
                            return v;
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
