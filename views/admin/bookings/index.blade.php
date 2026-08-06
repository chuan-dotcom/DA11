@extends('layouts.admin')

@section('title', $title)

@section('content')                
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
    @if(isset($_SESSION['success']))
        <div class="alert alert-success">{{ $_SESSION['success'] }}</div>
        @php unset($_SESSION['success']); @endphp
    @endif

    <div class="mb-3 d-flex flex-wrap gap-2 align-items-end">
        <a href="{{ route('admin/bookings/create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm Booking
        </a>
        <form method="get" class="d-flex flex-wrap gap-2 align-items-end ms-auto">
            <div>
                <label for="filter_tour_id" class="form-label small mb-1">Tour</label>
                <select name="tour_id" id="filter_tour_id" class="form-select form-select-sm">
                    <option value="">Tất cả</option>
                    @foreach($tours as $t)
                        <option value="{{ $t['id'] }}" {{ (isset($tourId) && (int)$tourId === (int)$t['id']) ? 'selected' : '' }}>
                            {{ $t['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="filter_departure_id" class="form-label small mb-1">Chuyến khởi hành</label>
                <select name="departure_id" id="filter_departure_id" class="form-select form-select-sm">
                    <option value="">Tất cả</option>
                    @foreach($departures as $d)
                        <option value="{{ $d['id'] }}" data-tour="{{ (int)($d['tour_id'] ?? 0) }}" {{ (isset($departureId) && (int)$departureId === (int)$d['id']) ? 'selected' : '' }}>
                            #{{ $d['id'] }} - {{ $d['group_name'] ?? ('Đoàn ' . ($d['tour_name'] ?? 'Tour')) }} ({{ !empty($d['departure_date']) ? date('d/m/Y', strtotime($d['departure_date'])) : '-' }})
                        </option>
                    @endforeach
                </select>
                <div class="form-text small opacity-75">Chọn Tour ở trên sẽ tự lọc chuyến khởi hành phù hợp</div>
            </div>
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
            @if(!empty($tourId) || !empty($departureId) || $status !== null)
                <a href="{{ route('admin/bookings') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg me-1"></i>Xóa lọc
                </a>
            @endif
        </form>
    </div>

    @if(!empty($departureId) || !empty($tourId) || $status !== null)
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
            if (!empty($departureId)) {
                foreach ($departures as $d) {
                    if ((int)$d['id'] === (int)$departureId) {
                        $name = !empty($d['group_name']) ? $d['group_name'] : ('Đoàn #' . (int)$d['id']);
                        $date = !empty($d['departure_date']) ? date('d/m/Y', strtotime($d['departure_date'])) : '-';
                        $filterParts[] = 'Chuyến khởi hành: <strong>' . htmlspecialchars($name) . ' (' . $date . ')</strong>';
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
            </div>
        @endif
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Tour</th>
                            <th>Chuyến khởi hành</th>
                            <th>Địa chỉ tour</th>
                            <th>Khách hàng</th>
                            <th>Email</th>
                            <th>SĐT</th>
                            <th>Địa chỉ đón</th>
                            <th>Số người</th>
                            <th>Tổng tiền</th>
                            <th>Ngày đặt</th>
                            <th>Trạng thái</th>
                            <th width="360">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($bookings))
                            <tr>
                                <td colspan="13" class="text-center">Chưa có dữ liệu</td>
                            </tr>
                        @else
                            @php
                                $departureStatusMap = [
                                    'scheduled'   => ['Lên lịch', 'bg-primary'],
                                    'in_progress' => ['Đang diễn ra', 'bg-warning text-dark'],
                                    'completed'   => ['Hoàn thành', 'bg-success'],
                                    'cancelled'   => ['Đã hủy', 'bg-secondary'],
                                ];
                            @endphp
                            @foreach($bookings as $booking)
                                @php
                                    $pickup = !empty($booking['pickup_address']) ? $booking['pickup_address'] : (!empty($booking['departure_meeting_point']) ? $booking['departure_meeting_point'] : null);
                                @endphp
                                <tr>
                                    <td>{{ $booking['id'] }}</td>
                                    <td>{{ $booking['tour_name'] }}</td>
                                    <td>
                                        @if(!empty($booking['departure_id']))
                                            <div class="d-flex flex-column gap-1">
                                                <div class="d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-calendar-week text-primary me-1"></i>
                                                    <span class="fw-semibold text-dark">
                                                        {{ !empty($booking['departure_group_name']) ? $booking['departure_group_name'] : ('Đoàn #' . (int)$booking['departure_id']) }}
                                                    </span>
                                                </div>
                                                <div class="small text-muted">
                                                    {{ !empty($booking['departure_date_info']) ? date('d/m/Y', strtotime($booking['departure_date_info'])) : '-' }}
                                                    @if(!empty($booking['departure_return_date']) && !empty($booking['departure_date_info']) && $booking['departure_return_date'] !== $booking['departure_date_info'])
                                                        <span class="mx-1">→</span>
                                                        {{ date('d/m/Y', strtotime($booking['departure_return_date'])) }}
                                                    @endif
                                                </div>
                                                @php
                                                    $ds = !empty($booking['departure_status']) ? $departureStatusMap[$booking['departure_status']] ?? null : null;
                                                @endphp
                                                @if($ds)
                                                    <div><span class="badge {{ $ds[1] }}" style="width:fit-content">{{ $ds[0] }}</span></div>
                                                @endif
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    <a href="{{ route('admin/departures/edit/' . (int)$booking['departure_id']) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-box-arrow-up-right me-1"></i>Mở đoàn
                                                    </a>
                                                    <a href="{{ route('admin/bookings/unassign-departure/' . (int)$booking['id']) }}"
                                                       class="btn btn-sm btn-outline-warning"
                                                       onclick="return confirm('Gỡ booking này ra khỏi đoàn khởi hành hiện tại?')">
                                                        <i class="bi bi-x-circle me-1"></i>Gỡ
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            <div class="d-flex flex-column gap-1">
                                                <span class="text-muted small">Chưa gắn vào đoàn</span>
                                                <a href="{{ route('admin/guest-groups/show/' . (int)$booking['tour_id']) }}" class="btn btn-sm btn-outline-success w-100">
                                                    <i class="bi bi-link-45deg me-1"></i>Gắn vào đoàn
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($booking['tour_location']))
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-geo-alt me-1"></i>{{ $booking['tour_location'] }}
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $booking['customer_name'] }}</td>
                                    <td>{{ $booking['customer_email'] }}</td>
                                    <td>{{ $booking['customer_phone'] }}</td>
                                    <td>
                                        @if(!empty($pickup))
                                            <span class="d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-geo text-primary"></i>
                                                <span style="max-width:200px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ htmlentities($pickup) }}">{{ $pickup }}</span>
                                            </span>
                                            @if(!empty($booking['pickup_address']) && !empty($booking['departure_meeting_point']) && $booking['pickup_address'] !== $booking['departure_meeting_point'])
                                                <div><small class="text-muted">Điểm tập kết: {{ $booking['departure_meeting_point'] }}</small></div>
                                            @endif
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
                                        @if(!empty($booking['departure_id']))
                                            <a href="{{ route('admin/tours/participants/' . $booking['tour_id']) }}" class="btn btn-outline-primary btn-sm" title="Xem danh sách khách của tour này">
                                                <i class="bi bi-people"></i> Khách tour
                                            </a>
                                        @else
                                            <a href="{{ route('admin/tours/participants/' . $booking['tour_id']) }}" class="btn btn-outline-primary btn-sm" title="Xem danh sách khách của tour này">
                                                <i class="bi bi-people"></i> Khách tour
                                            </a>
                                        @endif
                                        <a href="{{ route('admin/bookings/show/' . $booking['id']) }}" class="btn btn-info btn-sm">Chi tiết</a>
                                        <a href="{{ route('admin/bookings/edit/' . $booking['id']) }}" class="btn btn-warning btn-sm">Sửa</a>
                                        <a href="{{ route('admin/bookings/delete/' . $booking['id']) }}" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
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
document.addEventListener('DOMContentLoaded', function() {
    const tourSel = document.getElementById('filter_tour_id');
    const depSel = document.getElementById('filter_departure_id');
    if (!tourSel || !depSel) return;
    const depOpts = Array.from(depSel.options).filter(o => o.value !== '');
    function filterDeps() {
        const tourVal = tourSel.value ? parseInt(tourSel.value, 10) : 0;
        const prev = depSel.value;
        depSel.innerHTML = '';
        const allOpt = document.createElement('option');
        allOpt.value = '';
        allOpt.textContent = 'Tất cả';
        depSel.appendChild(allOpt);
        depOpts.forEach(o => {
            const t = parseInt(o.getAttribute('data-tour') || '0', 10);
            if (!tourVal || tourVal === t) {
                const copy = o.cloneNode(true);
                depSel.appendChild(copy);
            }
        });
        depSel.value = prev;
    }
    tourSel.addEventListener('change', filterDeps);
    filterDeps();
});
</script>
@endsection
