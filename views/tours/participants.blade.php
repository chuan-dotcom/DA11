@extends('layouts.admin')

@section('title', $title)

@section('content')
<style>
    .tour-info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .stat-card {
        border-radius: 10px;
        padding: 1rem 1.25rem;
        border: 1px solid #e5e7eb;
        background: #fff;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,.06);
    }
    .stat-card .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #fff;
    }
    .stat-value {
        font-size: 1.6rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .stat-label {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 2px;
    }
    .tour-mini-thumb {
        width: 70px;
        height: 70px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid rgba(255,255,255,.3);
    }
    .badge-pill {
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 0.8rem;
    }
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">
            <i class="bi bi-people-fill"></i> {{ $title }}
        </h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin/tours') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-list-ul"></i> Danh sách tour
            </a>
            <a href="{{ route('admin/tours/show/' . $tour['id']) }}" class="btn btn-outline-info btn-sm">
                <i class="bi bi-info-circle"></i> Chi tiết tour
            </a>
        </div>
    </div>

    @if(isset($_SESSION['success']))
        <div class="alert alert-success">{{ $_SESSION['success'] }}</div>
        @php unset($_SESSION['success']); @endphp
    @endif
    @if(isset($_SESSION['error']))
        <div class="alert alert-danger">{{ $_SESSION['error'] }}</div>
        @php unset($_SESSION['error']); @endphp
    @endif
    @if(isset($_SESSION['flash']['success']))
        <div class="alert alert-success">{{ $_SESSION['flash']['success'] }}</div>
        @php unset($_SESSION['flash']['success']); @endphp
    @endif
    @if(isset($_SESSION['flash']['error']))
        <div class="alert alert-danger">{{ $_SESSION['flash']['error'] }}</div>
        @php unset($_SESSION['flash']['error']); @endphp
    @endif

    <div class="tour-info-card">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            @if(!empty($tour['image']))
                <img src="{{ file_url($tour['image']) }}" alt="{{ $tour['name'] }}" class="tour-mini-thumb">
            @else
                <div class="tour-mini-thumb d-flex align-items-center justify-content-center" style="background:rgba(255,255,255,.15);">
                    <i class="bi bi-image" style="font-size:1.6rem;opacity:.7;"></i>
                </div>
            @endif
            <div class="flex-grow-1">
                <h4 class="mb-1 fw-bold">{{ $tour['name'] }}</h4>
                <div class="d-flex flex-wrap gap-3 align-items-center small opacity-95">
                    <span>
                        <i class="bi bi-tag"></i>
                        {{ $tour['category_name'] ?? 'Chưa phân loại' }}
                    </span>
                    <span>
                        <i class="bi bi-clock"></i>
                        {{ $tour['duration'] ?: 'Chưa cập nhật' }}
                    </span>
                    <span>
                        <i class="bi bi-cash-stack"></i>
                        {{ number_format($tour['price']) }} VNĐ
                    </span>
                    <span>
                        <i class="bi bi-upc-scan"></i>
                        Mã tour #{{ $tour['id'] }}
                    </span>
                    @if($tour['status'] == 1)
                        <span class="badge badge-pill bg-light text-dark">
                            <i class="bi bi-eye"></i> Hiển thị
                        </span>
                    @else
                        <span class="badge badge-pill bg-light text-dark opacity-75">
                            <i class="bi bi-eye-slash"></i> Ẩn
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#3b82f6;">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div>
                    <div class="stat-value">{{ (int)($stats['total_bookings'] ?? 0) }}</div>
                    <div class="stat-label">Tổng lượt booking</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#8b5cf6;">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <div class="stat-value">{{ (int)($stats['total_people'] ?? 0) }}</div>
                    <div class="stat-label">Tổng số người đăng ký</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#10b981;">
                    <i class="bi bi-person-check"></i>
                </div>
                <div>
                    <div class="stat-value text-success">{{ (int)($stats['confirmed_people'] ?? 0) }}</div>
                    <div class="stat-label">Người đã xác nhận</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#ef4444;">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <div class="stat-value text-danger">{{ number_format((float)($stats['confirmed_revenue'] ?? 0)) }}</div>
                    <div class="stat-label">Doanh thu (VNĐ)</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#f59e0b;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="stat-value text-warning">{{ (int)($stats['pending_people'] ?? 0) }}</div>
                    <div class="stat-label">Người chờ xác nhận</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#06b6d4;">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div>
                    <div class="stat-value">{{ round((float)($stats['avg_people_per_booking'] ?? 0), 1) }}</div>
                    <div class="stat-label">Trung bình người / booking</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-list-check"></i> Danh sách người tham gia
            </h5>
            <div class="d-flex gap-2">
                <span class="badge bg-primary">
                    <i class="bi bi-person"></i> {{ (int)($stats['total_people'] ?? 0) }} người
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Tên khách hàng</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Số người</th>
                            <th>Tổng tiền</th>
                            <th>Ngày đi</th>
                            <th>Trạng thái</th>
                            <th>Ghi chú</th>
                            <th width="180">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($bookings))
                            <tr>
                                <td colspan="10" class="text-center">
                                    <div class="py-5 text-muted">
                                        <i class="bi bi-inbox" style="font-size:2.5rem;"></i>
                                        <div class="mt-2">Chưa có người tham gia tour này</div>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach($bookings as $booking)
                                <tr>
                                    <td>#{{ $booking['id'] }}</td>
                                    <td>
                                        <div class="fw-semibold">
                                            <i class="bi bi-person-circle me-1 text-muted"></i>
                                            {{ $booking['customer_name'] }}
                                        </div>
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $booking['customer_email'] }}" class="text-decoration-none text-dark">
                                            <i class="bi bi-envelope me-1 text-muted small"></i>
                                            {{ $booking['customer_email'] }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="tel:{{ $booking['customer_phone'] }}" class="text-decoration-none text-dark">
                                            <i class="bi bi-telephone me-1 text-muted small"></i>
                                            {{ $booking['customer_phone'] }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-white">
                                            <i class="bi bi-people me-1"></i>{{ $booking['num_people'] }}
                                        </span>
                                    </td>
                                    <td class="text-danger fw-semibold">
                                        {{ number_format($booking['total_price']) }} VNĐ
                                    </td>
                                    <td>
                                        <i class="bi bi-calendar3 me-1 text-muted small"></i>
                                        {{ date('d/m/Y', strtotime($booking['booking_date'])) }}
                                    </td>
                                    <td class="text-center">
                                        @if($booking['status'] == 0)
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-hourglass-split me-1"></i>Chờ xác nhận
                                            </span>
                                        @elseif($booking['status'] == 1)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check2-circle me-1"></i>Đã xác nhận
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i>Đã hủy
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($booking['note']))
                                            <span data-bs-toggle="tooltip" title="{{ htmlspecialchars($booking['note']) }}">
                                                {{ mb_strlen($booking['note']) > 20 ? mb_substr($booking['note'], 0, 20) . '...' : $booking['note'] }}
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('admin/bookings/show/' . $booking['id']) }}"
                                           class="btn btn-sm btn-info text-white" title="Xem chi tiết booking">
                                            <i class="bi bi-eye"></i> Xem
                                        </a>
                                        <a href="{{ route('admin/bookings/edit/' . $booking['id']) }}"
                                           class="btn btn-sm btn-warning" title="Sửa booking">
                                            <i class="bi bi-pencil"></i> Sửa
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
    });
</script>
@endsection
