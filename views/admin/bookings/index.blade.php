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
<<<<<<< HEAD
            <div>
                <label for="filter_status" class="form-label small mb-1">Trạng thái</label>
                <select name="status" id="filter_status" class="form-select form-select-sm">
                    <option value="">Tất cả</option>
                    <option value="0" {{ (isset($status) && (string)$status === '0') ? 'selected' : '' }}>Chờ xác nhận</option>
                    <option value="1" {{ (isset($status) && (string)$status === '1') ? 'selected' : '' }}>Đã xác nhận</option>
                    <option value="2" {{ (isset($status) && (string)$status === '2') ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-funnel me-1"></i>Lọc
            </button>
            @if(!empty($tourId) || $status !== null)
                <a href="{{ route('admin/bookings') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg me-1"></i>Xóa lọc
                </a>
            @endif
        </form>
    </div>

    @if(!empty($tourId) || $status !== null)
        @php
            $filterParts = [];
            if (!empty($tourId)) {
                foreach ($tours as $t) {
                    if ((int)$t['id'] === (int)$tourId) {
                        $filterParts[] = 'Tour: <strong>' . htmlspecialchars($t['name']) . '</strong>';
                        break;
                    }
                }
            }
            if ($status !== null && $status !== '') {
                $map = ['0' => 'Chờ xác nhận', '1' => 'Đã xác nhận', '2' => 'Đã hủy'];
                $filterParts[] = 'Trạng thái: <strong>' . ($map[(string)$status] ?? ((int)$status)) . '</strong>';
            }
        @endphp
        @if(!empty($filterParts))
            <div class="alert alert-info py-2 mb-3">
                Đang xem: {!! implode(' | ', $filterParts) !!}
=======
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
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
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
<<<<<<< HEAD
                            <th>Tour</th>
                            <th>Địa chỉ tour</th>
=======
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
                            <th>Khách hàng</th>
                            <th>Tour</th>
                            <th>Ngày đặt</th>
                            <th>Số người</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
<<<<<<< HEAD
                            <th width="320">Thao tác</th>
=======
                            <th>Thao tác</th>
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($bookings))
                            <tr>
<<<<<<< HEAD
                                <td colspan="12" class="text-center">Chưa có dữ liệu</td>
                            </tr>
                        @else
                            @foreach($bookings as $booking)
                                @php
                                    $pickup = !empty($booking['pickup_address']) ? $booking['pickup_address'] : null;
                                @endphp
=======
                                <td colspan="8" class="text-center">Chưa có booking nào</td>
                            </tr>
                        @else
                            @foreach($bookings as $b)
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
                                <tr>
                                    <td>{{ $b['id'] }}</td>
                                    <td>
<<<<<<< HEAD
                                        @if(!empty($booking['tour_location']))
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-geo-alt me-1"></i>{{ $booking['tour_location'] }}
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
=======
                                        <div class="fw-semibold">{{ $b['customer_name'] ?? '-' }}</div>
                                        <div class="small text-muted">{{ $b['customer_email'] ?? '' }} · {{ $b['customer_phone'] ?? '' }}</div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin/bookings/show/' . $b['id']) }}" class="fw-semibold text-decoration-none text-dark">
                                            {{ $b['tour_name'] ?? 'N/A' }}
                                        </a>
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
                                    </td>
                                    <td>{{ !empty($b['booking_date']) ? date('d/m/Y', strtotime($b['booking_date'])) : '-' }}</td>
                                    <td class="text-center">{{ $b['num_people'] ?? 0 }}</td>
                                    <td class="text-end">{{ !empty($b['total_price']) ? number_format($b['total_price'], 0, ',', '.') . ' ₫' : '0 ₫' }}</td>
                                    <td>
<<<<<<< HEAD
                                        @if(!empty($pickup))
                                            <span class="d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-geo text-primary"></i>
                                                <span style="max-width:200px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ htmlentities($pickup) }}">{{ $pickup }}</span>
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $booking['num_people'] }}</td>
                                    <td class="text-danger">{{ number_format($booking['total_price']) }} VNĐ</td>
                                    <td>{{ $booking['booking_date'] }}</td>
                                    <td>
                                        @if($booking['status'] == 0)
                                            <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                                        @elseif($booking['status'] == 1)
                                            <span class="badge bg-success">Đã xác nhận</span>
                                        @else
                                            <span class="badge bg-danger">Đã hủy</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('admin/tours/participants/' . $booking['tour_id']) }}" class="btn btn-outline-primary btn-sm" title="Xem danh sách khách của tour này">
                                            <i class="bi bi-people"></i> Khách tour
                                        </a>
                                        <a href="{{ route('admin/bookings/show/' . $booking['id']) }}" class="btn btn-info btn-sm">Chi tiết</a>
                                        <a href="{{ route('admin/bookings/edit/' . $booking['id']) }}" class="btn btn-warning btn-sm">Sửa</a>
                                        <a href="{{ route('admin/bookings/delete/' . $booking['id']) }}" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
=======
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
>>>>>>> aa059c0a460dbe3ab4b1a8320f08f6d7fe5b043c
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
