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
                            <th>Địa chỉ tour</th>
                            <th>Khách hàng</th>
                            <th>Email</th>
                            <th>SĐT</th>
                            <th>Địa chỉ đón</th>
                            <th>Số người</th>
                            <th>Tổng tiền</th>
                            <th>Ngày đặt</th>
                            <th>Trạng thái</th>
                            <th width="320">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(empty($bookings))
                            <tr>
                                <td colspan="12" class="text-center">Chưa có dữ liệu</td>
                            </tr>
                        @else
                            @foreach($bookings as $booking)
                                @php
                                    $pickup = !empty($booking['pickup_address']) ? $booking['pickup_address'] : null;
                                @endphp
                                <tr>
                                    <td>{{ $booking['id'] }}</td>
                                    <td>{{ $booking['tour_name'] }}</td>
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
