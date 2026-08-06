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

    <div class="mb-3">
        <a href="{{ route('admin/bookings/create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm Booking
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
                                    $pickup = !empty($booking['pickup_address']) ? $booking['pickup_address'] : (!empty($booking['departure_meeting_point']) ? $booking['departure_meeting_point'] : null);
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
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-booking-guests"
                                            data-booking-id="{{ $booking['id'] }}"
                                            data-tour-id="{{ $booking['tour_id'] }}"
                                            title="Xem danh sách {{ $booking['num_people'] }} người tham gia booking #{{ $booking['id'] }}">
                                            <i class="bi bi-people"></i> Khách tour
                                        </button>
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

<div class="modal fade" id="bookingGuestsModal" tabindex="-1" aria-labelledby="bookingGuestsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title fw-semibold" id="bookingGuestsModalLabel">
                    <i class="bi bi-people text-primary me-2"></i>Danh sách người tham gia booking
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="bookingGuestsModalBody">
                <div class="text-center py-5 text-muted">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div>Đang tải danh sách người tham gia...</div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <a id="bookingGuestsGoTourParticipants" href="#" class="btn btn-outline-primary btn-sm me-auto d-none" target="_blank" rel="noopener">
                    <i class="bi bi-diagram-3 me-1"></i>Xem khách của toàn tour
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    Đóng
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal      = document.getElementById('bookingGuestsModal');
    const modalBody  = document.getElementById('bookingGuestsModalBody');
    const goTourBtn  = document.getElementById('bookingGuestsGoTourParticipants');
    const modalTitle = document.getElementById('bookingGuestsModalLabel');

    const spinnerHTML = '<div class="text-center py-5 text-muted"><div class="spinner-border text-primary mb-3" role="status"><span class="visually-hidden">Loading...</span></div><div>Đang tải danh sách người tham gia...</div></div>';
    const errorHTML   = (msg) => `<div class="alert alert-danger mb-0"><i class="bi bi-exclamation-triangle me-2"></i>${msg}</div>`;
    let currentFetchCtrl = null;

    function getBaseUrl() {
        const path = window.location.pathname;
        const adminIdx = path.indexOf('/admin');
        if (adminIdx >= 0) {
            return path.slice(0, adminIdx);
        }
        return '';
    }

    document.body.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-booking-guests');
        if (!btn) return;
        e.preventDefault();

        const bookingId = parseInt(btn.dataset.bookingId, 10);
        const tourId    = parseInt(btn.dataset.tourId, 10);
        if (!bookingId || bookingId <= 0) return;

        const titleText = btn.getAttribute('title') || '';
        modalTitle.innerHTML = '<i class="bi bi-people text-primary me-2"></i>Danh sách người tham gia booking #' + bookingId + (titleText ? ` <small class="text-muted fw-normal ms-2">${titleText}</small>` : '');
        modalBody.innerHTML = spinnerHTML;
        goTourBtn.classList.add('d-none');
        if (tourId > 0) {
            goTourBtn.href = getBaseUrl() + '/admin/tours/participants/' + tourId;
            goTourBtn.classList.remove('d-none');
        }

        if (currentFetchCtrl && typeof currentFetchCtrl.abort === 'function') {
            try { currentFetchCtrl.abort(); } catch (err) {}
        }
        currentFetchCtrl = ('AbortController' in window) ? new AbortController() : null;

        const base = getBaseUrl();
        const url  = base + '/admin/bookings/guests/' + bookingId;

        const fetchOptions = {
            method: 'GET',
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        };
        if (currentFetchCtrl) fetchOptions.signal = currentFetchCtrl.signal;

        fetch(url, fetchOptions)
            .then(function (res) {
                if (!res.ok) {
                    throw new Error('HTTP ' + res.status + ' - ' + (res.statusText || 'Lỗi'));
                }
                return res.text();
            })
            .then(function (html) {
                modalBody.innerHTML = html || errorHTML('Nội dung trống.');
            })
            .catch(function (err) {
                if (err && err.name === 'AbortError') return;
                modalBody.innerHTML = errorHTML('Không thể tải danh sách khách. Lỗi: ' + (err && err.message ? err.message : 'Unknown'));
            });

        if (window.bootstrap && window.bootstrap.Modal) {
            const bsModal = window.bootstrap.Modal.getOrCreateInstance(modal);
            bsModal.show();
        }
    });
});
</script>
@endsection
