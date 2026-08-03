@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
    .status-scheduled { background-color: #0d6efd; }
    .status-in_progress { background-color: #ffc107; color: #000; }
    .status-completed { background-color: #198754; }
    .status-cancelled { background-color: #6c757d; }
    .stat-card {
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
</style>

<div class="container mt-4">
    <h2 class="mb-4">{{ $title }}</h2>

    @if(isset($_SESSION['flash']['success']))
        <div class="alert alert-success">{{ $_SESSION['flash']['success'] }}</div>
        @php unset($_SESSION['flash']['success']); @endphp
    @endif
    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="row mb-4 g-3">
        @php
            $statusMap = [];
            foreach($statusCounts as $sc) { $statusMap[$sc['status']] = $sc['count']; }
        @endphp
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Tổng chuyến</div>
                            <div class="fs-4 fw-bold">{{ count($departures) }}</div>
                        </div>
                        <i class="bi bi-calendar3 fs-2 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Lên lịch</div>
                            <div class="fs-4 fw-bold text-primary">{{ $statusMap['scheduled'] ?? 0 }}</div>
                        </div>
                        <i class="bi bi-clock fs-2 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Đang diễn ra</div>
                            <div class="fs-4 fw-bold text-warning">{{ $statusMap['in_progress'] ?? 0 }}</div>
                        </div>
                        <i class="bi bi-play-circle fs-2 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Hoàn thành</div>
                            <div class="fs-4 fw-bold text-success">{{ $statusMap['completed'] ?? 0 }}</div>
                        </div>
                        <i class="bi bi-check-circle fs-2 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3 d-flex gap-2 flex-wrap">
        <a href="{{ route('admin/departures/create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Thêm chuyến khởi hành
        </a>
        <a href="{{ route('admin/staff-assignments') }}" class="btn btn-outline-info">
            <i class="bi bi-people"></i> Quản lý phân bổ
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Tour</th>
                            <th>Ngày khởi hành</th>
                            <th>Ngày trở về</th>
                            <th>Điểm tập trung</th>
                            <th>Phương tiện</th>
                            <th>Số khách</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($departures))
                            <tr>
                                <td colspan="9" class="text-center">Chưa có chuyến khởi hành nào</td>
                            </tr>
                        @else
                            @foreach($departures as $d)
                                <tr>
                                    <td>{{ $d['id'] }}</td>
                                    <td>
                                        <a href="{{ route('admin/departures/show/' . $d['id']) }}" class="fw-semibold text-decoration-none text-dark">
                                            {{ $d['tour_name'] ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>{{ date('d/m/Y', strtotime($d['departure_date'])) }}</td>
                                    <td>{{ $d['return_date'] ? date('d/m/Y', strtotime($d['return_date'])) : '-' }}</td>
                                    <td>{{ $d['meeting_point'] ?? '-' }}</td>
                                    <td>{{ $d['vehicle'] ?? '-' }}</td>
                                    <td class="text-center">{{ $d['max_participants'] }}</td>
                                    <td>
                                        @php
                                            $statusText = [
                                                'scheduled' => 'Lên lịch',
                                                'in_progress' => 'Đang diễn ra',
                                                'completed' => 'Hoàn thành',
                                                'cancelled' => 'Đã hủy'
                                            ];
                                        @endphp
                                        <span class="badge status-{{ $d['status'] }}">
                                            {{ $statusText[$d['status']] ?? $d['status'] }}
                                        </span>
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('admin/departures/show/' . $d['id']) }}" class="btn btn-sm btn-info text-white">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin/departures/edit/' . $d['id']) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('admin/staff-assignments/create') }}?departure_id={{ $d['id'] }}" class="btn btn-sm btn-success">
                                            <i class="bi bi-person-plus"></i>
                                        </a>
                                        <a href="{{ route('admin/departures/delete/' . $d['id']) }}"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Bạn có chắc muốn xóa chuyến khởi hành này?')">
                                            <i class="bi bi-trash"></i>
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
