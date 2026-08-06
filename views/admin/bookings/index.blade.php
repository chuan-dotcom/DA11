@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">{{ $title }}</h2>

    @if(isset($_SESSION['flash']['success']))
        <div class="alert alert-success py-2 mb-3">{{ $_SESSION['flash']['success'] }}</div>
        @php unset($_SESSION['flash']['success']); @endphp
    @endif
    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger py-2 mb-3">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif
    @if(isset($_SESSION['flash']['warning']))
        <div class="alert alert-warning py-2 mb-3">{{ $_SESSION['flash']['warning'] }}</div>
        @php unset($_SESSION['flash']['warning']); @endphp
    @endif
    @if(isset($_SESSION['flash']['info']))
        <div class="alert alert-info py-2 mb-3">{{ $_SESSION['flash']['info'] }}</div>
        @php unset($_SESSION['flash']['info']); @endphp
    @endif

    @php
        $countsMap = [];
        foreach ($statusCounts as $sc) {
            $countsMap[(int) $sc['status']] = (int) $sc['count'];
        }
        $totalBookings = array_sum($countsMap);
        $pending = $countsMap[0] ?? 0;
        $confirmed = $countsMap[1] ?? 0;
        $cancelled = $countsMap[2] ?? 0;
    @endphp

    <div class="row mb-3">
        <div class="col-md-3 mb-2">
            <div class="stat-card card text-bg-primary">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-journal-text fs-3 opacity-75"></i>
                        </div>
                        <div>
                            <div class="small opacity-75">Tổng Booking</div>
                            <div class="fs-4 fw-bold">{{ $totalBookings }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="stat-card card text-bg-warning">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-clock-history fs-3 opacity-75"></i>
                        </div>
                        <div>
                            <div class="small opacity-75">Chờ xác nhận</div>
                            <div class="fs-4 fw-bold">{{ $pending }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="stat-card card text-bg-success">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-check-circle fs-3 opacity-75"></i>
                        </div>
                        <div>
                            <div class="small opacity-75">Đã xác nhận</div>
                            <div class="fs-4 fw-bold">{{ $confirmed }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="stat-card card text-bg-danger">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-x-circle fs-3 opacity-75"></i>
                        </div>
                        <div>
                            <div class="small opacity-75">Đã hủy</div>
                            <div class="fs-4 fw-bold">{{ $cancelled }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body py-3">
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <a href="{{ route('admin/bookings/create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Thêm booking
                    </a>
                </div>
                <div class="col ms-auto">
                    <form action="{{ route('admin/bookings') }}" method="GET" class="d-flex flex-wrap gap-2 justify-content-end">
                        <div class="col-auto">
                            <select class="form-select form-select-sm" name="tour_id">
                                <option value="">-- Tất cả tour --</option>
                                @foreach($tours as $t)
                                    <option value="{{ $t['id'] }}" {{ $tourId && $tourId == $t['id'] ? 'selected' : '' }}>{{ $t['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <select class="form-select form-select-sm" name="status">
                                <option value="">-- Tất cả trạng thái --</option>
                                <option value="0" {{ $status !== null && (int) $status === 0 ? 'selected' : '' }}>Chờ xác nhận</option>
                                <option value="1" {{ $status !== null && (int) $status === 1 ? 'selected' : '' }}>Đã xác nhận</option>
                                <option value="2" {{ $status !== null && (int) $status === 2 ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <input type="date" class="form-control form-control-sm" name="from_date" value="{{ $fromDate }}">
                        </div>
                        <div class="col-auto">
                            <input type="date" class="form-control form-control-sm" name="to_date" value="{{ $toDate }}">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="bi bi-funnel"></i> Lọc
                            </button>
                            <a href="{{ route('admin/bookings') }}" class="btn btn-sm btn-secondary">
                                <i class="bi bi-x-circle"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Tour</th>
                            <th>Ngày đặt</th>
                            <th>Số người</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($bookings))
                            <tr>
                                <td colspan="8" class="text-center">Chưa có booking nào</td>
                            </tr>
                        @else
                            @foreach($bookings as $b)
                                <tr>
                                    <td>{{ $b['id'] }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $b['customer_name'] ?? '-' }}</div>
                                        <div class="small text-muted">{{ $b['customer_email'] ?? '' }} · {{ $b['customer_phone'] ?? '' }}</div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin/bookings/show/' . $b['id']) }}" class="fw-semibold text-decoration-none text-dark">
                                            {{ $b['tour_name'] ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>{{ !empty($b['booking_date']) ? date('d/m/Y', strtotime($b['booking_date'])) : '-' }}</td>
                                    <td class="text-center">{{ $b['num_people'] ?? 0 }}</td>
                                    <td class="text-end">{{ !empty($b['total_price']) ? number_format($b['total_price'], 0, ',', '.') . ' ₫' : '0 ₫' }}</td>
                                    <td>
                                        @php
                                            $statusInt = (int) ($b['status'] ?? 0);
                                            $statusMeta = match($statusInt) {
                                                1 => ['text' => 'Đã xác nhận', 'class' => 'bg-success'],
                                                2 => ['text' => 'Đã hủy', 'class' => 'bg-danger'],
                                                default => ['text' => 'Chờ xác nhận', 'class' => 'bg-warning text-dark'],
                                            };
                                        @endphp
                                        <span class="badge {{ $statusMeta['class'] }}">
                                            {{ $statusMeta['text'] }}
                                        </span>
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('admin/bookings/show/' . $b['id']) }}" class="btn btn-sm btn-info text-white">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin/bookings/edit/' . $b['id']) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('admin/bookings/delete/' . $b['id']) }}"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Bạn có chắc muốn xóa booking này?')">
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
